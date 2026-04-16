<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalContributionRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Environment;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    private const ALLOWED_ENVIRONMENT_SLUG = 'parque-de-diversoes';

    public function __construct(
        private readonly GoalService $goalService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['environment_id']);

        return view('goals.index', [
            'goals' => $this->goalService->getPaginatedForUser($request->user(), $filters),
            'filters' => [
                'environment_id' => $filters['environment_id'] ?? '',
            ],
            'environments' => Environment::query()
                ->where('is_active', true)
                ->where('slug', self::ALLOWED_ENVIRONMENT_SLUG)
                ->orderBy('display_order')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedEnvironmentId = $request->integer('environment_id') ?: null;
        $selectedEnvironment = $selectedEnvironmentId
            ? Environment::query()->whereKey($selectedEnvironmentId)->first()
            : Environment::query()->where('slug', self::ALLOWED_ENVIRONMENT_SLUG)->first();

        if (! $selectedEnvironment || $selectedEnvironment->slug !== self::ALLOWED_ENVIRONMENT_SLUG) {
            abort(404);
        }

        return view('goals.create', [
            'goal' => new Goal([
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'environment_id' => $selectedEnvironment->id,
            ]),
            'environments' => Environment::query()
                ->where('is_active', true)
                ->where('slug', self::ALLOWED_ENVIRONMENT_SLUG)
                ->orderBy('display_order')
                ->get(),
            'selectedEnvironment' => $selectedEnvironment,
        ]);
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $this->ensureEnvironmentSupportsGoals($request->input('environment_id'));
        $this->goalService->create($request->user(), $request->validated());

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta criada com sucesso.');
    }

    public function edit(Request $request, Goal $goal): View
    {
        abort_if($goal->user_id !== $request->user()->id, 403);

        return view('goals.edit', [
            'goal' => $goal->load('contributions'),
            'environments' => Environment::query()
                ->where('is_active', true)
                ->where('slug', self::ALLOWED_ENVIRONMENT_SLUG)
                ->orderBy('display_order')
                ->get(),
            'selectedEnvironment' => $goal->environment,
        ]);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);
        $this->ensureEnvironmentSupportsGoals($request->input('environment_id'));

        $this->goalService->update($goal, $request->validated());

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta atualizada com sucesso.');
    }

    public function destroy(Request $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);

        $this->goalService->delete($goal);

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta removida com sucesso.');
    }

    public function storeContribution(StoreGoalContributionRequest $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);

        $this->goalService->addContribution($goal, $request->validated());

        return redirect()
            ->route('goals.edit', $goal)
            ->with('status', 'Contribuicao registrada com sucesso.');
    }

    private function ensureEnvironmentSupportsGoals(mixed $environmentId): void
    {
        $environment = Environment::query()->findOrFail($environmentId);

        abort_unless($environment->slug === self::ALLOWED_ENVIRONMENT_SLUG, 404);
    }
}
