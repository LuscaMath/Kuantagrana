<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Nova senha</span>
        <p class="mt-4 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Defina uma nova senha para voltar ao seu mapa financeiro.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar senha" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Redefinir senha
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
