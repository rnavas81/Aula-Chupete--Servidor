<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Mail\ActivarPadre;
use App\Models\Alumno;
use App\Models\Aula_Alumno;
use App\Models\Diario;
use App\Models\Diario_Entrada;
use App\Models\Padre_Alumno;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Alumnos extends Controller
{

    /** FUNCIONES PARA LA API */
    public function getAll(Request $params)
    {

        if (!isset($id)) {
            $id = auth()->user()->id;
        }
        $alumnos = app(Users::class)->getAlumnosDB($id);
        return response()->json($alumnos, 200);
    }
    public function get(Request $request, $idAlumno)
    {
        if ($idAlumno == 0) return response()->noContent(204);
        else $entrada = $this->getDB(['id' => $idAlumno], 1);
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
                if (isset($request['idAula'])) {
                    Aula_Alumno::create([
                        'idAula' => $request['idAula'],
                        'idAlumno' => $nuevo->id
                    ]);
                }
                if (isset($request['padres'])) {
                    $this->updateParents($nuevo->id, $request['padres']);
                }
                DB::commit();
                $alumno = $this->getDB(['id' => $nuevo->id], 1);
                return response()->json($alumno, 201);
            } else {
                throw new Exception('Error al crear nuevo alumno', 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->noContent(406);
        }
    }
    public function update(Request $request, $idAlumno)
    {
        try {
            $data = $this->getRequestData($request);
            DB::beginTransaction();
            $entrada = $this->updateDB($idAlumno, $data);
            if ($entrada) {
                if (isset($request['idAula'])) {
                    Aula_Alumno::create([
                        'idAula' => $request['idAula'],
                        'idAlumno' => $entrada->id
                    ]);
                }
                if (isset($request['padres'])) {
                    $this->updateParents($entrada->id, $request['padres']);
                }
                DB::commit();
                $alumno = $this->getDB(['id' => $entrada->id], 1);
                return response()->json($alumno, 201);
            } else {
                throw new Exception('Error al modificar el alumno', 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return response()->noContent(406);
        }
    }
    /**
     * Elimina una entrada
     */
    public function delete(Request $request, $idAlumno)
    {
        if ($this->deleteDB($idAlumno)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }
    public function setFalta(Request $request)
    {
        $data = $request->toArray();
        $idDiario = $data['diario'];
        $idAlumno = $data['alumno'];
        unset($data['diario'], $data['alumno']);
        $entrada = $this->setDiarioAlumnoDB($idDiario, $idAlumno, $data);
        return response()->json($entrada, 200);
    }
    public function getDiario($idAlumno, $idAula, $fecha)
    {
        // Recupera los diarios de las aulas de un alumno en una fecha
        $diario = Diario::where('idAula',$idAula)->where('date',$fecha)->first();
        if($diario){
            $entrada = Diario_Entrada::where('idDiario',$diario['id'])
                ->where('idAlumno',$idAlumno)
                ->first();
            if($entrada)$diario['entrada']=$entrada?$entrada->toArray():null;
            else $diario['entrada']=[];

        }
        return response()->json($diario,200);
    }
    public function setDiario(Request $request, $idAlumno, $diario)
    {
        $data = $request->toArray();
        $entrada = $this->setDiarioAlumnoDB($diario, $idAlumno, $data);
        return response()->json($entrada, 200);
    }

    public function getAulas($idAlumno)
    {
        $aulas = Aula_Alumno::with('aula')->whereHas('aula',function(Builder $query){
            $query->orderBy('default','desc')->orderBy('year','desc');
        })->where('idAlumno',$idAlumno)->get();
        foreach ($aulas as $key => $aula) {
            $aulas[$key]=$aula->aula;
        }
        return response()->json($aulas,200);
    }

    /** FUNCIONES PARA LA PERSISTENCIA DE DATOS */
    private function getDB($where = [], $take = false, $orderBy = [])
    {
        $users = Alumno::with('padres.padre')->where('active', 1);

        if (array_key_exists('id', $where)) $users = $users->where('id', $where['id']);
        if (array_key_exists('owner', $where)) $users = $users->where('owner', $where['owner']);

        if (is_array($orderBy)) {
            foreach ($orderBy as $field => $order) {
                $users = $users->orderBy($field, $order);
            }
        }


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
        $nuevo = Alumno::create($data);
        return $nuevo;
    }
    /**
     * Actualiza los datos de una entrada en la base de datos
     */
    public function updateDB($id, $data)
    {
        $update = Alumno::where('id', $id)->where('active', 1)->update($data);
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
        return Alumno::where('id', $id)->update([
            'active' => 0
        ]) == 1;
    }
    public function setDiarioAlumnoDB($idDiario, $idAlumno, $data)
    {
        $entrada = Diario_Entrada::where('idDiario', $idDiario)->where('idAlumno', $idAlumno)->first();
        if ($entrada) {
            Diario_Entrada::where('idDiario', $idDiario)->where('idAlumno', $idAlumno)->update($data);
            $entrada = Diario_Entrada::where('idDiario', $idDiario)->where('idAlumno', $idAlumno)->first();
        } else {
            $data['idDiario'] = $idDiario;
            $data['idAlumno'] = $idAlumno;
            $entrada = Diario_Entrada::create($data);
        }
        return $entrada;
    }
    public function updateParents($idAlumno, $padres)
    {
        Padre_Alumno::where('idAlumno', $idAlumno)->delete();
        foreach ($padres as $key => $item) {
            $padre = $item['padre'];
            $element = User::where('id', $padre['id'])->first();
            if ($element) {
                Padre_Alumno::create([
                    'idAlumno' => $idAlumno,
                    'idUser' => $padre['id']
                ]);
                if ($element->activated == 0 || $element->blocked == 1) {
                    $element->activated_token = Str::random(128) . time();
                    $element->activated = 0;
                    $element->blocked = 0;
                    $element->save();
                    $this->sendActivateParent($element->activated_token, $element->email, $element->name, $element->lastname);
                }
            } else {
                unset($padre['id']);
                $nuevo = $this->makeParent($padre);
                Padre_Alumno::create([
                    'idAlumno' => $idAlumno,
                    'idUser' => $nuevo->id
                ]);
            }
        }
    }
    public function makeParent($data)
    {
        $password = Str::random(10);
        $data['password'] = bcrypt($password);
        $data['activated_token'] = Str::random(128) . time();
        $data['owner'] = auth()->user()->id;
        $nuevo = app(Users::class)->insertDB($data);
        app(Users::class)->addRol($nuevo->id, 'parent');
        if (isset($data['email'])) {
            $this->sendActivateParent($data['activated_token'], $nuevo->email, $nuevo->name, $nuevo->lastname,$password);
        }

        return $nuevo;
    }
    public function sendActivateParent($token, $email, $name, $lastname,$password=null)
    {
        $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '') .  DIRECTORY_SEPARATOR . env('ACTVATE_URL') . DIRECTORY_SEPARATOR . $token;
        Mail::to($email)->send(new ActivarPadre($name, $lastname, $url, $password));
        return !Mail::failures();
    }
    /** FUNCIONES EXTRA */

    /**
     * Recupera los datos recibidos
     */
    public function getRequestData(Request $request)
    {
        $data = [];
        if (isset($request['owner'])) $data['owner'] = $request['owner'];
        else $data['owner'] = auth()->user()->id;
        if (isset($request['name'])) $data['name'] = $request['name'];
        if (isset($request['lastname'])) $data['lastname'] = $request['lastname'];
        if (isset($request['gender'])) $data['gender'] = $request['gender'];
        if (isset($request['birthday'])) $data['birthday'] = $request['birthday'];
        if (isset($request['allergies'])) $data['allergies'] = $request['allergies'];
        if (isset($request['intolerances'])) $data['intolerances'] = $request['intolerances'];
        if (isset($request['diseases'])) $data['diseases'] = $request['diseases'];
        if (isset($request['observations'])) $data['observations'] = $request['observations'];

        return $data;
    }
}
