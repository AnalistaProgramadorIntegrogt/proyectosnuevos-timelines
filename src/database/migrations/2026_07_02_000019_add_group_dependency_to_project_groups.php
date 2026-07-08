<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_groups', function (Blueprint $table) {
            $table->foreignId('unlocks_group_id')->nullable()->constrained('project_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unlocks_group_id');
        });
    }
};
