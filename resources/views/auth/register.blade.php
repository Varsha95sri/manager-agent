<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2" id="register-title">Create Account</h2>
            <p class="text-center text-gray-500 text-sm mb-6" id="register-subtitle">Please select your role to register</p>
            
            <!-- Custom Tabs -->
            <div class="flex p-1 space-x-1 bg-gray-100/80 rounded-xl mb-4" style="background-color: #f3f4f6; border-radius: 0.75rem; padding: 0.25rem; display: flex; gap: 0.25rem;">
                <button type="button" onclick="switchRoleTab('employee')" id="tab-employee" class="w-full py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 bg-white text-indigo-600 shadow ring-1 ring-black/5" style="flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; cursor: pointer; background: white; color: #4f46e5; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.05);">
                    <div class="flex items-center justify-center gap-2" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        <span>Employee Portal</span>
                    </div>
                </button>
                <button type="button" onclick="switchRoleTab('manager')" id="tab-manager" class="w-full py-2.5 text-sm font-medium text-gray-500 rounded-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50" style="flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: #6b7280; box-shadow: none;">
                    <div class="flex items-center justify-center gap-2" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Manager / Admin</span>
                    </div>
                </button>
            </div>
            <input type="hidden" name="role" id="role_type" value="employee">
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function switchRoleTab(type) {
            const employeeTab = document.getElementById('tab-employee');
            const managerTab = document.getElementById('tab-manager');
            const roleType = document.getElementById('role_type');
            
            // Base inactive styles
            const inactiveClass = "w-full py-2.5 text-sm font-medium text-gray-500 rounded-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50";
            const inactiveStyle = "flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: #6b7280; box-shadow: none;";
            
            // Base active styles
            const activeClass = "w-full py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 bg-white text-indigo-600 shadow ring-1 ring-black/5";
            const activeStyle = "flex: 1; padding: 0.625rem 0; border-radius: 0.5rem; border: none; cursor: pointer; background: white; color: #4f46e5; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.05);";

            // Reset both to inactive
            employeeTab.className = inactiveClass;
            employeeTab.style.cssText = inactiveStyle;
            managerTab.className = inactiveClass;
            managerTab.style.cssText = inactiveStyle;
            
            // Add active classes to selected
            if (type === 'employee') {
                employeeTab.className = activeClass;
                employeeTab.style.cssText = activeStyle;
                roleType.value = 'employee';
            } else {
                managerTab.className = activeClass;
                managerTab.style.cssText = activeStyle;
                roleType.value = 'manager';
            }
        }
    </script>
</x-guest-layout>

