<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts, one per role, documented in the README. All share the password below.
     *
     * @var array<string, array{name: string, rol: int}>
     */
    public const ACCOUNTS = [
        'admin@irsoblog.test' => ['name' => 'Ana Administradora', 'rol' => Role::ADMIN],
        'editor@irsoblog.test' => ['name' => 'Emilio Editor', 'rol' => Role::EDITOR],
        'usuario@irsoblog.test' => ['name' => 'Ulises Usuario', 'rol' => Role::USER],
    ];

    public const PASSWORD = '12345678';

    /**
     * Run the database seeds. Safe to run again: existing accounts are left as they are.
     */
    public function run(): void
    {
        foreach (self::ACCOUNTS as $email => $account) {
            if (User::withTrashed()->where('email', $email)->exists()) {
                continue;
            }

            // "rol" is not mass assignable, so the account is filled explicitly.
            User::forceCreate([
                'name' => $account['name'],
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make(self::PASSWORD),
                'rol' => $account['rol'],
            ]);
        }
    }
}
