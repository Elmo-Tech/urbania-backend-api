<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class OuterTicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $editUrl;


    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $frontendUrl = rtrim(config('app.frontend_url', 'https://urbania.testingelmo.com'), '/');
        $this->editUrl = $frontendUrl . '/sollecito?ticketId=' . $ticket->id . '&token=' . $ticket->email_token;
    }


        public function build()
        {
            return $this->subject('Ticket Accepted')
                        ->view('emails.createdOuterTicket')
                        ->with([
                            'ticket' => $this->ticket,
                            'editUrl' => $this->editUrl
                        ]);
        }


}
