<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalContributionRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Environment;
use App\Models\Goal;
use App\Services\GamificationService;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function __construct(
        private readonly GoalService $goalService,
        private readonly GamificationService $gamificationService,
    ) {
    }

    public function index(Request $request): View
    {
        $selectedEnvironment = Environment::query()->active()->supporting('goals')->orderBy('display_order')->firstOrFail();
        $filters = [
            'environment_id' => (string) $selectedEnvironment->id,
        ];

        return view('goals.index', [
            'goals' => $this->goalService->getPaginatedForUser($request->user(), $filters),
            'filters' => $filters,
            'environments' => Environment::query()->active()->supporting('goals')->orderBy('display_order')->get(),
            'selectedEnvironment' => $selectedEnvironment,
        ]);
    }

    public function create(Request $request): RedirectResponse|View
    {
        $selectedEnvironmentId = $request->integer('environment_id') ?: null;
        $selectedEnvironment = $selectedEnvironmentId
            ? Environment::query()->whereKey($selectedEnvironmentId)->first()
            : Environment::query()->active()->supporting('goals')->orderBy('display_order')->first();

        if (! $selectedEnvironment || ! $selectedEnvironment->supportsFeature('goals')) {
            return redirect()
                ->route('goals.index')
                ->with('status', 'As metas sao organizadas dentro do Parque.');
        }

        return view('goals.create', [
            'goal' => new Goal([
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'environment_id' => $selectedEnvironment->id,
            ]),
            'environments' => Environment::query()->active()->supporting('goals')->orderBy('display_order')->get(),
            'selectedEnvironment' => $selectedEnvironment,
        ]);
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $this->ensureEnvironmentSupportsGoals($request->input('environment_id'));
        $snapshot = $this->gamificationService->snapshot($request->user());
        $goal = $this->goalService->create($request->user(), $request->validated());
        $this->gamificationService->flashFeedback($request->session(), $request->user(), $snapshot, 'Meta criada com sucesso.');

        return redirect()
            ->route('goals.index', ['environment_id' => $goal->environment_id])
            ->with('status', 'Meta criada com sucesso.');
    }

    public function edit(Request $request, Goal $goal): View
    {
        abort_if($goal->user_id !== $request->user()->id, 403);

        return view('goals.edit', [
            'goal' => $goal->load('contributions'),
            'environments' => Environment::query()->active()->supporting('goals')->orderBy('display_order')->get(),
            'selectedEnvironment' => $goal->environment,
        ]);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);
        $this->ensureEnvironmentSupportsGoals($request->input('environment_id'));
        $snapshot = $this->gamificationService->snapshot($request->user());

        $goal = $this->goalService->update($goal, $request->validated());
        $this->gamificationService->flashFeedback($request->session(), $request->user(), $snapshot, 'Meta atualizada com sucesso.');

        return redirect()
            ->route('goals.index', ['environment_id' => $goal->environment_id])
            ->with('status', 'Meta atualizada com sucesso.');
    }

    public function destroy(Request $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);
        $environmentId = $goal->environment_id;

        $this->goalService->delete($goal);

        return redirect()
            ->route('goals.index', ['environment_id' => $environmentId])
            ->with('status', 'Meta removida com sucesso.');
    }

    public function storeContribution(StoreGoalContributionRequest $request, Goal $goal): RedirectResponse
    {
        abort_if($goal->user_id !== $request->user()->id, 403);
        $snapshot = $this->gamificationService->snapshot($request->user());

        $this->goalService->addContribution($goal, $request->validated());
        $this->gamificationService->flashFeedback($request->session(), $request->user(), $snapshot, 'Contribuicao registrada com sucesso.');

        return redirect()
            ->route('goals.edit', $goal)
            ->with('status', 'Contribuicao registrada com sucesso.');
    }

    private function ensureEnvironmentSupportsGoals(mixed $environmentId): void
    {
        $environment = Environment::query()->findOrFail($environmentId);

        abort_unless($environment->supportsFeature('goals'), 404);
    }
}
