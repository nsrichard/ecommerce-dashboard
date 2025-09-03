<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'store_id',
        'external_id',
        'number',
        'status',
        'customer_name',
        'customer_email',
        'total',
        'currency',
        'external_created_at',
    ];

    protected $casts = [
        'total'               => 'decimal:2',
        'external_created_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function (Builder $qb) use ($q) {
                $qb->where('customer_name', 'like', "%{$q}%")
                   ->orWhere('customer_email',  'like', "%{$q}%");
            });
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }
        if (isset($filters['min_total'])) {
            $query->where('total', '>=', $filters['min_total']);
        }
        if (isset($filters['max_total'])) {
            $query->where('total', '<=', $filters['max_total']);
        }


        return $query;
    }
}
