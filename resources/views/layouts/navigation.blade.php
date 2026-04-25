<nav x-data="{ open: false }" class="border-b-4" style="border-color: var(--vm-border); background-color: rgba(255, 248, 221, 0.92);">
    <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
        <div class="flex min-h-16 justify-between gap-3 py-2 sm:h-16 sm:py-0">
            <div class="flex min-w-0 items-center">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('environments.index') }}">
                        <x-application-logo class="text-[10px]" />
                    </a>
                </div>

                <div class="hidden items-center gap-4 sm:ms-8 sm:flex">
                    <a href="{{ route('environments.index') }}" class="pixel-btn px-3 py-2 text-[10px] sm:px-4 {{ request()->routeIs('environments.*') ? '' : 'pixel-btn-secondary' }}">
                        Mapa
                    </a>

                    <a href="{{ route('dashboard') }}" class="pixel-btn px-3 py-2 text-[10px] sm:px-4 {{ request()->routeIs('dashboard') ? '' : 'pixel-btn-secondary' }}">
                        Painel
                    </a>

                    <div class="flex items-center gap-4 border-l-4 pl-4" style="border-color: rgba(61, 43, 31, 0.28);">
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">
                            Atalhos
                        </span>

                        <x-nav-link :href="route('financial-transactions.index')" :active="request()->routeIs('financial-transactions.*')">
                            Transacoes
                        </x-nav-link>

                        <x-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')">
                            Metas
                        </x-nav-link>

                    </div>
                </div>
            </div>

            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 border-4 px-3 py-2 text-sm font-extrabold uppercase tracking-[0.12em]" style="border-color: var(--vm-border); background-color: var(--vm-panel-strong); color: var(--vm-border);">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-1 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center border-4 p-2" style="border-color: var(--vm-border); background-color: var(--vm-panel-strong); color: var(--vm-border);">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 px-3 pb-3 pt-2">
            <a href="{{ route('environments.index') }}" class="pixel-btn w-full {{ request()->routeIs('environments.*') ? '' : 'pixel-btn-secondary' }}">
                Abrir mapa
            </a>

            <a href="{{ route('dashboard') }}" class="pixel-btn w-full {{ request()->routeIs('dashboard') ? '' : 'pixel-btn-secondary' }}">
                Abrir painel
            </a>

            <p class="px-1 pt-3 text-[10px] font-extrabold uppercase tracking-[0.18em] text-[color:var(--vm-wood)]">
                Atalhos por modulo
            </p>

            <x-responsive-nav-link :href="route('financial-transactions.index')" :active="request()->routeIs('financial-transactions.*')">
                Transacoes
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')">
                Metas
            </x-responsive-nav-link>

        </div>

        <div class="border-t-4 pb-1 pt-4" style="border-color: var(--vm-border);">
            <div class="px-4 pb-1">
                <div class="text-base font-extrabold" style="color: var(--vm-border);">{{ Auth::user()->name }}</div>
                <div class="text-sm font-bold" style="color: var(--vm-wood);">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
