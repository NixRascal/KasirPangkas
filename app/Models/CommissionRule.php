<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionRule extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'rule_id');
    }

    public function scopeEffective($query, string $date)
    {
        return $query->where(function ($query) use ($date) {
            $query->whereNull('start_date')->orWhere('start_date', '<=', $date);
        })->where(function ($query) use ($date) {
            $query->whereNull('end_date')->orWhere('end_date', '>=', $date);
        })->where('is_active', true);
    }
}
