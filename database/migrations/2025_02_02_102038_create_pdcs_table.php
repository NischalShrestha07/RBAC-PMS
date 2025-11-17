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
        Schema::create('pdcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commitment_id')->nullable(); // Links to commitment
            $table->unsignedBigInteger('agent_id')->nullable(); // Adding the agent_id column

            $table->string('name');
            $table->decimal('commitment_amount', 25, 2)->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by'); // Tracks the user who created the PDC
            $table->string('department')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('commitment_id')->references('id')->on('commitments')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');

            $table->unsignedBigInteger('client_id')->nullable(); // Link commitment to client
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdcs');
    }
};
