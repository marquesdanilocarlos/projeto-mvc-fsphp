<?php

namespace Source\Core;

use Exception;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use Source\Interface\EmailInterface;

class Email implements EmailInterface
{
    private array $data;

    public function __construct(
        private PHPMailer $mail = new PHPMailer(),
        private Message $message = new Message()
    ) {
        $this->data = [];
        $this->configMail();
    }

    public function bootstrap(string $subject, string $body, string $recipient, string $recipientName): self
    {
        $this->data['subject'] = $subject;
        $this->data['body'] = $body;
        $this->data['recipientEmail'] = $recipient;
        $this->data['recipientName'] = $recipientName;
        $this->data['attachments'] = [];

        return $this;
    }

    public function getMail(): PHPMailer
    {
        return $this->mail;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function send(string $from = CONF_MAIL_SENDER_EMAIL, string $fromName = CONF_MAIL_SENDER_NAME): bool
    {
        if (empty($this->data)) {
            $this->message->error('Erro ao enviar, favor, verifique os dados.');
            return false;
        }

        if (!isEmail($this->data['recipientEmail'])) {
            $this->message->error('E-mail de destinatário inválido.');
            return false;
        }

        if (!isEmail($from)) {
            $this->message->error('E-mail de remetente inválido.');
            return false;
        }

        try {
            $this->mail->Subject = $this->data['subject'];
            $this->mail->msgHTML($this->data['body']);
            $this->mail->addAddress($this->data['recipientEmail'], $this->data['recipientName']);
            $this->mail->setFrom($from, $fromName);

            foreach ($this->data['attachments'] as $filePath => $fileName) {
                $this->mail->addAttachment($filePath, $fileName);
            }

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            $this->message->error($e->getMessage());
            return false;
        }
    }

    public function queue(string $from = CONF_MAIL_SENDER_EMAIL, string $fromName = CONF_MAIL_SENDER_NAME): bool
    {
        try {
            $stmt = Connection::getInstance()->prepare(
                "INSERT INTO mail_queue (subject, body, from_email, from_name, recipient_email, recipient_name)
                       VALUES (:subject, :body, :from_email, :from_name, :recipient_email, :recipient_name)"
            );
            $stmt->bindValue(":subject", $this->data['subject'], PDO::PARAM_STR);
            $stmt->bindValue(":body", $this->data['body'], PDO::PARAM_STR);
            $stmt->bindValue(":from_email", $from, PDO::PARAM_STR);
            $stmt->bindValue(":from_name", $fromName, PDO::PARAM_STR);
            $stmt->bindValue(":recipient_email", $this->data['recipientEmail'], PDO::PARAM_STR);
            $stmt->bindValue(":recipient_name", $this->data['recipientName'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            $this->message->error($e->getMessage());
            return false;
        }
    }

    public function sendQueue(int $perSecond = 5): bool
    {
        $stmt = Connection::getInstance()->query("SELECT * FROM mail_queue WHERE sent_at IS NULL");

        if (!$stmt->rowCount()) {
            return false;
        }

        foreach ($stmt->fetchAll() as $send) {
            $email = $this->bootstrap(
                $send->subject,
                $send->body,
                $send->recipient_email,
                $send->recipient_name,
            );

            if ($email->send($send->from_email, $send->from_name)) {
                usleep(1000000 / $perSecond);
                Connection::getInstance()->exec("UPDATE mail_queue SET sent_at = NOW() WHERE id = {$send->id}");
            }
        }

        return true;
    }

    public function attach(string $filePath, string $fileName): self
    {
        $this->data['attachments'][$filePath] = $fileName;
        return $this;
    }

    private function configMail(): void
    {
        $this->mail->isSMTP();
        $this->mail->setLanguage(CONF_MAIL_OPTION_LANG);
        $this->mail->isHTML(CONF_MAIL_OPTION_HTML);
        $this->mail->SMTPAuth = CONF_MAIL_OPTION_AUTH;
        //$this->mail->SMTPSecure = CONF_MAIL_OPTION_SECURE;
        $this->mail->CharSet = CONF_MAIL_OPTION_CHARSET;
        $this->mail->Host = CONF_MAIL_HOST;
        $this->mail->Port = CONF_MAIL_PORT;
    }

}