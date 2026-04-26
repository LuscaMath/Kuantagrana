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

        $redirectUrl = $this->resolveRedirectUrl(url()->previous());

        return Redirect::to($redirectUrl)->with('status', 'Tutorial ocultado. Voce pode reabrir quando quiser.');
    }

    private function resolveRedirectUrl(string $previousUrl): string
    {
        $parts = parse_url($previousUrl);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return route('dashboard');
        }

        $query = [];

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            unset($query['tutorial']);
        }

        $path = $parts['path'] ?? '';
        $rebuiltUrl = $parts['scheme'].'://'.$parts['host'];

        if (isset($parts['port'])) {
            $rebuiltUrl .= ':'.$parts['port'];
        }

        $rebuiltUrl .= $path;

        if ($query !== []) {
            $rebuiltUrl .= '?'.http_build_query($query);
        }

        return $rebuiltUrl;
    }
}
