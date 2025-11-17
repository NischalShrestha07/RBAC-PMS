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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pc_id');
            $table->enum('type', ['commitment', 'pdc']);
            $table->unsignedBigInteger('installment_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('agent_id');
            $table->decimal('pay_amount', 25, 2);
            $table->unsignedBigInteger('login_uid');
            $table->text('remarks')->nullable();
            $table->timestamps();


            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('login_uid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
