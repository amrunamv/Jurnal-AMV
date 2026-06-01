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
        Schema::create('manuscripts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // For secure file access
            $table->string('title');
            $table->string('slug')->unique(); // SEO & Caching
            $table->text('abstract');
            $table->json('keywords')->nullable();
            $table->string('status')->default('draft')->index(); // State Machine
            // Status: draft, submitted, editor_review, under_review, revision_required, 
            //         revision_submitted, copyediting, production, published, rejected, withdrawn
            
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('submitter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('current_editor_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('manuscript_file')->nullable();
            $table->string('doi')->nullable()->unique();
            $table->json('metadata')->nullable(); // Dublin Core extra data
            
            $table->integer('page_start')->nullable();
            $table->integer('page_end')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'journal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscripts');
    }
};
