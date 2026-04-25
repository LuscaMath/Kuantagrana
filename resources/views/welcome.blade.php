<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Vale das Moedas') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <main class="relative min-h-screen overflow-hidden pixel-grid">
            <div class="absolute inset-x-0 bottom-0 h-32 border-t-4 sm:h-48" style="border-color: var(--vm-border); background: linear-gradient(180deg, #95d46b 0%, #5fb75d 100%);"></div>
            <div class="absolute right-10 top-10 hidden h-16 w-16 border-4 lg:block" style="border-color: var(--vm-border); background-color: #fff8dd; box-shadow: 6px 6px 0 rgba(61, 43, 31, 0.85);"></div>
            <div class="absolute left-10 top-24 hidden h-20 w-20 border-4 lg:block" style="border-color: var(--vm-border); background-color: #f5e2a8; box-shadow: 6px 6px 0 rgba(61, 43, 31, 0.85);"></div>

            <div class="relative z-10 mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-application-logo class="w-fit text-[10px] sm:text-xs" />

                    @if (Route::has('login'))
                        <div class="grid w-full gap-3 sm:flex sm:w-auto sm:flex-wrap">
                            @auth
                                <a href="{{ route('environments.index') }}" class="pixel-btn">Abrir mapa</a>
                                <a href="{{ route('dashboard') }}" class="pixel-btn" style="background-color: var(--vm-panel);">Ver painel</a>
                            @else
                                <a href="{{ route('login') }}" class="pixel-btn">Login</a>
                                <a href="{{ route('register') }}" class="pixel-btn" style="background-color: var(--vm-panel);">Cadastro</a>
                            @endauth
                        </div>
                    @endif
                </header>

                <section class="grid flex-1 items-center gap-6 py-8 sm:gap-8 sm:py-10 lg:grid-cols-[1.15fr_0.85fr] lg:py-16">
                    <div class="pixel-card-soft p-4 sm:p-8">
                        <h1 class="mt-4 text-2xl leading-relaxed sm:mt-6 sm:text-4xl">
                            Vale das Moedas
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm font-bold leading-7 sm:mt-5 sm:text-lg sm:leading-8">
                            Um sistema web para organizacao financeira pessoal com gamificacao, estruturado em ambientes que ajudam o usuario a registrar gastos, aprender melhor e evoluir com constancia.
                        </p>

                        <div class="mt-6 grid gap-4 sm:mt-8 sm:grid-cols-2">
                            <div class="pixel-card">
                                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Casa e Rotina</p>
                                <p class="mt-3 text-sm font-bold leading-6">A Casa concentra receitas e despesas, enquanto Mercado e Farmacia registram gastos por contexto.</p>
                            </div>
                            <div class="pixel-card">
                                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">Aprendizado e Progresso</p>
                                <p class="mt-3 text-sm font-bold leading-6">A Escola oferece dicas financeiras e o Parque transforma metas e constancia em progresso gamificado.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pixel-card pixel-scene flex min-h-[300px] flex-col justify-between sm:min-h-[360px]">
                        <div>
                            <h2 class="mt-4 text-xl leading-relaxed sm:mt-5 sm:text-2xl">Um mapa para decidir melhor</h2>
                            <p class="mt-4 text-sm font-bold leading-6 sm:text-base sm:leading-7">
                                Cada ambiente tem um papel proprio: a Casa organiza a visao financeira principal, a Escola orienta, Mercado e Farmacia registram despesas do dia a dia e o Parque concentra metas, progresso e recompensas.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div class="border-4 p-3 text-center text-xs font-extrabold uppercase" style="border-color: var(--vm-border); background-color: #fffdf2;">Casa</div>
                            <div class="border-4 p-3 text-center text-xs font-extrabold uppercase" style="border-color: var(--vm-border); background-color: #fffdf2;">Escola</div>
                            <div class="border-4 p-3 text-center text-xs font-extrabold uppercase" style="border-color: var(--vm-border); background-color: #fffdf2;">Mercado</div>
                            <div class="border-4 p-3 text-center text-xs font-extrabold uppercase" style="border-color: var(--vm-border); background-color: #fffdf2;">Farmacia</div>
                            <div class="border-4 p-3 text-center text-xs font-extrabold uppercase sm:col-span-2" style="border-color: var(--vm-border); background-color: #fffdf2;">Parque</div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
