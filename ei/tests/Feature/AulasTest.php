<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AulasTest extends TestCase
{
    public function test_recuperar_todas_las_aulas()
    {
        $response = $this->get('/api/aula');
        $response->assertStatus(200);
    }
    public function test_recuperar_un_aula()
    {
        $response = $this->get('/api/aula/1');
        $response->assertStatus(200);
    }
    public function test_recuperar_un_aula_que_no_existe()
    {
        $response = $this->get('/api/aula/100');
        $response->assertStatus(204);
    }
    public function test_insertar_aula()
    {
        DB::beginTransaction();
        $response = $this->post('api/aula', [
            'name' => 'test',
            'idUser' => 1,
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }

    public function test_editar_aula()
    {
        DB::beginTransaction();
        $response = $this->put('api/aula/1', [
            'name' => 'test123',
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }

    public function test_eliminar_aula()
    {
        DB::beginTransaction();
        $response = $this->delete('api/aula/1');
        $response->assertStatus(200);
        DB::rollback();
    }
}
