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
        $this->assertStringContainsString('<html lang="it"', $html);
        $this->assertStringContainsString('<title>Urbania | Assistenza</title>', $html);
        $this->assertStringNotContainsString('Original subject', $html);
        $this->assertStringNotContainsString('<h1>', $html);
        $this->assertStringContainsString(e($body), $html);
    }

    public function test_custom_email_uses_urbania_logo_and_configured_support_address(): void
    {
        config(['mail.from.address' => 'assistenza@example.test']);
        $html = (new ClientTicketEmail('Private subject', 'Authored reply'))->render();
        $document = new DOMDocument;
        $document->loadHTML($html);
        $logo = $document->getElementsByTagName('img')->item(0);

        $this->assertSame('Logo Urbania', $logo->getAttribute('alt'));
        $this->assertStringStartsWith('data:image/png;base64,', $logo->getAttribute('src'));
        $this->assertSame(
            file_get_contents(public_path('images/email/urbania-logo.png')),
            base64_decode(substr($logo->getAttribute('src'), strlen('data:image/png;base64,')))
        );
        $this->assertContains('mailto:assistenza@example.test', $this->links($html));
        $this->assertStringContainsString('Ufficio Assistenza', $html);
        $this->assertStringNotContainsString('supporto@azienda.it', $html);
        $this->assertStringNotContainsString('Private subject', $html);
        $this->assertStringContainsString('Authored reply', $html);
    }

    public function test_custom_email_preserves_lines_spacing_and_signature_gap_without_rendering_authored_html(): void
    {
        foreach (["\n", "\r\n", "\r"] as $newline) {
            $lines = [
                'Buongiorno,',
                '',
                '  Riferimento:  123 & 456',
                '<script>alert(1)</script>',
                '',
                '',
                'Mario Rossi',
                'Assistenza',
            ];
            $mail = new ClientTicketEmail('Ticket subject', implode($newline, $lines));
            $html = $mail->render();
            $document = new DOMDocument;
            $document->loadHTML($html);
            $content = $document->getElementsByTagName('div')->item(0);

            $this->assertSame('Ticket subject', $mail->subject);
            $this->assertStringNotContainsString('Ticket subject', $html);
            $this->assertSame(0, $document->getElementsByTagName('script')->length);
            $this->assertSame(count($lines) - 1, $content->getElementsByTagName('br')->length);
            $this->assertStringContainsString('white-space: pre-wrap', $content->getAttribute('style'));

            $renderedText = '';
            foreach ($content->childNodes as $node) {
                $renderedText .= $node->nodeName === 'br' ? "\n" : $node->textContent;
            }
            $this->assertSame(implode("\n", $lines), $renderedText);
        }
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
