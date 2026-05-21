<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class UserManagement extends Component
{
    // ── Create form ────────────────────────────────────────────────────────
    public string $name = '';
    public string $email = '';

    // ── Reset-password display ─────────────────────────────────────────────
    public ?string $generatedPassword = null;
    public ?int $resetUserId = null;

    // ── Validation rules ───────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ];
    }

    // ── Actions ────────────────────────────────────────────────────────────

    public function createUser(): void
    {
        $this->validate();

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make(Str::password(12)),
        ]);

        $this->reset('name', 'email');
        $this->dispatch('user-created');
    }

    public function resetPassword(int $userId): void
    {
        $user = User::findOrFail($userId);

        $plain = Str::password(12);

        $user->update(['password' => Hash::make($plain)]);

        $this->generatedPassword = $plain;
        $this->resetUserId = $userId;
    }

    public function dismissPassword(): void
    {
        $this->generatedPassword = null;
        $this->resetUserId = null;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => User::latest()->get(),
        ])->layout('layouts.app');
    }
}
