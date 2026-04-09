<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Entrar</span>
        <h2 class="mt-4 text-xl leading-relaxed sm:text-2xl">Continue sua jornada</h2>
        <p class="mt-3 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Acesse sua conta para acompanhar saldo, metas e recompensas do Vale das Moedas.
        </p>
    </div>

    <x-auth-session-status class="mb-4 text-sm font-bold text-[color:var(--vm-leaf)]" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center gap-3 text-sm font-bold">
                <input id="remember_me" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="remember">
                <span>Lembrar de mim</span>
            </label>
        </div>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="pixel-link" href="{{ route('password.request') }}">
                    Esqueceu sua senha?
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto">
                Entrar
            </x-primary-button>
        </div>

        <p class="mt-6 text-sm font-bold">
            Novo por aqui?
            <a href="{{ route('register') }}" class="pixel-link">Criar conta</a>
        </p>
    </form>
</x-guest-layout>
