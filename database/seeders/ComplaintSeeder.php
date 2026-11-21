<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * One pending report, so the moderation queue has something to show in the demo.
     * Safe to run again: a reader reports each publication once.
     */
    public function run(): void
    {
        $publication = Publication::where('slug', 'el-nuevo-iphone-12')->first();
        $reader = User::where('email', 'usuario@irsoblog.test')->first();

        if ($publication && $reader) {
            Complaint::firstOrCreate(
                ['publication_id' => $publication->id, 'user_id' => $reader->id],
                [
                    'reason' => 'spam',
                    'description' => 'Parece más un anuncio que una reseña.',
                    'status' => Complaint::STATUS_PENDING,
                ],
            );
        }
    }
}
