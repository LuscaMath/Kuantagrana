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
        <div class="relative min-h-screen overflow-hidden pixel-grid">
            <div class="absolute inset-x-0 bottom-0 h-28 border-t-4 sm:h-40" style="border-color: var(--vm-border); background: linear-gradient(180deg, #95d46b 0%, #5fb75d 100%);"></div>
            <div class="absolute left-[8%] top-20 hidden h-16 w-16 border-4 md:block" style="border-color: var(--vm-border); background-color: #fff8dd; box-shadow: 6px 6px 0 rgba(61, 43, 31, 0.85);"></div>
            <div class="absolute right-[10%] top-28 hidden h-20 w-20 border-4 md:block" style="border-color: var(--vm-border); background-color: #f5e2a8; box-shadow: 6px 6px 0 rgba(61, 43, 31, 0.85);"></div>

            <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-4 py-6 sm:py-10 lg:flex-row lg:items-center lg:gap-12">
                <section class="order-2 mb-8 max-w-xl lg:order-1 lg:mb-0">
                    <a href="{{ url('/') }}" class="inline-flex">
                        <x-application-logo class="text-xs sm:text-sm" />
                    </a>

                    <div class="mt-6 pixel-card-soft p-4 sm:mt-8 sm:p-6">
                        <span class="pixel-badge">Mapa Financeiro</span>
                        <h1 class="mt-4 text-xl leading-relaxed sm:mt-5 sm:text-3xl" style="color: var(--vm-border);">
                            Seu progresso financeiro com clima de aventura e visual inspirado em pixel art.
                        </h1>
                        <p class="mt-4 text-sm font-bold leading-6 sm:mt-5 sm:text-base sm:leading-7">
                            O Breeze entra como base técnica do login. O visual daqui em diante já segue a identidade do Vale das Moedas: leve, lúdica e fácil de evoluir no TCC.
                        </p>
                    </div>
                </section>

                <div class="order-1 w-full max-w-xl lg:order-2 lg:max-w-md">
                    <div class="pixel-card">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
