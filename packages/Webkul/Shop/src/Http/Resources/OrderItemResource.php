<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'type' => $this->type,
            'quantity' => $this->qty_ordered,
            'price' => core()->formatPrice($this->price, $this->order?->order_currency_code),
            'base_price' => $this->base_price,
            'total' => core()->formatPrice($this->total, $this->order?->order_currency_code),
            'base_total' => $this->base_total,
            'tax_amount' => core()->formatPrice($this->tax_amount, $this->order?->order_currency_code),
            'base_tax_amount' => $this->base_tax_amount,
            'discount_amount' => core()->formatPrice($this->discount_amount, $this->order?->order_currency_code),
            'base_discount_amount' => $this->base_discount_amount,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'sku' => $this->product->sku,
                    'url_key' => $this->product->url_key,
                    'base_image' => product_image()->getProductBaseImage($this->product),
                ];
            }),
            'additional' => $this->additional,
        ];
    }
}
