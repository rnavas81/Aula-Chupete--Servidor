<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Aula;
use App\Models\Aula_Alumno;
use App\Models\Diario;
use App\Models\Diario_Entrada;
use App\Models\Dietario;
use App\Models\Menu_Dia;
use DateInterval;
use DateTime;
use Exception;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Integer;

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
        else $entrada = $this->getDB(['alumnos'], ['id' => $id], 1);
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
            $data['idUser'] = auth()->user()->id;
            $nuevo = $this->insertDB($data);
            if ($nuevo) {
                if (isset($data['default']) && $data['default'] === 1) {
                    Aula::where('id', '!=', $nuevo->id)
                        ->update(['default' => 0]);
                }
                $alumnos = isset($request['alumnos']) ? $request['alumnos'] : [];
                $this->insertAlumnos($nuevo->id, $alumnos);
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
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $this->getRequestData($request);
            if ($this->updateDB($id, $data)) {
                if (isset($data['default']) && $data['default'] === 1) {
                    Aula::where('id', '<>', $id)
                        ->update(['default' => 0]);
                }
                $alumnos = isset($request['alumnos']) ? $request['alumnos'] : [];
                $this->insertAlumnos($id, $alumnos);
                DB::commit();
                $entrada = $this->getDB(['alumnos'], ['id' => $id], 1);
                return response()->json($entrada, 201);
            } else {
                throw new Exception('Error al modificar el aviso', 1);
            }
        } catch (\Throwable $th) {
            dd($th);
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
     * Recupera los alumnos de un aula
     */
    public function getAlumnos(Request $request, $id)
    {
        $alumnos = $this->getAlumnosDB($id);
        return response()->json($alumnos, 200);
    }
    public function setDefault(Request $request, $id)
    {
        $this->setDefaultDB($id);
        return response()->noContent(200);
    }
    public function getFaltas(Request $request, $id, $fecha)
    {
        $diario = Diario::with('entradas')->where('idAula', $id)->where('date', $fecha)->first();
        $ids = [];
        if ($diario) {
            $diario = $diario->toArray();
            foreach ($diario['entradas'] as $entrada) {
                if ($entrada['absence'] == 1) $ids[] = $entrada['idAlumno'];
            }
            return response()->json($ids, 200);
        }
    }
    public function getDiario($id, $fecha)
    {
        $diario = Diario::with('entradas')->where('idAula', $id)->where('date', $fecha)->first();
        return response()->json($diario, 200);
    }
    public function setDiario(Request $request, $id, $fecha)
    {
        $data = $request->toArray();
        $diario = Diario::where('idAula', $id)->where('date', $fecha)->first();
        if ($diario) {
            foreach ($data as $key => $value) {
                $diario[$key] = $value;
            }
            $diario->save();
        } else {
            $data['idAula'] = $id;
            $data['date'] = $fecha;
            $diario = Diario::create($data);
        }
        return response()->json($diario, 200);
    }
    public function addAlumno($idAula, $idAlumno)
    {
        if ($this->addAlumnoDB($idAula, $idAlumno)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }
    public function removeAlumno($idAula, $idAlumno)
    {
        if ($this->removeAlumnoDB($idAula, $idAlumno)) {
            return response()->noContent(200);
        } else {
            return response()->noContent(406);
        }
    }

    public function getDietarioSemana($idAula, $fecha)
    {
        $f = new DateTime($fecha);
        $dietario = [];
        while ($f->format('N') < 6) {
            $dietario[] = $this->getDietarioDiaDB($idAula, $f);
            $f->add(new DateInterval('P1D'));
        }
        return response()->json($dietario, 200);
    }
    public function getDietarioDia($idAula, $fecha)
    {
        $dieta = $this->getDietarioDiaDB($idAula, $fecha);
        return response()->json($dieta, 200);
    }
    public function getDietarioDiaDB($idAula, $fecha)
    {
        $dieta = Dietario::where('idAula', $idAula)->where('date', $fecha)->first();
        if ($dieta) {
            if (strlen($dieta->breakfast_allergens) > 0) $dieta->breakfast_allergens = explode(',', $dieta->breakfast_allergens);
            else $dieta->breakfast_allergens = [];
            if (strlen($dieta->lunch_allergens) > 0) $dieta->lunch_allergens = explode(',', $dieta->lunch_allergens);
            else $dieta->lunch_allergens = [];
            if (strlen($dieta->desert_allergens) > 0) $dieta->desert_allergens = explode(',', $dieta->desert_allergens);
            else $dieta->desert_allergens = [];
        }
        return $dieta;
    }

    public function setDietarioComida(Request $request, $idAula)
    {
        $request->validate([
            'id'     => ['required', 'integer'],
            'comida' => ['required', 'string', 'max:255'],
            'plato'  => ['string', 'max:500'],
            'date'  => ['required', 'date'],
        ]);
        $id = $request['id'];
        $data = [
            'idAula' => $idAula,
            'date' => $request['date'],
            $request['comida'] => $request['plato'],
            $request['comida'] . '_allergens' => isset($request['alergenos']) ? implode(',', $request['alergenos']) : ''
        ];
        $entrada = $this->setDietarioComidaDB($id, $data);
        return response()->json($entrada, 200);
    }

    public function setMenu(Request $request, $idAula)
    {
        $request->validate([
            'idMenu'     => ['required', 'integer'],
            'date'  => ['required', 'date'],
        ]);
        $this->setMenuDB($idAula, $request['date'], $request['idMenu']);
        $f = new DateTime($request['date']);
        $dietario = [];
        while ($f->format('N') < 6) {
            $dietario[] = $this->getDietarioDiaDB($idAula, $f);
            $f->add(new DateInterval('P1D'));
        }
        return response()->json($dietario, 200);
    }

    /** FUNCIONES PARA LA PERSISTENCIA DE DATOS */
    private function getDB($with = [], $where = [], $take = false)
    {

        $classes = \App\Models\Aula::where('idUser', auth()->user()->id);
        if (array_key_exists('active', $where)) {
            $classes = $classes->where('active', $where['active']);
        } else {
            $classes = $classes->where('active', 1);
        }
        if (array_key_exists('id', $where)) $classes = $classes->where('id', $where['id']);

        if ($take === false) {
            $classes = $classes->get();
        } elseif ($take == 1) {
            $classes = $classes->first();
        } else {
            $classes = $classes->take($take)->get()->toArray();
        }
        if (!!$classes) {
            if (in_array('alumnos', $with)) {
                if ($take == 1) {
                    // $alumnos = \App\Models\Aula_Alumno::with('alumnos')->where('idAula',$classes['id'])->get()->toArray();
                    $classes['alumnos'] = $this->getAlumnosDB($classes['id']);
                } else {
                    foreach ($classes as $key => $class) {
                        $classes[$key]['alumnos'] = $this->getAlumnosDB($class['id']);
                    }
                }
            }
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
        return $update == 1;
        // if ($update == 1) {
        //     return $this->getDB(['alumnos'], ['id' => $id], 1);
        // } else {
        //     return false;
        // }
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

    /**
     * Recupera los alumnos de un aula de la base de datos
     */
    public function getAlumnosDB(Int $id)
    {
        return Alumno::where('active', 1)
            ->with('padres.padre')
            ->whereHas('aulas', function ($query)  use ($id) {
                return $query->where('idAula', $id);
            })
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();
    }
    public function insertAlumnos($idAula, $alumnos)
    {
        Aula_Alumno::where('idAula', $idAula)->delete();
        foreach ($alumnos as $item) {
            $alumno = Alumno::where('id', $item['id'])->first();
            if (!$alumno) {
                unset($item['id']);
                $item['owner'] = auth()->user()->id;
                try {
                    //code...
                    $alumno = Alumno::create($item);
                } catch (\Throwable $th) {
                    //throw $th;
                    dd($item, $th);
                }
            }
            Aula_Alumno::create([
                'idAula' => $idAula,
                'idAlumno' => $alumno->id
            ]);
        }
        return true;
    }
    public function setDefaultDB($idAula, $idUser = null)
    {
        if (!$idUser) $idUser = auth()->user()->id;
        Aula::where('id', $idAula)
            ->where('idUser', $idUser)
            ->update(['default' => '1']);
        Aula::where('id', '!=', $idAula)
            ->where('idUser', $idUser)
            ->update(['default' => '0']);
        return true;
    }
    public function addAlumnoDB($idAula, $idAlumno)
    {
        Aula_Alumno::create([
            'idAula' => $idAula, 'idAlumno' => $idAlumno
        ]);
        return true;
    }
    public function removeAlumnoDB($idAula, $idAlumno)
    {
        Aula_Alumno::where('idAula', $idAula)->where('idAlumno', $idAlumno)->delete();
        return true;
    }

    public function setDietarioComidaDB($id, $data)
    {
        $result = Dietario::where('id', $id)->update($data);
        if ($result == 0) {
            $dieta = Dietario::create($data);
        } else $dieta = $this->getDietarioDiaDB($data['idAula'], $data['date']);
        return $dieta;
    }

    public function setMenuDB($idAula, $fecha, $idMenu)
    {
        $data = Menu_Dia::where('idMenu', $idMenu)->orderBy('dia', 'ASC')->get(['breakfast', 'breakfast_allergens', 'lunch', 'lunch_allergens', 'desert', 'desert_allergens'])->toArray();
        $f = new DateTime($fecha);
        $cont = 0;
        while ($f->format('N') < 6) {
            $result = Dietario::where('idAula', $idAula)->where('date', $f)->update($data[$cont]);
            if ($result == 0) {
                $data[$cont]['idAula'] = $idAula;
                $data[$cont]['date'] = $f;
                Dietario::create($data[$cont]);
            }
            $f->add(new DateInterval('P1D'));
            $cont++;
        }
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
        if (isset($request['default'])) $data['default'] = $request['default'];
        if (isset($request['age_range'])) $data['age_range'] = $request['age_range'];
        if (isset($request['year'])) $data['year'] = $request['year'];

        return $data;
    }
}
