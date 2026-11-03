<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_deadline_cannot_be_set_before_a_card_deadline(): void
    {
        $user = User::factory()->create();
        $list = $this->createList($user);
        $card = Card::create([
            'card_list_id' => $list->id,
            'title' => 'Tarefa com prazo',
            'position' => 1,
            'due_date' => '2099-12-31',
        ]);

        $response = $this->actingAs($user)
            ->from(route('boards.show', $list->board_id))
            ->put(route('lists.update', $list), [
                'title' => $list->title,
                'start_date' => '2099-12-01',
                'end_date' => '2099-12-30',
            ]);

        $response->assertSessionHasErrors([
            'end_date' => 'O prazo da lista não pode ser anterior ao início ou ao prazo de uma tarefa.',
        ]);
        $this->assertDatabaseHas('card_lists', [
            'id' => $list->id,
            'end_date' => '2099-12-31 00:00:00',
        ]);
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'due_date' => '2099-12-31 00:00:00',
        ]);
    }

    public function test_list_deadline_can_match_the_latest_card_deadline(): void
    {
        $user = User::factory()->create();
        $list = $this->createList($user);
        Card::create([
            'card_list_id' => $list->id,
            'title' => 'Tarefa com prazo',
            'position' => 1,
            'due_date' => '2099-12-31',
        ]);

        $response = $this->actingAs($user)->put(route('lists.update', $list), [
            'title' => $list->title,
            'start_date' => '2099-12-01',
            'end_date' => '2099-12-31',
        ]);

        $response->assertRedirect(route('boards.show', $list->board_id, absolute: false));
        $this->assertDatabaseHas('card_lists', [
            'id' => $list->id,
            'end_date' => '2099-12-31 00:00:00',
        ]);
    }

    private function createList(User $user): CardList
    {
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Quadro de teste',
        ]);

        return CardList::create([
            'board_id' => $board->id,
            'title' => 'Lista de teste',
            'position' => 1,
            'end_date' => '2099-12-31',
        ]);
    }
}
