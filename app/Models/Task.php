<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'priority',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Scope query for pending tasks
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope query for completed tasks
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope query for nearest deadline pending tasks
     */
    public function scopeNearestUpcoming(Builder $query): Builder
    {
        return $query->pending()
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc');
    }

    /**
     * Check whether deadline is within the next 1 hour
     */
    public function isNearDeadline(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }

        $now = Carbon::now();
        return $this->deadline->greaterThanOrEqualTo($now) && $this->deadline->diffInMinutes($now) <= 60;
    }
}
