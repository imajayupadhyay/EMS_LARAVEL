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
    Schema::table('admin_notifications', function (Blueprint $table) {
        $table->text('body')->nullable()->change();
    });
}

public function down()
{
    Schema::table('admin_notifications', function (Blueprint $table) {
        $table->text('body')->nullable(false)->change();
    });
}

};
