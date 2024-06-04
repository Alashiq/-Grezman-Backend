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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('photo')->default('assets/avatar.webp');



            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            
            $table->integer('login_attempts')->default(0);
            $table->dateTime('attempts_at')->nullable();
            $table->timestamp('locked_until')->nullable();


            $table->integer('status')->default(0); // 0 Not Active - 1 active  - 2 Banned - 9 delete
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
