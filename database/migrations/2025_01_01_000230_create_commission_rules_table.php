<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('scope', ['per_service', 'per_employee_level', 'global']);
            $table->foreignUuid('service_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('employee_level', ['junior', 'senior', 'master'])->nullable();
            $table->enum('type', ['percent', 'flat']);
            $table->decimal('value', 12, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->index(['scope', 'is_active']);
            $table->index(['service_id', 'is_active']);
            $table->index('employee_level');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
