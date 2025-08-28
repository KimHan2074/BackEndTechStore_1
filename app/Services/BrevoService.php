<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoService
{
    public function sendEmail($to, $subject, $htmlContent, $attachmentPath = null)
    {
        $payload = [
            "sender" => [
                "email" => env('MAIL_FROM_ADDRESS'),
                "name"  => env('MAIL_FROM_NAME'),
            ],
            "to" => [
                ["email" => $to],
            ],
            "subject" => $subject,
            "htmlContent" => $htmlContent,
        ];

        // Nếu có file đính kèm (PDF)
        if ($attachmentPath && file_exists($attachmentPath)) {
            $payload['attachment'] = [[
                "content" => base64_encode(file_get_contents($attachmentPath)),
                "name" => basename($attachmentPath)
            ]];
        }

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        return $response->json();
    }
}


