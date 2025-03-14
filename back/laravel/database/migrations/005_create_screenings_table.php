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
            $table->date('date')->unique();
            $table->enum('time', ['16:00', '18:00', '20:00']);
            $table->boolean('is_special')->default(false);
            $table->boolean('is_vip_active')->default(false);
            $table->integer('total_seats')->default(120); // Ej: Capacidad total
            $table->integer('vip_seats')->default(10); // Ej: Capacidad VIP
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('screenings');
    }
};
