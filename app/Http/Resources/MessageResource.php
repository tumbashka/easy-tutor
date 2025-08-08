<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var $this Message*/
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'text' => $this->text,
            'created_at' => $this->created_at->format('H:i'),
            'is_read' => $this->user_id === auth()->id() ? $this->isRead() : $this->isReadByUser(),
        ];
    }
}
