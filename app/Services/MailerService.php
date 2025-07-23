<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    protected $mail;

    // public function __construct()
    // {
    //     $this->mail = new PHPMailer(true);
    //     $this->mail->isSMTP();
    //     $this->mail->Host = config('mail.host'); 
    //     $this->mail->SMTPAuth = true;
    //     $this->mail->Username = config('mail.username'); 
    //     $this->mail->Password = config('mail.password'); 
    //     $this->mail->SMTPSecure = config('mail.encryption');
    //     $this->mail->Port = config('mail.port'); 
    //     $this->mail->setFrom(config('mail.from.address'), config('mail.from.name'));


    //     $fromAddress = config('mail.from.address');
    //     $fromName = config('mail.from.name');

    //     if (empty($fromAddress)) {
    //         throw new \Exception("MAIL_FROM_ADDRESS is not set.");
    //     }

    //     $this->mail->setFrom($fromAddress, $fromName);
    //     $this->mail->isHTML(true);
    // }

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = config('mail.host');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = config('mail.username');
        $this->mail->Password = config('mail.password');
        $this->mail->SMTPSecure = config('mail.encryption');
        $this->mail->Port = config('mail.port');

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', 'No-Reply');

        if (empty($fromAddress)) {
            throw new \Exception("MAIL_FROM_ADDRESS is not set.");
        }

        $this->mail->setFrom($fromAddress, $fromName);
        $this->mail->isHTML(true);
    }

     public function send($to, $subject, $body, $attachmentPath = null)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            if ($attachmentPath) {
                $this->mail->addAttachment($attachmentPath);
            }

            $this->mail->send();

            $this->mail->clearAddresses();

            return true;
        } catch (Exception $e) {
            \Log::error('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
}
