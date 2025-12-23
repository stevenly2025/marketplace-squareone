<x-guest-layout>
    {{-- Header Khusus Seller --}}
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-slate-800">Mulai Berjualan</h2>
        <p class="text-sm text-slate-500">Gabung di Seller Centre SquareOne</p>
    </div>

    <form method="POST" action="{{ route('seller.register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Toko / Penjual')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Contoh: Toko Berkah Jaya" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Bisnis')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Nomor WhatsApp / HP')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required placeholder="08..." />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4 bg-blue-600 hover:bg-blue-700">
                {{ __('Buka Toko Sekarang') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>