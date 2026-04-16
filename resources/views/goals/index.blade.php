<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Planejamento</p>
                <h2 class="text-lg leading-relaxed sm:text-2xl">Metas financeiras</h2>
            </div>

            <a href="{{ route('goals.create') }}" class="pixel-btn w-full sm:w-auto">
                Nova meta
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
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Metas ativas</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ $goals->where('status', 'active')->count() }}</p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Concluidas</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ $goals->where('status', 'completed')->count() }}</p>
                </div>

                <div class="pixel-stat">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Valor acumulado</p>
                    <p class="mt-2 text-2xl font-extrabold">
                        R$ {{ number_format($goals->sum('current_amount'), 2, ',', '.') }}
                    </p>
                </div>
            </section>

            <section class="pixel-card">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Lista de metas</p>
                    <h3 class="mt-2 text-lg">Objetivos cadastrados</h3>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($goals as $goal)
                        @php
                            $progress = $goal->target_amount > 0
                                ? min(100, ($goal->current_amount / $goal->target_amount) * 100)
                                : 0;
                        @endphp

                        <article class="border-4 p-4" style="border-color: rgba(61, 43, 31, 0.7); background-color: #fffdf2;">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                        {{ $goal->environment?->name ?? 'Sem ambiente' }}
                                    </p>
                                    <h4 class="mt-1 text-lg font-extrabold">{{ $goal->title }}</h4>
                                </div>

                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-bold text-[color:var(--vm-wood)]">Status</p>
                                    <p class="mt-1 text-lg font-extrabold">
                                        @if ($goal->status === 'active')
                                            Ativa
                                        @elseif ($goal->status === 'completed')
                                            Concluida
                                        @else
                                            Cancelada
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center justify-between gap-3 text-sm font-bold">
                                    <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                                    <span>R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</span>
                                </div>

                                <div class="mt-2 h-5 border-4" style="border-color: var(--vm-border); background-color: #fff8dd;">
                                    <div class="h-full" style="width: {{ $progress }}%; background-color: var(--vm-accent);"></div>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="text-sm font-bold text-[color:var(--vm-wood)]">
                                    @if ($goal->target_date)
                                        Prazo: {{ $goal->target_date->format('d/m/Y') }}
                                    @else
                                        Sem prazo definido
                                    @endif
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('goals.edit', $goal) }}" class="pixel-btn w-full sm:w-auto pixel-btn-secondary">
                                        Gerenciar
                                    </a>

                                    <form method="POST" action="{{ route('goals.destroy', $goal) }}">
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
                            Voce ainda nao cadastrou metas financeiras.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $goals->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
