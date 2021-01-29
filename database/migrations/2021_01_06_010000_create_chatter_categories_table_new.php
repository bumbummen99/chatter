<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatterCategoriesTableNew extends Migration
{
    public function up()
    {
        Schema::create('chatter_categories', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('chatter_categories')
                        ->onDelete('set null')
                        ->onUpdate('cascade');

            $table->integer('order')->default(1);
            $table->string('name');
            $table->string('color', 20);
            $table->string('slug');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('chatter_categories');
    }
}
