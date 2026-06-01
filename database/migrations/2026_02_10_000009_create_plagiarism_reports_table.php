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
        Schema::create('plagiarism_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->foreignId('revision_id')->nullable()->constrained('manuscript_revisions')->nullOnDelete();
            
            $table->decimal('similarity_score', 5, 2)->nullable(); // Percentage 0.00 - 100.00
            $table->string('provider')->default('turnitin'); // turnitin, ithenticate, etc
            $table->string('report_url')->nullable();
            $table->string('certificate_file')->nullable();
            $table->json('details')->nullable(); // Detailed breakdown
            
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index('manuscript_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plagiarism_reports');
    }
};
