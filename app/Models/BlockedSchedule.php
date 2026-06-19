<?php

namespace App\Models;

use App\Enums\BlockType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedSchedule extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'type',
        'reason',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => BlockType::class,
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    public function isFullDay(): bool
    {
        return $this->type === BlockType::FULL_DAY;
    }
}
