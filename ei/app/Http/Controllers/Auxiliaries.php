<?php

namespace App\Http\Controllers;

use App\Models\Auxiliary;
use Illuminate\Http\Request;

class Auxiliaries extends Controller
{
    public function getAgeRange()
    {
        $data = Auxiliary::where('type',1)->get();
        return response()->json($data,200);
    }
    public function getGenders()
    {
        $data = Auxiliary::where('type',2)->get();
        return response()->json($data,200);
    }
    public function getAllergens()
    {
        $data = Auxiliary::where('type',3)->get();
        return response()->json($data,200);
    }
}
