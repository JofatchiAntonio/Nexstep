@extends('layouts.app-sidebar')

@section('page-title', 'Os meus quadros')
@section('header', 'Os meus quadros')

@section('content')
    <div class="boards-page">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show workspace-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <section class="boards-intro">
            <div>
                <div class="section-kicker">O SEU ESPAÇO DE TRABALHO</div>
                <h1>Os seus projetos, organizados.</h1>
                <p>Crie quadros para planear tarefas, acompanhar progresso e manter tudo no caminho certo.</p>
            </div>
            <button type="button" class="btn btn-create-board" data-bs-toggle="modal" data-bs-target="#createBoardModal">
                <i class="bi bi-plus-lg"></i>
                <span>Novo quadro</span>
            </button>
        </section>

        <div class="boards-section-heading">
            <div>
                <h2>Todos os quadros</h2>
                <span>{{ $boards->count() }} {{ $boards->count() === 1 ? 'quadro' : 'quadros' }} no seu espaço</span>
            </div>
        </div>

        <div class="row g-4">
            @forelse ($boards as $board)
                <div class="col-12 col-md-6 col-xl-4">
                    <article class="board-tile">
                        <div class="board-tile-accent" style="background-color: {{ $board->color }}"></div>
                        <div class="board-tile-content">
                            <div class="board-tile-icon" style="color: {{ $board->color }}; background-color: {{ $board->color }}18">
                                <i class="bi bi-kanban"></i>
                            </div>
                            <h3>{{ $board->title }}</h3>
                            <p>Um espaço para estruturar as suas tarefas e acompanhar cada etapa.</p>
                            <div class="board-tile-actions">
                                <a href="{{ route('boards.show', $board) }}" class="board-open-link">
                                    Abrir quadro <i class="bi bi-arrow-up-right"></i>
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="button" class="board-delete-button" data-bs-toggle="modal" data-bs-target="#editBoardModal-{{ $board->id }}" aria-label="Editar quadro {{ $board->title }}" title="Editar quadro">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="board-delete-button" data-bs-toggle="modal" data-bs-target="#deleteBoardModal-{{ $board->id }}" aria-label="Apagar quadro {{ $board->title }}" title="Apagar quadro">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="modal fade" id="editBoardModal-{{ $board->id }}" tabindex="-1" aria-labelledby="editBoardModalLabel-{{ $board->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            <form action="{{ route('boards.update', $board) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">DEFINIÇÕES DO PROJETO</div>
                                        <h2 class="modal-title" id="editBoardModalLabel-{{ $board->id }}">Editar quadro</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4">
                                        <label for="board-title-{{ $board->id }}" class="form-label">Nome do quadro</label>
                                        <input type="text" class="form-control" id="board-title-{{ $board->id }}" name="title" required maxlength="255" value="{{ $board->title }}">
                                    </div>
                                    <div>
                                        <label for="board-color-{{ $board->id }}" class="form-label">Cor de identificação</label>
                                        <input type="color" class="form-control form-control-color" id="board-color-{{ $board->id }}" name="color" value="{{ $board->color }}" required>
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
                <div class="modal fade" id="deleteBoardModal-{{ $board->id }}" tabindex="-1" aria-labelledby="deleteBoardModalLabel-{{ $board->id }}" aria-describedby="deleteBoardModalDescription-{{ $board->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            <form action="{{ route('boards.destroy', $board) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">CONFIRMAR EXCLUSÃO</div>
                                        <h2 class="modal-title" id="deleteBoardModalLabel-{{ $board->id }}">Apagar quadro?</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-delete-warning">
                                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                        <p id="deleteBoardModalDescription-{{ $board->id }}">O quadro <strong>{{ $board->title }}</strong>, as suas listas e tarefas serão movidos para a lixeira. Poderá restaurá-los mais tarde.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Manter quadro</button>
                                    <button type="submit" class="btn btn-modal-delete"><i class="bi bi-trash3 me-1"></i>Apagar quadro</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="boards-empty-state">
                        <div class="empty-state-icon"><i class="bi bi-kanban"></i></div>
                        <h3>O seu primeiro projeto começa aqui</h3>
                        <p>Crie um quadro e transforme as suas ideias em tarefas organizadas.</p>
                        <button type="button" class="btn btn-create-board" data-bs-toggle="modal" data-bs-target="#createBoardModal">
                            <i class="bi bi-plus-lg"></i> Criar o primeiro quadro
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="createBoardModal" tabindex="-1" aria-labelledby="createBoardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content board-modal">
                <form action="{{ route('boards.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div>
                            <div class="section-kicker">NOVO PROJETO</div>
                            <h2 class="modal-title" id="createBoardModalLabel">Criar quadro</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="title" class="form-label">Nome do quadro</label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="255" value="{{ old('title') }}" placeholder="Ex.: Projeto de equipa">
                            @error('title')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="color" class="form-label">Cor de identificação</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', '#4f46e5') }}" title="Escolha a cor do quadro">
                                <span class="text-secondary small">Escolha uma cor para identificar o seu projeto.</span>
                            </div>
                            @error('color')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-create-board"><i class="bi bi-plus-lg me-1"></i>Criar quadro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
