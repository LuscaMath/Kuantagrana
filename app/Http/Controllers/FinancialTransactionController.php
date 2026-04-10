<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinancialTransactionRequest;
use App\Http\Requests\UpdateFinancialTransactionRequest;
use App\Models\Category;
use App\Models\Environment;
use App\Models\FinancialTransaction;
use App\Services\FinancialTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialTransactionController extends Controller
{
    public function __construct(
        private readonly FinancialTransactionService $financialTransactionService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['type', 'month', 'environment_id']);

        return view('financial-transactions.index', [
            'transactions' => $this->financialTransactionService->getPaginatedForUser($request->user(), $filters),
            'summary' => $this->financialTransactionService->getSummary($request->user(), $filters['month'] ?? null, $filters['environment_id'] ?? null),
            'filters' => [
                'type' => $filters['type'] ?? '',
                'month' => $filters['month'] ?? now()->format('Y-m'),
                'environment_id' => $filters['environment_id'] ?? '',
            ],
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedEnvironmentId = $request->integer('environment_id') ?: null;

        return view('financial-transactions.create', [
            'transaction' => new FinancialTransaction([
                'type' => 'expense',
                'transaction_date' => now()->toDateString(),
                'is_completed' => true,
                'is_recurring' => false,
                'environment_id' => $selectedEnvironmentId,
            ]),
            'categories' => Category::query()->where('is_active', true)->orderBy('type')->orderBy('name')->get(),
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
            'selectedEnvironment' => $selectedEnvironmentId
                ? Environment::query()->whereKey($selectedEnvironmentId)->first()
                : null,
        ]);
    }

    public function store(StoreFinancialTransactionRequest $request): RedirectResponse
    {
        $this->financialTransactionService->create($request->user(), $request->validated());

        return redirect()
            ->route('financial-transactions.index')
            ->with('status', 'Transação registrada com sucesso.');
    }

    public function edit(Request $request, FinancialTransaction $financialTransaction): View
    {
        abort_if($financialTransaction->user_id !== $request->user()->id, 403);

        return view('financial-transactions.edit', [
            'transaction' => $financialTransaction,
            'categories' => Category::query()->where('is_active', true)->orderBy('type')->orderBy('name')->get(),
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
            'selectedEnvironment' => $financialTransaction->environment,
        ]);
    }

    public function update(UpdateFinancialTransactionRequest $request, FinancialTransaction $financialTransaction): RedirectResponse
    {
        abort_if($financialTransaction->user_id !== $request->user()->id, 403);

        $this->financialTransactionService->update($financialTransaction, $request->validated());

        return redirect()
            ->route('financial-transactions.index')
            ->with('status', 'Transação atualizada com sucesso.');
    }

    public function destroy(Request $request, FinancialTransaction $financialTransaction): RedirectResponse
    {
        abort_if($financialTransaction->user_id !== $request->user()->id, 403);

        $this->financialTransactionService->delete($financialTransaction);

        return redirect()
            ->route('financial-transactions.index')
            ->with('status', 'Transação removida com sucesso.');
    }
}
