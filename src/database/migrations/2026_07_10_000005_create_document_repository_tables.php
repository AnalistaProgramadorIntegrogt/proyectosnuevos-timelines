<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('document_folders')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_folder_id')->nullable()->constrained('document_folders')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('document_file_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_file_id')->constrained('document_files')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('upload_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_file_versions');
        Schema::dropIfExists('document_files');
        Schema::dropIfExists('document_folders');
    }
};
