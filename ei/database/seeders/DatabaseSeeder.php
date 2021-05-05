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
            // Usuario administrador
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
            // Educador para pruebas
            \App\Models\User::create([
                'name' => 'Educador',
                'password' => bcrypt("123"),
                'email' => 'educador@test.com',
                'email_verified_at' => now(),
                'activated' => 1,
                'blocked' => 0,
            ]);
            \App\Models\User_Rol::create([
                'idUser' => 2,
                'idRol' => 2
            ]);
            \App\Models\User_Rol::create([
                'idUser' => 2,
                'idRol' => 3
            ]);
            // Crea aulas para los educadores
            \App\Models\Aula::create([
                'name' => '20/21',
                'idUser' => 2,
                'default' => 1,
            ]);
            // Padre para pruebas
            \App\Models\User::create([
                'name' => 'Padre',
                'password' => bcrypt("123"),
                'email' => 'padre@test.com',
                'email_verified_at' => now(),
                'activated' => 1,
                'blocked' => 0,
            ]);
            \App\Models\User_Rol::create([
                'idUser' => 3,
                'idRol' => 3
            ]);
            // Crea educadores y padres
            for ($i = 4; $i < 12; $i++) {
                \App\Models\User::create([
                    'name' => $fak->firstName,
                    'lastName' => $fak->lastName,
                    'password' => bcrypt(123),
                    'email' => $fak->email,
                    'email_verified_at' => now(),
                    'activated' => 1,
                    'blocked' => 0,
                ]);
                if ($i < 7) {
                    //Rol educador
                    \App\Models\User_Rol::create([
                        'idUser' => $i,
                        'idRol' => 2
                    ]);
                    // Crea aulas para los educadores
                    \App\Models\Aula::create([
                        'name' => '20/21',
                        'idUser' => $i,
                        'default' => 1,
                    ]);
                }
                // Rol Padre
                \App\Models\User_Rol::create([
                    'idUser' => $i,
                    'idRol' => 3
                ]);
            }
        }
    }
}
