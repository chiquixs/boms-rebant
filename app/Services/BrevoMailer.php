<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrevoMailer
{
    public function send(
        string $toEmail,
        ?string $toName,
        string $subject,
        string $html,
        ?string $replyTo = null
    ): void {
        $payload = [
            'sender' => [
                'name'  => config('services.brevo.sender_name'),
                'email' => config('services.brevo.sender_email'),
            ],
            'to'          => [['email' => $toEmail, 'name' => $toName ?: $toEmail]],
            'subject'     => $subject,
            'htmlContent' => $html,
        ];

        if ($replyTo) {
            $payload['replyTo'] = ['email' => $replyTo];
        }

        $response = Http::withHeaders([
                'api-key' => config('services.brevo.key'),
                'accept'  => 'application/json',
            ])
            ->timeout(15)
            ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Brevo error ' . $response->status() . ': ' . $response->body()
            );
        }
    }
}