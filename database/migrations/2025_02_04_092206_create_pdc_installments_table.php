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
        Schema::create('pdc_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdc_id')->constrained('pdcs')->onDelete('cascade'); // Foreign key linking to Pdc table
            $table->bigInteger('chequeno');
            $table->decimal('amount', 25, 2);
            $table->date('due_date');
            $table->enum('status', ['Pending', 'Complete', 'Overdue'])->default('Pending');
            $table->string('bankName')->nullable();
            $table->string('data1')->nullable();
            $table->string('data2')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdc_installments');
    }
};
