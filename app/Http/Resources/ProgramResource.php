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

        $imageThumb = method_exists($this->resource, 'getImageUrl')
            ? $this->resource->getImageUrl('thumb')
            : null;

        $stateValue = null;
        if ($this->state instanceof \BackedEnum) {
            $stateValue = $this->state->value;
        } elseif ($this->state !== null) {
            $stateValue = (string) $this->state;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'host' => $this->host,
            'description' => $this->description,
            'state' => $stateValue,
            'state_label' => $this->when($stateValue !== null, fn () => ucfirst(__($stateValue))),
            'image' => $image,
            'image_thumb' => $imageThumb,
            'image_id' => $this->when(is_numeric($this->image), fn () => (int) $this->image),
            'image_raw' => $this->image,
            'has_image' => $this->when(method_exists($this->resource, 'hasImage'), fn () => $this->resource->hasImage()),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
