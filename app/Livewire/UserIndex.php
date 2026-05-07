<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';

    public ?int $resettingId = null;
    public ?string $lastResetPassword = null;

    public function mount(): void
    {
        if (! auth()->user()?->isAdmin()) {
            throw new AuthorizationException();
        }
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }

    public function resetPassword(int $id): void
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);

        $newPassword = Str::password(12, true, true, false);
        $user->update(['password' => Hash::make($newPassword)]);

        $this->resettingId = $id;
        $this->lastResetPassword = $newPassword;

        $this->dispatch('user-password-reset');
    }

    public function dismissPassword(): void
    {
        $this->resettingId = null;
        $this->lastResetPassword = null;
    }

    public function toggleDisabled(int $id): void
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return; // can't disable self
        }

        $user->update(['is_disabled' => ! $user->is_disabled]);
    }

    public function disableTwoFactor(int $id): void
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function resetWebauthn(int $id): void
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);

        // Hard delete all credentials for this user. Used by an admin when a
        // user has lost all their devices and cannot recover. After this the
        // user falls back to password (and TOTP if enabled) until they
        // register a new passkey.
        $user->webAuthnCredentials()->delete();
    }

    public function changeRole(int $id, string $role): void
    {
        $this->ensureAdmin();

        if (! in_array($role, [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_USER], true)) {
            return;
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->id() && $role !== User::ROLE_ADMIN) {
            return; // can't demote self
        }

        $user->update(['role' => $role]);
    }

    public function deleteUser(int $id): void
    {
        $this->ensureAdmin();

        if ($id === auth()->id()) {
            return;
        }

        User::findOrFail($id)->delete();
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->isAdmin()) {
            throw new AuthorizationException();
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$this->search}%")
                   ->orWhere('username', 'like', "%{$this->search}%")
                   ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.user-index', compact('users'));
    }
}
