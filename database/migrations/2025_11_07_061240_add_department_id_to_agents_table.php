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
        Schema::table('agents', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
        Schema::table('commitments', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
        Schema::table('pdcs', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
