<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Recuperar acesso</span>
        <p class="mt-4 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Informe seu e-mail e enviaremos um link para redefinir sua senha.
        </p>
    </div>

    <x-auth-session-status class="mb-4 text-sm font-bold text-[color:var(--vm-leaf)]" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Enviar link
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
