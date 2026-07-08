<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_visibility_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_role_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('group');
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('can_view')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_visibility_rules');
    }
};
