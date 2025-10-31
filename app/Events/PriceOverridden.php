<?php

namespace App\Events;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceOverridden
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public OrderItem $item,
        public User $actor,
        public ?User $approver = null,
    ) {
    }
}
