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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            // $table->string('voucher_code');
            // $table->string('voucher_serial');

            $table->boolean('one_state')->default(false);
            $table->string('one_label');
            $table->string('one_value');
            $table->boolean('two_state')->default(false);
            $table->string('two_label');
            $table->string('two_value');
            $table->boolean('three_state')->default(false);
            $table->string('three_label');
            $table->string('three_value');
            $table->boolean('four_state')->default(false);
            $table->string('four_label');
            $table->string('four_value');

            $table->string('hash_key')->default('no');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedBigInteger('batch_id');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');

            $table->integer('status')->default(0);  // 0 available,1 sold,2 redeemed, 3 canceld         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
