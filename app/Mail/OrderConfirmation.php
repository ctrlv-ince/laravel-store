<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    protected $pdfData = null;
    protected $pdfName = null;
    protected $pdfOptions = [];

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Attach PDF data to the email.
     *
     * @param string $data
     * @param string $name
     * @param array $options
     * @return $this
     */
    public function attachData($data, $name, array $options = [])
    {
        $this->pdfData = $data;
        $this->pdfName = $name;
        $this->pdfOptions = $options;
        return $this;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation #' . $this->order->order_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-receipt',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfData && $this->pdfName) {
            $attachment = Attachment::fromData(fn () => $this->pdfData, $this->pdfName);
            
            // Apply mime type if provided
            if (isset($this->pdfOptions['mime'])) {
                $attachment->withMime($this->pdfOptions['mime']);
            } else {
                $attachment->withMime('application/pdf');
            }
            
            return [$attachment];
        }
        
        return [];
    }
}