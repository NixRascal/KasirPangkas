<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignUuid('rule_id')->nullable()->constrained('commission_rules')->nullOnDelete();
            $table->decimal('base_amount', 12, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->boolean('settled')->default(false);
            $table->timestampTz('settled_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
