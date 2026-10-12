<x-layouts::admin title="Sign in">
    <div class="max-w-sm mx-auto mt-10">
        <h1 class="text-2xl font-black mb-6">Sign in</h1>

        <form method="POST" action="{{ route('admin.login') }}" class="bg-white border border-border rounded-lg p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1.5" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       autocomplete="username" required autofocus
                       @invalid('email')
                       class="field">
                <x-form-error field="email" />
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" for="password">Password</label>
                <input id="password" name="password" type="password"
                       autocomplete="current-password" required
                       @invalid('password')
                       class="field">
                <x-form-error field="password" />
            </div>

            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="remember" value="1" class="accent-brand">
                Keep me signed in
            </label>

            <button type="submit"
                    class="w-full bg-brand hover:bg-brand-dark text-white font-semibold px-6 py-3 rounded transition-colors">
                Sign in
            </button>
        </form>
    </div>
</x-layouts::admin>
