<?php

namespace Database\Seeders;

use App\Models\Auxiliary;
use App\Models\Diario;
use App\Models\Diario_Entrada;
use App\Models\Dietario;
use App\Models\Menu;
use App\Models\Menu_Dia;
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
            $padres = [];
            $posicion = 0;
            for ($i = 0; $i < 6; $i++) {
                $padre = $this->crearPadre($user->id, $fak, $i == 0 ? "padre" : false);
                $padre1 = $this->crearPadre($user->id, $fak, $i == 0 ? "madre" : false);
                $padres[] = [$padre, $padre1];
            }
            for ($n = 1; $n < 7; $n++) {
                if ($n == 1 || $n == 5) {
                    $posicion = 0;
                    $alumnos = [];
                    for ($i = 0; $i < 6; $i++) {
                        // Escoger padres
                        $padre1 = $padres[$posicion][0];
                        $padre2 = $padres[$posicion][1];
                        $lastnames = $padre1->lastname . " " . $padre2->lastname;
                        // Crea el alumno
                        $birthday = $fak->dateTimeBetween('2018-1-1', '2018-12-31');
                        $alumno = $this->crearAlumno($user->id, $birthday, $fak, $lastnames);
                        $alumnos[] = $alumno->id;
                        $this->relacionAlumnoPadre($padre1, $alumno->id);
                        $this->relacionAlumnoPadre($padre2, $alumno->id);
                        if ($posicion == count($padres)) $posicion = 0;
                        else $posicion += 1;
                    }
                }
                // Crea aulas para el educador de prueba
                $aula = $this->crearAula($user->id, $n, $fak, $n == 1);
                foreach ($alumnos as $idAlumno) {
                    $this->relacionAlumnoAula($aula->id, $idAlumno);
                }
                // Crea diarios para el aula
                $this->crearDiario($aula, $alumnos, $fak);
                // Crea Dietarios
                $this->crearDietario($aula);
            }
            // Crea Menús
            for ($i = 0; $i < 4; $i++) {
                $this->crearMenu('Menú ' . $i, $user);
            }

            ///////////////////////////////////////////
            // Crea más datos de prueba
            for ($i = 1; $i < 2; $i++) {
                // Crea educadores
                $user = $this->crearEducador($fak);
                // Crea el aula
                $aula = $this->crearAula($user->id, $i % 5, $fak);
                // Crea los alumnos
                $alumnos = [];
                for ($j = 0; $j < 5; $j++) {
                    // Crea padres para el alumno
                    $padre1 = $this->crearPadre($user->id, $fak);
                    $padre2 = $this->crearPadre($user->id, $fak);
                    $lastnames = $padre1->lastname . " " . $padre2->lastname;
                    // Crea el alumno
                    $birthday = $fak->dateTimeBetween($aula->year . '-1-1', $aula->year . '-12-31');
                    $alumno = $this->crearAlumno($user->id, $birthday, $fak, $lastnames);
                    $this->relacionAlumnoAula($aula->id, $alumno->id);
                    $alumnos[] = $alumno->id;
                    $this->relacionAlumnoPadre($padre1, $alumno->id);
                    $this->relacionAlumnoPadre($padre2, $alumno->id);
                }
                // Crea diarios para el aula
                $this->crearDiario($aula, $alumnos, $fak);
                // Crea menú
                $this->crearMenu('Mi Menú', $user);
                // Crea dietarios para el aula
                $this->crearDietario($aula);
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
    private function crearAula($idUser, $n, $fak, $default = false)
    {
        $curso = 2021 - $n;
        $rango = $n % 4 == 0 ? 4 : $n % 4;
        $aula = \App\Models\Aula::create([
            'default' => $default ? 1 : 0,
            'idUser' => $idUser,
            'name' => 'Aula ' . $curso,
            'year' => $curso,
            'age_range' => $rango,
        ]);
        return $aula;
    }
    /**
     * Crea un alumno
     */
    public function crearAlumno($owner, $birthday, $fak, $lastnames = false)
    {
        $genero = rand(0, 100) > 50 ? 1 : 2;
        $alumno = \App\Models\Alumno::create([
            'name' => $genero == 1 ? $fak->firstNameMale : $fak->firstNameFemale,
            'lastname' => $lastnames,
            'birthday' => $birthday,
            'gender' => $genero,
            'owner' => $owner,
        ]);
        return $alumno;
    }
    public function relacionAlumnoAula($idAula, $idAlumno)
    {
        // Crea la relación con el aula
        \App\Models\Aula_Alumno::create([
            'idAula' => $idAula,
            'idAlumno' => $idAlumno,
        ]);
    }
    /**
     * Crea un padre para un alumno
     */
    public function crearPadre($owner, $fak, $name = false)
    {
        if (!$name) {
            $name = rand(0, 100) % 2 == 0 ? $fak->firstNameMale : $fak->firstNameFemale;
            $email =  $fak->email;
        } else {
            $email = "$name@test.com";
        }
        $padre = \App\Models\User::create([
            'name' => $name,
            'lastname' => $fak->lastName,
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
        return $padre;
    }
    public function relacionAlumnoPadre($padre, $idAlumno)
    {
        \App\Models\Padre_Alumno::create([
            'idUser' => $padre->id,
            'idAlumno' => $idAlumno
        ]);
    }
    /**
     * Crear un diario para cada alumno del aula
     */
    public function crearDiario($aula, $alumnos, $fak)
    {
        $year = intval($aula->year);
        $fecha = new DateTime("$year-09-01");
        $fecha_fin = new DateTime(($year + 1) . "-08-1");
        if ($fecha_fin > new DateTime()) $fecha_fin = new DateTime();
        while ($fecha < $fecha_fin) {
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
    public function crearDietario($aula)
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\es_PE\Restaurant($faker));
        $year = intval($aula->year);
        $fecha = new DateTime("$year-09-01");
        $fecha_fin = new DateTime(($year + 1) . "-08-1");
        if ($fecha_fin > new DateTime()) $fecha_fin = new DateTime();
        while ($fecha <= $fecha_fin) {
            if ($fecha->format('N') < 6) {
                $a1 =  [];
                while (count($a1) < 3) {
                    $i = rand(1, 14);
                    if (!in_array($i, $a1)) $a1[] = $i;
                }
                Dietario::create([
                    'idAula' => $aula->id,
                    'date' => $fecha,
                    'breakfast' => 'Leche con galletas',
                    'breakfast_allergens' => '3,5',
                    'lunch' => $faker->foodName(),
                    'lunch_allergens' => implode(",", $a1),
                    'desert' => $faker->fruitName(),
                    'desert_allergens' => '3',
                ]);
            }
            $fecha->add(new DateInterval('P1D'));
        }
    }
    public function crearMenu($name, $owner)
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\es_PE\Restaurant($faker));
        //Crea el menú
        $menu = Menu::create([
            'name' => $name,
            'owner' => $owner->id,
        ]);
        // Crea las entradas para el menú
        for ($i = 1; $i < 6; $i++) {
            $a1 =  [];
            while (count($a1) < 3) {
                $n = rand(1, 14);
                if (!in_array($n, $a1)) $a1[] = $i;
            }
            $dia = Menu_Dia::create([
                'idMenu' => $menu->id,
                'dia' => $i,
                'breakfast' => 'Leche con galletas',
                'breakfast_allergens' => '3,5',
                'lunch' => $faker->foodName(),
                'lunch_allergens' => implode(",", $a1),
                'desert' => $faker->fruitName(),
                'desert_allergens' => '3',
            ]);
        }
        return $menu;
    }
}
