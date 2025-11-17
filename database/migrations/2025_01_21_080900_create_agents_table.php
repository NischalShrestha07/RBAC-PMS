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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('contact')->nullable();
            $table->string('department')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->string('email')->nullable();
            $table->softDeletes(); // Adds a 'deleted_at' column

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
