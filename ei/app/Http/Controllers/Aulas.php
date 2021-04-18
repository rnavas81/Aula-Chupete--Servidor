<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Aulas extends Controller
{
    /** FUNCIONES PARA LA API */
    public function getAll()
    {
        $classes = $this->getDB();
        return response()->json($classes, 200);
    }
    public function get($id)
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
            DB::beginTransaction();
            $data = $this->getRequestData($request);
            $nuevo = $this->insertDB($data);
            if ($nuevo) {
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
            DB::beginTransaction();
            $data = $this->getRequestData($request);
            $entrada = $this->updateDB($id, $data);
            if ($entrada) {
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

    /** FUNCIONES PARA LA PERSISTENCIA DE DATOS */
    private function getDB($where = [], $take = false)
    {
        $classes = \App\Models\Aula::with(['users']);
        if (array_key_exists("active", $where)) {
            $classes = $classes->where('active', $where['active']);
        } else {
            $classes = $classes->where('active', 1);
        }
        if (array_key_exists("id", $where)) $classes = $classes->where('id', $where['id']);

        if ($take === false) {
            $classes = $classes->get();
        } elseif ($take == 1) {
            $classes = $classes->first();
        } else {
            $classes = $classes->take($take)->get();
        }
        return $classes;
    }
    /**
     * Crea una nueva entrada en la base de datos
     */
    public function insertDB($data)
    {
        $nuevo = Aula::create($data);
        return $nuevo;
    }
    /**
     * Actualiza los datos de una entrada en la base de datos
     */
    public function updateDB($id, $data)
    {
        $update = Aula::where('id', $id)->where('active', 1)->update($data);
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
        return Aula::where('id', $id)->update([
            'active' => 0
        ]) == 1;
    }

    /** FUNCIONES EXTRA */

    /**
     * Recupera los datos enviados
     */
    public function getRequestData(Request $request)
    {
        $data = [];
        if (isset($request['name'])) $data['name'] = $request['name'];
        if (isset($request['idUser'])) $data['idUser'] = $request['idUser'];

        return $data;
    }
}
