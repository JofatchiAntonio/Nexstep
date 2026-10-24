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
                            <details class="task-list-menu">
                                <summary aria-label="Editar lista {{ $list->title }}" title="Editar lista">
                                    <i class="bi bi-pencil"></i>
                                </summary>
                                <form action="{{ route('lists.update', $list) }}" method="POST" class="task-list-rename-form">
                                    @csrf
                                    @method('PUT')
                                    <label class="visually-hidden" for="list-rename-{{ $list->id }}">Nome da lista</label>
                                    <input id="list-rename-{{ $list->id }}" type="text" name="title" value="{{ $list->title }}" class="form-control" required maxlength="255">
                                    <button type="submit" class="btn-add-card" aria-label="Guardar nome da lista"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </details>
                            <form action="{{ route('lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Apagar esta lista e mover as tarefas para a lixeira?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="task-delete-button task-list-delete" aria-label="Apagar lista {{ $list->title }}" title="Apagar lista">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </header>

                    <div class="task-list-cards">
                        @forelse ($list->cards as $card)
                            <article class="task-card">
                                <div class="task-card-title">{{ $card->title }}</div>
                                @if ($card->due_date)
                                    <div class="task-card-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ \Carbon\Carbon::parse($card->due_date)->format('d/m/Y') }}
                                    </div>
                                @endif
                                <form action="{{ route('cards.destroy', $card) }}" method="POST" class="task-card-actions" onsubmit="return confirm('Tem a certeza de que pretende apagar este cartão?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="task-delete-button" aria-label="Apagar cartão {{ $card->title }}" title="Apagar cartão">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </article>
                        @empty
                            <p class="task-list-empty">Ainda não existem tarefas nesta lista.</p>
                        @endforelse
                    </div>

                    <form action="{{ route('cards.store', $list) }}" method="POST" class="add-card-form">
                        @csrf
                        <label class="visually-hidden" for="card-title-{{ $list->id }}">Título da tarefa</label>
                        <input id="card-title-{{ $list->id }}" type="text" name="title" class="form-control" placeholder="Nova tarefa..." required maxlength="255">
                        <button type="submit" class="btn-add-card" aria-label="Adicionar tarefa à lista {{ $list->title }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </form>
                </section>
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
                    <button type="submit" class="btn btn-create-board w-100 mt-3">
                        Criar lista <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
