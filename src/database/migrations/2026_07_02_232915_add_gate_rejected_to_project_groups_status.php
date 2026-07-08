<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: drop the existing CHECK constraint and recreate with 'gate_rejected' added
        DB::statement('ALTER TABLE project_groups DROP CONSTRAINT IF EXISTS project_groups_status_check');

        DB::statement("
            ALTER TABLE project_groups
            ADD CONSTRAINT project_groups_status_check
            CHECK (status::text = ANY (ARRAY[
                'locked'::character varying,
                'active'::character varying,
                'awaiting_decision'::character varying,
                'completed_viable'::character varying,
                'completed_nonviable'::character varying,
                'completed'::character varying,
                'gate_rejected'::character varying
            ]::text[]))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE project_groups DROP CONSTRAINT IF EXISTS project_groups_status_check');

        DB::statement("
            ALTER TABLE project_groups
            ADD CONSTRAINT project_groups_status_check
            CHECK (status::text = ANY (ARRAY[
                'locked'::character varying,
                'active'::character varying,
                'awaiting_decision'::character varying,
                'completed_viable'::character varying,
                'completed_nonviable'::character varying,
                'completed'::character varying
            ]::text[]))
        ");
    }
};
