<?php

namespace Tests\Feature;

use App\Mail\ClientTicketEmail;
use App\Mail\ConfirmReservation;
use App\Mail\OuterTicketCreated;
use App\Mail\OuterTicketRejected;
use App\Mail\RefuseReservation;
use App\Models\ClientOuterTicket;
use App\Models\Ticket;
use DOMDocument;
use Tests\TestCase;

class ItalianEmailTest extends TestCase
{
    public function test_acceptance_copy_is_italian_and_sollecito_link_is_unchanged(): void
    {
        config(['app.frontend_url' => 'https://frontend.example.test/']);
        $ticket = (new Ticket)->forceFill([
            'id' => 42,
            'ticket_number' => '42_2026',
            'email_token' => 'test-token',
            'created_at' => '2026-10-04 12:30:00',
        ]);
        $mail = new OuterTicketCreated($ticket);
        $html = $mail->render();

        $this->assertSame('Segnalazione accettata', $mail->subject);
        $this->assertStringContainsString('<html lang="it">', $html);
        $this->assertStringContainsString('Segnalazione accettata correttamente.', $html);
        $this->assertStringContainsString('Numero segnalazione:', $html);
        $this->assertStringContainsString('Data di creazione:', $html);
        $this->assertStringContainsString('Modifica / integra / sollecita segnalazione', $html);
        $this->assertStringContainsString('42_2026', $html);
        $this->assertStringContainsString('04/10/2026 12:30', $html);
        $this->assertSame(['https://frontend.example.test/sollecito?ticketId=42&token=test-token'], $this->links($html));
    }

    public function test_rejection_uses_requested_italian_phrase_and_preserves_escaped_reason(): void
    {
        $reason = "Missing document\n<script>alert(1)</script>";
        $ticket = (new ClientOuterTicket)->forceFill([
            'number' => 'EXT-42',
            'rejection_reason' => $reason,
        ]);
        $mail = new OuterTicketRejected($ticket);
        $html = $mail->render();

        $this->assertSame('Urbania ha rifiutato la segnalazione', $mail->subject);
        $this->assertStringContainsString('<html lang="it">', $html);
        $this->assertStringContainsString('Urbania ha rifiutato la segnalazione.</h1>', $html);
        $this->assertStringContainsString('Numero segnalazione:', $html);
        $this->assertStringContainsString('Motivo del rifiuto:', $html);
        $this->assertStringContainsString('EXT-42', $html);
        $this->assertStringContainsString(e($reason), $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_reservation_confirmation_is_italian_and_control_link_is_unchanged(): void
    {
        config(['app.frontend_url' => 'https://frontend.example.test/']);
        $reservation = (object) [
            'id' => 7,
            'confirmation_token' => 'reservation-token',
            'date' => '2026-10-04 12:30:00',
        ];
        $mail = new ConfirmReservation($reservation);
        $html = $mail->render();

        $this->assertSame('Conferma appuntamento', $mail->subject);
        $this->assertStringContainsString('<html lang="it">', $html);
        $this->assertStringContainsString('Il suo appuntamento &egrave; stato confermato con i seguenti dettagli:', $html);
        $this->assertStringContainsString('Data:', $html);
        $this->assertStringContainsString('04/10/2026 12:30', $html);
        $this->assertStringContainsString('>Annulla appuntamento</a>', $html);
        $this->assertStringContainsString('Grazie per averci scelto!', $html);
        $this->assertSame(['https://frontend.example.test/reservation-control?reservationId=7&token=reservation-token'], $this->links($html));
    }

    public function test_reservation_refusal_is_italian_and_preserves_optional_reason(): void
    {
        $reservation = (object) ['number' => 'RES-7', 'refuse_reason' => 'Original reason <text>'];
        $mail = new RefuseReservation($reservation);
        $html = $mail->render();

        $this->assertSame('Appuntamento rifiutato', $mail->subject);
        $this->assertStringContainsString('<html lang="it">', $html);
        $this->assertStringContainsString('Richiesta di appuntamento non accettata.', $html);
        $this->assertStringContainsString('Purtroppo la sua richiesta di appuntamento &egrave; stata rifiutata.', $html);
        $this->assertStringContainsString('Numero appuntamento:', $html);
        $this->assertStringContainsString('RES-7', $html);
        $this->assertStringContainsString('Motivo:', $html);
        $this->assertStringContainsString(e($reservation->refuse_reason), $html);

        $reservation->refuse_reason = null;
        $this->assertStringNotContainsString('Motivo:', (new RefuseReservation($reservation))->render());
    }

    public function test_custom_email_keeps_authored_subject_body_and_url(): void
    {
        $body = 'Original message https://example.test/document?id=7&token=original';
        $mail = new ClientTicketEmail('Original subject', $body);
        $html = $mail->render();

        $this->assertSame('Original subject', $mail->subject);
        $this->assertStringContainsString('<html lang="it">', $html);
        $this->assertStringContainsString('<title>Nuova segnalazione creata</title>', $html);
        $this->assertStringContainsString('<h1>Original subject</h1>', $html);
        $this->assertStringContainsString(e($body), $html);
    }

    private function links(string $html): array
    {
        $document = new DOMDocument;
        $document->loadHTML($html);
        $links = [];
        foreach ($document->getElementsByTagName('a') as $link) {
            $links[] = $link->getAttribute('href');
        }

        return $links;
    }
}
