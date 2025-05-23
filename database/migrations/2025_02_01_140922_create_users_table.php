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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(false);
            $table->dateTime('email_verified_at')->nullable();

            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);

            $table->string('avatar')->nullable();
            $table->mediumText('about')->nullable();
            $table->string('phone', 20)->nullable();

            $table->string('telegram_username', 50)->nullable();
            $table->string('telegram_id', 255)->nullable();
            $table->string('telegram_token', 64)->nullable();

            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
