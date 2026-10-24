<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BoardController extends Controller
{
    // Exibe todos os quadros do usuário autenticado
    public function index(): View
    {
        $boards = Board::where('user_id', Auth::id())->get();

        return view('boards.index', compact('boards'));
    }

    // Salva um novo quadro no banco de dados
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => ['required', 'regex:/\A#[0-9A-Fa-f]{6}\z/'],
        ]);

        Board::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'color' => $request->color ?? '#026aa7',
        ]);

        return redirect()->route('boards.index')->with('success', 'Quadro criado com sucesso!');
    }

    public function update(Request $request, Board $board): RedirectResponse
    {
        $this->authorizeBoard($board);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'color' => ['required', 'regex:/\A#[0-9A-Fa-f]{6}\z/'],
        ]);

        $board->update($validated);

        return redirect()->route('boards.index')->with('success', 'Quadro atualizado com sucesso!');
    }

    // Exibe o painel interno de um quadro específico com suas listas e cartões
    public function show(Board $board): View
    {
        $this->authorizeBoard($board);

        // Carrega as listas ordenadas e os cartões de cada lista
        $board->load('lists.cards.histories.user');

        return view('boards.show', compact('board'));
    }

    public function destroy(Board $board): RedirectResponse
    {
        $this->authorizeBoard($board);

        $board->load('lists.cards');

        DB::transaction(function () use ($board): void {
            foreach ($board->lists as $list) {
                foreach ($list->cards as $card) {
                    $card->delete();
                }

                $list->delete();
            }

            $board->delete();
        });

        return redirect()->route('boards.index')->with('success', 'Quadro apagado com sucesso!');
    }

    public function trash(): View
    {
        $boards = Board::onlyTrashed()
            ->where('user_id', Auth::id())
            ->latest('deleted_at')
            ->get();

        $lists = CardList::onlyTrashed()
            ->whereHas('board', fn ($query) => $query->where('user_id', Auth::id()))
            ->with('board')
            ->latest('deleted_at')
            ->get();

        return view('boards.trash', compact('boards', 'lists'));
    }

    public function restore(int $board): RedirectResponse
    {
        $board = Board::onlyTrashed()->findOrFail($board);
        $this->authorizeBoard($board);

        DB::transaction(function () use ($board): void {
            $board->restore();

            $lists = CardList::withTrashed()
                ->where('board_id', $board->id)
                ->get();

            foreach ($lists as $list) {
                $list->restore();

                Card::withTrashed()
                    ->where('card_list_id', $list->id)
                    ->restore();
            }
        });

        return redirect()->route('boards.trash')->with('success', 'Quadro e respetivas tarefas restaurados com sucesso!');
    }

    public function permanentlyDelete(int $board): RedirectResponse
    {
        $board = Board::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($board);

        DB::transaction(function () use ($board): void {
            $listIds = CardList::withTrashed()
                ->where('board_id', $board->id)
                ->pluck('id');

            Card::withTrashed()->whereIn('card_list_id', $listIds)->forceDelete();
            CardList::withTrashed()->where('board_id', $board->id)->forceDelete();
            $board->forceDelete();
        });

        return redirect()->route('boards.trash')->with('success', 'Quadro apagado permanentemente!');
    }

    private function authorizeBoard(Board $board): void
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
