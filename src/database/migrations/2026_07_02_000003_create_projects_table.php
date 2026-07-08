<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->date('start_date');
            $table->foreignId('default_approver_id')->nullable()->constrained('users');
            $table->string('lifecycle_status', 20)->default('not_started');
            $table->string('outcome', 20)->nullable();
            $table->boolean('archived')->default(false);
            $table->foreignId('process_template_version_id')->nullable()->constrained('process_template_versions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
