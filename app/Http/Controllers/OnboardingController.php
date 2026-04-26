<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OnboardingController extends Controller
{
    public function show(): RedirectResponse
    {
        return Redirect::route('dashboard', ['tutorial' => 1]);
    }

    public function dismiss(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'onboarding_dismissed_at' => now(),
        ])->save();

        return Redirect::back()->with('status', 'Tutorial ocultado. Voce pode reabrir quando quiser.');
    }
}
