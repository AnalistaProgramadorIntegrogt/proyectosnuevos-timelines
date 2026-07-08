<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_template_version_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('order');
            $table->boolean('is_gate')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_groups');
    }
};
