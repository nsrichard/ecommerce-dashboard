<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'store_id','external_id','name','sku','price','currency','stock','image_url'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if ($from = $filters['from'] ?? null) {
            $query->where('external_created_at', '>=', $from);
        }
        if ($to = $filters['to'] ?? null) {
            $query->where('external_created_at', '<=', $to);
        }
        if ($email = $filters['email'] ?? null) {
            $query->where('customer_email', 'like', "%{$email}%");
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
