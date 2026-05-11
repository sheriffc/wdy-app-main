<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AndroidStackTraceReport extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $fromDateTime;
    public string $toDateTime;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $fromDateTime, $toDateTime)
    {
        $this->data = $data;
        $this->fromDateTime = $fromDateTime;
        $this->toDateTime = $toDateTime;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('info@wideya.org', 'wideya.org'),
            subject: 'Android Stack Trace Report from '.env("APP_URL","wideya.org"),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.android-stack-trace-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
