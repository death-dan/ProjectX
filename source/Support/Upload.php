<?php

namespace Source\Support;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Uploader\Media;
use Source\Support\Message;

class Upload
{
    /** @var Message */
    private $message;

    public function __construct()
    {
        $this->message = new Message();
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
    
    /**
     * image
     *
     * @param  mixed $image
     * @param  mixed $name
     * @param  mixed $width
     * @return string
     */
    public function image(array $image, string $name, int $width = CONF_IMAGE_SIZE): ?string
    {
        $upload = new Image(CONF_UPLOAD_DIR, CONF_UPLOAD_IMAGE_DIR);
       
        if (empty($image['type']) || !in_array($image['type'], $upload::isAllowed())) {
            $this->message->error("Você não selecionou uma imagem válida");
            return null;
        }

        return $upload->upload($image, $name, $width, CONF_IMAGE_QUALITY);
    }
    
    /**
     * file
     *
     * @param  mixed $file
     * @param  mixed $name
     * @return string
     */
    public function file(array $file, string $name): ?string
    {
        $upload = new File(CONF_UPLOAD_DIR, CONF_UPLOAD_FILE_DIR);
       
        if (empty($file['type']) || !in_array($file['type'], $upload::isAllowed())) {
            $this->message->error("Você não selecionou um arquivo válido");
            return null;
        }

        return $upload->upload($file, $name);
    }
    
    /**
     * media
     *
     * @param  mixed $media
     * @param  mixed $name
     * @return string
     */
    public function media(array $media, string $name): ?string
    {
        $upload = new Media(CONF_UPLOAD_DIR, CONF_UPLOAD_MEDIA_DIR);
       
        if (empty($media['type']) || !in_array($media['type'], $upload::isAllowed())) {
            $this->message->error("Você não selecionou uma media válida");
            return null;
        }

        return $upload->upload($media, $name);
    }
    
    /**
     * remove
     *
     * @param  mixed $filePath
     * @return void
     */
    public function remove(string $filePath): void
    {
        var_dump($filePath, file_exists($filePath));
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }
}