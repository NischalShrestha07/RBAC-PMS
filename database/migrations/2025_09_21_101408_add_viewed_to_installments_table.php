<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->boolean('viewed')->default(false)->after('status');
        });

        Schema::table('pdc_installments', function (Blueprint $table) {
            $table->boolean('viewed')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('viewed');
        });

        Schema::table('pdc_installments', function (Blueprint $table) {
            $table->dropColumn('viewed');
        });
    }
};
