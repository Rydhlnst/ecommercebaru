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
            'qty_invoiced' => $this->qty_invoiced,
            'qty_shipped' => $this->qty_shipped,
            'qty_refunded' => $this->qty_refunded,
            'qty_canceled' => $this->qty_canceled,
            'price' => $this->price,
            'base_price' => $this->base_price,
            'total' => $this->total,
            'base_total' => $this->base_total,
            'tax_amount' => $this->tax_amount,
            'base_tax_amount' => $this->base_tax_amount,
            'discount_amount' => $this->discount_amount,
            'base_discount_amount' => $this->base_discount_amount,
            'product' => $this->whenLoaded('product', fn () => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
                'url_key' => $this->product->url_key,
                'base_image' => product_image()->getProductBaseImage($this->product),
            ]),
            'additional' => $this->additional,
        ];
    }
}
