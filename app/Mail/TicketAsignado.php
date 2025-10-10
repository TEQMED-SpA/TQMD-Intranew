<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAsignado extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Nuevo ticket asignado: ' . $this->ticket->numero_ticket)
                    ->view('emails.ticket_asignado');
    }
}