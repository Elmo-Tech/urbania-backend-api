<?php

namespace App\Services\Microsoft;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;

class GraphMailClient
{
    public function __construct(private array $credentials, private array $sender)
    {
    }

    public function sender(): array
    {
        if (! filter_var($this->sender['address'] ?? '', FILTER_VALIDATE_EMAIL)) {
            throw new TransportException('Microsoft Graph mail requires a valid MAIL_FROM_ADDRESS.');
        }

        return $this->sender;
    }

    public function send(array $message): void
    {
        $sender = $this->sender();
        $payload = ['message' => $message, 'saveToSentItems' => true];

        if (strlen(json_encode($payload, JSON_THROW_ON_ERROR)) >= 4 * 1024 * 1024) {
            throw new TransportException('Email exceeds the Microsoft Graph 4 MB request limit. Reduce the message or attachments.');
        }

        $url = 'https://graph.microsoft.com/v1.0/users/'.rawurlencode($sender['address']).'/sendMail';
        $response = $this->sendRequest($url, $payload, $this->accessToken());

        // A rejected token is safe to refresh; do not replay ambiguous send failures.
        if ($response->status() === 401) {
            Cache::forget($this->cacheKey());
            $response = $this->sendRequest($url, $payload, $this->accessToken());
        }

        if ($response->status() !== 202) {
            throw new TransportException('Microsoft Graph rejected the email (HTTP '.$response->status().').');
        }
    }

    private function sendRequest(string $url, array $payload, string $token): Response
    {
        try {
            return Http::withToken($token)->acceptJson()
                ->connectTimeout(10)->timeout($this->credentials['timeout'] ?? 30)
                ->withOptions(['allow_redirects' => false])
                ->post($url, $payload);
        } catch (ConnectionException) {
            throw new TransportException('Microsoft Graph mail connection failed. Delivery is unconfirmed; check Sent Items before retrying.');
        }
    }

    private function accessToken(): string
    {
        foreach (['tenant_id', 'client_id', 'client_secret'] as $field) {
            if (empty($this->credentials[$field])) {
                throw new TransportException('Microsoft Graph mail configuration is missing '.$field.'.');
            }
        }

        $key = $this->cacheKey();
        $cached = Cache::get($key);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $response = Http::asForm()->acceptJson()
                ->connectTimeout(10)->timeout($this->credentials['timeout'] ?? 30)
                ->withOptions(['allow_redirects' => false])
                ->post('https://login.microsoftonline.com/'.rawurlencode($this->credentials['tenant_id']).'/oauth2/v2.0/token', [
                    'client_id' => $this->credentials['client_id'],
                    'client_secret' => $this->credentials['client_secret'],
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]);
        } catch (ConnectionException) {
            throw new TransportException('Microsoft Graph token connection failed.');
        }

        if (! $response->successful()) {
            throw new TransportException('Microsoft Graph token request failed (HTTP '.$response->status().').');
        }

        $token = $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in');
        if (! is_string($token) || $token === '' || $expiresIn <= 0) {
            throw new TransportException('Microsoft Graph returned an invalid token response.');
        }

        if ($expiresIn > 60) {
            Cache::put($key, $token, $expiresIn - 60);
        }

        return $token;
    }

    private function cacheKey(): string
    {
        return 'graph-mail-token:'.hash('sha256', json_encode([
            $this->credentials['tenant_id'] ?? '',
            $this->credentials['client_id'] ?? '',
            $this->credentials['client_secret'] ?? '',
        ], JSON_THROW_ON_ERROR));
    }
}
