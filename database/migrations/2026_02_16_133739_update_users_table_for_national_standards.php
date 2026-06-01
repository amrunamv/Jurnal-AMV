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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('id');
            $table->string('salutation', 20)->nullable()->after('username');
            $table->string('first_name', 50)->nullable()->after('salutation');
            $table->string('middle_name', 50)->nullable()->after('first_name');
            $table->string('last_name', 50)->nullable()->after('middle_name');
            $table->string('scopus_id', 50)->nullable()->after('orcid');
            $table->string('country_code', 2)->nullable()->after('affiliation_id');
            $table->text('bio_statement')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'salutation',
                'first_name',
                'middle_name',
                'last_name',
                'scopus_id',
                'country_code',
                'bio_statement',
            ]);
        });
    }
};
