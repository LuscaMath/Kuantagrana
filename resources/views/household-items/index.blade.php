<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Organização da casa</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Itens domésticos</h2>
            </div>

            <a href="{{ route('household-items.create') }}" class="pixel-btn w-full sm:w-auto">
                Novo item
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session('status'))
                <div class="pixel-card text-sm font-bold text-[color:var(--vm-leaf)]">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-3">
                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Itens ativos</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $items->where('is_active', true)->count() }}</p>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Abaixo do mínimo</p>
                    <p class="mt-3 text-2xl font-extrabold">
                        {{ $items->filter(fn ($item) => $item->quantity <= $item->minimum_quantity)->count() }}
                    </p>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Com validade</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $items->whereNotNull('expires_at')->count() }}</p>
                </div>
            </section>

            <section class="pixel-card">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Controle doméstico</p>
                    <h3 class="mt-2 text-lg">Lista de itens cadastrados</h3>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($items as $item)
                        @php
                            $isLowStock = $item->quantity <= $item->minimum_quantity;
                        @endphp

                        <article class="border-4 p-4" style="border-color: var(--vm-border); background-color: {{ $isLowStock ? '#ffe1d8' : '#fffdf2' }};">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                        {{ $item->environment?->name ?? 'Sem ambiente' }}
                                    </p>
                                    <h4 class="mt-2 text-lg font-extrabold">{{ $item->name }}</h4>
                                    <p class="mt-2 text-sm font-bold">
                                        {{ $item->quantity }} {{ $item->unit }} disponíveis
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-[color:var(--vm-wood)]">
                                        Mínimo recomendado: {{ $item->minimum_quantity }} {{ $item->unit }}
                                    </p>
                                    @if ($item->notes)
                                        <p class="mt-2 text-sm leading-6">{{ $item->notes }}</p>
                                    @endif
                                </div>

                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-bold text-[color:var(--vm-wood)]">Status</p>
                                    <p class="mt-1 text-lg font-extrabold">
                                        {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                                    </p>
                                    @if ($item->expires_at)
                                        <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                            Validade: {{ $item->expires_at->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex flex-wrap gap-2 text-xs font-extrabold uppercase tracking-[0.14em]">
                                    @if ($isLowStock)
                                        <span class="border-2 px-2 py-1" style="border-color: var(--vm-border);">
                                            Repor estoque
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('household-items.edit', $item) }}" class="pixel-btn w-full sm:w-auto">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('household-items.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="pixel-btn w-full sm:w-auto" style="background-color: #f7c0b8;">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            Você ainda não cadastrou itens domésticos.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $items->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
