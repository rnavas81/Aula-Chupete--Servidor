<?php

namespace Database\Seeders;

use App\Models\Auxiliary;
use App\Models\Diario;
use App\Models\Diario_Entrada;
use Database\Seeders\Auxiliary as SeedersAuxiliary;
use DateInterval;
use DateTime;
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
            // Usuario administrador
            $fak = \Faker\Factory::create('es_ES');
            \App\Models\User::create([
                'name' => 'admin',
                'password' => bcrypt('admin'),
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
            $user = $this->crearEducador([
                'name' => 'Educador',
                'password' => bcrypt('123'),
                'email' => 'educador@test.com',
                'email_verified_at' => now(),
                'activated' => 1,
                'blocked' => 0,
            ]);
            // Crea aulas para el educador de prueba
            $aula = $this->crearAula($user->id, 3, $fak);
            // Crea los alumnos para el aula
            $alumnos = [];
            for ($i = 0; $i < 5; $i++) {
                // Crea el alumno
                $alumno = $this->crearAlumno($user->id, $aula, $i, $fak);
                $lastnames = explode(" ", $alumno->lastname);
                $this->crearPadre($user->id, $alumno->id, $lastnames[0], $fak);
                if ($alumno->id > 1 && rand(0, 100) > 35) {
                    $this->crearPadre($user->id, $alumno->id, $lastnames[1], $fak);
                }
                $alumnos[] = $alumno->id;
            }
            // Crea diarios para el aula
            $this->crearDiario($aula, $alumnos, $fak);
            for ($i = 1; $i < 5; $i++) {
                // Crea educadores
                $user = $this->crearEducador($fak);
                // Crea el aula
                $aula = $this->crearAula($user->id, $i % 5, $fak);
                // Crea los alumnos
                $alumnos = [];
                for ($j = 0; $j < 5; $j++) {
                    // Crea el alumno
                    $alumno = $this->crearAlumno($user->id, $aula, $j, $fak);
                    $lastnames = explode(' ', $alumno->lastname);
                    // Crea padres para el alumno
                    $this->crearPadre($user->id, $alumno->id, $lastnames[0], $fak);
                    if (rand(0, 100) > 35) {
                        $this->crearPadre($user->id, $alumno->id, $lastnames[1], $fak);
                    }
                    $alumnos[] = $alumno->id;
                }
                // Crea diarios para el aula
                $this->crearDiario($aula, $alumnos, $fak);
            }
        }
    }
    // Crear educador
    public function crearEducador($fak)
    {
        if (is_object($fak)) {
            $user = \App\Models\User::create([
                'name' => $fak->firstName,
                'lastname' => $fak->lastName,
                'password' => bcrypt(123),
                'email' => $fak->email,
                'email_verified_at' => now(),
                'activated' => 1,
                'blocked' => 0,
            ]);
        } else {
            // dd(is_object($fak),$fak);
            $user = \App\Models\User::create($fak);
        }
        //Rol educador
        \App\Models\User_Rol::create([
            'idUser' => $user->id,
            'idRol' => 2
        ]);
        // Rol Padre
        \App\Models\User_Rol::create([
            'idUser' => $user->id,
            'idRol' => 3
        ]);
        return $user;
    }
    // Crea el aula
    private function crearAula($idUser, $curso, $fak)
    {
        $curso = 2020 - $curso;
        $aula = \App\Models\Aula::create([
            'default' => 1,
            'idUser' => $idUser,
            'name' => 'Aula ' . $curso,
            'year' => $curso,
            'age_range' => 2020 - $curso
        ]);
        return $aula;
    }
    /**
     * Crea un alumno
     */
    public function crearAlumno($owner, $aula, $i, $fak)
    {
        $lastname1 = $fak->lastName;
        $lastname2 = $fak->lastName;
        $alumno = \App\Models\Alumno::create([
            'name' => $i % 2 == 0 ? $fak->firstNameMale : $fak->firstNameFemale,
            'lastname' => $lastname1 . " " . $lastname2,
            'birthday' => $fak->dateTimeBetween($aula->year . '-1-1', $aula->year . '-12-31'),
            'gender' => $i % 2 == 0 ? 1 : 2,
            'owner' => $owner,
        ]);
        // Crea la relación con el aula
        \App\Models\Aula_Alumno::create([
            'idAula' => $aula->id,
            'idAlumno' => $alumno->id,
        ]);
        return $alumno;
    }
    /**
     * Crea un padre para un alumno
     */
    public function crearPadre($owner, $idAlumno, $lastname, $fak)
    {
        if($idAlumno != 1){
            $name = rand(0, 100) % 2 == 0 ? $fak->firstNameMale : $fak->firstNameFemale;
            $email =  $fak->email;
        } else {
            $email =  'padre@test.com';
            $name = 'Padre';
            $lastname = 'Test';

        }
        $email = $idAlumno != 1 ? $fak->email : 'padre@test.com';
        $padre = \App\Models\User::create([
            'name' => $name,
            'lastname' => $lastname,
            'password' => bcrypt('123'),
            'email' => $email,
            'email_verified_at' => now(),
            'activated' => 1,
            'blocked' => 0,
            'contact' => $fak->phoneNumber,
            'owner' => $owner,
        ]);
        \App\Models\User_Rol::create([
            'idUser' => $padre->id,
            'idRol' => 3
        ]);
        \App\Models\Padre_Alumno::create([
            'idUser' => $padre->id,
            'idAlumno' => $idAlumno
        ]);
        return $padre;
    }
    public function crearDiario($aula, $alumnos, $fak)
    {

        $fecha = new DateTime();
        $fecha->sub(new DateInterval('P1M'));
        while ($fecha < new DateTime()) {
            if ($fecha->format('N') < 6) {
                $diario = Diario::create([
                    'idAula' => $aula->id,
                    'date' => $fecha,
                    'title' => $fak->realText(200),
                    'content' => $fak->realText(1000),
                ]);
                // Crea entradas para los diarios del alumno
                foreach ($alumnos as $alumnoId) {
                    $do = rand(0, 100);
                    if ($do > 10) {
                        if ($do > 80) {
                            Diario_Entrada::create([
                                'idDiario' => $diario->id,
                                'idAlumno' => $alumnoId,
                                'absence' => 1,
                            ]);
                        } else {
                            Diario_Entrada::create([
                                'idDiario' => $diario->id,
                                'idAlumno' => $alumnoId,
                                'activity' => $fak->realText(500),
                                'food' => $fak->realText(500),
                                'behaviour' => $fak->realText(500),
                                'sphincters' => $fak->realText(500),
                            ]);
                        }
                    }
                }
            }

            $fecha->add(new DateInterval('P1D'));
        }
    }
}
