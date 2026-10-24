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
    Schema::create('card_lists', function (Blueprint $table) {
        $table->id();
        $table->foreignId('board_id')->constrained()->onDelete('cascade'); // Pertence a um quadro
        $table->string('title');
        $table->integer('position')->default(0); // Guarda a ordem das colunas
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_lists');
    }
};
