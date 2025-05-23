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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('lesson_time_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name', 100);
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->unsignedInteger('price');
            $table->boolean('is_paid')->default(false);
            $table->text('note')->nullable();
            $table->boolean('is_canceled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
