<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vale das Moedas') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        @php
            $showOnboarding = auth()->check() && (request()->boolean('tutorial') || auth()->user()->onboarding_dismissed_at === null);
            $gamificationFeedback = session('gamification_feedback', []);
            $tutorialSteps = [
                [
                    'title' => 'Bem-vindo ao mapa',
                    'body' => 'O sistema e organizado por ambientes. A ideia e entrar pelo contexto certo para decidir melhor o que fazer.',
                ],
                [
                    'title' => 'Casa e a base financeira',
                    'body' => 'Use a Casa para acompanhar receitas, despesas da rotina e a visao geral do saldo do mes.',
                ],
                [
                    'title' => 'Mercado e Farmacia sao contextos de gasto',
                    'body' => 'Esses ambientes ajudam a registrar despesas especificas do dia a dia, sem perder a visao geral da Casa.',
                ],
                [
                    'title' => 'Escola orienta',
                    'body' => 'A Escola existe para apoiar com dicas financeiras, orientacao e aprendizado antes de agir.',
                ],
                [
                    'title' => 'Parque mostra metas e progresso',
                    'body' => 'No Parque voce acompanha metas, conquistas, quests e a parte mais visivel da gamificacao.',
                ],
            ];
        @endphp
        <div class="min-h-screen pixel-grid">
            @include('layouts.navigation')

            @if ($gamificationFeedback !== [])
                <div class="fixed right-4 top-20 z-50 flex w-[min(24rem,calc(100%-2rem))] flex-col gap-3 sm:right-6 sm:top-24">
                    @foreach ($gamificationFeedback as $index => $feedback)
                        <div
                            x-data="{ open: true }"
                            x-show="open"
                            x-transition.opacity.duration.300ms
                            x-init="setTimeout(() => open = false, 4200)"
                            class="pixel-card-quiet"
                            style="background-color: rgba(255, 253, 242, 0.98);"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                        {{ $feedback['type'] === 'level' ? 'Level Up' : ($feedback['type'] === 'achievement' ? 'Conquista' : ($feedback['type'] === 'challenge' ? 'Quest' : 'Progresso')) }}
                                    </p>
                                    <p class="mt-2 text-sm font-extrabold">{{ $feedback['title'] }}</p>
                                    <p class="mt-2 text-sm font-bold leading-6 text-[color:var(--vm-wood)]">{{ $feedback['body'] }}</p>
                                </div>

                                <button type="button" @click="open = false" class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">
                                    Fechar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @auth
                <div
                    x-data="{
                        open: {{ $showOnboarding ? 'true' : 'false' }},
                        step: 0,
                        steps: {{ count($tutorialSteps) }},
                    }"
                >
                    <div
                        x-show="open"
                        x-transition.opacity.duration.200ms
                        class="fixed inset-0 z-40 bg-[rgba(34,24,18,0.58)]"
                        style="display: none;"
                    ></div>

                    <div
                        x-show="open"
                        x-transition.duration.250ms
                        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
                        style="display: none;"
                    >
                        <div class="pixel-card w-full max-w-2xl">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Tutorial interativo</p>
                                    <h3 class="mt-2 text-lg leading-relaxed sm:text-2xl">Como navegar pelo sistema</h3>
                                </div>

                                <span class="pixel-badge" x-text="`Passo ${step + 1} de ${steps}`"></span>
                            </div>

                            <div class="mt-5 space-y-4">
                                @foreach ($tutorialSteps as $index => $tutorialStep)
                                    <section x-show="step === {{ $index }}" style="display: none;">
                                        <h4 class="text-base font-extrabold sm:text-lg">{{ $tutorialStep['title'] }}</h4>
                                        <p class="mt-3 text-sm font-bold leading-7 sm:text-base">{{ $tutorialStep['body'] }}</p>
                                    </section>
                                @endforeach
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($tutorialSteps as $index => $tutorialStep)
                                    <span
                                        class="h-3 w-3 border-2"
                                        :style="step === {{ $index }}
                                            ? 'border-color: var(--vm-border); background-color: var(--vm-accent);'
                                            : 'border-color: rgba(61, 43, 31, 0.55); background-color: rgba(255, 253, 242, 0.92);'"
                                    ></span>
                                @endforeach
                            </div>

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <form method="POST" action="{{ route('onboarding.dismiss') }}">
                                    @csrf
                                    <button type="submit" class="pixel-btn pixel-btn-secondary w-full sm:w-auto">
                                        Pular por agora
                                    </button>
                                </form>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <button type="button" @click="step = Math.max(0, step - 1)" class="pixel-btn pixel-btn-secondary w-full sm:w-auto" x-bind:disabled="step === 0">
                                        Voltar
                                    </button>

                                    <button type="button" @click="step = Math.min(steps - 1, step + 1)" class="pixel-btn w-full sm:w-auto" x-show="step < steps - 1">
                                        Proximo
                                    </button>

                                    <form method="POST" action="{{ route('onboarding.dismiss') }}" x-show="step === steps - 1" style="display: none;">
                                        @csrf
                                        <button type="submit" class="pixel-btn w-full sm:w-auto">
                                            Concluir tutorial
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="px-4 pt-4 sm:px-6 sm:pt-8 lg:px-8">
                    <div class="mx-auto max-w-7xl pixel-card-soft p-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-6 sm:pb-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
