@php
    $contextEnvironment = $selectedEnvironment ?? null;

    $itemContext = match ($contextEnvironment?->slug) {
        'casa' => [
            'title' => 'Item da rotina da casa',
            'description' => 'Na Casa, este cadastro funciona muito bem para itens domesticos, mantimentos basicos e produtos que precisam de reposicao.',
            'name_label' => 'Item da casa',
            'name_placeholder' => 'Ex.: Agua sanitaria',
            'notes_placeholder' => 'Ex.: Usado na limpeza semanal.',
        ],
        'mercado' => [
            'title' => 'Item para compra ou reposicao',
            'description' => 'No Mercado, use este cadastro para organizar produtos que voce costuma comprar e monitorar quando precisa repor.',
            'name_label' => 'Item do mercado',
            'name_placeholder' => 'Ex.: Arroz',
            'notes_placeholder' => 'Ex.: Comprar quando chegar ao minimo.',
        ],
        'farmacia' => [
            'title' => 'Item de cuidado e farmacia',
            'description' => 'Na Farmacia, este espaco ajuda a controlar remedios, higiene pessoal e itens de cuidado recorrente.',
            'name_label' => 'Item de cuidado',
            'name_placeholder' => 'Ex.: Sabonete',
            'notes_placeholder' => 'Ex.: Conferir validade e quantidade restante.',
        ],
        default => [
            'title' => 'Item de apoio da rotina',
            'description' => 'Cadastre um item importante e associe a Casa, Mercado ou Farmacia.',
            'name_label' => 'Nome do item',
            'name_placeholder' => 'Ex.: Papel higienico',
            'notes_placeholder' => 'Ex.: Observacoes que ajudem no controle.',
        ],
    };
@endphp

@if ($contextEnvironment)
    <div class="border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">{{ $itemContext['title'] }}</p>
        <p class="mt-2">{{ $itemContext['description'] }}</p>
        <p class="mt-3 text-xs font-extrabold uppercase tracking-[0.14em]">
            Ambiente atual: {{ $contextEnvironment->name }}
        </p>
    </div>
@endif

<div class="{{ $contextEnvironment ? 'mt-4' : '' }}">
    <x-input-label for="name" :value="$itemContext['name_label']" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $item->name)" placeholder="{{ $itemContext['name_placeholder'] }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
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
                <option value="{{ $environment->id }}" @selected((string) old('environment_id', $item->environment_id) === (string) $environment->id)>
                    {{ $environment->name }}
                </option>
            @endforeach
        </select>
    @endif
    <x-input-error :messages="$errors->get('environment_id')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <x-input-label for="quantity" value="Quantidade" />
        <x-text-input id="quantity" name="quantity" type="number" min="0" :value="old('quantity', $item->quantity)" required />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>

    <div>
        <x-input-label for="minimum_quantity" value="Quantidade minima" />
        <x-text-input id="minimum_quantity" name="minimum_quantity" type="number" min="0" :value="old('minimum_quantity', $item->minimum_quantity)" required />
        <x-input-error :messages="$errors->get('minimum_quantity')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>

    <div>
        <x-input-label for="unit" value="Unidade" />
        <x-text-input id="unit" name="unit" type="text" :value="old('unit', $item->unit)" required />
        <x-input-error :messages="$errors->get('unit')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="expires_at" value="Validade" />
    <x-text-input id="expires_at" name="expires_at" type="date" :value="old('expires_at', optional($item->expires_at)->format('Y-m-d') ?? $item->expires_at)" />
    <x-input-error :messages="$errors->get('expires_at')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="notes" value="Observacoes" />
    <textarea id="notes" name="notes" rows="4" class="pixel-input" placeholder="{{ $itemContext['notes_placeholder'] }}">{{ old('notes', $item->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <label for="is_active" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_active" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
        <span>Item ativo</span>
    </label>
</div>
