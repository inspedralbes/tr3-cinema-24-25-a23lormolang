<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: Sala 1, Sala Premium, etc.
            $table->boolean('has_vip')->default(false);
            $table->integer('total_seats');
            $table->integer('vip_seats')->default(0);
            // $table->json('seat_map')->nullable(); // Opcional: Esto seria por si quiero meter que los user editen la disposición de los asientos vip
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};


