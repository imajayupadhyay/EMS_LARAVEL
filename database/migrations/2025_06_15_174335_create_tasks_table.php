<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('employees')
                  ->onDelete('cascade');
            $table->date('task_date');
            $table->longText('task_content');
            $table->timestamps();

            $table->unique(['user_id', 'task_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
