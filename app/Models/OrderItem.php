<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'manual_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function chair(): BelongsTo
    {
        return $this->belongsTo(Chair::class);
    }

    public function manualApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manual_by');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(OrderItemPromotion::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function scopePaidBetween(Builder $query, string $start, string $end): Builder
    {
        return $query->whereHas('order', fn (Builder $orderQuery) => $orderQuery->where('status', 'paid')->whereBetween('paid_at', [$start, $end]));
    }

    public function scopeByEmployee(Builder $query, string $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByService(Builder $query, string $serviceId): Builder
    {
        return $query->where('service_id', $serviceId);
    }

    public function getEffectiveUnitPriceAttribute(): float
    {
        return (float) ($this->manual_price ?? $this->unit_price);
    }
}
