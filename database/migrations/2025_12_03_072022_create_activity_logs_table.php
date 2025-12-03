<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_uid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('action')->nullable(); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // App\Models\Commitment, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable();
            $table->text('description')->nullable();
            $table->text('route')->nullable();
            $table->string('method')->nullable();
            $table->json('request_data')->nullable();
            $table->string('status'); // success, failed, warning
            $table->timestamp('performed_at');
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
