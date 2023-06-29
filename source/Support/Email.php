<?php

namespace Source\Support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
     * @param  mixed $message
     * @param  mixed $toEmail
     * @param  mixed $toName
     * @return Email
     */
    public function bootstrap(string $subject, string $message, string $toEmail, string $toName): Email
    {
        $this->data = new \stdClass();
        $this->data->subject = $subject;
        $this->data->message = $message;
        $this->data->toMail = $toEmail;
        $this->data->toName = $toName;

        return $this;
    }

    public function attach(string $filePath, string $fileName): Email
    {
        $this->data->attach[$filePath] = $fileName;
        return $this;
    }
    
    /**
     * send
     *
     * @param  mixed $fromEmail
     * @param  mixed $fromName
     * @return bool
     */
    public function send(string $fromEmail = CONF_MAIL_SENDER['address'], string $fromName = CONF_MAIL_SENDER['name']): bool
    {
        if (empty($this->data)) {
            $this->message->error("Erro ao enviar, favor verifique os dados");
            return false;
        }

        if (!is_email($this->data->toMail)) {
            $this->message->warning("O e-mail de destinatário não é válido");
            return false;
        }

        if (!is_email($fromEmail)) {
            $this->message->warning("O e-mail de remetente não é válido");
            return false;
        }

        try {
            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->message);
            $this->mail->addAddress($this->data->toMail, $this->data->toName);
            $this->mail->setFrom($fromEmail, $fromName);

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