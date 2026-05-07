<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ImpersonationController extends Controller
{
    public function start(User $user): RedirectResponse
    {
        $actor = Auth::user();

        // Only admins may impersonate
        if (! $actor || ! $actor->isAdmin()) {
            throw new AccessDeniedHttpException('Only admins can impersonate.');
        }

        // No chaining
        if (session()->has('impersonating')) {
            throw new AccessDeniedHttpException('Impersonation chain forbidden.');
        }

        // Cannot impersonate yourself
        if ($user->id === $actor->id) {
            return redirect()->back();
        }

        // Cannot impersonate another admin
        if ($user->isAdmin()) {
            throw new AccessDeniedHttpException('Cannot impersonate another admin.');
        }

        session(['impersonating' => $actor->id]);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function stop(): RedirectResponse
    {
        $originalId = session('impersonating');

        if (! $originalId) {
            return redirect()->route('dashboard');
        }

        session()->forget('impersonating');
        Auth::loginUsingId($originalId);

        return redirect()->route('dashboard');
    }
}
