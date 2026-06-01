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
        // Add ORCID and affiliation fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('orcid')->nullable()->after('email');
            $table->foreignId('affiliation_id')->nullable()->after('orcid')->constrained()->nullOnDelete();
            $table->text('bio')->nullable()->after('affiliation_id');
            $table->string('phone')->nullable()->after('bio');
            $table->string('avatar')->nullable()->after('phone');
            $table->json('research_interests')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['affiliation_id']);
            $table->dropColumn(['orcid', 'affiliation_id', 'bio', 'phone', 'avatar', 'research_interests']);
        });
    }
};
