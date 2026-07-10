<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliverable_versions', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id')->nullable()->change();
            $table->foreignId('subtask_id')->nullable()->after('task_id')->constrained('subtasks')->nullOnDelete();
        });

        Schema::table('task_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id')->nullable()->change();
            $table->foreignId('subtask_id')->nullable()->after('task_id')->constrained('subtasks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deliverable_versions', function (Blueprint $table) {
            $table->dropForeign(['subtask_id']);
            $table->dropColumn('subtask_id');
            $table->unsignedBigInteger('task_id')->nullable(false)->change();
        });

        Schema::table('task_submissions', function (Blueprint $table) {
            $table->dropForeign(['subtask_id']);
            $table->dropColumn('subtask_id');
            $table->unsignedBigInteger('task_id')->nullable(false)->change();
        });
    }
};
