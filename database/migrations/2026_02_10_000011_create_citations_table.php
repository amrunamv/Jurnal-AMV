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
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(1);
            
            // Parsed reference data
            $table->text('raw_text'); // Original citation text
            $table->json('authors')->nullable(); // Parsed authors array
            $table->string('title')->nullable();
            $table->string('journal')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue')->nullable();
            $table->string('pages')->nullable();
            $table->year('year')->nullable();
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->string('publisher')->nullable();
            
            $table->string('type')->default('journal'); // journal, book, conference, website, etc
            $table->boolean('is_parsed')->default(false);
            
            $table->timestamps();

            $table->index(['manuscript_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
