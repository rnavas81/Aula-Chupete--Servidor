<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Aula;
use App\Models\Aula_Alumno;
use App\Models\Message;
use App\Models\Padre_Alumno;
use App\Models\User;
use App\Models\User_Rol;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    /** FUNCIONES PARA LA API */
    public function getAll()
    {
        $users = $this->getDB();
        return response()->json($users, 200);
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
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'lastname'  => ['string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:32'],
            'contact' => ['string', 'max:32']
        ]);
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
                throw new Exception('Error al crear nuevo usuario', 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->noContent(406);
        }
    }
    public function update(Request $request, $id = null)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'lastname'  => ['string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['string', 'min:8', 'max:32'],
            'contact' => ['string', 'max:32']
        ]);
        $id != null or $id = auth()->user()->id;
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
                throw new Exception('Error al modificar el usuario', 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->noContent(406);
        }
    }
    /**
     * Elimina una entrada
     */
    public function delete($id)
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
    public function activate($id)
    {
        if ($this->activateDB($id)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }

    /**
     * Recupera las aulas asociadas al usuario $id
     * @param Integer $id identificador del usuario, si es null se toma el usuario activo
     */
    public function getAulas($id = null)
    {
        if (!isset($id)) {
            $id = auth()->user()->id;
        }
        $aulas = $this->getAulasDB($id);
        return response()->json($aulas, 200);
    }
    /**
     * Recupera los alumnos de un usuario
     */
    public function getAlumnos($id = null)
    {
        if (!isset($id)) {
            $id = auth()->user()->id;
        }
        $alumnos = $this->getAlumnosDB($id);
        return response()->json($alumnos, 200);
    }

    public function verifyEmail($email)
    {
        $users = User::where('email', $email)->count();
        if ($users == 0) return response()->noContent(200);
        else return false;
    }

    public function getParents()
    {
        $users = User::where('owner', auth()->user()->id)
            ->where('activated', 1)
            ->where('blocked', 0)
            ->whereHas('roles', function (Builder $query) {
                $query->where('idRol', '3');
            })->get();
        return response()->json($users, 200);
    }
    public function getChilds()
    {
        $idPadre = auth()->user()->id;
        $alumnos = Padre_Alumno::with('alumno')->where('idUser', $idPadre)->get();
        foreach ($alumnos as $key => $alumno) {
            $alumno = $alumno->alumno->toArray();
            $alumnos[$key] = $alumno;
        }
        return response()->json($alumnos, 200);
    }

    public function addMessage(Request $request)
    {
        $request->validate([
            'message'     => ['required', 'string', 'max:255'],
        ]);
        $data = [
            'message' => $request['message'],
            'user' => $request->user('api') !== null ? $request->user('api')->id: 0,
        ];
        Message::create($data);
        return response()->noContent(200);
    }

    /** FUNCIONES PARA LA PERSISTENCIA DE DATOS */
    private function getDB($where = [], $take = false)
    {
        $users = \App\Models\User::with(['roles', 'aulas'])->whereHas('roles', function (Builder $query) {
            $query->where('idRol', '<>', '1');
        });
        if (array_key_exists('activated', $where)) {
            $users = $users->where('activated', $where['activated']);
        } else {
            $users = $users->where('activated', 1);
        }
        if (array_key_exists('blocked', $where)) {
            $users = $users->where('blocked', $where['blocked']);
        } else {
            $users = $users->where('blocked', 0);
        }
        if (array_key_exists('id', $where)) $users = $users->where('id', $where['id']);

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
    /**
     * Recupera de la base de datos las aulas de un usuario
     */
    public function getAulasDB($id)
    {
        return Aula::with('age')
            ->where('idUser', $id)
            ->where('active', 1)
            ->orderBy('default', 'DESC')
            ->orderBy('year', 'DESC')
            ->get();
    }
    /**
     * Recupera los alumnos de un usuario
     */
    public function getAlumnosDB($id)
    {
        $alumnos = Alumno::where('owner', $id)
            ->where('active', 1)
            ->addSelect('*')
            ->addSelect(DB::raw('DATE_FORMAT(birthday,\'%Y\') as year'))
            ->orderBy('year', 'desc')
            ->orderBy('lastname', 'asc')
            ->get()->toArray();
        foreach ($alumnos as $key => $alumno) {
            unset($alumnos[$key]['year']);
        }
        return $alumnos;
    }
    /** FUNCIONES EXTRA */

    /**
     * Recupera los datos recibidos
     */
    public function getRequestData(Request $request)
    {
        $data = [];
        if (isset($request['name'])) $data['name'] = $request['name'];
        if (isset($request['lastname'])) $data['lastname'] = $request['lastname'];
        if (isset($request['email'])) $data['email'] = $request['email'];
        if (isset($request['password'])) $data['password'] = bcrypt($request['password']);
        if (isset($request['contact'])) $data['contact'] = $request['contact'];

        return $data;
    }
}
