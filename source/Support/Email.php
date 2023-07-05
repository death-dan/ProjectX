<?php

namespace Source\Support;

use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Source\Core\Connect;
use stdClass;

class Email
{
    /** @var object */
    private $data;

    /** @var PHPMailer */
    private $mail;

    /** @var Message */
    private $message;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->message = new Message();
        
        //SETUP
        $this->mail->isSMTP();
        $this->mail->setLanguage(CONF_MAIL_OPTION_LANG);
        $this->mail->isHTML(CONF_MAIL_OPTION_HTML);
        // $this->mail->SMTPauth = CONF_MAIL_OPTION_AUTH;
        $this->mail->SMTPSecure = CONF_MAIL_OPTION_SECURE;
        $this->mail->CharSet = CONF_MAIL_OPTION_CHARSET;

        //AUTH
        $this->mail->Host = CONF_MAIL_HOST;
        $this->mail->Port = CONF_MAIL_PORT;
        $this->mail->Username = CONF_MAIL_USER;
        $this->mail->Password = CONF_MAIL_PASS;   
    }
    
    /**
     * bootstrap
     *
     * @param  mixed $subject
     * @param  mixed $body
     * @param  mixed $recipient
     * @param  mixed $recipientName
     * @return Email
     */
    public function bootstrap(string $subject, string $body, string $recipient, string $recipientName): Email
    {
        $this->data = new \stdClass();
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->recipient_email = $recipient;
        $this->data->recipient_name = $recipientName;

        return $this;
    }
    
    /**
     * attach
     *
     * @param  mixed $filePath
     * @param  mixed $fileName
     * @return Email
     */
    public function attach(string $filePath, string $fileName): Email
    {
        $this->data->attach[$filePath] = $fileName;
        return $this;
    }
    
    /**
     * send
     *
     * @param  mixed $from
     * @param  mixed $fromName
     * @return bool
     */
    public function send(string $from = CONF_MAIL_SENDER['address'], string $fromName = CONF_MAIL_SENDER['name']): bool
    {
        if (empty($this->data)) {
            $this->message->error("Erro ao enviar, favor verifique os dados");
            return false;
        }

        if (!is_email($this->data->reciíent_email)) {
            $this->message->warning("O e-mail de destinatário não é válido");
            return false;
        }

        if (!is_email($from)) {
            $this->message->warning("O e-mail de remetente não é válido");
            return false;
        }

        try {
            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->body);
            $this->mail->addAddress($this->data->recipient_email, $this->data->recipient_name);
            $this->mail->setFrom($from, $fromName);

            if (!empty($this->data->attach)) {
                foreach ($this->data->attach as $path => $name) {
                    $this->mail->addAttachment($path, $name);
                }
            }

            $this->mail->send();
            return true;            

        } catch (Exception $exception) {
            $this->message->error($exception->getMessage());
            return false;
        }
    }
    
    /**
     * queue
     * Cria uma agenda de envio de e-mails
     *
     * @param  string $from
     * @param  string $fromName
     * @return bool
     */
    public function queue(string $from = CONF_MAIL_SENDER['address'], string $fromName = CONF_MAIL_SENDER['name']): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare(
                "INSERT INTO 
                mail_queue (subject, body, from_mail, from_name, recipient_email, recipient_name)
                VALUES (:subject, :body, :from_mail, :from_name, :recipient_email, :recipient_name)"
            );

            $stmt->bindValue(":subject", $this->data->subject, \PDO::PARAM_STR);
            $stmt->bindValue(":body", $this->data->body, \PDO::PARAM_STR);
            $stmt->bindValue(":from_mail", $from, \PDO::PARAM_STR);
            $stmt->bindValue(":from_name", $fromName, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_email", $this->data->recipient_email, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_name", $this->data->recipient_name, \PDO::PARAM_STR);


        } catch (\PDOException $exception) {
            $this->message->error($exception->getMessage());
            return false;
        }
    }
    
    /**
     * sendQueue
     * Envia os e-mails q estão agendados
     *
     * @param  int $perSecond
     * @return void
     */
    public function sendQueue(int $perSecond = 5)
    {
        $stmt = Connect::getInstance()->query("SELECT * FROM mail_queue WHERE sent_at IS NULL");
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll() as $send) {
                $email = $this->bootstrap(
                    $send->subject,
                    $send->body,
                    $send->recipient_email,
                    $send->recipient_name
                );
            }

            if ($email->send($send->from_email, $send->form_name)) {
                usleep(1000000 / $perSecond);
                Connect::getInstance()->exec("UPDATE mail_queue SET sent_at = NOW() WHERE id = {$send->id}");
            }
        }
    }
    
    /**
     * mail
     *
     * @return PHPMailer
     */
    public function mail(): PHPMailer
    {
        return $this->mail;
    }
    
    /**
     * message
     *
     * @return Message
     */
    public function message(): Message
    {
        return $this->message;
    }
}