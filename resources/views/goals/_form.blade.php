<div>
    <x-input-label for="title" value="Título da meta" />
    <x-text-input id="title" name="title" type="text" :value="old('title', $goal->title)" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="target_amount" value="Valor alvo" />
    <x-text-input id="target_amount" name="target_amount" type="number" step="0.01" min="0.01" :value="old('target_amount', $goal->target_amount)" required />
    <x-input-error :messages="$errors->get('target_amount')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="environment_id" value="Ambiente" />
    <select id="environment_id" name="environment_id" class="pixel-input">
        <option value="">Selecione um ambiente</option>
        @foreach ($environments as $environment)
            <option value="{{ $environment->id }}" @selected((string) old('environment_id', $goal->environment_id) === (string) $environment->id)>
                {{ $environment->name }}
            </option>
        @endforeach
    </select>
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
        <option value="completed" @selected(old('status', $goal->status) === 'completed')>Concluída</option>
        <option value="cancelled" @selected(old('status', $goal->status) === 'cancelled')>Cancelada</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Descrição" />
    <textarea id="description" name="description" rows="4" class="pixel-input">{{ old('description', $goal->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>
