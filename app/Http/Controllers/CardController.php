<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function store(Request $request, CardList $list)
    {
        if ($list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $lastPosition = $list->cards()->max('position') ?? 0;

        Card::create([
            'card_list_id' => $list->id,
            'title' => $validated['title'],
            'position' => $lastPosition + 1,
            'due_date' => $validated['due_date'] ?? null,
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

    public function toggleComplete(Card $card): RedirectResponse
    {
        if ($card->list->board->user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($card): void {
            if ($card->completed_at !== null) {
                $card->update(['completed_at' => null]);
                $card->histories()->create([
                    'user_id' => Auth::id(),
                    'event' => 'reopened',
                ]);

                return;
            }

            $dueDate = $card->due_date ?? $card->list->end_date;

            $card->update(['completed_at' => now()]);
            $card->histories()->create([
                'user_id' => Auth::id(),
                'event' => 'completed',
                'deadline_state' => $dueDate === null
                    ? null
                    : ($dueDate->isBefore(today()) ? 'late' : 'on_time'),
            ]);
        });

        return redirect()->route('boards.show', $card->list->board_id)->with('success', 'Estado da tarefa atualizado!');
    }
}