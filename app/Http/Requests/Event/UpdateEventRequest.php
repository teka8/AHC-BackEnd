<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $eventId = $this->route('event') ?? $this->route('id');
        $event = \App\Models\Event::find($eventId);
        return $event ? $this->user()->can('update', $event) : $this->user()->can('create', \App\Models\Event::class);
    }

    public function rules(): array
    {
        // same as store rules for now
        return (new StoreEventRequest())->rules();
    }
}
