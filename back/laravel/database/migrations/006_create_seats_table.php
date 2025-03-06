<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_id')->constrained();
            $table->char('row', 1);
            $table->integer('number');
            $table->enum('type', ['normal', 'vip']);
            $table->boolean('is_occupied')->default(false);
            $table->timestamps();
            $table->unique(['screening_id', 'row', 'number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
