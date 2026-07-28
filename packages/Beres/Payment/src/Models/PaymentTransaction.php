<?php

namespace Beres\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Payment\Contracts\PaymentTransaction as PaymentTransactionContract;

class PaymentTransaction extends Model implements PaymentTransactionContract
{
    use HasFactory;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_type',
        'transaction_id',
        'order_id_midtrans',
        'gross_amount',
        'status',
        'fraud_status',
        'payment_response',
    ];

    protected $casts = [
        'gross_amount'     => 'decimal:2',
        'payment_response' => 'array',
    ];

    /**
     * Get the order.
     */
    public function order()
    {
        return $this->belongsTo(\Webkul\Sales\Models\Order::class);
    }
}
