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
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable(); // Adding the agent_id column
            $table->string('name'); //client name
            $table->decimal('total_amount', 25, 2);
            $table->decimal('paid_amount', 25, 2)->default(0);
            $table->decimal('pending_amount', 25, 2);
            $table->date('due_date');
            $table->text('status');
            $table->text('remarks')->nullable;
            $table->string('department')->nullable();
            $table->string('commitment_age')->nullable();
            $table->unsignedBigInteger('created_by'); // Tracks the user who created the commitment

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('client_id')->nullable(); // Link commitment to client
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitments');
    }
};
