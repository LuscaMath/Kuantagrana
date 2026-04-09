<?php

namespace App\Http\Controllers;

use App\Services\EnvironmentExperienceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnvironmentController extends Controller
{
    public function __construct(
        private readonly EnvironmentExperienceService $environmentExperienceService,
    ) {
    }

    public function index(Request $request): View
    {
        return view('environments.index', $this->environmentExperienceService->getMapData($request->user()));
    }

    public function show(Request $request, string $slug): View
    {
        return view('environments.show', $this->environmentExperienceService->getEnvironmentPageData($request->user(), $slug));
    }
}
