<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <h2 class="text-xl font-semibold text-slate-800 mb-6">Sign in to your account</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                required autofocus autocomplete="username" placeholder="you@example.com"
                class="w-full border-slate-300 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password"
                required autocomplete="current-password"
                class="w-full border-slate-300 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-400 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember"
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 transition">
                    Forgot your password?
                </a>
            @endif
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Log in
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-slate-600 transition">← Back to home</a>
    </p>
</x-guest-layout>
