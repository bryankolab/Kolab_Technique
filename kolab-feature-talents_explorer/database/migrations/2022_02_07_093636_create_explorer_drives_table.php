<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExplorerDrivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explorer_drives', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_proposition')->nullable();
            $table->foreign('id_proposition')->references('id')->on('explorer_mission_propositions')->onDelete('set null');

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
        Schema::dropIfExists('explorer_drives');
    }
}
