<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoService
{
    public function sendEmail($to, $subject, $htmlContent)
    {
        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "email" => env('MAIL_FROM_ADDRESS'),
                "name"  => env('MAIL_FROM_NAME'),
            ],
            "to" => [
                ["email" => $to],
            ],
            "subject" => $subject,
            "htmlContent" => $htmlContent,
        ]);

        return $response->json();
    }
}

