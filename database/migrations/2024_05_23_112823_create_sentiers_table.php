<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sentiers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('image_url');
            $table->text('description');
            $table->float('longueur');
            $table->float('duree');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('theme_id')->unsigned();
            $table->foreign('theme_id')
                ->references('id')
                ->on('themes')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiers');
    }
};
