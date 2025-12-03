<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedLoginAttemptsTable extends Migration
{
    public function up()
    {
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index('email');
            $table->index('ip_address');
            $table->index('attempted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_login_attempts');
    }
}
