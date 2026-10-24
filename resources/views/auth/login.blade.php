<x-guest-layout>
    <main class="login-screen">
        <section class="login-panel">
            <a href="{{ route('login') }}" class="brand-lockup text-decoration-none">
                <span class="brand-mark"><i class="bi bi-kanban-fill"></i></span>
                <span>
                    <strong>Nexstep</strong>
                    <small>Organização simples, trabalho melhor</small>
                </span>
            </a>

            <div class="login-content">
                <div class="login-eyebrow"><span></span> A sua área de trabalho</div>
                <h1>Entrar na sua conta</h1>
                <p class="login-description">Aceda aos seus quadros e continue de onde ficou.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">Não foi possível iniciar sessão.</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Endereço de e-mail</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nome@exemplo.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label">Palavra-passe</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-link">Esqueceu-se?</a>
                            @endif
                        </div>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock"></i>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="Introduza a sua palavra-passe">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <label for="remember_me" class="remember-option">
                            <input id="remember_me" type="checkbox" name="remember" class="form-check-input" value="1">
                            <span>Manter sessão iniciada</span>
                        </label>
                    </div>

                    <button type="submit" class="btn login-submit w-100">
                        Entrar <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="login-register text-center">
                        Ainda não tem conta? <a href="{{ route('register') }}" class="login-link fw-semibold">Criar conta</a>
                    </p>
                @endif
            </div>

            <div class="login-footer">
                <span><i class="bi bi-shield-check me-1"></i> Acesso seguro à sua conta</span>
                <span>© {{ date('Y') }} Nexstep</span>
            </div>
        </section>

        <aside class="login-showcase">
            @if (file_exists(public_path('videos/login-tasks.mp4')))
                <video class="showcase-video" autoplay muted loop playsinline aria-hidden="true">
                    <source src="{{ asset('videos/login-tasks.mp4') }}" type="video/mp4">
                </video>
            @endif
            <div class="showcase-video-overlay"></div>
            <div class="showcase-orb showcase-orb-one"></div>
            <div class="showcase-orb showcase-orb-two"></div>
            <div class="showcase-content">
                <div class="showcase-badge"><i class="bi bi-stars me-2"></i> Planeie com clareza</div>
                <h2>Todo o seu trabalho.<br><span>Num só lugar.</span></h2>
                <p>Organize projetos, acompanhe tarefas e mantenha a equipa alinhada — sem perder o foco.</p>

                <div class="showcase-board">
                    <div class="showcase-board-heading">
                        <span><i class="bi bi-kanban me-2"></i> Projeto em andamento</span>
                        <i class="bi bi-three-dots"></i>
                    </div>
                    <div class="showcase-columns">
                        <div class="showcase-column">
                            <div class="showcase-column-title"><span class="status-dot status-blue"></span> A fazer <small>2</small></div>
                            <div class="showcase-task"><span class="task-line task-line-long"></span><span class="task-line"></span><span class="task-meta"><i class="bi bi-calendar3"></i> 24 Out</span></div>
                            <div class="showcase-task"><span class="task-line task-line-long"></span><span class="task-line task-line-short"></span></div>
                        </div>
                        <div class="showcase-column">
                            <div class="showcase-column-title"><span class="status-dot status-orange"></span> Em curso <small>1</small></div>
                            <div class="showcase-task"><span class="task-line task-line-long"></span><span class="task-line"></span><span class="task-avatar">JT</span></div>
                        </div>
                        <div class="showcase-column showcase-column-done">
                            <div class="showcase-column-title"><span class="status-dot status-green"></span> Concluído <small>3</small></div>
                            <div class="showcase-task"><span class="task-line task-line-long"></span><span class="task-line task-line-short"></span><span class="task-check"><i class="bi bi-check2"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="showcase-trust"><span class="trust-avatars"><i>AM</i><i>RS</i><i>JT</i></span><span>Um espaço mais claro para fazer acontecer.</span></div>
            </div>
            <div class="showcase-decoration"><i class="bi bi-grid-3x3-gap-fill"></i></div>
        </aside>
    </main>
</x-guest-layout>
