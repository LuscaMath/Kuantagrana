@php
    $selectedType = old('type', $transaction->type);
    $selectedCategoryId = (int) old('category_id', $transaction->category_id);
    $selectedEnvironmentId = old('environment_id', $transaction->environment_id);
    $contextEnvironment = $selectedEnvironment ?? null;

    $transactionContext = match ($contextEnvironment?->slug) {
        'casa' => [
            'title' => 'Registro da base financeira',
            'description' => 'Na Casa voce registra receitas e despesas da rotina principal, como salario, ajuda familiar, aluguel, luz, agua e internet.',
            'title_label' => 'Receita ou despesa',
            'title_placeholder' => 'Ex.: Salario do mes',
            'description_placeholder' => 'Ex.: Valor recebido ou conta da rotina principal.',
        ],
        'mercado' => [
            'title' => 'Registro de compras do mercado',
            'description' => 'Aqui faz mais sentido registrar compras do mes, alimentacao, feiras e pequenas reposicoes de mantimentos.',
            'title_label' => 'Compra ou gasto',
            'title_placeholder' => 'Ex.: Compra da semana',
            'description_placeholder' => 'Ex.: Frutas, legumes e itens basicos.',
        ],
        'farmacia' => [
            'title' => 'Registro de farmacia e cuidados',
            'description' => 'Use este espaco para remedios, higiene pessoal e outros gastos ligados a saude e cuidado.',
            'title_label' => 'Despesa de cuidado',
            'title_placeholder' => 'Ex.: Remedio para gripe',
            'description_placeholder' => 'Ex.: Medicamentos e itens de higiene.',
        ],
        default => [
            'title' => 'Registro financeiro',
            'description' => 'Escolha entre Casa, Mercado ou Farmacia para registrar no contexto certo.',
            'title_label' => 'Titulo',
            'title_placeholder' => 'Ex.: Conta de internet',
            'description_placeholder' => 'Ex.: Detalhes que ajudem voce a lembrar desse registro.',
        ],
    };
@endphp

@if ($contextEnvironment)
    <div class="border-4 p-4 text-sm font-bold" style="border-color: var(--vm-border); background-color: #fffdf2;">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-wood)]">{{ $transactionContext['title'] }}</p>
        <p class="mt-2">{{ $transactionContext['description'] }}</p>
        <p class="mt-3 text-xs font-extrabold uppercase tracking-[0.14em]">
            Ambiente atual: {{ $contextEnvironment->name }}
        </p>
    </div>
@endif

<div class="{{ $contextEnvironment ? 'mt-4' : '' }}">
    @if ($contextEnvironment && $contextEnvironment->slug !== 'casa')
        <input type="hidden" id="type" name="type" value="expense">
        <x-input-label for="type_locked" value="Tipo" />
        <div id="type_locked" class="pixel-input flex items-center justify-between gap-3">
            <span>Despesa</span>
            <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)]">Definido pelo ambiente</span>
        </div>
    @else
        <x-input-label for="type" value="Tipo" />
        <select id="type" name="type" class="pixel-input">
            <option value="income" @selected($selectedType === 'income')>Receita</option>
            <option value="expense" @selected($selectedType === 'expense')>Despesa</option>
        </select>
    @endif
    <x-input-error :messages="$errors->get('type')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="title" :value="$transactionContext['title_label']" />
    <x-text-input id="title" name="title" type="text" :value="old('title', $transaction->title)" placeholder="{{ $transactionContext['title_placeholder'] }}" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="amount" value="Valor" />
    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" :value="old('amount', $transaction->amount)" required />
    <x-input-error :messages="$errors->get('amount')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4">
    <x-input-label for="transaction_date" value="Data da transacao" />
    <x-text-input id="transaction_date" name="transaction_date" type="date" :value="old('transaction_date', optional($transaction->transaction_date)->format('Y-m-d') ?? $transaction->transaction_date)" required />
    <x-input-error :messages="$errors->get('transaction_date')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
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
                <option value="{{ $environment->id }}" @selected((string) $selectedEnvironmentId === (string) $environment->id)>
                    {{ $environment->name }}
                </option>
            @endforeach
        </select>
    @endif
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
    <x-input-label for="description" value="Descricao" />
    <textarea id="description" name="description" rows="4" class="pixel-input" placeholder="{{ $transactionContext['description_placeholder'] }}">{{ old('description', $transaction->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2 text-sm font-bold text-[color:var(--vm-danger)]" />
</div>

<div class="mt-4 grid gap-3 sm:grid-cols-2">
    <label for="is_completed" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_completed" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_completed" value="1" @checked(old('is_completed', $transaction->is_completed ?? true))>
        <span>Transacao concluida</span>
    </label>

    <label for="is_recurring" class="inline-flex items-center gap-3 text-sm font-bold">
        <input id="is_recurring" type="checkbox" class="h-5 w-5 rounded-none border-2" style="border-color: var(--vm-border); color: var(--vm-accent-strong);" name="is_recurring" value="1" @checked(old('is_recurring', $transaction->is_recurring ?? false))>
        <span>Transacao recorrente</span>
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
