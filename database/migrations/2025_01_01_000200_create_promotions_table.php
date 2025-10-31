<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['percent', 'flat']);
            $table->decimal('value', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->json('conditions_json')->nullable();
            $table->timestampsTz();
        });

        Schema::create('order_item_promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('promotion_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('discount_amount', 12, 2);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_promotions');
        Schema::dropIfExists('promotions');
    }
};
