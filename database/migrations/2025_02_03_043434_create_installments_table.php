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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commitment_id')->constrained()->onDelete('cascade'); // Link to commitments table

            $table->decimal('amount', 25, 2);
            $table->decimal('paid_amount', 25, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['Pending', 'Complete', 'Overdue'])->default('Pending');
            $table->string('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Enables soft deletion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
