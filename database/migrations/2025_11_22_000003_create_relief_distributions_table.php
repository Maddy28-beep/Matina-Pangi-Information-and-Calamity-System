<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relief_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relief_item_id')->constrained('relief_items')->onDelete('cascade');
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('calamity_id')->nullable()->constrained('calamities')->onDelete('set null');
            $table->integer('quantity');
            $table->dateTime('distributed_at');
            $table->foreignId('staff_in_charge')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relief_distributions');
    }
};
