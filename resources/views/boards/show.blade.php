@extends('layouts.app-sidebar')

@section('page-title', $board->title)
@section('header', $board->title)

@section('content')
    <div class="board-page">
        <div class="board-page-heading">
            <div>
                <a href="{{ route('boards.index') }}" class="board-back-link">
                    <i class="bi bi-arrow-left"></i> Voltar aos quadros
                </a>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <span class="board-heading-mark" style="background-color: {{ $board->color }}">
                        <i class="bi bi-kanban"></i>
                    </span>
                    <div>
                        <div class="section-kicker">QUADRO DE PROJETO</div>
                        <h1>{{ $board->title }}</h1>
                    </div>
                </div>
                <p class="board-page-description">Organize as tarefas e acompanhe o progresso do seu projeto.</p>
            </div>
            <div class="board-page-stat">
                <span>{{ $board->lists->count() }}</span>
                <small>{{ $board->lists->count() === 1 ? 'lista' : 'listas' }}</small>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show workspace-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger workspace-alert" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="board-lists" aria-label="Listas do quadro">
            @forelse ($board->lists as $list)
                <section class="task-list">
                    <header class="task-list-header">
                        <div>
                            <span class="task-list-indicator"></span>
                            <h2>{{ $list->title }}</h2>
                            <span class="task-list-count">{{ $list->cards->count() }}</span>
                            <div class="task-list-actions">
                                <button type="button" class="task-list-edit-button" data-bs-toggle="modal" data-bs-target="#editListModal-{{ $list->id }}" aria-label="Editar lista {{ $list->title }}" title="Editar lista">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="task-delete-button task-list-delete" data-bs-toggle="modal" data-bs-target="#deleteListModal-{{ $list->id }}" aria-label="Apagar lista {{ $list->title }}" title="Apagar lista">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </header>

                    @if ($list->start_date || $list->end_date)
                        <div class="task-list-dates">
                            @if ($list->start_date)
                                <span><i class="bi bi-play-circle"></i> Começa {{ $list->start_date->format('d/m/Y') }}</span>
                            @endif
                            @if ($list->end_date)
                                <span><i class="bi bi-flag"></i> Termina {{ $list->end_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="task-list-cards">
                        @forelse ($list->cards as $card)
                            <article class="task-card">
                                <div class="task-card-heading">
                                    <form action="{{ route('cards.completion', $card) }}" method="POST" class="task-completion-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" class="form-check-input" id="card-completed-{{ $card->id }}" aria-label="Marcar {{ $card->title }} como feita" @checked($card->completed_at) onchange="this.form.submit()">
                                    </form>
                                    <div class="task-card-title {{ $card->completed_at ? 'is-completed' : '' }}">{{ $card->title }}</div>
                                    <span class="task-status-badge task-status-{{ $card->status($list->end_date) }}">{{ ucfirst($card->status($list->end_date)) }}</span>
                                </div>
                                @php($taskDeadline = $card->due_date ?? $list->end_date)
                                @if ($taskDeadline)
                                    <div class="task-card-date">
                                        <i class="bi bi-calendar3"></i>
                                        @if ($card->due_date)
                                            Prazo: {{ $card->due_date->format('d/m/Y') }}
                                        @else
                                            Prazo da lista: {{ $list->end_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                @endif
                                @if ($card->histories->isNotEmpty())
                                    <details class="task-card-history">
                                        <summary><i class="bi bi-clock-history"></i> Histórico ({{ $card->histories->count() }})</summary>
                                        <ul>
                                            @foreach ($card->histories as $history)
                                                <li>
                                                    <span>{{ $history->event === 'completed' ? 'Concluída' : 'Reaberta' }}</span>
                                                    <time datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                                                    @if ($history->event === 'completed' && $history->deadline_state)
                                                        <small>{{ $history->deadline_state === 'on_time' ? 'No prazo' : 'Concluída com atraso' }}</small>
                                                    @endif
                                                    @if ($history->user)
                                                        <small>por {{ $history->user->name }}</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @endif
                                <div class="task-card-actions">
                                    <button type="button" class="task-delete-button" data-bs-toggle="modal" data-bs-target="#deleteCardModal-{{ $card->id }}" aria-label="Apagar cartão {{ $card->title }}" title="Apagar cartão">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </article>
                            <div class="modal fade" id="deleteCardModal-{{ $card->id }}" tabindex="-1" aria-labelledby="deleteCardModalLabel-{{ $card->id }}" aria-describedby="deleteCardModalDescription-{{ $card->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content board-modal">
                                        <form action="{{ route('cards.destroy', $card) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <div>
                                                    <div class="section-kicker">CONFIRMAR EXCLUSÃO</div>
                                                    <h2 class="modal-title" id="deleteCardModalLabel-{{ $card->id }}">Apagar tarefa?</h2>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="list-delete-warning">
                                                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                                    <p id="deleteCardModalDescription-{{ $card->id }}">A tarefa <strong>{{ $card->title }}</strong> será movida para a lixeira. Poderá restaurá-la mais tarde.</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Manter tarefa</button>
                                                <button type="submit" class="btn btn-modal-delete"><i class="bi bi-trash3 me-1"></i>Apagar tarefa</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="task-list-empty">Ainda não existem tarefas nesta lista.</p>
                        @endforelse
                    </div>

                    <form action="{{ route('cards.store', $list) }}" method="POST" class="add-card-form">
                        @csrf
                        <label class="visually-hidden" for="card-title-{{ $list->id }}">Título da tarefa</label>
                        <input id="card-title-{{ $list->id }}" type="text" name="title" class="form-control" placeholder="Nova tarefa..." required maxlength="255">
                        <label class="visually-hidden" for="card-due-date-{{ $list->id }}">Prazo da tarefa</label>
                        <input id="card-due-date-{{ $list->id }}" type="date" name="due_date" class="form-control task-due-date-input" aria-label="Prazo da tarefa" min="{{ now()->toDateString() }}" max="{{ $list->end_date?->format('Y-m-d') }}">
                        <button type="submit" class="btn-add-card" aria-label="Adicionar tarefa à lista {{ $list->title }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </form>
                </section>
                <div class="modal fade" id="editListModal-{{ $list->id }}" tabindex="-1" aria-labelledby="editListModalLabel-{{ $list->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            @php($latestCardDueDate = $list->cards->filter(fn ($card) => $card->due_date !== null)->max(fn ($card) => $card->due_date->toDateString()))
                            <form action="{{ route('lists.update', $list) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">ORGANIZAÇÃO DO QUADRO</div>
                                        <h2 class="modal-title" id="editListModalLabel-{{ $list->id }}">Editar lista</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="list-title-{{ $list->id }}" class="form-label">Nome da lista</label>
                                        <input type="text" class="form-control" id="list-title-{{ $list->id }}" name="title" required maxlength="255" value="{{ $list->title }}">
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label for="list-start-date-{{ $list->id }}" class="form-label">Data de começo</label>
                                            <input type="date" class="form-control" id="list-start-date-{{ $list->id }}" name="start_date" value="{{ $list->start_date?->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="list-end-date-{{ $list->id }}" class="form-label">Data de término</label>
                                            <input type="date" class="form-control" id="list-end-date-{{ $list->id }}" name="end_date" value="{{ $list->end_date?->format('Y-m-d') }}" min="{{ $latestCardDueDate }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-create-board"><i class="bi bi-check2 me-1"></i>Guardar alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deleteListModal-{{ $list->id }}" tabindex="-1" aria-labelledby="deleteListModalLabel-{{ $list->id }}" aria-describedby="deleteListModalDescription-{{ $list->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            <form action="{{ route('lists.destroy', $list) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">CONFIRMAR EXCLUSÃO</div>
                                        <h2 class="modal-title" id="deleteListModalLabel-{{ $list->id }}">Apagar lista?</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-delete-warning">
                                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                        <p id="deleteListModalDescription-{{ $list->id }}">A lista <strong>{{ $list->title }}</strong> e as respetivas tarefas serão movidas para a lixeira. Poderá restaurá-las mais tarde.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Manter lista</button>
                                    <button type="submit" class="btn btn-modal-delete"><i class="bi bi-trash3 me-1"></i>Apagar lista</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="board-empty-list">
                    <div class="empty-state-icon"><i class="bi bi-layout-three-columns"></i></div>
                    <h2>Comece por criar uma lista</h2>
                    <p>As listas ajudam a organizar tarefas por etapa, prioridade ou tema.</p>
                </div>
            @endforelse

            <section class="new-list-panel">
                <div class="new-list-heading">
                    <span class="new-list-icon"><i class="bi bi-plus-lg"></i></span>
                    <div>
                        <h2>Nova lista</h2>
                        <p>Adicione uma etapa ao seu quadro.</p>
                    </div>
                </div>
                <form action="{{ route('lists.store', $board) }}" method="POST">
                    @csrf
                    <label class="form-label" for="list-title">Nome da lista</label>
                    <input type="text" id="list-title" name="title" class="form-control" placeholder="Ex.: A fazer" required maxlength="255">
                    <div class="row g-3 mt-1">
                        <div class="col-sm-6">
                            <label class="form-label" for="list-start-date">Data de começo</label>
                            <input type="date" id="list-start-date" name="start_date" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="list-end-date">Data de término</label>
                            <input type="date" id="list-end-date" name="end_date" class="form-control" min="{{ now()->toDateString() }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-create-board w-100 mt-3">
                        Criar lista <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
