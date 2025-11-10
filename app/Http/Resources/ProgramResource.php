<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = method_exists($this->resource, 'getImageUrl')
            ? $this->resource->getImageUrl()
            : null;

        $imageThumb = null;
        if (method_exists($this->resource, 'getImageUrl')) {
            try {
                $imageThumb = $this->resource->getImageUrl('thumb');
            } catch (\Throwable $exception) {
                $imageThumb = $image;
            }
        }

        $stateValue = null;
        if ($this->state instanceof \BackedEnum) {
            $stateValue = $this->state->value;
        } elseif ($this->state !== null) {
            $stateValue = (string) $this->state;
        }

        $contacts = collect($this->contact_people ?? [])
            ->map(fn ($contact) => [
                'name' => (string) ($contact['name'] ?? ''),
                'bio' => (string) ($contact['bio'] ?? ''),
                'contact' => (string) ($contact['contact'] ?? ''),
                'image' => (string) ($contact['image'] ?? ''),
            ])
            ->filter(fn ($contact) => $contact['name'] !== '' || $contact['bio'] !== '' || $contact['contact'] !== '')
            ->values();

        $primaryContact = $contacts->first() ?? [
            'name' => (string) ($this->contact_name ?? ''),
            'bio' => (string) ($this->contact_bio ?? ''),
            'contact' => (string) ($this->contact_details ?? ''),
            'image' => '',
        ];

        return [
            'id' => $this->id,
            'title' => $this->title,
            'host' => $this->host,
            'country' => $this->country,
            'description' => $this->description,
            'contact_people' => $contacts->all(),
            'contact_name' => $primaryContact['name'] ?? null,
            'contact_bio' => $primaryContact['bio'] ?? null,
            'contact_details' => $primaryContact['contact'] ?? null,
            'contact_image' => $primaryContact['image'] ?? null,
            'partners_involved' => $this->partners_involved,
            'state' => $stateValue,
            'state_label' => $this->when($stateValue !== null, fn () => ucfirst(__($stateValue))),
            'image' => $image,
            'image_thumb' => $imageThumb,
            'image_id' => $this->when(is_numeric($this->image), fn () => (int) $this->image),
            'image_raw' => $this->image,
            'categories' => $this->categories,
            'category_labels' => $this->category_labels,
            'has_image' => $this->when(method_exists($this->resource, 'hasImage'), fn () => $this->resource->hasImage()),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
