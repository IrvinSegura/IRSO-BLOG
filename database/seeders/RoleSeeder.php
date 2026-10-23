<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds. Safe to run again: the ids must stay 1, 2 and 3 because
     * Role::ADMIN, Role::EDITOR and Role::USER point at them.
     */
    public function run(): void
    {
        foreach ([Role::ADMIN => 'Administrador', Role::EDITOR => 'Editor', Role::USER => 'Usuario'] as $id => $name) {
            // "id" is not mass assignable, so firstOrCreate() would drop it.
            Role::withTrashed()->find($id) ?? Role::forceCreate(['id' => $id, 'name' => $name]);
        }
    }
}
