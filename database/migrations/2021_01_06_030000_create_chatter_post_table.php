<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatterPostTable extends Migration
{
    public function up()
    {
        Schema::create('chatter_post', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('chatter_discussion_id')->unsigned();
            $table->foreign('chatter_discussion_id')->references('id')->on('chatter_discussion')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

            $table->text('body');

            $table->boolean('markdown')->default(0);
            $table->boolean('locked')->default(0);
            
            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('chatter_post');
    }
}