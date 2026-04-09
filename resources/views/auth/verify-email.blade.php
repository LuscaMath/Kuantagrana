<x-guest-layout>
    <div class="mb-6">
        <span class="pixel-badge">Verificar e-mail</span>
        <p class="mt-4 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
            Antes de continuar, confirme seu e-mail pelo link enviado no cadastro. Se precisar, podemos reenviar.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-bold text-[color:var(--vm-leaf)]">
            Um novo link de verificação foi enviado para o seu e-mail.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Reenviar verificação
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="pixel-link">
                Sair
            </button>
        </form>
    </div>
</x-guest-layout>
