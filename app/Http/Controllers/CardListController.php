<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CardListController extends Controller
{
    public function store(Request $request, Board $board): RedirectResponse
    {
        // Verifica se o quadro pertence ao usuário logado
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        // Pega a última posição para colocar a nova lista no final
        $lastPosition = $board->lists()->max('position') ?? 0;

        CardList::create([
            'board_id' => $board->id,
            'title' => $validated['title'],
            'position' => $lastPosition + 1,
        ]);

        return redirect()->route('boards.show', $board->id)->with('success', 'Lista criada com sucesso!');
    }

    public function update(Request $request, CardList $list): RedirectResponse
    {
        $this->authorizeList($list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $list->update($validated);

        return redirect()->route('boards.show', $list->board_id)->with('success', 'Lista atualizada com sucesso!');
    }

    public function destroy(CardList $list): RedirectResponse
    {
        $this->authorizeList($list);

        DB::transaction(function () use ($list): void {
            $list->cards->each(fn (Card $card) => $card->delete());
            $list->delete();
        });

        return redirect()->route('boards.show', $list->board_id)->with('success', 'Lista movida para a lixeira!');
    }

    public function restore(int $list): RedirectResponse
    {
        $list = CardList::onlyTrashed()->findOrFail($list);
        $this->authorizeList($list);

        if ($list->board()->withTrashed()->firstOrFail()->trashed()) {
            abort(404);
        }

        DB::transaction(function () use ($list): void {
            $list->restore();

            Card::withTrashed()
                ->where('card_list_id', $list->id)
                ->restore();
        });

        return redirect()->route('boards.trash')->with('success', 'Lista e respetivas tarefas restauradas com sucesso!');
    }

    private function authorizeList(CardList $list): void
    {
        if ($list->board()->withTrashed()->firstOrFail()->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
