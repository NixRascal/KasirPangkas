<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'surcharge_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'change_due' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (! $order->order_no) {
                $order->order_no = Str::upper(Str::random(10));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopePaidBetween(Builder $query, string $start, string $end): Builder
    {
        return $query->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end]);
    }

    public function scopeByEmployee(Builder $query, string $employeeId): Builder
    {
        return $query->whereHas('items', fn (Builder $itemQuery) => $itemQuery->where('employee_id', $employeeId));
    }

    public function scopeByService(Builder $query, string $serviceId): Builder
    {
        return $query->whereHas('items', fn (Builder $itemQuery) => $itemQuery->where('service_id', $serviceId));
    }

    public function getOutstandingAttribute(): float
    {
        return (float) ($this->grand_total - $this->paid_total);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'paid' => 'Paid',
            'void' => 'Void',
            default => ucfirst($this->status),
        };
    }
}
