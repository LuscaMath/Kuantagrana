<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">
                    {{ $selectedEnvironment?->name ?? 'Itens por ambiente' }}
                </p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">
                    {{ $selectedEnvironment ? 'Itens de ' . $selectedEnvironment->name : 'Escolha um ambiente para gerenciar itens' }}
                </h2>
            </div>

            @if ($selectedEnvironment)
                <a href="{{ route('household-items.create', ['environment_id' => $selectedEnvironment->id]) }}" class="pixel-btn w-full sm:w-auto">
                    Novo item
                </a>
            @else
                <a href="{{ route('environments.index') }}" class="pixel-btn pixel-btn-secondary w-full sm:w-auto">
                    Abrir mapa
                </a>
            @endif
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session('status'))
                <div class="pixel-card-quiet text-sm font-bold text-[color:var(--vm-leaf)]">
                    {{ session('status') }}
                </div>
            @endif

            @if (! $selectedEnvironment)
                <section class="pixel-card-soft p-5 sm:p-6">
                    <h3 class="text-lg leading-relaxed sm:text-2xl">Os itens fazem parte de um contexto</h3>
                    <p class="mt-3 max-w-3xl text-sm font-bold leading-6 sm:text-base sm:leading-7">
                        Escolha Casa, Mercado ou Farmacia para gerenciar os itens dentro do ambiente certo.
                    </p>
                </section>

                <section class="grid gap-4 md:grid-cols-3">
                    @foreach ($environments as $environment)
                        <article class="pixel-card-quiet">
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">{{ $environment->name }}</p>
                            <h3 class="mt-2 text-lg">{{ $environment->getHighlights()['title'] }}</h3>
                            <p class="mt-3 text-sm font-bold leading-6">{{ $environment->getHighlights()['description'] }}</p>

                            <div class="mt-5 space-y-3">
                                <a href="{{ route('household-items.index', ['environment_id' => $environment->id]) }}" class="pixel-btn w-full">
                                    Ver itens de {{ $environment->name }}
                                </a>
                                <a href="{{ route('household-items.create', ['environment_id' => $environment->id]) }}" class="pixel-btn pixel-btn-secondary w-full">
                                    Adicionar item
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>
            @else
                <section class="grid gap-4 sm:grid-cols-3">
                    <div class="pixel-stat">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Itens ativos</p>
                        <p class="mt-2 text-2xl font-extrabold">{{ $items->where('is_active', true)->count() }}</p>
                    </div>

                    <div class="pixel-stat">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Abaixo do minimo</p>
                        <p class="mt-2 text-2xl font-extrabold">
                            {{ $items->filter(fn ($item) => $item->quantity <= $item->minimum_quantity)->count() }}
                        </p>
                    </div>

                    <div class="pixel-stat">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Com validade</p>
                        <p class="mt-2 text-2xl font-extrabold">{{ $items->whereNotNull('expires_at')->count() }}</p>
                    </div>
                </section>

                <section class="pixel-card">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Controle do ambiente</p>
                            <h3 class="mt-2 text-lg">Itens cadastrados em {{ $selectedEnvironment->name }}</h3>
                        </div>

                        <a href="{{ route('environments.show', $selectedEnvironment->slug) }}" class="pixel-btn pixel-btn-secondary w-full sm:w-auto">
                            Voltar ao ambiente
                        </a>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($items as $item)
                            @php
                                $isLowStock = $item->quantity <= $item->minimum_quantity;
                            @endphp

                            <article class="border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: {{ $isLowStock ? '#ffe1d8' : '#fffdf2' }};">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                            {{ $item->environment?->name ?? 'Sem ambiente' }}
                                        </p>
                                        <h4 class="mt-1 text-lg font-extrabold">{{ $item->name }}</h4>
                                        <p class="mt-1 text-sm font-bold">
                                            {{ $item->quantity }} {{ $item->unit }} disponiveis
                                        </p>
                                    </div>

                                    <div class="text-left sm:text-right">
                                        <p class="text-sm font-bold text-[color:var(--vm-wood)]">Status</p>
                                        <p class="mt-1 text-lg font-extrabold">
                                            {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                                        </p>
                                        @if ($item->expires_at)
                                            <p class="mt-1 text-sm font-bold text-[color:var(--vm-wood)]">
                                                {{ $item->expires_at->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex flex-wrap gap-2 text-xs font-extrabold uppercase tracking-[0.14em]">
                                        <span class="border-2 px-2 py-1" style="border-color: rgba(61, 43, 31, 0.7);">
                                            Minimo: {{ $item->minimum_quantity }} {{ $item->unit }}
                                        </span>

                                        @if ($isLowStock)
                                            <span class="border-2 px-2 py-1" style="border-color: rgba(61, 43, 31, 0.7);">
                                                Repor estoque
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <a href="{{ route('household-items.edit', $item) }}" class="pixel-btn w-full sm:w-auto pixel-btn-secondary">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('household-items.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="pixel-btn pixel-btn-danger w-full sm:w-auto">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="border-4 p-4 text-sm font-bold" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                                Voce ainda nao cadastrou itens em {{ $selectedEnvironment->name }}.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
