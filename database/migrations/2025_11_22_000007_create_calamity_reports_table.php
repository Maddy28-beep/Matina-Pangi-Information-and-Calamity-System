<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calamity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
            $table->date('report_date');
            $table->integer('total_casualties')->default(0);
            $table->integer('total_evacuated')->default(0);
            $table->integer('relief_used_items')->default(0);
            $table->decimal('total_damage_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calamity_reports');
    }
};
