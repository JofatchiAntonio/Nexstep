@extends('layouts.app-sidebar')

@section('page-title', 'O meu perfil')
@section('header', 'O meu perfil')

@section('content')
    <div class="profile-page">
        <div class="profile-page-heading">
            <div>
                <div class="section-kicker">DEFINIÇÕES DA CONTA</div>
                <h1>O meu perfil</h1>
                <p>Atualize os seus dados, fotografia e opções de segurança.</p>
            </div>
        </div>

        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            <section class="profile-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon"><i class="bi bi-person-vcard"></i></span>
                    <div>
                        <h2>Informações pessoais</h2>
                        <p>Nome, endereço de e-mail e fotografia de perfil.</p>
                    </div>
                </div>
                @livewire('profile.update-profile-information-form')
            </section>
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <section class="profile-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon"><i class="bi bi-shield-lock"></i></span>
                    <div>
                        <h2>Palavra-passe</h2>
                        <p>Escolha uma palavra-passe forte para proteger a sua conta.</p>
                    </div>
                </div>
                @livewire('profile.update-password-form')
            </section>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <section class="profile-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon"><i class="bi bi-shield-check"></i></span>
                    <div>
                        <h2>Autenticação de dois fatores</h2>
                        <p>Adicione uma camada extra de segurança à sua conta.</p>
                    </div>
                </div>
                @livewire('profile.two-factor-authentication-form')
            </section>
        @endif

        <section class="profile-section">
            <div class="profile-section-heading">
                <span class="profile-section-icon"><i class="bi bi-laptop"></i></span>
                <div>
                    <h2>Sessões ativas</h2>
                    <p>Reveja e termine sessões iniciadas noutros dispositivos.</p>
                </div>
            </div>
            @livewire('profile.logout-other-browser-sessions-form')
        </section>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <section class="profile-section profile-danger-section">
                <div class="profile-section-heading">
                    <span class="profile-section-icon"><i class="bi bi-person-x"></i></span>
                    <div>
                        <h2>Eliminar conta</h2>
                        <p>Eliminar a conta e os dados associados de forma permanente.</p>
                    </div>
                </div>
                @livewire('profile.delete-user-form')
            </section>
        @endif
    </div>
@endsection
