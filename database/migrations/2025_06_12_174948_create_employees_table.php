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
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('password');
        $table->date('dob')->nullable();
        $table->date('doj')->nullable();
        $table->string('gender');
        $table->string('marital_status')->nullable();
        $table->string('contact');
        $table->string('emergency_contact')->nullable();
        $table->string('address')->nullable();
        $table->string('zip')->nullable();
        $table->string('pay_scale')->nullable();
        $table->string('work_location')->nullable();
        $table->foreignId('department_id')->constrained()->onDelete('cascade');
        $table->foreignId('designation_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
