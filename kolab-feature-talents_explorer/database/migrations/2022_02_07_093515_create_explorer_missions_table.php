<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExplorerMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explorer_missions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('budget');
            $table->string('deadline');
            $table->string('type');
            $table->longText('description');
            $table->string('status');
            $table->userstamps();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('explorer_missions');
    }
}
