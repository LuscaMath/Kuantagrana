<div>
    <x-input-label for="name" value="Nome do item" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $item->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="environment_id" value="Ambiente" />
    <select id="environment_id" name="environment_id" class="pixel-input">
        <option value="">Selecione um ambiente</option>
        @foreach ($environments as $environment)
            <option value="{{ $environment->id }}" @selected((string) old('environment_id', $item->environment_id) === (string) $environment->id)>
                {{ $environment->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('environment_id')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <x-input-label for="quantity" value="Quantidade" />
        <x-text-input id="quantity" name="quantity" type="number" min="0" :value="old('quantity', $item->quantity)" required />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
    </div>

    <div>
        <x-input-label for="minimum_quantity" value="Quantidade mínima" />
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
    <x-input-label for="notes" value="Observações" />
    <textarea id="notes" name="notes" rows="4" class="pixel-input">{{ old('notes', $item->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <label for="is_active" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_active" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
        <span>Item ativo</span>
    </label>
</div>
