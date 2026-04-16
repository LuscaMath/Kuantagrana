<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Controle financeiro</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Transacoes financeiras</h2>
            </div>

            <a href="{{ route('financial-transactions.create') }}" class="pixel-btn w-full sm:w-auto">
                Nova transacao
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session('status'))
                <div class="pixel-card-quiet text-sm font-bold text-[color:var(--vm-leaf)]">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-3">
                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Receitas</p>
                    <p class="mt-2 text-2xl font-extrabold">R$ {{ number_format($summary['income'], 2, ',', '.') }}</p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Despesas</p>
                    <p class="mt-2 text-2xl font-extrabold">R$ {{ number_format($summary['expense'], 2, ',', '.') }}</p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Saldo do mes</p>
                    <p class="mt-2 text-2xl font-extrabold">R$ {{ number_format($summary['balance'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-sm font-bold text-[color:var(--vm-wood)]">{{ $summary['month_label'] }}</p>
                </div>
            </section>

            <section class="pixel-card-quiet">
                <form method="GET" action="{{ route('financial-transactions.index') }}" class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <x-input-label for="month" value="Mes" />
                        <x-text-input id="month" name="month" type="month" :value="$filters['month']" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Tipo" />
                        <select id="type" name="type" class="pixel-input">
                            <option value="">Todos</option>
                            <option value="income" @selected($filters['type'] === 'income')>Receitas</option>
                            <option value="expense" @selected($filters['type'] === 'expense')>Despesas</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="pixel-btn w-full pixel-btn-secondary">Filtrar</button>
                    </div>
                </form>
            </section>

            <section class="pixel-card">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Historico</p>
                    <h3 class="mt-2 text-lg">Ultimas movimentacoes</h3>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($transactions as $transaction)
                        <article class="border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                        {{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                    </p>
                                    <h4 class="mt-1 text-lg font-extrabold">{{ $transaction->title }}</h4>
                                    <p class="mt-1 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">
                                        {{ $transaction->category->name }}
                                        @if ($transaction->environment)
                                            - {{ $transaction->environment->name }}
                                        @endif
                                    </p>
                                </div>

                                <div class="text-left sm:text-right">
                                    <p class="text-xl font-extrabold">
                                        R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-[color:var(--vm-wood)]">
                                        {{ $transaction->transaction_date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex flex-wrap gap-2 text-xs font-extrabold uppercase tracking-[0.14em]">
                                    <span class="border-2 px-2 py-1" style="border-color: rgba(61, 43, 31, 0.7);">
                                        {{ $transaction->is_completed ? 'Concluida' : 'Pendente' }}
                                    </span>

                                    @if ($transaction->is_recurring)
                                        <span class="border-2 px-2 py-1" style="border-color: rgba(61, 43, 31, 0.7);">
                                            Recorrente
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('financial-transactions.edit', $transaction) }}" class="pixel-btn w-full sm:w-auto pixel-btn-secondary">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('financial-transactions.destroy', $transaction) }}">
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
                            Nenhuma transacao encontrada para o filtro selecionado.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
