<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Menu_Dia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Menus extends Controller
{
    // Llamadas por url
    public function get()
    {
        $menus = $this->getDB();
        return response()->json($menus, 200);
    }

    public function getDias($idMenu)
    {
        $dias = $this->getDiasDB($idMenu);
        return response()->json($dias, 200);
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
        ]);
        $data = [
            'name' => $request['name'],
            'owner' => auth()->user()->id,
        ];
        $dias = $request['dias'] ?: null;
        $menu = $this->insertDB($data, $dias);
        return $menu;
    }


    // Funciones para la persistencia de datos
    private function getDB($with = [], $where = [], $take = false)
    {

        $items = Menu::where('owner', auth()->user()->id);
        if (array_key_exists('active', $where)) {
            $items = $items->where('active', $where['active']);
        } else {
            $items = $items->where('active', 1);
        }
        if (array_key_exists('id', $where)) $items = $items->where('id', $where['id']);

        if ($take === false) {
            $items = $items->get();
        } elseif ($take == 1) {
            $items = $items->first();
        } else {
            $items = $items->take($take)->get()->toArray();
        }
        if (!!$items) {
            if (in_array('dias', $with)) {
                if ($take == 1) {
                    $items['dias'] = $this->getDiasDB($items['id']);
                } else {
                    foreach ($items as $key => $class) {
                        $items[$key]['dias'] = $this->getDiasDB($class['id']);
                    }
                }
            }
        }
        return $items;
    }
    public function getDiasDB($idMenu)
    {
        $dias = Menu_Dia::where('idMenu', $idMenu)->orderBy('dia')->get();
        foreach ($dias as $key => $dia) {
            if (strlen($dia->breakfast_allergens) > 0) $dias[$key]->breakfast_allergens = explode(',', $dia->breakfast_allergens);
            else $dias[$key]->breakfast_allergens = [];
            if (strlen($dia->lunch_allergens) > 0) $dias[$key]->lunch_allergens = explode(',', $dia->lunch_allergens);
            else $dias[$key]->lunch_allergens = [];
            if (strlen($dia->desert_allergens) > 0) $dias[$key]->desert_allergens = explode(',', $dia->desert_allergens);
            else $dias[$key]->desert_allergens = [];
        }
        return $dias;
    }
    public function insertDB($data, $dias = null)
    {
        try {
            DB::beginTransaction();
            $menu = Menu::create($data);
            $menu = $menu->toArray();
            $menu['dias'] = [];
            foreach ($dias as $dia) {
                $dia['idMenu'] = $menu['id'];
                if (isset($dia['breakfast_allergens'])) $dia['breakfast_allergens'] = implode(',',$dia['breakfast_allergens']);
                if (isset($dia['lunch_allergens'])) $dia['lunch_allergens'] = implode(',',$dia['lunch_allergens']);
                if (isset($dia['desert_allergens'])) $dia['desert_allergens'] = implode(',',$dia['desert_allergens']);
                $dia = Menu_Dia::create($dia);
                $menu['dias'][] = $dia;
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
        return $menu;
    }
}
