<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('decision_maker_id')->constrained('users');
            $table->enum('outcome', ['viable', 'nonviable']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_decisions');
    }
};
