<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Cadastro</span>
        <h2 class="mt-4 text-xl leading-relaxed sm:text-2xl">Comece sua vila financeira</h2>
        <p class="mt-3 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Crie sua conta para registrar receitas, despesas, metas e evoluir no mapa do Vale das Moedas.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar senha" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a class="pixel-link" href="{{ route('login') }}">
                Já tem conta?
            </a>

            <x-primary-button class="w-full sm:w-auto">
                Criar conta
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
