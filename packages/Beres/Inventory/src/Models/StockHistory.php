<?php

namespace Beres\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Inventory\Contracts\StockHistory as StockHistoryContract;

class StockHistory extends Model implements StockHistoryContract
{
    use HasFactory;

    protected $table = 'stock_histories';

    protected $fillable = [
        'product_id',
        'inventory_source_id',
        'action',
        'quantity',
        'old_quantity',
        'new_quantity',
        'reference_type',
        'reference_id',
        'note',
        'user_id',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class);
    }

    /**
     * Get the inventory source.
     */
    public function inventorySource()
    {
        return $this->belongsTo(\Webkul\Inventory\Models\InventorySource::class);
    }

    /**
     * Get the user (admin).
     */
    public function user()
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class);
    }

    /**
     * Create a stock history entry.
     */
    public static function log(
        int $productId,
        string $action,
        int $quantity,
        int $oldQuantity,
        int $newQuantity,
        int $inventorySourceId = null,
        string $referenceType = null,
        int $referenceId = null,
        string $note = null
    ): self {
        $user = auth()->guard('admin')->user();

        return static::create([
            'product_id'          => $productId,
            'inventory_source_id' => $inventorySourceId,
            'action'              => $action,
            'quantity'            => $quantity,
            'old_quantity'        => $oldQuantity,
            'new_quantity'        => $newQuantity,
            'reference_type'      => $referenceType,
            'reference_id'        => $referenceId,
            'note'                => $note,
            'user_id'             => $user?->id,
        ]);
    }
}
