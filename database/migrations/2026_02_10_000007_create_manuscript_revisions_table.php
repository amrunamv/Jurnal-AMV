<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manuscript_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->integer('version')->default(1);
            
            // Metadata Snapshots (SINTA requirement)
            $table->string('title_snapshot');
            $table->text('abstract_snapshot');
            $table->json('keywords_snapshot')->nullable();
            
            $table->string('file_path');
            $table->text('author_notes')->nullable();
            $table->text('editor_notes')->nullable();
            
            $table->string('status'); // submitted, revision_requested, accepted
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();

            $table->unique(['manuscript_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscript_revisions');
    }
};
