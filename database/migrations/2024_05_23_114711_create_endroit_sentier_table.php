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
        Schema::create('endroit_sentier', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('endroit_id')->unsigned();
            $table->integer('sentier_id')->unsigned();
            $table->foreign('endroit_id')->references('id')->on('endroits')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
            $table->foreign('sentier_id')->references('id')->on('sentiers')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endroit_sentier');
    }
};
