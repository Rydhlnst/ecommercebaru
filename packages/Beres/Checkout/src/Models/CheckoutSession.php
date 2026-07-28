<?php

namespace Beres\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Checkout\Contracts\CheckoutSession as CheckoutSessionContract;

class CheckoutSession extends Model implements CheckoutSessionContract
{
    use HasFactory;

    protected $table = 'checkout_sessions';

    protected $fillable = [
        'cart_id',
        'customer_id',
        'shipping_address',
        'billing_address',
        'shipping_method',
        'shipping_cost',
        'payment_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'shipping_cost'    => 'decimal:2',
    ];

    /**
     * Get the cart.
     */
    public function cart()
    {
        return $this->belongsTo(\Webkul\Checkout\Models\Cart::class);
    }

    /**
     * Get the customer.
     */
    public function customer()
    {
        return $this->belongsTo(\Webkul\Customer\Models\Customer::class);
    }

    /**
     * Check if session is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
