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
        Schema::create('failed_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commitment_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 25, 2);
            $table->date('due_date');
            $table->string('status')->default('Failed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_installments');
    }
};
