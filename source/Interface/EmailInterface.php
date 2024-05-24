<?php

namespace Source\Interface;

interface EmailInterface
{
    public function bootstrap(string $subject, string $body, string $recipient, string $recipientName);

    public function getMail();

    public function getMessage();

    public function attach(string $filePath, string $fileName);

    public function send(string $from = CONF_MAIL_SENDER_EMAIL, string $fromName = CONF_MAIL_SENDER_NAME);
}