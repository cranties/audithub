<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ $users->count() }} {{ Str::plural('user', $users->count()) }} registered</p>
            </div>

            {{-- New user button — opens Alpine modal --}}
            <button type="button" @click="$dispatch('open-create-modal')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add User
            </button>
        </div>

        {{-- ── Generated-password banner ─────────────────────────────────── --}}
        @if ($generatedPassword)
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-800">Password reset successfully</p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Share this password with the user — it will be hidden once dismissed:
                    </p>
                    <code class="inline-block mt-1.5 px-3 py-1 bg-amber-100 rounded-lg text-sm font-mono text-amber-900 tracking-wider select-all">
                        {{ $generatedPassword }}
                    </code>
                </div>
                <button type="button" wire:click="dismissPassword"
                    class="text-amber-400 hover:text-amber-600 transition shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- ── Users table ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            @if ($users->isEmpty())
                <div class="py-16 text-center text-slate-400 text-sm">No users registered yet.</div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Created At</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $user->name }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                                <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button type="button"
                                        wire:click="resetPassword({{ $user->id }})"
                                        wire:confirm="Reset password for {{ $user->name }}?"
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                                        Reset password
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- ── Create user modal ────────────────────────────────────────────── --}}
    <div
        x-data="{ open: false }"
        x-on:open-create-modal.window="open = true"
        x-on:user-created.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="open = false"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-6"
             @click.stop>

            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-slate-800">Add User</h2>
                <button type="button" @click="open = false"
                    class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="createUser" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                    <input type="text" wire:model="name" placeholder="Jane Smith"
                        class="w-full border-slate-300 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                    <input type="email" wire:model="email" placeholder="jane@example.com"
                        class="w-full border-slate-300 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-slate-400">
                    A random password will be assigned. Use &ldquo;Reset Password&rdquo; on the user row to reveal it in plain text.
                </p>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition">
                        Create User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
