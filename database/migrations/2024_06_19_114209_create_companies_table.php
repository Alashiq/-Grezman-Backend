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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('address');
            $table->string('background')->default('companies/default.png');
            $table->string('logo')->default('companies/default.png');
            $table->string('phone');


            $table->text('cities')->nullable();
            $table->decimal('longitude', 10, 7)->default(0);
            $table->decimal('latitude', 10, 7)->default(0);

            $table->string('move_price')->default('50 د.ل');
            $table->string('join_price')->default('650 د.ل');


            

            $table->boolean('is_in_store')->default(true);
            $table->boolean('is_in_map')->default(true);
            $table->boolean('is_have_account')->default(true);
            $table->integer('system_type')->default(0);  // 0 no - 1 adv - 2 sass4 - 3 ussid 
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('support_phone')->nullable();

            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
