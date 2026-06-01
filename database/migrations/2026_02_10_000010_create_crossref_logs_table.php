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
        Schema::create('crossref_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            
            $table->string('batch_id')->nullable(); // Crossref batch ID
            $table->string('doi');
            $table->string('status')->default('pending'); // pending, submitted, registered, failed
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->index(['manuscript_id', 'status']);
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crossref_logs');
    }
};
