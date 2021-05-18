<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiarioEntradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diario_entradas', function (Blueprint $table) {
            $table->integer('idDiario')->unsigned();
            $table->integer('idAlumno')->unsigned();
            $table->string('activity',1000)->nullable();
            $table->string('food',1000)->nullable();
            $table->string('behaviour',1000)->nullable();
            $table->string('sphincters',1000)->nullable();
            $table->tinyInteger('absence')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diario_entradas');
    }
}
