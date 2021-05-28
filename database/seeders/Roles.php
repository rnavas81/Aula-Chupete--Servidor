<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Rol::create([
            'name' => 'admin'
        ]);
        \App\Models\Rol::create([
            'name' => 'teacher'
        ]);
        \App\Models\Rol::create([
            'name' => 'parent'
        ]);
    }
}
