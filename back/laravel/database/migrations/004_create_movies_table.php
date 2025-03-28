<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_id')->unique();
            $table->string('title');
            $table->text('description');
            $table->integer('duration'); // en minutos
            $table->string('poster_url')->nullable();
            $table->integer('year');
            $table->string('genre');
            $table->string('director');
            $table->text('actors');
            $table->text('awards')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->string('box_office')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
