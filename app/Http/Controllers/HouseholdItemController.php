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
    private const ALLOWED_ENVIRONMENT_SLUGS = ['casa', 'mercado', 'farmacia'];

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
            'environments' => Environment::query()
                ->where('is_active', true)
                ->whereIn('slug', self::ALLOWED_ENVIRONMENT_SLUGS)
                ->orderBy('display_order')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedEnvironmentId = $request->integer('environment_id') ?: null;
        $selectedEnvironment = $selectedEnvironmentId
            ? Environment::query()->whereKey($selectedEnvironmentId)->first()
            : null;

        if ($selectedEnvironment && ! in_array($selectedEnvironment->slug, self::ALLOWED_ENVIRONMENT_SLUGS, true)) {
            abort(404);
        }

        return view('household-items.create', [
            'item' => new HouseholdItem([
                'unit' => 'un',
                'quantity' => 0,
                'minimum_quantity' => 0,
                'is_active' => true,
                'environment_id' => $selectedEnvironmentId,
            ]),
            'environments' => Environment::query()
                ->where('is_active', true)
                ->whereIn('slug', self::ALLOWED_ENVIRONMENT_SLUGS)
                ->orderBy('display_order')
                ->get(),
            'selectedEnvironment' => $selectedEnvironment,
        ]);
    }

    public function store(StoreHouseholdItemRequest $request): RedirectResponse
    {
        $this->ensureEnvironmentSupportsItems($request->input('environment_id'));
        $this->householdItemService->create($request->user(), $request->validated());

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item domestico cadastrado com sucesso.');
    }

    public function edit(Request $request, HouseholdItem $householdItem): View
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);

        return view('household-items.edit', [
            'item' => $householdItem,
            'environments' => Environment::query()
                ->where('is_active', true)
                ->whereIn('slug', self::ALLOWED_ENVIRONMENT_SLUGS)
                ->orderBy('display_order')
                ->get(),
            'selectedEnvironment' => $householdItem->environment,
        ]);
    }

    public function update(UpdateHouseholdItemRequest $request, HouseholdItem $householdItem): RedirectResponse
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);
        $this->ensureEnvironmentSupportsItems($request->input('environment_id'));

        $this->householdItemService->update($householdItem, $request->validated());

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item domestico atualizado com sucesso.');
    }

    public function destroy(Request $request, HouseholdItem $householdItem): RedirectResponse
    {
        abort_if($householdItem->user_id !== $request->user()->id, 403);

        $this->householdItemService->delete($householdItem);

        return redirect()
            ->route('household-items.index')
            ->with('status', 'Item domestico removido com sucesso.');
    }

    private function ensureEnvironmentSupportsItems(mixed $environmentId): void
    {
        if (! $environmentId) {
            return;
        }

        $environment = Environment::query()->findOrFail($environmentId);

        abort_unless(in_array($environment->slug, self::ALLOWED_ENVIRONMENT_SLUGS, true), 404);
    }
}
