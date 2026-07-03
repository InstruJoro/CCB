<?php

namespace App\Mail;

use App\Models\Incidente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmacionReporte extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Incidente $incidente)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CCB — Confirmación de reporte ' . $this->incidente->codigo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.confirmacion');
    }
}
