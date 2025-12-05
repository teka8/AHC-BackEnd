<?php

namespace App\Mail;

use App\Models\EmailSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NewContentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The new content model instance (e.g., Post, Event, Scholarship).
     */
    public Model $content;

    /**
     * The subscriber instance.
     */
    public EmailSubscription $subscriber;

    /**
     * Create a new message instance.
     *
     * @param  Model  $content
     * @param  EmailSubscription  $subscriber
     */
    public function __construct(Model $content, EmailSubscription $subscriber)
    {
        $this->content = $content;
        $this->subscriber = $subscriber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Check if content is a newsletter
        $contentType = $this->content instanceof \App\Models\Others 
            && $this->content->resource_type === \App\Models\Others::TYPE_NEWSLETTER
            ? 'Newsletter'
            : Str::studly(class_basename($this->content));

        return new Envelope(
            subject: 'New ' . $contentType . ': ' . $this->content->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-content-notification',
            with: [
                'content' => $this->content,
                'subscriber' => $this->subscriber,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
