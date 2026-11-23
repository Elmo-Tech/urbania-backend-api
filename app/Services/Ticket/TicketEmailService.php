<?php

namespace App\Services\Ticket;

use App\Mail\OuterTicketCreated;
use App\Models\Ticket;
use App\Models\TicketClient;
use App\Models\TicketClientContact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class TicketEmailService
{
    private const COOLDOWN_SECONDS = 60;

    public function resendAcceptance(int $ticketId, int $userId): array
    {
        $key = 'ticket-acceptance-resend:'.$ticketId;
        // Serialize resends across users, including while the provider is responding.
        $lock = Cache::lock($key.':lock', 180);
        if (! $lock->get()) {
            $this->throttled(self::COOLDOWN_SECONDS);
        }

        try {
            $ticket = Ticket::find($ticketId);
            if (! $ticket) {
                $this->reject(404, 'ticket_not_found', 'Ticket non trovato.');
            }

            // Older converted tickets may lack client_outer_ticket_id. Keep their existing links usable.
            if (trim((string) $ticket->email_token) === '') {
                $this->reject(409, 'ticket_email_unavailable', 'Il ticket non dispone di un link email valido per il reinvio.');
            }

            $contact = null;
            if ($ticket->ticket_client_id && TicketClient::whereKey($ticket->ticket_client_id)->exists()) {
                $contact = TicketClientContact::where('ticket_client_id', $ticket->ticket_client_id)
                    ->orderBy('id')->first();
            }
            $email = trim((string) $contact?->email);
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->reject(422, 'ticket_email_invalid', 'Il contatto del ticket non ha un indirizzo email valido.');
            }

            if (RateLimiter::tooManyAttempts($key, 1)) {
                $this->throttled(max(1, RateLimiter::availableIn($key)));
            }

            // Keep the cooldown after failure too: a timeout does not prove non-delivery.
            RateLimiter::hit($key, self::COOLDOWN_SECONDS);
            $context = [
                'ticket_id' => $ticket->id,
                'contact_id' => $contact->id,
                'user_id' => $userId,
                'recipient' => $email,
                'mail_type' => 'ticket_acceptance',
            ];
            Log::info('Ticket acceptance email resend requested.', $context);

            try {
                Mail::to($email)->send(new OuterTicketCreated($ticket));
            } catch (TransportExceptionInterface $exception) {
                Log::warning('Ticket acceptance email resend failed.', $context + [
                    'exception_type' => get_class($exception),
                ]);
                $this->reject(502, 'ticket_email_send_failed', "Impossibile confermare l'invio dell'email. Verificare la posta inviata prima di riprovare.");
            }

            Log::info('Ticket acceptance email resend accepted.', $context);

            return [
                'message' => 'Email reinviata correttamente.',
            ];
        } finally {
            $lock->release();
        }
    }

    private function reject(int $status, string $code, string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => $message,
            'code' => $code,
        ], $status));
    }

    private function throttled(int $retryAfter): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Reinvio gia richiesto. Attendere prima di riprovare.',
            'code' => 'ticket_email_throttled',
            'retryAfter' => $retryAfter,
        ], 429, ['Retry-After' => $retryAfter]));
    }
}
