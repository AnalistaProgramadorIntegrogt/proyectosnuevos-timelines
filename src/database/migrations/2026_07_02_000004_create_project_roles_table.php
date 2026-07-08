<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('can_manage_settings')->default(false);
            $table->boolean('can_manage_roles')->default(false);
            $table->boolean('can_add_edit_tasks')->default(false);
            $table->boolean('can_reorder_tasks')->default(false);
            $table->boolean('can_upload_deliverables')->default(false);
            $table->boolean('can_approve_tasks')->default(false);
            $table->boolean('can_edit_status')->default(false);
            $table->boolean('can_view_audit')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_roles');
    }
};
