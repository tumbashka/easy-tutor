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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title',200);
            $table->mediumText('description')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('reminder_before_deadline')->default(false);
            $table->integer('reminder_before_hours')->nullable();
            $table->boolean('reminder_daily')->default(false);
            $table->time('reminder_daily_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
