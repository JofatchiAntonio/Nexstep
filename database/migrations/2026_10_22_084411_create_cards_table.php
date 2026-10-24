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
    Schema::create('cards', function (Blueprint $table) {
        $table->id();
        $table->foreignId('card_list_id')->constrained()->onDelete('cascade'); // Pertence a uma lista
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('position')->default(0); // Guarda a ordem do cartão na lista
        $table->date('due_date')->nullable(); // Data de entrega
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
