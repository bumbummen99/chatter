<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatterDiscussionTable extends Migration
{
    public function up()
    {
        Schema::create('chatter_discussion', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('chatter_category_id')->unsigned();
            $table->foreign('chatter_category_id')->references('id')->on('chatter_categories')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('color', 20)->nullable()->default('#232629');
            $table->boolean('sticky')->default(false);
            $table->integer('views')->unsigned()->default(0);
            $table->boolean('answered')->default(0);
            
            $table->timestamps();
            $table->timestamp('last_reply_at')->nullable();

            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('chatter_discussion');
    }
}
