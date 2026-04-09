<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Ambiente</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">{{ $environment->name }}</h2>
            </div>

            <a href="{{ route('environments.index') }}" class="pixel-btn w-full sm:w-auto" style="background-color: var(--vm-panel);">
                Voltar ao mapa
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="pixel-card environment-card pixel-scene {{ $theme['card_class'] }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span class="pixel-badge">{{ $environment->name }}</span>
                        <span class="border-2 px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.14em]" style="border-color: var(--vm-border); background-color: rgba(255, 253, 242, 0.88);">
                            {{ $theme['label'] }}
                        </span>
                    </div>

                    <div class="environment-scene">
                        <span class="environment-scene-label">{{ strtoupper($environment->name) }}</span>
                        <p class="environment-scene-caption">{{ $highlights['focus'][0] ?? 'Ambiente' }}</p>
                    </div>

                    <h3 class="mt-4 text-lg leading-relaxed sm:text-2xl">{{ $highlights['title'] }}</h3>
                    <p class="mt-4 max-w-2xl text-sm font-bold leading-6 sm:text-base sm:leading-7">{{ $highlights['description'] }}</p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($highlights['focus'] as $focus)
                            <span class="border-2 px-2 py-1 text-xs font-extrabold uppercase tracking-[0.14em]" style="border-color: var(--vm-border); background-color: rgba(255, 253, 242, 0.88);">
                                {{ $focus }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                    @if ($highlights['kind'] === 'educational')
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Trilha da escola</p>
                        <h3 class="mt-3 text-lg leading-relaxed">Aprender antes de gastar</h3>
                        <p class="mt-3 text-sm font-bold leading-6">
                            A Escola e a area de educacao financeira do sistema. Aqui o foco principal e orientar, explicar conceitos e
                            incentivar planejamento com dicas praticas do dia a dia.
                        </p>

                        <div class="mt-4 space-y-3 text-sm font-bold">
                            <div class="environment-panel">
                                Monte sua base com dicas de organizacao, poupanca e uso consciente do dinheiro.
                            </div>
                            <div class="environment-panel">
                                Use metas e desafios como apoio, mas veja a Escola como o lugar para entender o porque de cada escolha.
                            </div>
                        </div>

                        <a href="#dicas" class="pixel-btn mt-4 w-full">
                            Ver dicas financeiras
                        </a>
                        <a href="{{ $actionLinks['goals'] }}" class="pixel-btn mt-3 w-full" style="background-color: var(--vm-panel);">
                            Ver planejamento e metas
                        </a>
                    @elseif ($highlights['kind'] === 'gamified')
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Area principal</p>
                        <h3 class="mt-3 text-lg leading-relaxed">Seu espaco de recompensa</h3>
                        <p class="mt-3 text-sm font-bold leading-6">
                            O Parque de Diversoes concentra metas, desafios e a sensacao de progresso. E o lugar para acompanhar sua
                            evolucao e transformar disciplina em conquista.
                        </p>

                        <a href="{{ $actionLinks['goals'] }}" class="pixel-btn mt-4 w-full">
                            Ver metas do ambiente
                        </a>
                        <a href="{{ $actionLinks['goals_create'] }}" class="pixel-btn mt-3 w-full" style="background-color: var(--vm-panel);">
                            Criar nova meta
                        </a>
                        <a href="{{ route('dashboard') }}" class="pixel-btn mt-3 w-full">
                            Ver painel de progresso
                        </a>
                    @else
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Acoes rapidas</p>
                        <h3 class="mt-3 text-lg leading-relaxed">Organize este ambiente</h3>
                        <p class="mt-3 text-sm font-bold leading-6">
                            Use este espaco para registrar o que faz parte da sua rotina neste contexto e acompanhar como isso afeta sua vida financeira.
                        </p>

                        <a href="{{ $actionLinks['transactions'] }}" class="pixel-btn mt-4 w-full">
                            Ver transacoes do ambiente
                        </a>
                        <a href="{{ $actionLinks['transactions_create'] }}" class="pixel-btn mt-3 w-full" style="background-color: var(--vm-panel);">
                            Nova transacao aqui
                        </a>
                        <a href="{{ $actionLinks['items'] }}" class="pixel-btn mt-3 w-full">
                            Ver itens do ambiente
                        </a>
                    @endif
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Receitas</p>
                    <p class="mt-3 text-2xl font-extrabold">R$ {{ number_format($summary['income_total'], 2, ',', '.') }}</p>
                </div>
                <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Despesas</p>
                    <p class="mt-3 text-2xl font-extrabold">R$ {{ number_format($summary['expense_total'], 2, ',', '.') }}</p>
                </div>
                <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Metas</p>
                    <p class="mt-3 text-2xl font-extrabold">{{ $summary['goals_count'] }}</p>
                </div>
            </section>

            @if ($highlights['kind'] === 'educational')
                <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div id="dicas" class="pixel-card environment-card {{ $theme['card_class'] }}">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Conteudo educativo</p>
                        <h3 class="mt-3 text-lg leading-relaxed">Dicas para tomar decisoes melhores</h3>

                        <div class="mt-4 space-y-3">
                            @forelse ($tips as $tip)
                                <div class="environment-panel p-4">
                                    <p class="text-sm font-extrabold">{{ $tip->title }}</p>
                                    <p class="mt-2 text-sm leading-6">{{ $tip->content }}</p>
                                </div>
                            @empty
                                <div class="environment-panel p-4 text-sm font-bold">
                                    Ainda nao ha dicas cadastradas para a Escola.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Ideias para estudar aqui</p>
                            <div class="mt-4 space-y-3 text-sm font-bold">
                                <div class="environment-panel">
                                    Entender a diferenca entre gasto essencial e gasto impulsivo.
                                </div>
                                <div class="environment-panel">
                                    Aprender a planejar metas antes de assumir novas despesas.
                                </div>
                                <div class="environment-panel">
                                    Revisar desafios e conquistas como forma de motivacao, nao como fim em si.
                                </div>
                            </div>
                        </div>

                        <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Desafios de aprendizado</p>
                            <div class="mt-4 space-y-3">
                                @forelse ($challenges as $challenge)
                                    <div class="environment-panel">
                                        <p class="text-sm font-extrabold">{{ $challenge->name }}</p>
                                        <p class="mt-2 text-sm leading-6">{{ $challenge->description }}</p>
                                    </div>
                                @empty
                                    <div class="environment-panel text-sm font-bold">
                                        Nenhum desafio educativo disponivel neste momento.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <section class="grid gap-6 {{ $tips->isNotEmpty() ? 'lg:grid-cols-3' : '' }}">
                    <div class="pixel-card environment-card {{ $theme['card_class'] }} {{ $tips->isNotEmpty() ? 'lg:col-span-2' : '' }}">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Movimentacoes recentes</p>
                        <div class="mt-4 space-y-3">
                            @forelse ($recentTransactions as $transaction)
                                <div class="environment-panel">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-extrabold">{{ $transaction->title }}</p>
                                            <p class="text-sm font-bold text-[color:var(--vm-wood)]">{{ $transaction->category->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-extrabold">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</p>
                                            <p class="text-xs font-bold">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="environment-panel text-sm font-bold">
                                    Nenhuma movimentacao registrada neste ambiente ainda.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($tips->isNotEmpty())
                        <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Dicas do ambiente</p>
                            <div class="mt-4 space-y-3">
                                @foreach ($tips as $tip)
                                    <div class="environment-panel">
                                        <p class="text-sm font-extrabold">{{ $tip->title }}</p>
                                        <p class="mt-2 text-sm leading-6">{{ $tip->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            @if ($recentGoals->isNotEmpty() || $challenges->isNotEmpty())
                <section class="grid gap-6 lg:grid-cols-2">
                    @if ($recentGoals->isNotEmpty())
                        <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Metas relacionadas</p>
                                <a href="{{ $actionLinks['goals'] }}" class="pixel-link">Abrir metas</a>
                            </div>

                            <div class="mt-4 space-y-3">
                                @foreach ($recentGoals as $goal)
                                    <div class="environment-panel">
                                        <p class="text-sm font-extrabold">{{ $goal->title }}</p>
                                        <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                            R$ {{ number_format($goal->current_amount, 2, ',', '.') }} de R$ {{ number_format($goal->target_amount, 2, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($challenges->isNotEmpty())
                        <div class="pixel-card environment-card {{ $theme['card_class'] }}">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                    {{ $highlights['kind'] === 'gamified' ? 'Desafios e conquistas' : 'Desafios do ambiente' }}
                                </p>
                                @if ($highlights['kind'] !== 'educational')
                                    <a href="{{ route('dashboard') }}" class="pixel-link">Ver progresso</a>
                                @endif
                            </div>

                            <div class="mt-4 space-y-3">
                                @foreach ($challenges as $challenge)
                                    <div class="environment-panel">
                                        <p class="text-sm font-extrabold">{{ $challenge->name }}</p>
                                        <p class="mt-2 text-sm leading-6">{{ $challenge->description }}</p>
                                        <p class="mt-2 text-xs font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)]">
                                            Meta: {{ $challenge->goal_target }} - +{{ $challenge->points_reward }} pontos
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            @if ($highlights['kind'] !== 'educational' && $recentItems->isNotEmpty())
                <section class="pixel-card environment-card {{ $theme['card_class'] }}">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Complemento do ambiente</p>
                            <h3 class="mt-2 text-lg leading-relaxed">
                                {{ $environment->slug === 'casa' ? 'Itens e cuidados da rotina domestica' : 'Itens e apoio deste ambiente' }}
                            </h3>
                        </div>

                        <a href="{{ $actionLinks['items_create'] }}" class="pixel-btn w-full sm:w-auto" style="background-color: var(--vm-panel);">
                            Adicionar item
                        </a>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($recentItems as $item)
                            <div class="environment-panel">
                                <p class="text-sm font-extrabold">{{ $item->name }}</p>
                                <p class="mt-2 text-sm font-bold text-[color:var(--vm-wood)]">
                                    {{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}
                                </p>
                                <p class="mt-2 text-xs font-extrabold uppercase tracking-[0.14em]">
                                    Minimo: {{ rtrim(rtrim(number_format($item->minimum_quantity, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
