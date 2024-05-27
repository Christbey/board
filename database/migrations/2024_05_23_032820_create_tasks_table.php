<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User ID foreign key
            $table->string('task'); // Task description
            $table->boolean('completed')->default(false); // Task completion status
            $table->string('status')->default('pending'); // Task status
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // Task priority
            $table->timestamp('reminder_date')->nullable(); // Reminder date
            $table->timestamp('due_date')->nullable(); // Due date
            $table->timestamps(); // created_at and updated_at columns
            $table->softDeletes(); // deleted_at column for soft deletes

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('reminder_date');
            $table->index('due_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
