<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxiliariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $primaryKey = ['type','id'];
    public function up()
    {
        Schema::create('auxiliaries', function (Blueprint $table) {
            $table->tinyInteger('type')->unsigned();
            $table->integer('id')->unsigned();
            $table->string('value','1000');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auxiliaries');
    }
}
