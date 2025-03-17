<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = ['Aprendiz', 'Experto', 'Colaborador', 'Admin'];

        foreach ($roles as $roleName) {
            Role::create([
                'Permiso' => $roleName,
            ]);
        }
    }
}
