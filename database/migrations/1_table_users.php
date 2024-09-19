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
            $table->string('phone')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('photo')->default('assets/avatar.webp');
            $table->string('platform')->default('no');

            $table->integer('login_attempts')->default(0);
            $table->dateTime('attempts_at')->nullable();
            $table->dateTime('ban_expires_at')->nullable();


            $table->string('otp')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->dateTime('otp_attempts_at')->nullable();


            $table->integer('point')->default(0);


            $table->integer('last_notification')->default(0);
            $table->decimal('balance', 10, 2)->default(0.0);

            
            $table->string('device_token')->default('no');
            $table->integer('status')->default(0); // 0 first - 1 active and new - 2 user   - 3 banned  - 9 delete

            $table->rememberToken();
            $table->timestamps();

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
