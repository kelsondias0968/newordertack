<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class ResendTransport extends AbstractTransport
{
    public function __construct(private readonly string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = [
            'from' => $this->formatAddress($email->getFrom()[0] ?? null),
            'to' => array_map(fn ($a) => $a->getAddress(), $email->getTo()),
            'subject' => $email->getSubject(),
            'html' => $email->getHtmlBody(),
            'text' => $email->getTextBody(),
        ];

        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map(fn ($a) => $a->getAddress(), $cc);
        }

        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map(fn ($a) => $a->getAddress(), $bcc);
        }

        $response = Http::withToken($this->apiKey)
            ->post('https://api.resend.com/emails', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Resend API error: ' . $response->body());
        }
    }

    private function formatAddress(?\Symfony\Component\Mime\Address $address): string
    {
        if (!$address) {
            return config('mail.from.address');
        }

        return $address->getName()
            ? "{$address->getName()} <{$address->getAddress()}>"
            : $address->getAddress();
    }

    public function __toString(): string
    {
        return 'resend';
    }
}
