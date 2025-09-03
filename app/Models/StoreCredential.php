<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreCredential extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'auth_type', 'credentials', 'scopes', 'rotated_at'];

    protected $casts = [
        // Laravel 11: cifrado nativo de columnas
        'credentials' => 'encrypted:array',
        'scopes' => 'array',
        'rotated_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
