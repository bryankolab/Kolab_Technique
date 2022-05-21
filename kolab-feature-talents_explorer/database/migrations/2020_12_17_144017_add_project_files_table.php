<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('project_files', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('path');
        $table->string('extension');
        $table->string('uniqid');
        $table->string('url_view');
        $table->string('url_download');

        $table->unsignedBigInteger('project_id')->nullable();
        $table->foreign('project_id')->references('id')->on('projects')->onDelete('CASCADE');

        $table->timestamps();
        $table->userstamps();
        $table->softUserstamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('project_files');
    }
}
