<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Users extends TestCase
{
    use WithoutMiddleware;


    public function test_recuperar_todos_usuarios()
    {
        $response = $this->get('/api/user');

        $response->assertStatus(200);
    }

    public function test_no_puede_recuperar_usuario_administrador()
    {
        $response = $this->get('/api/user/1');
        $response->assertStatus(204);
    }

    public function test_recupera_un_usuario_que_no_existe()
    {
        $response = $this->get('/api/user/100');
        $response->assertStatus(204);
    }
    public function test_recupera_un_usuario()
    {
        $response = $this->get('/api/user/2');
        $response->assertStatus(200);
    }

    public function test_insertar_usuario()
    {
        DB::beginTransaction();
        $response = $this->post('api/user', [
            'name' => 'test',
            'lastname' => 'insert',
            'password' => bcrypt("123"),
            'email' => 'test1@nomail.com',
            'email_verified_at' => now(),
            'activated' => 0,
            'blocked' => 0,
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }
    public function test_insertar_email_que_ya_existe()
    {
        DB::beginTransaction();
        $response = $this->post('api/user', [
            'name' => 'admin',
            'password' => bcrypt("admin"),
            'email' => 'aula.chupetes@gmail.com',
            'email_verified_at' => now(),
            'activated' => 1,
            'blocked' => 0,
        ]);
        $response->assertStatus(406);
        DB::rollback();
    }

    public function test_editar_usuario()
    {
        DB::beginTransaction();
        $response = $this->put('api/user/10', [
            'name' => 'test',
            'lastname' => 'update',
            'password' => bcrypt("123"),
            'email' => 'test2@nomail.com',
            'email_verified_at' => now(),
            'activated' => 0,
            'blocked' => 0,
        ]);
        $response->assertStatus(201);
        DB::rollback();
    }

    public function test_activar_usuario()
    {
        DB::beginTransaction();
        try {
            $response2 = $this->put('api/user/activate/10');
            $response2->assertStatus(200);
        } finally {
            DB::rollback();
        }
    }

    public function test_eliminar_usuario()
    {
        DB::beginTransaction();
        $response = $this->delete('api/user/1');
        $response->assertStatus(200);
        DB::rollback();
    }
}
