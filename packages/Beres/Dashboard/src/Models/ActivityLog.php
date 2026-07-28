<?php

namespace Beres\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Beres\Dashboard\Contracts\ActivityLog as ActivityLogContract;

class ActivityLog extends Model implements ActivityLogContract
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject (polymorphic).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user (polymorphic).
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create an activity log entry.
     */
    public static function log(
        string $action,
        $subject = null,
        string $description = null,
        array $properties = null,
        $user = null
    ): self {
        $user = $user ?? auth()->guard('admin')->user();

        return static::create([
            'user_id'      => $user?->id,
            'user_type'    => $user ? get_class($user) : 'system',
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'properties'   => $properties,
            'ip_address'   => request()->ip(),
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
     * Scope to filter by user.
     */
    public function scopeForUser($query, $userId, string $userType = 'admin')
    {
        return $query->where('user_id', $userId)
                     ->where('user_type', $userType);
    }

    /**
     * Scope to filter by subject type.
     */
    public function scopeForSubject($query, string $subjectType)
    {
        return $query->where('subject_type', $subjectType);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
