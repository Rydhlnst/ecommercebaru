<?php

namespace Beres\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Product\Contracts\ProductActivityLog as ProductActivityLogContract;

class ProductActivityLog extends Model implements ProductActivityLogContract
{
    use HasFactory;

    protected $table = 'product_activity_logs';

    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class);
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class);
    }

    /**
     * Create an activity log entry.
     */
    public static function log(
        string $action,
        $product,
        array $oldValues = null,
        array $newValues = null
    ): self {
        $user = auth()->guard('admin')->user();

        return static::create([
            'product_id'  => $product->id,
            'user_id'     => $user?->id,
            'action'      => $action,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
        ]);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
