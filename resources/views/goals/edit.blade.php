<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">Planejamento</p>
            <h2 class="text-lg leading-relaxed sm:text-2xl">Gerenciar meta</h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[1fr_0.9fr]">
            <section class="pixel-card">
                <form method="POST" action="{{ route('goals.update', $goal) }}">
                    @csrf
                    @method('PUT')

                    @include('goals._form')

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <a href="{{ route('goals.index') }}" class="pixel-btn w-full sm:w-auto" style="background-color: var(--vm-panel);">
                            Voltar
                        </a>

                        <x-primary-button class="w-full sm:w-auto">
                            Atualizar meta
                        </x-primary-button>
                    </div>
                </form>
            </section>

            <section class="space-y-6">
                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Resumo</p>
                    <p class="mt-3 text-sm font-bold">Atual: R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</p>
                    <p class="mt-2 text-sm font-bold">Meta: R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                    <p class="mt-2 text-sm font-bold">Contribuições: {{ $goal->contributions->count() }}</p>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Nova contribuição</p>

                    <form method="POST" action="{{ route('goals.contributions.store', $goal) }}" class="mt-4">
                        @csrf

                        <div>
                            <x-input-label for="amount" value="Valor da contribuição" />
                            <x-text-input id="amount" name="amount" type="number" min="0.01" step="0.01" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="contribution_date" value="Data" />
                            <x-text-input id="contribution_date" name="contribution_date" type="date" :value="old('contribution_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('contribution_date')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="notes" value="Observações" />
                            <textarea id="notes" name="notes" rows="3" class="pixel-input">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
                        </div>

                        <x-primary-button class="mt-5 w-full">
                            Registrar contribuição
                        </x-primary-button>
                    </form>
                </div>

                <div class="pixel-card">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Histórico de contribuições</p>

                    <div class="mt-4 space-y-3">
                        @forelse ($goal->contributions->sortByDesc('contribution_date') as $contribution)
                            <div class="border-4 p-3" style="border-color: var(--vm-border); background-color: #fffdf2;">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-extrabold">
                                        R$ {{ number_format($contribution->amount, 2, ',', '.') }}
                                    </p>
                                    <p class="text-sm font-bold text-[color:var(--vm-wood)]">
                                        {{ $contribution->contribution_date->format('d/m/Y') }}
                                    </p>
                                </div>

                                @if ($contribution->notes)
                                    <p class="mt-2 text-sm leading-6">{{ $contribution->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="border-4 p-3 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
                                Nenhuma contribuição registrada ainda.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
