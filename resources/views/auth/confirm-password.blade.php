<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Confirmação</span>
        <p class="mt-4 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Esta é uma área protegida. Confirme sua senha para continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Senha" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Confirmar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
