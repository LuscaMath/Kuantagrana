<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Environment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'environment_id' => ['required', 'exists:environments,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'is_completed' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $category = Category::query()->find($this->integer('category_id'));
            $environment = Environment::query()->find($this->integer('environment_id'));

            if ($category && $category->type !== $this->input('type')) {
                $validator->errors()->add('category_id', 'A categoria selecionada nao corresponde ao tipo informado.');
            }

            if ($category && $environment && $category->environment_id !== $environment->id) {
                $validator->errors()->add('category_id', 'A categoria selecionada nao pertence ao ambiente informado.');
            }

            if ($environment && ! $environment->supportsFeature('transactions')) {
                $validator->errors()->add('environment_id', 'O ambiente selecionado nao aceita transacoes.');
            }

            if ($environment && $this->input('type') === 'income' && ! $environment->supportsFeature('income_transactions')) {
                $validator->errors()->add('type', 'Receitas devem ser registradas na Casa.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_recurring' => $this->boolean('is_recurring'),
            'is_completed' => $this->boolean('is_completed', true),
        ]);
    }
}
