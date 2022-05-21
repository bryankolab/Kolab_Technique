<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExplorerMissionConversationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explorer_mission_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->longText('message');
            $table->unsignedBigInteger('id_quote')->nullable();
            $table->foreign('id_quote')->references('id')->on('explorer_mission_quote')->onDelete('set null');

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
        Schema::dropIfExists('explorer_mission_conversation_messages');
    }
}
