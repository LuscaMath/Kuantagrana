<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'environment_id' => ['nullable', 'exists:environments,id'],
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

            if ($category && $category->type !== $this->input('type')) {
                $validator->errors()->add('category_id', 'A categoria selecionada não corresponde ao tipo informado.');
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
