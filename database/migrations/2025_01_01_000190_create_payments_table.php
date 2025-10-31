<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('method', ['cash', 'qris', 'debit', 'ewallet', 'transfer']);
            $table->decimal('amount', 12, 2);
            $table->string('reference_no')->nullable();
            $table->string('paid_by')->nullable();
            $table->foreignUuid('received_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestampTz('paid_at');
            $table->timestampsTz();

            $table->index(['method', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
