<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->order_code,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'address' => [
                'label' => $this->address_label,
                'detail' => $this->address_detail,
            ],
            'subtotal' => $this->subtotal,
            'ongkir' => $this->ongkir,
            'discount' => $this->discount,
            'total' => $this->total,
            'promo_title' => $this->promo_title,
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($i) => [
                'product_id' => $i->product_id,
                'name' => $i->product_name,
                'price' => $i->price,
                'quantity' => $i->quantity,
                'subtotal' => $i->price * $i->quantity,
            ])),
            'customer' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
