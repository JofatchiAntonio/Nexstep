@extends('layouts.app-sidebar')

@section('page-title', 'Lixeira')
@section('header', 'Lixeira')

@section('content')
    <div class="trash-page">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show workspace-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <div class="boards-intro">
            <div>
                <div class="section-kicker">ITENS ELIMINADOS</div>
                <h1>A sua lixeira</h1>
                <p>Restaure quadros e listas quando precisar deles novamente.</p>
            </div>
            <a href="{{ route('boards.index') }}" class="btn btn-modal-cancel text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i>Voltar aos quadros
            </a>
        </div>

        <section class="trash-section">
            <div class="boards-section-heading">
                <div>
                    <h2>Quadros</h2>
                    <span>{{ $boards->count() }} {{ $boards->count() === 1 ? 'quadro apagado' : 'quadros apagados' }}</span>
                </div>
            </div>

            @forelse ($boards as $board)
                <article class="trash-item">
                    <span class="trash-item-icon"><i class="bi bi-kanban"></i></span>
                    <div class="trash-item-details">
                        <strong>{{ $board->title }}</strong>
                        <span>Apagado em {{ $board->deleted_at->format('d/m/Y H:i') }} · inclui as listas e tarefas do quadro</span>
                    </div>
                    <form action="{{ route('boards.restore', $board->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-restore"><i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar quadro</button>
                    </form>
                    <button type="button" class="btn btn-permanent-delete" data-bs-toggle="modal" data-bs-target="#deleteBoardPermanentlyModal-{{ $board->id }}">
                        <i class="bi bi-trash3 me-1"></i>Apagar definitivamente
                    </button>
                </article>
                <div class="modal fade" id="deleteBoardPermanentlyModal-{{ $board->id }}" tabindex="-1" aria-labelledby="deleteBoardPermanentlyModalLabel-{{ $board->id }}" aria-describedby="deleteBoardPermanentlyModalDescription-{{ $board->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            <form action="{{ route('boards.permanently-delete', $board->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">AÇÃO IRREVERSÍVEL</div>
                                        <h2 class="modal-title" id="deleteBoardPermanentlyModalLabel-{{ $board->id }}">Apagar quadro definitivamente?</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-delete-warning">
                                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                        <p id="deleteBoardPermanentlyModalDescription-{{ $board->id }}">O quadro <strong>{{ $board->title }}</strong>, todas as suas listas e tarefas serão apagados permanentemente. Não será possível restaurá-los.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-modal-delete"><i class="bi bi-trash3 me-1"></i>Apagar definitivamente</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="trash-empty">Não existem quadros apagados.</p>
            @endforelse
        </section>

        <section class="trash-section">
            <div class="boards-section-heading">
                <div>
                    <h2>Listas</h2>
                    <span>{{ $lists->count() }} {{ $lists->count() === 1 ? 'lista apagada' : 'listas apagadas' }}</span>
                </div>
            </div>

            @forelse ($lists as $list)
                <article class="trash-item">
                    <span class="trash-item-icon"><i class="bi bi-list-task"></i></span>
                    <div class="trash-item-details">
                        <strong>{{ $list->title }}</strong>
                        <span>Quadro: {{ $list->board->title }} · Apagada em {{ $list->deleted_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <form action="{{ route('lists.restore', $list->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-restore"><i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar lista</button>
                    </form>
                    <button type="button" class="btn btn-permanent-delete" data-bs-toggle="modal" data-bs-target="#deleteListPermanentlyModal-{{ $list->id }}">
                        <i class="bi bi-trash3 me-1"></i>Apagar definitivamente
                    </button>
                </article>
                <div class="modal fade" id="deleteListPermanentlyModal-{{ $list->id }}" tabindex="-1" aria-labelledby="deleteListPermanentlyModalLabel-{{ $list->id }}" aria-describedby="deleteListPermanentlyModalDescription-{{ $list->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content board-modal">
                            <form action="{{ route('lists.permanently-delete', $list->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <div>
                                        <div class="section-kicker">AÇÃO IRREVERSÍVEL</div>
                                        <h2 class="modal-title" id="deleteListPermanentlyModalLabel-{{ $list->id }}">Apagar lista definitivamente?</h2>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-delete-warning">
                                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                                        <p id="deleteListPermanentlyModalDescription-{{ $list->id }}">A lista <strong>{{ $list->title }}</strong> e todas as suas tarefas serão apagadas permanentemente. Não será possível restaurá-las.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-modal-delete"><i class="bi bi-trash3 me-1"></i>Apagar definitivamente</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="trash-empty">Não existem listas apagadas.</p>
            @endforelse
        </section>
    </div>
@endsection
