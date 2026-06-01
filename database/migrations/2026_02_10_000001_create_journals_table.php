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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('issn')->nullable();
            $table->string('e_issn')->nullable();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('website_url')->nullable();
            $table->json('metadata')->nullable(); // For Dublin Core extra data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
