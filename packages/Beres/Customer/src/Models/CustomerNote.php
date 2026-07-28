<?php

namespace Beres\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Customer\Contracts\CustomerNote as CustomerNoteContract;

class CustomerNote extends Model implements CustomerNoteContract
{
    use HasFactory;

    protected $table = 'customer_notes';

    protected $fillable = [
        'customer_id',
        'admin_id',
        'note',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Get the customer.
     */
    public function customer()
    {
        return $this->belongsTo(\Webkul\Customer\Models\Customer::class);
    }

    /**
     * Get the admin.
     */
    public function admin()
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class);
    }
}
