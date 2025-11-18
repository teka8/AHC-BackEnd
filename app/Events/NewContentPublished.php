<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewContentPublished
{
    use Dispatchable, SerializesModels;

    /**
     * The new content model instance (e.g., Post, Event, Scholarship).
     */
    public Model $content;

    /**
     * The type of content (e.g., 'news', 'event', 'scholarship').
     */
    public string $type;

    /**
     * Create a new event instance.
     *
     * @param  Model  $content The new content model instance.
     * @param  string  $type The type of content.
     */
    public function __construct(Model $content, string $type)
    {
        $this->content = $content;
        $this->type = $type;
    }
}
