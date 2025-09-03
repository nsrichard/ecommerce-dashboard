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
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('sku',  'like', "%{$q}%");
            });
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        return $query;
    }
}
