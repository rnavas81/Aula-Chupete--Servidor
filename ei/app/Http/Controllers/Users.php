<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\User_Rol;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    /** FUNCIONES PARA LA API */
    public function getAll(Request $params)
    {
        $users = $this->getDB();
        return response()->json($users, 200);
    }
    public function get(Request $request, $id)
    {
        if ($id == 0) return response()->noContent(204);
        else $entrada = $this->getDB(['id' => $id], 1);
        if (!empty($entrada))
            return response()->json($entrada, 200);
        else
            return response()->noContent(204);
    }

    public function insert(Request $request)
    {
        try {
            $data = $this->getRequestData($request);
            DB::beginTransaction();
            $nuevo = $this->insertDB($data);
            if ($nuevo) {
                if (isset($request['rol'])) {
                    $this->addRol($nuevo->id, $request['rol']);
                }
                DB::commit();
                return response()->json($nuevo, 201);
            } else {
                throw new Exception("Error al crear nuevo usuario", 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->noContent(406);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $data = $this->getRequestData($request);
            DB::beginTransaction();
            $entrada = $this->updateDB($id, $data);
            if ($entrada) {
                if (isset($request['rol'])) {
                    $this->addRol($entrada->id, $request['rol']);
                }
                DB::commit();
                return response()->json($entrada, 201);
            } else {
                throw new Exception("Error al modificar el aviso", 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->noContent(406);
        }
    }
    /**
     * Elimina una entrada
     */
    public function delete(Request $request, $id)
    {
        if ($this->deleteDB($id)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }

    /**
     * Activa un usuario
     */
    public function activate(Request $request, $id)
    {
        if ($this->activateDB($id)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }

    /** FUNCIONES PARA LA PERSISTENCIA DE DATOS */
    private function getDB($where = [], $take = false)
    {
        $users = \App\Models\User::with(['roles', 'aulas'])->whereHas('roles', function (Builder $query) {
            $query->where('idRol', '<>', '1');
        });
        if (array_key_exists("activated", $where)) {
            $users = $users->where('activated', $where['activated']);
        } else {
            $users = $users->where('activated', 1);
        }
        if (array_key_exists("blocked", $where)) {
            $users = $users->where('blocked', $where['blocked']);
        } else {
            $users = $users->where('blocked', 0);
        }
        if (array_key_exists("id", $where)) $users = $users->where('id', $where['id']);

        if ($take === false) {
            $users = $users->get();
        } elseif ($take == 1) {
            $users = $users->first();
        } else {
            $users = $users->take($take)->get();
        }
        return $users;
    }
    /**
     * Crea una nueva entrada en la base de datos
     */
    public function insertDB($data)
    {
        $nuevo = User::create($data);
        return $nuevo;
    }
    /**
     * Actualiza los datos de una entrada en la base de datos
     */
    public function updateDB($id, $data)
    {
        $update = User::where('id', $id)->where('activated', 1)->where('blocked', 0)->update($data);
        if ($update == 1) {
            return $this->getDB(['id' => $id], 1);
        } else {
            return false;
        }
    }
    /**
     * Elimina una entrada de la base de datos
     */
    public function deleteDB($id)
    {
        return User::where('id', $id)->update([
            'activated' => 0
        ]) == 1;
    }
    /**
     * Activa un usuario
     */
    public function activateDB($id)
    {
        return User::where('id', $id)->where('blocked', 0)->update([
            'activated' => 1
        ]) == 1;
    }

    /**
     * Agrega el rol a un usuario
     */
    public function addRol($idUser, $rol)
    {
        User_Rol::where('idUser', $idUser)->delete();
        $data = [];
        switch ($rol) {
            case 'teacher':
                $data = [
                    [
                        'idUser' => $idUser,
                        'idRol' => 2,
                    ],
                    [
                        'idUser' => $idUser,
                        'idRol' => 3,
                    ]
                ];
                break;
            case 'parent':
                $data = [
                    [
                        'idUser' => $idUser,
                        'idRol' => 3,
                    ]
                ];
                break;
        }
        User_Rol::insert($data);
    }

    /** FUNCIONES EXTRA */

    /**
     * Recupera los datos enviados
     */
    public function getRequestData(Request $request)
    {
        $data = [];
        if (isset($request['name'])) $data['name'] = $request['name'];
        if (isset($request['lastname'])) $data['lastname'] = $request['lastname'];
        if (isset($request['email'])) $data['email'] = $request['email'];
        if (isset($request['password'])) $data['password'] = $request['password'];

        return $data;
    }
}
