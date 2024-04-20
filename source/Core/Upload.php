<?php

namespace Source\Core;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Uploader\Media;
use Source\Interface\UploadInterface;

readonly class Upload implements UploadInterface
{

    public function __construct(
        private Message $message = new Message()
    )
    {
    }

    public function image(array $image, string $name, int $width = CONF_IMG_SIZE): ?string
    {
        $upload = new Image(CONF_UPLOAD_DIR, CONF_UPLOAD_IMG_DIR);
        if (empty($image['type']) || !in_array($image['type'], $upload::isAllowed())) {
            $this->message->error('Você não selecionou uma imagem válida');
            return null;
        }

        return $upload->upload($image, $name, $width, CONF_IMG_QUALITY);
    }

    public function file(array $file, string $name): ?string
    {
        $upload = new File(CONF_UPLOAD_DIR, CONF_UPLOAD_FILE_DIR);
        if (empty($file['type']) || !in_array($file['type'], $upload::isAllowed())) {
            $this->message->error('Você não selecionou um arquivo válido');
            return null;
        }

        return $upload->upload($file, $name);
    }

    public function media(array $media, string $name): ?string
    {
        $upload = new Media(CONF_UPLOAD_DIR, CONF_UPLOAD_MEDIA_DIR);
        if (empty($media['type']) || !in_array($media['type'], $upload::isAllowed())) {
            $this->message->error('Você não selecionou uma mídia válida');
            return null;
        }

        return $upload->upload($media, $name);
    }

    public function remove(string $filePath): void
    {
        if (!file_exists($filePath) || !is_file($filePath)) {
            return;
        }

        unlink($filePath);
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

}