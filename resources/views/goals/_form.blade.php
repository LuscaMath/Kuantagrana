@php
    $contextEnvironment = $selectedEnvironment ?? null;

    $goalContext = match ($contextEnvironment?->slug) {
        'parque-de-diversoes' => [
            'title' => 'Planeje uma meta do seu parque',
            'description' => 'O Parque e o melhor lugar para metas, sonhos, recompensas e objetivos que representam sua evolucao.',
            'title_placeholder' => 'Ex.: Viagem de fim de ano',
            'description_placeholder' => 'Ex.: Quero juntar esse valor aos poucos e acompanhar meu progresso.',
        ],
        default => [
            'title' => 'Meta financeira',
            'description' => 'As metas vivem no Parque de Diversoes, onde voce acompanha o progresso e as recompensas.',
            'title_placeholder' => 'Ex.: Comprar notebook',
            'description_placeholder' => 'Ex.: Juntar aos poucos para atingir esse objetivo.',
        ],
    };
@endphp

@if ($contextEnvironment)
    <div class="border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">{{ $goalContext['title'] }}</p>
        <p class="mt-2">{{ $goalContext['description'] }}</p>
        <p class="mt-3 text-xs font-extrabold uppercase tracking-[0.14em]">
            Ambiente atual: {{ $contextEnvironment->name }}
        </p>
    </div>
@endif

<div class="{{ $contextEnvironment ? 'mt-4' : '' }}">
    <x-input-label for="title" value="Titulo da meta" />
    <x-text-input id="title" name="title" type="text" :value="old('title', $goal->title)" placeholder="{{ $goalContext['title_placeholder'] }}" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="target_amount" value="Valor alvo" />
    <x-text-input id="target_amount" name="target_amount" type="number" step="0.01" min="0.01" :value="old('target_amount', $goal->target_amount)" required />
    <x-input-error :messages="$errors->get('target_amount')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    @if ($contextEnvironment)
        <input type="hidden" name="environment_id" value="{{ $contextEnvironment->id }}">
        <x-input-label for="environment_locked" value="Ambiente" />
        <div id="environment_locked" class="pixel-input flex items-center justify-between gap-3">
            <span>{{ $contextEnvironment->name }}</span>
            <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)]">Definido pelo mapa</span>
        </div>
    @else
        <x-input-label for="environment_id" value="Ambiente" />
        <select id="environment_id" name="environment_id" class="pixel-input">
            <option value="">Selecione um ambiente</option>
            @foreach ($environments as $environment)
                <option value="{{ $environment->id }}" @selected((string) old('environment_id', $goal->environment_id) === (string) $environment->id)>
                    {{ $environment->name }}
                </option>
            @endforeach
        </select>
    @endif
    <x-input-error :messages="$errors->get('environment_id')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="start_date" value="Data inicial" />
        <x-text-input id="start_date" name="start_date" type="date" :value="old('start_date', optional($goal->start_date)->format('Y-m-d') ?? $goal->start_date)" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>

    <div>
        <x-input-label for="target_date" value="Prazo" />
        <x-text-input id="target_date" name="target_date" type="date" :value="old('target_date', optional($goal->target_date)->format('Y-m-d') ?? $goal->target_date)" />
        <x-input-error :messages="$errors->get('target_date')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" class="pixel-input">
        <option value="active" @selected(old('status', $goal->status) === 'active')>Ativa</option>
        <option value="completed" @selected(old('status', $goal->status) === 'completed')>Concluida</option>
        <option value="cancelled" @selected(old('status', $goal->status) === 'cancelled')>Cancelada</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Descricao" />
    <textarea id="description" name="description" rows="4" class="pixel-input" placeholder="{{ $goalContext['description_placeholder'] }}">{{ old('description', $goal->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>
