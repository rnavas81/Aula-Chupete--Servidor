<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AulasTest extends TestCase
{
    // Mantener siempre como primer test para recoger el token de autentificación
    public function test_acceder_a_la_aplicacion()
    {
        $response = $this->post('api/login', [
            'email' => 'educador@test.com',
            'password' => 123,
        ]);
        $response->assertStatus(200);
        $data = json_decode($response->getContent(), true);
        $_SESSION['token'] = $data["token"];
    }
    public function test_recuperar_todas_las_aulas()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->get('/api/aula');
        $response->assertStatus(200);
    }
    public function test_recuperar_un_aula()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->get('/api/aula/1');
        $response->assertStatus(200);
    }
    public function test_recuperar_un_aula_que_no_existe()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->get('/api/aula/100');
        $response->assertStatus(204);
    }
    public function test_insertar_aula()
    {
        DB::beginTransaction();
        $response = $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->post('api/aula', [
            'name' => 'test',
            'idUser' => 1,
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }
    public function test_editar_aula()
    {
        DB::beginTransaction();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->put('api/aula/1', [
            'name' => 'test123',
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }
    public function test_eliminar_aula()
    {
        DB::beginTransaction();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->delete('api/aula/1');
        $response->assertStatus(200);
        DB::rollback();
    }
    public function test_recupera_los_alumnos_de_un_aula()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->get('api/aula/alumnos/1');
        $response->assertStatus(200);
    }
    public function test_asigna_un_aula_como_activa()
    {
        DB::beginTransaction();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->put('api/aula/default/2');
        $response->assertStatus(200);

        DB::rollback();
    }
    public function test_recupera_las_faltas_de_una_aula_en_un_día()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->get('api/aula/1/faltas/2020-06-04');

        $response->assertStatus(200);

    }
    // public function test_recupera_los_datos_del_diario_de_un_aula_en_una_fecha()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->get('api/aula/1/diario/{fecha}');
    // }
    // public function test_Asigna_datos_a_un_diario()
    // {
    //     DB::beginTransaction();
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->post('api/aula/1/diario/{fecha}');
    //     DB::rollback();
    // }
    // public function test_Agrega_un_alumno_a_un_aula()
    // {
    //     DB::beginTransaction();
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->post('api/aula/{idAula}/alumno/{idAlumno}');
    //     DB::rollback();
    // }
    // public function test_Quita_un_alumno_de_un_aula()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->delete('api/aula/{idAula}/alumno/{idAlumno}');
    // }
    // public function test_recupera_el_dietario_de_un_aula_para_un_dia()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->get('api/aula/{idAula}/dietario/{fecha}');
    // }
    // public function test_recupera_el_dietario_de_un_aula_para_la_semana()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->get('api/aula/{idAula}/dietario/semana/{fecha}');
    // }
    // public function test_recupera_el_dietario_de_un_aula()
    // {
    //     DB::beginTransaction();
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->post('api/aula/{idAula}/dietario/comida');
    //     DB::rollback();
    // }
    // public function test_Asigna_los_platos_de_un_menú_a_una_semana_del_dietario_del_aula()
    // {
    //     DB::beginTransaction();
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $_SESSION['token']
    //     ])->post('api/aula/{idAula}/dietario/menu');
    //     DB::rollback();
    // }

    // Mantener siempre como último test para salir de la sesión
    public function test_salir_de_la_aplicacion()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $_SESSION['token']
        ])->post('/api/logout');
        $response->assertStatus(200);
    }
}
