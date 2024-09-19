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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('profile_id')->default(0);
            $table->decimal('price', 10, 2)->default(0.0);
            $table->string('time');  // day hour month year
            $table->string('description');
            $table->string('download');
            $table->string('upload');
            $table->string('qouta');
            $table->boolean('next')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
