<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function store(Request $request, CardList $list)
    {
        if ($list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $lastPosition = $list->cards()->max('position') ?? 0;

        Card::create([
            'card_list_id' => $list->id,
            'title' => $request->title,
            'position' => $lastPosition + 1,
        ]);

        return redirect()->route('boards.show', $list->board_id)->with('success', 'Cartão criado com sucesso!');
    }

    // Atualizar cartão (Editar título, descrição ou data)
    public function update(Request $request, Card $card)
    {
        if ($card->list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $card->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('boards.show', $card->list->board_id)->with('success', 'Cartão atualizado com sucesso!');
    }

    // Apagar cartão (Soft Delete)
    public function destroy(Card $card)
    {
        if ($card->list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $card->delete(); // Executa o Soft Delete configurado no model

        return redirect()->route('boards.show', $card->list->board_id)->with('success', 'Cartão movido para a lixeira!');
    }
}