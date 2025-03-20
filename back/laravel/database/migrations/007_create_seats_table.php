<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->char('row', 1);
            $table->integer('number');
            $table->enum('type', ['normal', 'vip']);
            $table->timestamps();
            $table->unique(['room_id', 'row', 'number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('seats');
    }
};
