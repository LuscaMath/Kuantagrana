<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Painel inicial</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Resumo rapido do seu progresso</h2>
            </div>

            <span class="pixel-badge">{{ $currentLevel?->name ?? 'Sem nivel' }}</span>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="pixel-card pixel-scene">
                    <span class="pixel-badge">Visao geral</span>
                    <h3 class="mt-4 text-lg leading-relaxed sm:text-2xl">{{ $user->name }}, voce tem {{ $user->points }} pontos</h3>
                    <p class="mt-3 max-w-2xl text-sm font-bold leading-6 sm:text-base sm:leading-7">
                        Use o mapa para entrar no contexto certo e registrar o que importa agora.
                    </p>

                    <div class="mt-5">
                        <div class="flex items-center justify-between gap-3 text-sm font-bold">
                            <span>{{ $currentLevel?->name ?? 'Inicio' }}</span>
                            <span>{{ $nextLevel?->name ?? 'Nivel maximo' }}</span>
                        </div>

                        <div class="mt-2 h-5 border-4" style="border-color: var(--vm-border); background-color: #fff8dd;">
                            <div class="h-full" style="width: {{ $progressToNextLevel }}%; background-color: var(--vm-accent);"></div>
                        </div>

                        <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                            @if ($nextLevel)
                                {{ $progressToNextLevel }}% ate {{ $nextLevel->name }}
                            @else
                                Voce alcancou o nivel maximo atual.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="pixel-card-quiet">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Acesso principal</p>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('environments.index') }}" class="pixel-btn w-full">
                            Abrir mapa dos ambientes
                        </a>

                        <a href="{{ route('financial-transactions.create') }}" class="pixel-btn pixel-btn-secondary w-full">
                            Registrar transacao
                        </a>

                        <a href="{{ route('goals.create') }}" class="pixel-btn pixel-btn-secondary w-full">
                            Criar meta
                        </a>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Saldo do mes</p>
                    <p class="mt-2 text-2xl font-extrabold">
                        R$ {{ number_format($stats['income_month'] - $stats['expense_month'], 2, ',', '.') }}
                    </p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Metas ativas</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ $stats['active_goals'] }}</p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Itens em alerta</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ $stats['low_stock_items'] }}</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="pixel-card-quiet">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Conquista recente</p>
                            <h3 class="mt-2 text-lg leading-relaxed">Seu ultimo destaque</h3>
                        </div>

                        <a href="{{ route('environments.show', 'parque-de-diversoes') }}" class="pixel-link">Ver parque</a>
                    </div>

                    @if ($recentAchievements->isNotEmpty())
                        @php($featuredAchievement = $recentAchievements->first())

                        <div class="mt-4 border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            <p class="text-sm font-extrabold">{{ $featuredAchievement->achievement->name }}</p>
                            <p class="mt-2 text-sm leading-6">{{ $featuredAchievement->achievement->description }}</p>
                        </div>
                    @else
                        <div class="mt-4 border-4 p-4 text-sm font-bold" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            Ainda nao ha conquistas desbloqueadas.
                        </div>
                    @endif
                </div>

                <div class="pixel-card-quiet">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Desafio em foco</p>
                            <h3 class="mt-2 text-lg leading-relaxed">Proximo passo</h3>
                        </div>

                        <a href="{{ route('dashboard') }}" class="pixel-link">Atualizar</a>
                    </div>

                    @if ($challenges->isNotEmpty())
                        @php($focusedChallenge = $challenges->first())
                        @php($progress = $focusedChallenge->challenge->goal_target > 0 ? min(100, ($focusedChallenge->progress / $focusedChallenge->challenge->goal_target) * 100) : 0)

                        <div class="mt-4 border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            <p class="text-sm font-extrabold">{{ $focusedChallenge->challenge->name }}</p>

                            <div class="mt-3 h-4 border-4" style="border-color: var(--vm-border); background-color: #fff8dd;">
                                <div class="h-full" style="width: {{ $progress }}%; background-color: var(--vm-accent);"></div>
                            </div>

                            <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                {{ $focusedChallenge->progress }}/{{ $focusedChallenge->challenge->goal_target }} - +{{ $focusedChallenge->challenge->points_reward }} pontos
                            </p>
                        </div>
                    @elseif ($availableChallenges->isNotEmpty())
                        @php($availableChallenge = $availableChallenges->first())

                        <div class="mt-4 border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            <p class="text-sm font-extrabold">{{ $availableChallenge->name }}</p>
                            <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                Meta: {{ $availableChallenge->goal_target }} - +{{ $availableChallenge->points_reward }} pontos
                            </p>
                        </div>
                    @else
                        <div class="mt-4 border-4 p-4 text-sm font-bold" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            Nenhum desafio disponivel no momento.
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
