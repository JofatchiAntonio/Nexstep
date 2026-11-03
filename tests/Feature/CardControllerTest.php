<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_can_have_the_same_deadline_as_its_list(): void
    {
        $user = User::factory()->create();
        $list = $this->createList($user, '2099-12-31');

        $response = $this->actingAs($user)->post(route('cards.store', $list), [
            'title' => 'Tarefa no prazo',
            'due_date' => '2099-12-31',
        ]);

        $response->assertRedirect(route('boards.show', $list->board_id, absolute: false));
        $this->assertDatabaseHas('cards', [
            'card_list_id' => $list->id,
            'title' => 'Tarefa no prazo',
            'due_date' => '2099-12-31 00:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('boards.show', $list->board_id))
            ->assertOk()
            ->assertSee('max="2099-12-31"', false)
            ->assertSee('min="2099-12-31"', false);
    }

    public function test_card_creation_is_rejected_when_its_deadline_exceeds_the_list_deadline(): void
    {
        $user = User::factory()->create();
        $list = $this->createList($user, '2099-12-31');

        $response = $this->actingAs($user)
            ->from(route('boards.show', $list->board_id))
            ->post(route('cards.store', $list), [
                'title' => 'Tarefa fora do prazo',
                'due_date' => '2100-01-01',
            ]);

        $response->assertSessionHasErrors([
            'due_date' => 'O prazo do item não pode ultrapassar o prazo da lista.',
        ]);
        $this->assertDatabaseMissing('cards', [
            'card_list_id' => $list->id,
            'title' => 'Tarefa fora do prazo',
        ]);
    }

    public function test_card_update_is_rejected_when_its_deadline_exceeds_the_list_deadline(): void
    {
        $user = User::factory()->create();
        $list = $this->createList($user, '2099-12-31');
        $card = Card::create([
            'card_list_id' => $list->id,
            'title' => 'Tarefa existente',
            'position' => 1,
            'due_date' => '2099-12-30',
        ]);

        $response = $this->actingAs($user)
            ->from(route('boards.show', $list->board_id))
            ->put(route('cards.update', $card), [
                'title' => $card->title,
                'due_date' => '2100-01-01',
            ]);

        $response->assertSessionHasErrors([
            'due_date' => 'O prazo do item não pode ultrapassar o prazo da lista.',
        ]);
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'due_date' => '2099-12-30 00:00:00',
        ]);
    }

    private function createList(User $user, string $endDate): CardList
    {
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Quadro de teste',
        ]);

        return CardList::create([
            'board_id' => $board->id,
            'title' => 'Lista de teste',
            'position' => 1,
            'end_date' => $endDate,
        ]);
    }
}
