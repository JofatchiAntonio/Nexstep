<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_a_board(): void
    {
        $user = User::factory()->create();
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Planeamento',
            'color' => '#026aa7',
        ]);

        $response = $this->actingAs($user)->put(route('boards.update', $board), [
            'title' => 'Projeto novo',
            'color' => '#123456',
        ]);

        $response->assertRedirect(route('boards.index'));
        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'title' => 'Projeto novo',
            'color' => '#123456',
        ]);
    }

    public function test_another_user_cannot_update_a_board(): void
    {
        $board = Board::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Privado',
            'color' => '#026aa7',
        ]);

        $response = $this->actingAs(User::factory()->create())->put(route('boards.update', $board), [
            'title' => 'Alterado',
            'color' => '#123456',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('boards', ['id' => $board->id, 'title' => 'Privado']);
    }

    public function test_owner_can_update_a_list(): void
    {
        $user = User::factory()->create();
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Projeto',
            'color' => '#026aa7',
        ]);
        $list = CardList::create([
            'board_id' => $board->id,
            'title' => 'A fazer',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->put(route('lists.update', $list), ['title' => 'Em progresso']);

        $response->assertRedirect(route('boards.show', $board));
        $this->assertDatabaseHas('card_lists', ['id' => $list->id, 'title' => 'Em progresso']);
    }

    public function test_deleting_and_restoring_a_list_also_deletes_and_restores_its_cards(): void
    {
        $user = User::factory()->create();
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Projeto',
            'color' => '#026aa7',
        ]);
        $list = CardList::create([
            'board_id' => $board->id,
            'title' => 'A fazer',
            'position' => 1,
        ]);
        $card = Card::create([
            'card_list_id' => $list->id,
            'title' => 'Preparar entrega',
            'position' => 1,
        ]);

        $this->actingAs($user)
            ->delete(route('lists.destroy', $list))
            ->assertRedirect(route('boards.show', $board));

        $this->assertSoftDeleted('card_lists', ['id' => $list->id]);
        $this->assertSoftDeleted('cards', ['id' => $card->id]);

        $this->post(route('lists.restore', $list->id))
            ->assertRedirect(route('boards.trash'));

        $this->assertDatabaseHas('card_lists', ['id' => $list->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('cards', ['id' => $card->id, 'deleted_at' => null]);
    }

    public function test_restoring_a_board_restores_its_lists_and_cards(): void
    {
        $user = User::factory()->create();
        $board = Board::create([
            'user_id' => $user->id,
            'title' => 'Projeto',
            'color' => '#026aa7',
        ]);
        $list = CardList::create([
            'board_id' => $board->id,
            'title' => 'A fazer',
            'position' => 1,
        ]);
        $card = Card::create([
            'card_list_id' => $list->id,
            'title' => 'Preparar entrega',
            'position' => 1,
        ]);

        $this->actingAs($user)
            ->delete(route('boards.destroy', $board))
            ->assertRedirect(route('boards.index'));

        $this->assertSoftDeleted('boards', ['id' => $board->id]);
        $this->assertSoftDeleted('card_lists', ['id' => $list->id]);
        $this->assertSoftDeleted('cards', ['id' => $card->id]);

        $this->post(route('lists.restore', $list->id))->assertNotFound();
        $this->post(route('boards.restore', $board->id))->assertRedirect(route('boards.trash'));

        $this->assertDatabaseHas('boards', ['id' => $board->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('card_lists', ['id' => $list->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('cards', ['id' => $card->id, 'deleted_at' => null]);
    }
}
