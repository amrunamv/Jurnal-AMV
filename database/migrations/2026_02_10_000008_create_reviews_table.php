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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->foreignId('revision_id')->nullable()->constrained('manuscript_revisions')->nullOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            
            $table->string('status')->default('pending'); // pending, accepted, declined, completed
            $table->text('comments_to_author')->nullable();
            $table->text('comments_to_editor')->nullable(); // Confidential
            
            // Review scores (customizable rubric)
            $table->json('scores')->nullable();
            
            $table->string('recommendation')->nullable(); 
            // accept, minor_revision, major_revision, reject
            
            $table->integer('round')->default(1); // Review round
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['manuscript_id', 'round']);
            $table->index(['reviewer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
