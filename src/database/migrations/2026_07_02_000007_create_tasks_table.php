<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_group_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->integer('duration_days')->default(0);
            $table->date('calculated_start_date')->nullable();
            $table->date('calculated_end_date')->nullable();
            $table->date('real_end_date')->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_deliverable')->default(false);
            $table->foreignId('responsible_user_id')->nullable()->constrained('users');
            $table->foreignId('explicit_approver_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
