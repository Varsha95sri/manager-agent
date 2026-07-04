<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2" id="login-title">Welcome Back</h2>
        <p class="text-center text-gray-500 text-sm mb-6" id="login-subtitle">Please select your portal to continue</p>
        
        <!-- Custom Tabs -->
        <div class="flex p-1 space-x-1 bg-gray-100/80 rounded-xl mb-8" style="background-color: #f3f4f6; border-radius: 0.75rem; padding: 0.25rem; display: flex; gap: 0.25rem;">
            <button type="button" onclick="switchTab('user')" id="tab-user" class="w-full py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 bg-white text-indigo-600 shadow ring-1 ring-black/5" style="flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; cursor: pointer;">
                <div class="flex items-center justify-center gap-2" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <span>Employee Portal</span>
                </div>
            </button>
            <button type="button" onclick="switchTab('admin')" id="tab-admin" class="w-full py-2.5 text-sm font-medium text-gray-500 rounded-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50" style="flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: #6b7280;">
                <div class="flex items-center justify-center gap-2" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Manager / Admin</span>
                </div>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf
        <input type="hidden" name="login_type" id="login_type" value="user">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Log in to Account
            </button>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                    Register here
                </a>
            </p>
        </div>
    </form>

    <script>
        function switchTab(type) {
            const userTab = document.getElementById('tab-user');
            const adminTab = document.getElementById('tab-admin');
            const loginType = document.getElementById('login_type');
            
            // Base inactive styles
            const inactiveClass = "w-full py-2.5 text-sm font-medium text-gray-500 rounded-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50";
            const inactiveStyle = "flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: #6b7280; box-shadow: none;";
            
            // Base active styles
            const activeClass = "w-full py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 bg-white text-indigo-600 shadow ring-1 ring-black/5";
            const activeStyle = "flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; cursor: pointer; background: white; color: #4f46e5; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.05);";

            // Reset both to inactive
            userTab.className = inactiveClass;
            userTab.style.cssText = inactiveStyle;
            adminTab.className = inactiveClass;
            adminTab.style.cssText = inactiveStyle;
            
            // Add active classes to selected
            if (type === 'user') {
                userTab.className = activeClass;
                userTab.style.cssText = activeStyle;
                loginType.value = 'user';
            } else {
                adminTab.className = activeClass;
                adminTab.style.cssText = activeStyle;
                loginType.value = 'admin';
            }
        }
    </script>
</x-guest-layout>
