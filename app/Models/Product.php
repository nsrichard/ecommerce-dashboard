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
