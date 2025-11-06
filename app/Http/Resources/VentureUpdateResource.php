<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VentureUpdateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'venture_id' => $this->venture_id,
            'venture_name' => $this->venture->name ?? null,
            'venture_logo' => $this->venture->logo ? asset('storage/' . $this->venture->logo) : null,
            'title' => $this->title,
            'content' => $this->content,
            'update_type' => $this->update_type,
            'media' => $this->media,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'created_at' => $this->created_at,
        ];
    }
}
