<?php

namespace App\Mail\Transports;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

class HttpApiTransport extends AbstractTransport
{
    public function __construct(
        protected string $driver,
        protected array $config = []
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $fromAddresses = $email->getFrom();
        $from = $fromAddresses[0] ?? new Address(
            (string) config('mail.from.address', 'noreply@example.com'),
            (string) config('mail.from.name', 'Pal Varnan')
        );

        $toList = array_map(
            fn (Address $addr) => array_filter([
                'email' => $addr->getAddress(),
                'name' => $addr->getName() ?: null,
            ]),
            $email->getTo()
        );

        $subject = (string) ($email->getSubject() ?? '');
        $htmlBody = $email->getHtmlBody();
        $textBody = $email->getTextBody();

        match ($this->driver) {
            'brevo' => $this->sendViaBrevo($from, $toList, $subject, $htmlBody, $textBody),
            'resend_api' => $this->sendViaResend($from, $toList, $subject, $htmlBody, $textBody),
            'webhook' => $this->sendViaWebhook($from, $toList, $subject, $htmlBody, $textBody),
            default => throw new RuntimeException("Unsupported HTTP mail driver: {$this->driver}"),
        };
    }

    protected function sendViaBrevo(Address $from, array $toList, string $subject, ?string $html, ?string $text): void
    {
        $apiKey = (string) ($this->config['key'] ?? '');
        if ($apiKey === '') {
            throw new RuntimeException('BREVO_API_KEY is not configured.');
        }

        $payload = array_filter([
            'sender' => array_filter([
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: null,
            ]),
            'to' => array_values($toList),
            'subject' => $subject,
            'htmlContent' => $html,
            'textContent' => $text,
        ], fn ($v) => $v !== null);

        $response = Http::timeout(10)
            ->withHeaders([
                'api-key' => $apiKey,
                'Accept' => 'application/json',
            ])
            ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (!$response->successful()) {
            throw new RuntimeException('Brevo API error (' . $response->status() . '): ' . $response->body());
        }
    }

    protected function sendViaResend(Address $from, array $toList, string $subject, ?string $html, ?string $text): void
    {
        $apiKey = (string) ($this->config['key'] ?? '');
        if ($apiKey === '') {
            throw new RuntimeException('RESEND_API_KEY is not configured.');
        }

        $fromHeader = $from->getName()
            ? "{$from->getName()} <{$from->getAddress()}>"
            : $from->getAddress();

        $payload = array_filter([
            'from' => $fromHeader,
            'to' => array_map(fn ($item) => $item['email'], $toList),
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ], fn ($v) => $v !== null);

        $response = Http::timeout(10)
            ->withToken($apiKey)
            ->acceptJson()
            ->post('https://api.resend.com/emails', $payload);

        if (!$response->successful()) {
            throw new RuntimeException('Resend API error (' . $response->status() . '): ' . $response->body());
        }
    }

    protected function sendViaWebhook(Address $from, array $toList, string $subject, ?string $html, ?string $text): void
    {
        $url = (string) ($this->config['url'] ?? '');
        if ($url === '') {
            throw new RuntimeException('MAIL_WEBHOOK_URL is not configured.');
        }

        $primaryTo = $toList[0] ?? ['email' => ''];
        $payload = [
            'secret' => (string) ($this->config['secret'] ?? ''),
            'from' => $from->getAddress(),
            'from_name' => $from->getName(),
            'to' => $primaryTo['email'],
            'to_name' => $primaryTo['name'] ?? '',
            'subject' => $subject,
            'html' => $html ?? '',
            'text' => $text ?? '',
        ];

        $response = Http::timeout(15)
            ->withOptions(['allow_redirects' => true])
            ->post($url, $payload);

        if (!$response->successful()) {
            throw new RuntimeException('Mail webhook error (' . $response->status() . '): ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return $this->driver;
    }
}
