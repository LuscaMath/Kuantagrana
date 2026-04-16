<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Mapa principal</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Escolha por contexto</h2>
            </div>

            <span class="pixel-badge">Entrada principal</span>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="pixel-card-soft p-5 sm:p-6">
                <h3 class="text-lg leading-relaxed sm:text-2xl">Cada ambiente organiza uma parte da rotina</h3>
                <p class="mt-3 max-w-3xl text-sm font-bold leading-6 sm:text-base sm:leading-7">
                    Entre pelo lugar certo e encontre apenas o que faz sentido ali.
                </p>
            </section>

            <section class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($environments as $item)
                    <article class="pixel-card environment-card {{ $item['theme']['card_class'] }}">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="pixel-badge">{{ $item['environment']->name }}</span>
                            <span class="border-2 px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.14em]" style="border-color: var(--vm-border); background-color: rgba(255, 253, 242, 0.88);">
                                {{ $item['theme']['label'] }}
                            </span>
                        </div>

                        <div class="environment-scene">
                            <span class="environment-scene-label">{{ strtoupper($item['environment']->name) }}</span>
                            <p class="environment-scene-caption">{{ $item['highlights']['focus'][0] ?? 'Contexto financeiro' }}</p>
                        </div>

                        <h3 class="mt-4 text-lg leading-relaxed">{{ $item['highlights']['title'] }}</h3>
                        <p class="mt-2 text-sm font-bold leading-6">{{ $item['highlights']['description'] }}</p>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm font-bold">
                            <div class="environment-stat">
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Transacoes</p>
                                <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['transactions_count'] }}</p>
                            </div>

                            <div class="environment-stat">
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Metas</p>
                                <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['goals_count'] }}</p>
                            </div>
                        </div>

                        <a href="{{ route('environments.show', $item['environment']->slug) }}" class="pixel-btn mt-5 w-full">
                            Entrar em {{ $item['environment']->name }}
                        </a>
                    </article>
                @endforeach
            </section>
        </div>
    </div>
</x-app-layout>
