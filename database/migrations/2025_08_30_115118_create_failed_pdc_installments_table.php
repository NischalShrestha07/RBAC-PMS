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
        Schema::create('failed_pdc_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdc_id')->constrained('pdcs')->onDelete('cascade');
            $table->decimal('amount', 25, 2);
            $table->string('chequeno')->nullable();
            $table->string('bankName')->nullable();
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
        Schema::dropIfExists('failed_pdc_installments');
    }
};
