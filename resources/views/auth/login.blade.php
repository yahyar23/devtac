<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <div class="text-4xl font-bold text-purple-600 italic">سجل دخولك</div>
            </a>
        </x-slot>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}" dir="rtl">
            @csrf

            <div>
                <x-label for="email" :value="__('رقم الهاتف')" class="text-right" />

                <x-input id="email" class="block mt-1 w-full text-right" 
                         type="text" 
                         name="email" 
                         :value="old('email')" 
                         placeholder="07xxxxxxxx"
                         required autofocus />
            </div>

            <div class="mt-4">
                <x-label for="password" :value="__('كلمة المرور')" class="text-right" />

                <x-input id="password" class="block mt-1 w-full text-right"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <div class="block mt-4 flex justify-end">
                <label for="remember_me" class="inline-flex items-center">
                    <span class="mr-2 text-sm text-gray-600">{{ __('تذكرني') }}</span>
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50" name="remember">
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-purple-600" href="{{ route('password.request') }}">
                        {{ __('نسيت كلمة المرور؟') }}
                    </a>
                @endif

                <x-button class="ml-3 bg-purple-600 hover:bg-purple-700">
                    {{ __('تسجيل الدخول') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>