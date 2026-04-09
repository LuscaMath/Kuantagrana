<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Controle financeiro</p>
            <h2 class="text-lg leading-relaxed sm:text-2xl">Nova transação</h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <section class="pixel-card">
                <form method="POST" action="{{ route('financial-transactions.store') }}">
                    @csrf

                    @include('financial-transactions._form')

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <a href="{{ route('financial-transactions.index', ['environment_id' => old('environment_id', $transaction->environment_id)]) }}" class="pixel-btn w-full sm:w-auto" style="background-color: var(--vm-panel);">
                            Voltar
                        </a>

                        <x-primary-button class="w-full sm:w-auto">
                            Salvar transação
                        </x-primary-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
