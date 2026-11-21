<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Puts the reaction, save_publication and complaint tables to work: one reaction and one save
 * per user and publication, and complaints that point at the publication being reported.
 */
return new class extends Migration
{
    public function up(): void
    {
        // The tables existed but nothing wrote to them; drop any duplicates before the unique keys
        foreach (['reaction', 'save_publication'] as $table) {
            $keep = DB::table($table)->selectRaw('MIN(id)')->groupBy('user_id', 'publication_id');
            DB::table($table)->whereNotIn('id', $keep)->delete();
        }

        Schema::table('reaction', function (Blueprint $table) {
            $table->unique(['user_id', 'publication_id']);
        });

        Schema::table('save_publication', function (Blueprint $table) {
            $table->unique(['user_id', 'publication_id']);
        });

        Schema::table('complaint', function (Blueprint $table) {
            $table->foreignId('publication_id')->nullable()->after('user_id')->constrained('publications');
            $table->string('reason', 30)->nullable()->after('publication_id');
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->unique(['user_id', 'publication_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'publication_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('resolved_by');
            $table->dropConstrainedForeignId('publication_id');
            $table->dropColumn(['reason', 'resolved_at']);
        });

        Schema::table('save_publication', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'publication_id']);
        });

        Schema::table('reaction', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'publication_id']);
        });
    }
};
