<?php

namespace Database\Seeders;

use App\Models\Auxiliary;
use Database\Seeders\Auxiliary as SeedersAuxiliary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

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
        $this->call(SeedersAuxiliary::class);
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
            // Crea aulas para el educador de prueba
            $this->crearAula(2, 3, $fak);
            // Crea alumnos para el aula
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
            for ($i = 0; $i < 12; $i++) {

                $user = \App\Models\User::create([
                    'name' => $fak->firstName,
                    'lastName' => $fak->lastName,
                    'password' => bcrypt(123),
                    'email' => $fak->email,
                    'email_verified_at' => now(),
                    'activated' => 1,
                    'blocked' => 0,
                ]);
                if ($i < 3) {
                    //Rol educador
                    \App\Models\User_Rol::create([
                        'idUser' => $user->id,
                        'idRol' => 2
                    ]);
                    $this->crearAula($user->id, $i, $fak);
                }
                // Rol Padre
                \App\Models\User_Rol::create([
                    'idUser' => $user->id,
                    'idRol' => 3
                ]);
            }
        }
    }
    private function crearAula($idUser, $curso, $fak)
    {
        $curso = 2020 - $curso;
        // Crea el aula
        $aula = \App\Models\Aula::create([
            'default' => 1,
            'idUser' => $idUser,
            'name' => "Aula $curso",
            'year'=>$curso,
            'age_range'=>2020-$curso
        ]);
        // Crea los alumnos para el aula
        for ($i = 0; $i < 10; $i++) {
            // Crea el alumno
            $alumno = \App\Models\Alumno::create([
                'name' => $i%2==0?$fak->firstNameMale:$fak->firstNameFemale,
                'lastName' => $fak->lastName,
                'birthday' => $fak->dateTimeBetween($curso . '-1-1', $curso . '-12-31'),
                'gender' => $i%2==0?1:2
            ]);
            // Crea la relación con el aula
            \App\Models\Aula_Alumno::create([
                'idAula' => $aula->id,
                'idAlumno' => $alumno->id,
            ]);
        }
    }
}
