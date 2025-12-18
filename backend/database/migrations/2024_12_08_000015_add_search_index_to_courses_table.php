<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, add full-text search index if not already exists
        if (config('database.default') === 'pgsql') {
            // Check if index already exists
            $indexExists = DB::selectOne("
                SELECT EXISTS (
                    SELECT 1 FROM pg_indexes 
                    WHERE indexname = 'courses_search_idx'
                ) as exists
            ");

            if (!$indexExists->exists) {
                DB::statement("
                    CREATE INDEX courses_search_idx ON courses 
                    USING gin(to_tsvector('english', coalesce(title, '') || ' ' || coalesce(description, '')))
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS courses_search_idx');
        }
    }
};

