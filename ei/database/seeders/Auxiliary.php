<?php

namespace Database\Seeders;

use App\Models\Auxiliary as ModelsAuxiliary;
use Illuminate\Database\Seeder;

class Auxiliary extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Agrega los rangos de edad
        ModelsAuxiliary::create([
            'type'=>1,
            'id'=>1,
            'value'=>'mixto'
        ]);
        ModelsAuxiliary::create([
            'type'=>1,
            'id'=>2,
            'value'=>'0-1 años'
        ]);
        ModelsAuxiliary::create([
            'type'=>1,
            'id'=>3,
            'value'=>'1-2 años'
        ]);
        ModelsAuxiliary::create([
            'type'=>1,
            'id'=>4,
            'value'=>'2-3 años'
        ]);
        // Agrega los sexos
        ModelsAuxiliary::create([
            'type'=>2,
            'id'=>1,
            'value'=>'Hombre'
        ]);
        ModelsAuxiliary::create([
            'type'=>2,
            'id'=>2,
            'value'=>'Mujer'
        ]);
    }
}
