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
    Schema::table('leave_applications', function (Blueprint $table) {
        $table->enum('day_type', ['full', 'half'])->default('full');
    });
}

public function down()
{
    Schema::table('leave_applications', function (Blueprint $table) {
        $table->dropColumn('day_type');
    });
}

};
