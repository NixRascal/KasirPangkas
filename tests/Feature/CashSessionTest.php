<?php

namespace Tests\Feature;

use App\Events\CashSessionClosed;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CashSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_and_close_cash_session_tracks_variance(): void
    {
        Event::fake([CashSessionClosed::class]);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $shift = Shift::factory()->create();

        $this->actingAs($cashier);

        $openResponse = $this->postJson(route('pos.cash-sessions.open'), [
            'shift_id' => $shift->id,
            'opening_float' => 200000,
        ])->assertOk();

        $sessionId = $openResponse->json('id');

        $this->postJson(route('pos.cash-ledgers.store'), [
            'cash_session_id' => $sessionId,
            'type' => 'cash_in',
            'reason' => 'Tambahan modal',
            'amount' => 50000,
        ])->assertOk();

        $this->postJson(route('pos.cash-ledgers.store'), [
            'cash_session_id' => $sessionId,
            'type' => 'cash_out',
            'reason' => 'Biaya operasional',
            'amount' => 10000,
        ])->assertOk();

        $closeResponse = $this->postJson(route('pos.cash-sessions.close'), [
            'cash_session_id' => $sessionId,
            'counted_cash' => 235000,
            'notes' => 'Selisih negatif',
        ])->assertOk();

        $closeData = $closeResponse->json();

        $this->assertSame(200000.0, (float) $closeData['opening_float']);
        $this->assertSame(235000.0, (float) $closeData['closing_cash_counted']);
        $this->assertSame(-5000.0, (float) $closeData['variance']);

        Event::assertDispatched(CashSessionClosed::class);
    }
}
