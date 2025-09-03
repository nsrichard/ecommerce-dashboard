<?php

namespace App\Models;

use App\Domain\Enums\Platform;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'platform', 'domain', 'status'];

    protected $casts = [
        'platform' => Platform::class,
    ];

    public function credential(): HasOne
    {
        return $this->hasOne(StoreCredential::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }
}
