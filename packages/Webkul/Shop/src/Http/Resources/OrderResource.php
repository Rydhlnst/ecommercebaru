<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'increment_id' => $this->increment_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'grand_total' => $this->grand_total,
            'base_grand_total' => $this->base_grand_total,
            'grand_total_invoiced' => $this->grand_total_invoiced,
            'grand_total_refunded' => $this->grand_total_refunded,
            'total_due' => $this->total_due,
            'base_total_due' => $this->base_total_due,
            'order_currency_code' => $this->order_currency_code,
            'customer_email' => $this->customer_email,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'customer_full_name' => $this->customer_full_name,
            'is_guest' => $this->is_guest,
            'channel_name' => $this->channel?->name,
            'created_at' => $this->created_at,
            'datetime' => $this->datetime,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'invoices' => $this->whenLoaded('invoices'),
            'shipments' => $this->whenLoaded('shipments'),
            'refunds' => $this->whenLoaded('refunds'),
            'billing_address' => new OrderAddressResource($this->whenLoaded('addresses', fn () => $this->addresses->where('address_type', 'billing')->first())),
            'shipping_address' => new OrderAddressResource($this->whenLoaded('addresses', fn () => $this->addresses->where('address_type', 'shipping')->first())),
            'can_cancel' => $this->canCancel(),
            'can_invoice' => $this->canInvoice(),
            'can_ship' => $this->canShip(),
            'can_refund' => $this->canRefund(),
            'can_reorder' => $this->canReorder(),
        ];
    }
}
