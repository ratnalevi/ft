<?php

namespace App\Mail;

use App\Models\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbnormalityAlert extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Location $location;

    private array $alerts;

    private array $lines;

    private string $start;

    private string $end;

    /**
     * Create a new message instance.
     */
    public function __construct($location, $alerts, $lines, $start, $end)
    {
        $this->location = $location;
        $this->alerts = $alerts;
        $this->lines = $lines;
        $this->start = $start;
        $this->end = $end;
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name') . ' Alerts';
        $this->from($fromAddress, $fromName);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Floteq Alerts - ' . $this->location->LocationName,
        );
    }

    public function build(): AbnormalityAlert
    {
        return $this->view('emails.abnormality_alert')
            ->with([
                'location' => $this->location,
                'alerts' => $this->alerts,
                'lines' => $this->lines,
                'start' => $this->start,
                'end' => $this->end,
            ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
