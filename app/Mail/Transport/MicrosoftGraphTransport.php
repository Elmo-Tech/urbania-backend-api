<?php

namespace App\Mail\Transport;

use App\Services\Microsoft\GraphMailClient;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

class MicrosoftGraphTransport extends AbstractTransport
{
    public function __construct(private GraphMailClient $client)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();
        if (! $original instanceof Message) {
            throw new TransportException('Microsoft Graph mail requires a structured email message.');
        }
        // Normalize streams and inline content IDs using Symfony's MIME implementation.
        $email = MessageConverter::toEmail(new Message($original->getHeaders(), $original->getBody()));
        $sender = $this->client->sender();
        $payload = [
            'subject' => $email->getSubject() ?? '',
            'body' => [
                'contentType' => $email->getHtmlBody() !== null ? 'HTML' : 'Text',
                'content' => $email->getHtmlBody() ?? $email->getTextBody() ?? '',
            ],
            'from' => ['emailAddress' => ['address' => $sender['address'], 'name' => $sender['name'] ?? '']],
            'toRecipients' => $this->recipients($email->getTo()),
            'ccRecipients' => $this->recipients($email->getCc()),
            'bccRecipients' => $this->recipients($email->getBcc()),
            'replyTo' => $this->recipients($email->getReplyTo()),
            'attachments' => [],
        ];

        foreach ($email->getAttachments() as $attachment) {
            $body = $attachment->getBody();
            if (strlen($body) >= 3 * 1024 * 1024) {
                throw new TransportException('Microsoft Graph direct sending requires each attachment to be smaller than 3 MB.');
            }

            $file = [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => $attachment->getFilename() ?? 'attachment',
                'contentType' => $attachment->getMediaType().'/'.$attachment->getMediaSubtype(),
                'contentBytes' => base64_encode($body),
                'isInline' => $attachment->getDisposition() === 'inline',
            ];
            if ($file['isInline']) {
                $file['contentId'] = $attachment->getContentId();
            }
            $payload['attachments'][] = $file;
        }

        $this->client->send($payload);
    }

    private function recipients(array $addresses): array
    {
        return array_map(fn (Address $address) => [
            'emailAddress' => ['address' => $address->getAddress(), 'name' => $address->getName()],
        ], $addresses);
    }

    public function __toString(): string
    {
        return 'microsoft_graph';
    }
}
