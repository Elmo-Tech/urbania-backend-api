<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmReservation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $controlUrl;

    /**
     * Create a new message instance.
     *
     * @param $reservation
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
        $frontendUrl = rtrim(config('app.frontend_url', 'https://urbania.testingelmo.com'), '/');
        $this->controlUrl = $frontendUrl . '/reservation-control?reservationId=' . $reservation->id . '&token=' . $reservation->confirmation_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reservation Confirmation')
                    ->view('emails.reservation')
                    ->with([
                        'reservation' => $this->reservation,
                        'controlUrl' => $this->controlUrl,
                    ]);
    }
}
