<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surge_pricing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('strategy'); // flat, multiplier, time_based
            $table->decimal('multiplier', 5, 2)->nullable();
            $table->decimal('flat_amount', 10, 2)->nullable();
            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surge_pricing_logs');
    }
};
