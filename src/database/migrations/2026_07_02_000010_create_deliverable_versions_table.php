<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliverable_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('original_filename');
            $table->string('storage_key');
            $table->string('mime_type');
            $table->bigInteger('size_bytes');
            $table->string('checksum')->nullable();
            $table->foreignId('uploader_id')->constrained('users');
            $table->text('upload_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliverable_versions');
    }
};
