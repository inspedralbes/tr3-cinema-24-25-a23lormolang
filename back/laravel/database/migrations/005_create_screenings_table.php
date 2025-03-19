<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained();
            $table->date('date');
            $table->string('time', 5);
            $table->boolean('is_special')->default(false);
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('screenings');
    }
};
