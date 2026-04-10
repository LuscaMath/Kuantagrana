<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouseholdItemRequest;
use App\Http\Requests\UpdateHouseholdItemRequest;
use App\Models\Environment;
use App\Models\HouseholdItem;
use App\Services\HouseholdItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseholdItemController extends Controller
{
    public function __construct(
        private readonly HouseholdItemService $householdItemService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['environment_id']);

        return view('household-items.index', [
            'items' => $this->householdItemService->getPaginatedForUser($request->user(), $filters),
            'filters' => [
                'environment_id' => $filters['environment_id'] ?? '',
            ],
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedEnvironmentId = $request->integer('environment_id') ?: null;

        return view('household-items.create', [
            'item' => new HouseholdItem([
                'unit' => 'un',
                'quantity' => 0,
                'minimum_quantity' => 0,
                'is_active' => true,
                'environment_id' => $selectedEnvironmentId,
            ]),
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
            'selectedEnvironment' => $selectedEnvironmentId
                ? Environment::query()->whereKey($selectedEnvironmentId)->first()
                : null,
        ]);
    }

    public function store(StoreHouseholdItemRequest $request): RedirectResponse
    {
        $this->householdItemService->create($request->user(), $request->validated());

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item doméstico cadastrado com sucesso.');
    }

    public function edit(Request $request, HouseholdItem $householdItem): View
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);

        return view('household-items.edit', [
            'item' => $householdItem,
            'environments' => Environment::query()->where('is_active', true)->orderBy('display_order')->get(),
            'selectedEnvironment' => $householdItem->environment,
        ]);
    }

    public function update(UpdateHouseholdItemRequest $request, HouseholdItem $householdItem): RedirectResponse
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);

        $this->householdItemService->update($householdItem, $request->validated());

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item doméstico atualizado com sucesso.');
    }

    public function destroy(Request $request, HouseholdItem $householdItem): RedirectResponse
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);

        $this->householdItemService->delete($householdItem);

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item doméstico removido com sucesso.');
    }
}
