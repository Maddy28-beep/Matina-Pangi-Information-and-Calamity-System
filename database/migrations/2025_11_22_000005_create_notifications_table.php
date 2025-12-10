<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_id')->nullable()->constrained('calamities')->onDelete('set null');
            $table->enum('type', ['sms', 'email', 'system']);
            $table->string('title');
            $table->text('message');
            $table->enum('status', ['draft', 'sent', 'failed'])->default('draft');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
