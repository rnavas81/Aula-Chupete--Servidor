<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Agrega los roles
        $this->call(Roles::class);
        if (env('APP_ENV') === 'local') {
            // Crea los usuarios
            $fak = \Faker\Factory::create('es_ES');
            \App\Models\User::create([
                'name' => 'admin',
                'password' => bcrypt("admin"),
                'email' => 'aula.chupetes@gmail.com',
                'email_verified_at' => now(),
                'activated' => 1,
                'blocked' => 0,
            ]);
            \App\Models\User_Rol::create([
                'idUser' => 1,
                'idRol' => 1
            ]);
            // Crea educadores
            for ($i = 2; $i < 12; $i++) {
                \App\Models\User::create([
                    'name' => $fak->firstName,
                    'lastName' => $fak->lastName,
                    'password' => bcrypt(123),
                    'email' => $fak->email,
                    'email_verified_at' => now(),
                    'activated' => 1,
                    'blocked' => 0,
                ]);
                \App\Models\User_Rol::create([
                    'idUser' => $i,
                    'idRol' => $i < 7 ? 2 : 3
                ]);
            }
        }
    }
}
