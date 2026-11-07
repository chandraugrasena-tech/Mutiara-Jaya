<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->promo_id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'min_order' => $this->min_order,
            'type' => $this->type,
            'value' => $this->value,
            'max_discount' => $this->max_discount,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
