<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('order');
            $table->enum('status', [
                'locked',
                'active',
                'awaiting_decision',
                'completed_viable',
                'completed_nonviable',
                'completed',
            ])->default('locked');
            $table->boolean('is_gate')->default(false);
            $table->string('gate_decision_role')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_groups');
    }
};
