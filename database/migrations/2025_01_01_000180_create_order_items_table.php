<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignUuid('chair_id')->nullable()->constrained()->nullOnDelete();
            $table->string('person_label');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('manual_price', 12, 2)->nullable();
            $table->string('manual_reason')->nullable();
            $table->foreignUuid('manual_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('finished_at')->nullable();
            $table->timestampsTz();

            $table->index(['service_id', 'employee_id']);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `order_items` ADD CONSTRAINT `order_items_manual_reason_check` CHECK ((manual_price IS NULL AND manual_reason IS NULL) OR (manual_price IS NOT NULL AND manual_reason IS NOT NULL)))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
