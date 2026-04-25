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

            @php
                $operationalEnvironments = $environments->filter(fn ($item) => in_array($item['environment']->slug, ['casa', 'mercado', 'farmacia'], true));
                $specialEnvironments = $environments->filter(fn ($item) => in_array($item['environment']->slug, ['escola', 'parque-de-diversoes'], true));
            @endphp

            <section class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach ($operationalEnvironments as $item)
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
                                @if ($item['environment']->supportsFeature('income_transactions'))
                                    <div class="environment-stat">
                                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Receitas</p>
                                        <p class="mt-2 text-lg font-extrabold">
                                            R$ {{ number_format($item['summary']['income_total'], 2, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="environment-stat">
                                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Despesas</p>
                                        <p class="mt-2 text-lg font-extrabold">
                                            R$ {{ number_format($item['summary']['expense_total'], 2, ',', '.') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="environment-stat">
                                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Transacoes</p>
                                        <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['transactions_count'] }}</p>
                                    </div>

                                    <div class="environment-stat">
                                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Despesas</p>
                                        <p class="mt-2 text-lg font-extrabold">
                                            R$ {{ number_format($item['summary']['expense_total'], 2, ',', '.') }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('environments.show', $item['environment']->slug) }}" class="pixel-btn mt-5 w-full">
                                Entrar em {{ $item['environment']->name }}
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="map-special-section pixel-card-soft p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Aprendizado e Progresso</p>
                            <h3 class="mt-2 text-lg leading-relaxed sm:text-2xl">Ambientes que apoiam a evolucao</h3>
                        </div>

                        <p class="max-w-2xl text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
                            Escola e Parque aparecem juntos porque funcionam como camada de orientacao, metas e recompensa da jornada.
                        </p>
                    </div>

                    <div class="mt-5 grid gap-6 lg:grid-cols-2">
                        @foreach ($specialEnvironments as $item)
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
                                    @if ($item['environment']->supportsFeature('goals'))
                                        <div class="environment-stat">
                                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Metas</p>
                                            <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['goals_count'] }}</p>
                                        </div>

                                        <div class="environment-stat">
                                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Concluidas</p>
                                            <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['goals_completed'] }}</p>
                                        </div>
                                    @else
                                        <div class="environment-stat">
                                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Dicas</p>
                                            <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['tips_count'] }}</p>
                                        </div>

                                        <div class="environment-stat">
                                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Desafios</p>
                                            <p class="mt-2 text-lg font-extrabold">{{ $item['summary']['challenges_count'] }}</p>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('environments.show', $item['environment']->slug) }}" class="pixel-btn mt-5 w-full">
                                    Entrar em {{ $item['environment']->name }}
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
