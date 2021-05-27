<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyInteger('active')->default(1);
            $table->integer('owner')->unsigned();
            $table->string('name');
            $table->string('lastname');
            $table->tinyInteger('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('allergies')->nullable();
            $table->string('intolerances')->nullable();
            $table->string('diseases')->nullable();
            $table->string('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}
