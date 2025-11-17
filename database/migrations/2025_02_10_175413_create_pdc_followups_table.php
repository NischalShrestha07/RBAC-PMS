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
        Schema::create('pdc_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdc_id')->constrained()->onDelete('cascade'); // Reference to the PDC
            $table->foreignId('pdc_installment_id')
                ->nullable()
                ->constrained('pdc_installments')
                ->onDelete('cascade');

            $table->date('original_due_date');
            $table->date('followup_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('comments')->nullable();
            $table->enum('status', ['pending', 'followed_up', 'completed', 'overdue'])->default('pending');
            $table->integer('followup_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdc_followups');
    }
};
