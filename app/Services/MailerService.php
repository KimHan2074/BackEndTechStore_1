<?php

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

//     public function __construct()
//     {
//         $this->mail = new PHPMailer(true);
//         $this->mail->isSMTP();
//         $this->mail->Host = config('mail.host');
//         $this->mail->SMTPAuth = true;
//         $this->mail->Username = config('mail.username');
//         $this->mail->Password = config('mail.password');
//         $this->mail->SMTPSecure = config('mail.encryption');
//         $this->mail->Port = config('mail.port');

//         $fromAddress = config('mail.from.address');
//         $fromName = config('mail.from.name', 'No-Reply');

//         if (empty($fromAddress)) {
//             throw new \Exception("MAIL_FROM_ADDRESS is not set.");
//         }

//         $this->mail->setFrom($fromAddress, $fromName);
//         $this->mail->isHTML(true);
//     }

//      public function send($to, $subject, $body, $attachmentPath = null)
//     {
//         try {
//             $this->mail->addAddress($to);
//             $this->mail->Subject = $subject;
//             $this->mail->Body    = $body;

//             if ($attachmentPath) {
//                 $this->mail->addAttachment($attachmentPath);
//             }

//             $this->mail->send();

//             $this->mail->clearAddresses();

//             return true;
//         } catch (Exception $e) {
//             \Log::error('Mailer Error: ' . $e->getMessage());
//             return false;
//         }
//     }
// }

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailerService
{
    protected $mail;

    public function __construct()
    {
        Log::info('[MailerService] Initializing...');

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = config('mail.mailers.smtp.host');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = ('mail.mailers.smtp.username');
        $this->mail->Password = config('mail.password');
        $this->mail->SMTPSecure = config('mail.mailers.smtp.encryption');
        $this->mail->Port = config('mail.mailers.smtp.port');

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', 'No-Reply');

        Log::info('[MailerService] Mail config loaded', [
                'host'     => config('mail.mailers.smtp.host'),
                'username' => config('mail.mailers.smtp.username'),
                'from'     => $fromAddress,
                'port'     => config('mail.mailers.smtp.port'),
        ]);

        if (empty($fromAddress)) {
            Log::error('[MailerService] MAIL_FROM_ADDRESS is not set');
            throw new \Exception("MAIL_FROM_ADDRESS is not set.");
        }

        $this->mail->setFrom($fromAddress, $fromName);
        $this->mail->isHTML(true);
    }

    public function send($to, $subject, $body, $attachmentPath = null)
    {
        try {
            Log::info('[MailerService] Preparing to send email', [
                'to' => $to,
                'subject' => $subject,
                'has_attachment' => $attachmentPath ? true : false
            ]);

            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            if ($attachmentPath) {
                $this->mail->addAttachment($attachmentPath);
                Log::info('[MailerService] Attachment added', ['path' => $attachmentPath]);
            }

            $this->mail->send();
            $this->mail->clearAddresses();

            Log::info('[MailerService] Email sent successfully to ' . $to);

            return true;
        } catch (Exception $e) {
            Log::error('[MailerService] Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
}

