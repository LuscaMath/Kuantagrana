<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Painel inicial</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Bem-vindo ao Vale das Moedas</h2>
            </div>

            <span class="pixel-badge">{{ $currentLevel?->name ?? 'Sem nivel' }}</span>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="pixel-card pixel-scene">
                    <span class="pixel-badge">Seu progresso</span>
                    <h3 class="mt-4 text-lg leading-relaxed sm:text-2xl">{{ $user->name }}, você tem {{ $user->points }} pontos</h3>
                    <p class="mt-4 max-w-2xl text-sm font-bold leading-6 sm:text-base sm:leading-7">
                        O foco aqui e simples: acompanhe seu nivel, veja como esta o mes e entre rapidamente no ambiente que voce quer organizar agora.
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

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">O que fazer agora</p>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('environments.index') }}" class="pixel-btn w-full">
                            Abrir mapa dos ambientes
                        </a>

                        <a href="{{ route('financial-transactions.create') }}" class="pixel-btn w-full" style="background-color: var(--vm-panel);">
                            Registrar transacao
                        </a>

                        <a href="{{ route('goals.create') }}" class="pixel-btn w-full">
                            Criar nova meta
                        </a>
                    </div>

                    <div class="mt-5 border-4 p-4" style="border-color: var(--vm-border); background-color: #fffdf2;">
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Resumo do mes</p>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm font-bold">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Receitas</p>
                                <p class="mt-1 text-lg font-extrabold">R$ {{ number_format($stats['income_month'], 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em]">Despesas</p>
                                <p class="mt-1 text-lg font-extrabold">R$ {{ number_format($stats['expense_month'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Transacoes</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $stats['transactions_count'] }}</p>
                    <p class="mt-2 text-sm font-bold">Registros acumulados no sistema.</p>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Metas</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $stats['active_goals'] }}</p>
                    <p class="mt-2 text-sm font-bold">
                        {{ $stats['completed_goals'] }} concluidas ate agora.
                    </p>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Itens em alerta</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $stats['low_stock_items'] }}</p>
                    <p class="mt-2 text-sm font-bold">Produtos e itens que ja pedem reposicao.</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="pixel-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Conquistas recentes</p>
                            <h3 class="mt-2 text-lg leading-relaxed">Seu destaque mais recente</h3>
                        </div>

                        <a href="{{ route('environments.show', 'parque-de-diversoes') }}" class="pixel-link">Ir ao parque</a>
                    </div>

                    @if ($recentAchievements->isNotEmpty())
                        @php($featuredAchievement = $recentAchievements->first())

                        <div class="mt-4 border-4 p-4" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            <p class="text-sm font-extrabold">{{ $featuredAchievement->achievement->name }}</p>
                            <p class="mt-2 text-sm leading-6">{{ $featuredAchievement->achievement->description }}</p>
                            <p class="mt-3 text-xs font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)]">
                                +{{ $featuredAchievement->achievement->points_reward }} pontos
                            </p>
                        </div>

                        @if ($recentAchievements->count() > 1)
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($recentAchievements->slice(1) as $userAchievement)
                                    <span class="border-2 px-2 py-1 text-xs font-extrabold uppercase tracking-[0.14em]" style="border-color: var(--vm-border); background-color: #fffdf2;">
                                        {{ $userAchievement->achievement->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="mt-4 border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            Ainda nao ha conquistas desbloqueadas. Continue registrando a sua rotina para comecar a evoluir.
                        </div>
                    @endif
                </div>

                <div class="pixel-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Desafio em foco</p>
                            <h3 class="mt-2 text-lg leading-relaxed">Proximo passo para ganhar pontos</h3>
                        </div>

                        <a href="{{ route('dashboard') }}" class="pixel-link">Atualizar</a>
                    </div>

                    @if ($challenges->isNotEmpty())
                        @php($focusedChallenge = $challenges->first())
                        @php($progress = $focusedChallenge->challenge->goal_target > 0 ? min(100, ($focusedChallenge->progress / $focusedChallenge->challenge->goal_target) * 100) : 0)

                        <div class="mt-4 border-4 p-4" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-extrabold">{{ $focusedChallenge->challenge->name }}</p>
                                    <p class="mt-1 text-sm leading-6">{{ $focusedChallenge->challenge->description }}</p>
                                </div>
                                <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)]">
                                    {{ $focusedChallenge->status === 'completed' ? 'Concluido' : 'Em andamento' }}
                                </span>
                            </div>

                            <div class="mt-3 h-4 border-4" style="border-color: var(--vm-border); background-color: #fff8dd;">
                                <div class="h-full" style="width: {{ $progress }}%; background-color: var(--vm-accent);"></div>
                            </div>

                            <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                {{ $focusedChallenge->progress }}/{{ $focusedChallenge->challenge->goal_target }} - +{{ $focusedChallenge->challenge->points_reward }} pontos
                            </p>
                        </div>

                        @if ($challenges->count() > 1)
                            <p class="mt-3 text-sm font-bold">
                                Mais {{ $challenges->count() - 1 }} desafio(s) em segundo plano.
                            </p>
                        @endif
                    @elseif ($availableChallenges->isNotEmpty())
                        @php($availableChallenge = $availableChallenges->first())

                        <div class="mt-4 border-4 p-4" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            <p class="text-sm font-extrabold">{{ $availableChallenge->name }}</p>
                            <p class="mt-1 text-sm leading-6">{{ $availableChallenge->description }}</p>
                            <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                Meta: {{ $availableChallenge->goal_target }} - +{{ $availableChallenge->points_reward }} pontos
                            </p>
                        </div>
                    @else
                        <div class="mt-4 border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
                            Nenhum desafio disponivel no momento.
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
