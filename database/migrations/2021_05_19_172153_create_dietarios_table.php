<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dietarios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('idAula');
            $table->date('date');
            $table->string('breakfast',500)->nullable();
            $table->string('breakfast_allergens')->nullable();
            $table->string('lunch',500)->nullable();
            $table->string('lunch_allergens')->nullable();
            $table->string('desert',500)->nullable();
            $table->string('desert_allergens')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dietarios');
    }
}
