@php
    $selectedType = old('type', $transaction->type);
    $selectedCategoryId = (int) old('category_id', $transaction->category_id);
    $selectedEnvironmentId = old('environment_id', $transaction->environment_id);
@endphp

<div>
    <x-input-label for="type" value="Tipo" />
    <select id="type" name="type" class="pixel-input">
        <option value="income" @selected($selectedType === 'income')>Receita</option>
        <option value="expense" @selected($selectedType === 'expense')>Despesa</option>
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="title" value="Título" />
    <x-text-input id="title" name="title" type="text" :value="old('title', $transaction->title)" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="amount" value="Valor" />
    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" :value="old('amount', $transaction->amount)" required />
    <x-input-error :messages="$errors->get('amount')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="transaction_date" value="Data da transação" />
    <x-text-input id="transaction_date" name="transaction_date" type="date" :value="old('transaction_date', optional($transaction->transaction_date)->format('Y-m-d') ?? $transaction->transaction_date)" required />
    <x-input-error :messages="$errors->get('transaction_date')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="environment_id" value="Ambiente" />
    <select id="environment_id" name="environment_id" class="pixel-input">
        <option value="">Selecione um ambiente</option>
        @foreach ($environments as $environment)
            <option value="{{ $environment->id }}" @selected((string) $selectedEnvironmentId === (string) $environment->id)>
                {{ $environment->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('environment_id')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="category_id" value="Categoria" />
    <select id="category_id" name="category_id" class="pixel-input" required>
        <option value="">Selecione uma categoria</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" data-type="{{ $category->type }}" @selected($selectedCategoryId === $category->id)>
                {{ $category->name }} ({{ $category->type === 'income' ? 'Receita' : 'Despesa' }})
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Descrição" />
    <textarea id="description" name="description" rows="4" class="pixel-input">{{ old('description', $transaction->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4 grid gap-3 sm:grid-cols-2">
    <label for="is_completed" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_completed" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_completed" value="1" @checked(old('is_completed', $transaction->is_completed ?? true))>
        <span>Transação concluída</span>
    </label>

    <label for="is_recurring" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_recurring" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_recurring" value="1" @checked(old('is_recurring', $transaction->is_recurring ?? false))>
        <span>Transação recorrente</span>
    </label>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeField = document.getElementById('type');
        const categoryField = document.getElementById('category_id');

        if (!typeField || !categoryField) {
            return;
        }

        const syncCategoryOptions = () => {
            const currentType = typeField.value;

            Array.from(categoryField.options).forEach((option) => {
                if (!option.dataset.type) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.type !== currentType;
            });

            const selectedOption = categoryField.options[categoryField.selectedIndex];

            if (selectedOption && selectedOption.dataset.type && selectedOption.dataset.type !== currentType) {
                categoryField.value = '';
            }
        };

        typeField.addEventListener('change', syncCategoryOptions);
        syncCategoryOptions();
    });
</script>
