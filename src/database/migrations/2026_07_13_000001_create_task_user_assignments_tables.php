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
        Schema::create('task_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
        });

        Schema::create('task_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
        });

        Schema::create('subtask_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtask_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['subtask_id', 'user_id']);
        });

        Schema::create('subtask_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtask_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['subtask_id', 'user_id']);
        });

        Schema::create('template_task_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['template_task_id', 'user_id']);
        });

        Schema::create('template_task_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['template_task_id', 'user_id']);
        });

        Schema::create('template_subtask_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_subtask_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['template_subtask_id', 'user_id'], 'tsr_unique');
        });

        Schema::create('template_subtask_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_subtask_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['template_subtask_id', 'user_id'], 'tsa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_subtask_approvers');
        Schema::dropIfExists('template_subtask_responsibles');
        Schema::dropIfExists('template_task_approvers');
        Schema::dropIfExists('template_task_responsibles');
        Schema::dropIfExists('subtask_approvers');
        Schema::dropIfExists('subtask_responsibles');
        Schema::dropIfExists('task_approvers');
        Schema::dropIfExists('task_responsibles');
    }
};
