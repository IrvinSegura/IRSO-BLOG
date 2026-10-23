<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Merge categories and roles that share a name (the seeders once inserted them twice):
     * the oldest row is kept, its references are moved to it, and the copies are removed.
     */
    public function up(): void
    {
        $this->merge('category', 'publications', 'category_id');
        $this->merge('role', 'users', 'rol');
    }

    /**
     * The merged copies cannot be told apart again, so there is nothing to undo.
     */
    public function down(): void
    {
        //
    }

    /**
     * Point every reference to a duplicate at the kept row and delete the duplicates.
     */
    private function merge(string $table, string $referencingTable, string $foreignKey): void
    {
        DB::table($table)
            ->orderBy('id')
            ->get(['id', 'name', 'deleted_at'])
            ->groupBy(fn ($row) => mb_strtolower(trim($row->name)))
            ->filter(fn ($rows) => $rows->count() > 1)
            ->each(function ($rows) use ($table, $referencingTable, $foreignKey) {
                // Prefer an active row so a soft deleted copy never wins over a visible one.
                $keep = $rows->firstWhere('deleted_at', null) ?? $rows->first();
                $duplicates = $rows->where('id', '!=', $keep->id)->pluck('id');

                DB::transaction(function () use ($table, $referencingTable, $foreignKey, $keep, $duplicates) {
                    DB::table($referencingTable)->whereIn($foreignKey, $duplicates)->update([$foreignKey => $keep->id]);
                    DB::table($table)->whereIn('id', $duplicates)->delete();
                });
            });
    }
};
