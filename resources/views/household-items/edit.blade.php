<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">{{ $selectedEnvironment?->name ?? 'Itens por ambiente' }}</p>
            <h2 class="text-lg leading-relaxed sm:text-2xl">Editar item de {{ $selectedEnvironment?->name ?? 'apoio' }}</h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <section class="pixel-card">
                <form method="POST" action="{{ route('household-items.update', $item) }}">
                    @csrf
                    @method('PUT')

                    @include('household-items._form')

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <a href="{{ route('household-items.index', ['environment_id' => $selectedEnvironment?->id ?? $item->environment_id]) }}" class="pixel-btn pixel-btn-secondary w-full sm:w-auto">
                            Voltar
                        </a>

                        <x-primary-button class="w-full sm:w-auto">
                            Atualizar item
                        </x-primary-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
