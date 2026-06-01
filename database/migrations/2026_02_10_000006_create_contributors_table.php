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
        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // For non-registered authors
            $table->string('given_name');
            $table->string('family_name');
            $table->string('email');
            $table->string('orcid')->nullable();
            
            $table->foreignId('affiliation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('affiliation_text')->nullable(); // Fallback text affiliation
            
            $table->string('role')->default('author'); // author, co-author, corresponding
            $table->integer('order')->default(1); // Author ordering
            $table->boolean('is_corresponding')->default(false);
            
            $table->timestamps();

            $table->index(['manuscript_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
