<form wire:submit="updateProfileInformation" class="profile-form">
    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div class="profile-photo-field" x-data="{ photoPreview: null }">
            <div class="profile-photo-preview">
                <img x-show="! photoPreview" src="{{ $this->user->profile_photo_url }}" alt="Fotografia de {{ $this->user->name }}" class="profile-photo-image">
                <img x-show="photoPreview" x-bind:src="photoPreview" alt="Pré-visualização da nova fotografia" class="profile-photo-image" style="display: none;">
            </div>
            <div class="profile-photo-actions">
                <input
                    id="photo"
                    type="file"
                    class="visually-hidden"
                    accept="image/jpeg,image/png"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                        const file = $refs.photo.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (event) => photoPreview = event.target.result;
                            reader.readAsDataURL(file);
                        }
                    "
                >
                <button type="button" class="btn btn-profile-secondary" x-on:click.prevent="$refs.photo.click()">
                    <i class="bi bi-camera me-1"></i>Escolher fotografia
                </button>
                @if ($this->user->profile_photo_path)
                    <button type="button" class="btn btn-profile-secondary" wire:click="deleteProfilePhoto" wire:loading.attr="disabled">
                        Remover fotografia
                    </button>
                @endif
                <small class="profile-photo-hint">JPG ou PNG, até 1 MB.</small>
                @error('photo', 'updateProfileInformation')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div wire:loading wire:target="photo" class="profile-photo-hint">A carregar fotografia...</div>
            </div>
        </div>
    @endif

    <div class="profile-form-fields">
        <div>
            <label for="name" class="form-label">Nome</label>
            <input id="name" type="text" class="form-control" wire:model="state.name" required autocomplete="name">
            @error('name', 'updateProfileInformation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="email" class="form-label">Endereço de e-mail</label>
            <input id="email" type="email" class="form-control" wire:model="state.email" required autocomplete="username">
            @error('email', 'updateProfileInformation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="profile-verification-notice">
                    O seu endereço de e-mail ainda não foi verificado.
                    <button type="button" wire:click.prevent="sendEmailVerification">Reenviar e-mail de verificação</button>
                </p>
                @if ($this->verificationLinkSent)
                    <p class="text-success small">Foi enviado um novo link de verificação.</p>
                @endif
            @endif
        </div>
    </div>

    <div class="profile-form-actions">
        <span wire:dirty.remove wire:target="state.name,state.email" class="profile-saved-message" role="status">
            <i class="bi bi-check-circle me-1"></i>Alterações guardadas
        </span>
        <button type="submit" class="btn btn-profile-save" wire:loading.attr="disabled" wire:target="updateProfileInformation,photo">
            <span wire:loading.remove wire:target="updateProfileInformation">Guardar alterações</span>
            <span wire:loading wire:target="updateProfileInformation">A guardar...</span>
        </button>
    </div>
</form>
