<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'real_end_date')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->date('real_end_date')->nullable()->after('calculated_end_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('real_end_date');
        });
    }
};
