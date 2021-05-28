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
        $rangos = [
            'mixto',
            '0-1 años',
            '1-2 años',
            '2-3 años',
        ];
        foreach ($rangos as $key => $value) {
            ModelsAuxiliary::create([
                'type'=>1,
                'id'=>$key+1,
                'value'=>$value
            ]);
        }
        // Agrega los sexos
        $sexos = ['Hombre','Mujer'];
        foreach ($sexos as $key => $value) {
            ModelsAuxiliary::create([
                'type'=>2,
                'id'=>$key+1,
                'value'=>$value
            ]);
        }

        // Alergenos
        $alergenos =[
            'Pescado',
            'Frutos secos',
            'Lácteos',
            'Moluscos',
            'Cereales con gluten',
            ' Crustáceos',
            'Huevo',
            'Cacahuetes',
            'Soja',
            'Apio',
            'Mostaza',
            'Sésamo',
            'Altramuz',
            'Sulfitos'
        ];
        foreach ($alergenos as $key => $value) {
            ModelsAuxiliary::create([
                'type'=>3,
                'id'=>$key+1,
                'value'=>$value
            ]);
        }

    }
}
