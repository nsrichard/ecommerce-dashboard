<?php

namespace App\Models;

use App\Domain\Enums\ExportStatus;
use App\Domain\Enums\ExportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'type', 'status', 'path', 'meta', 'started_at', 'finished_at'];

    protected $casts = [
        'type' => ExportType::class,
        'status' => ExportStatus::class,
        'meta' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
