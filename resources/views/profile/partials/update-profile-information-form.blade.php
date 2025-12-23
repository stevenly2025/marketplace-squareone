@php
    /** @var \App\Models\User $user */
@endphp

<section class="bg-[#3C4142] p-5 sm:p-8 rounded-[2rem] shadow-2xl border border-white/5 font-sans relative overflow-hidden">
    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-white/[0.02] rounded-full blur-3xl"></div>

    <header class="mb-8 relative z-10">
        <h2 class="text-xl font-bold text-white tracking-tight">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-white/50">
            {{ __("Perbarui foto profil, detail akun, alamat pengiriman, dan informasi kontak Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8 relative z-10" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="bg-white/[0.03] p-6 rounded-2xl border border-white/10 shadow-inner">
            <x-input-label for="avatar" :value="__('Foto Profil')" class="text-white/80 font-bold mb-4" />
            
            <div class="flex flex-col sm:flex-row items-center gap-8 mt-3">
                <div class="relative flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                             class="w-24 h-24 rounded-2xl object-cover border-2 border-white/10 shadow-2xl transition duration-500 hover:scale-105"
                             alt="Avatar {{ $user->name }}">
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-[#4a5153] flex items-center justify-center text-white/20 font-black text-4xl shadow-xl border-2 border-white/10">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <div class="absolute -bottom-2 -right-2 bg-white rounded-lg px-2 py-0.5 shadow-lg border border-gray-100">
                        <span class="text-[9px] font-black uppercase tracking-widest {{ $user->role === 'seller' ? 'text-orange-600' : 'text-blue-600' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
                
                <div class="flex-1 w-full space-y-3">
                    <input id="avatar" 
                           name="avatar" 
                           type="file" 
                           accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="block w-full text-xs text-white/40
                                  file:mr-4 file:py-2.5 file:px-6
                                  file:rounded-xl file:border-0
                                  file:text-xs file:font-black file:uppercase
                                  file:bg-white file:text-[#3C4142]
                                  file:shadow-md hover:file:bg-gray-200
                                  file:transition file:cursor-pointer
                                  cursor-pointer border-2 border-dashed border-white/10 rounded-xl p-3 bg-white/5
                                  hover:border-white/20 transition"/>
                    
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-white/30 font-medium flex items-center gap-1 uppercase tracking-tighter">
                            <span>📸</span> Format: JPG, PNG, GIF (Max. 2MB)
                        </p>
                        
                        @if($user->avatar)
                            <a href="{{ route('profile.avatar.delete') }}" 
                               onclick="event.preventDefault(); if(confirm('Hapus foto profil?')) document.getElementById('delete-avatar-form').submit();"
                               class="text-[10px] text-red-400/60 hover:text-red-400 font-bold transition uppercase tracking-widest">
                                🗑️ Hapus Foto
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-white/60 ml-1 text-xs uppercase tracking-widest" />
                <input id="name" name="name" type="text" class="block w-full bg-white/[0.03] border-white/10 rounded-xl text-sm text-white py-3 px-4 focus:ring-1 focus:ring-white/20 focus:border-white/30 transition placeholder-white/10" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email')" class="text-white/60 ml-1 text-xs uppercase tracking-widest" />
                <input id="email" name="email" type="email" class="block w-full bg-white/[0.03] border-white/10 rounded-xl text-sm text-white py-3 px-4 focus:ring-1 focus:ring-white/20 focus:border-white/30 transition placeholder-white/10" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="p-3 bg-yellow-400/5 border border-yellow-400/10 rounded-xl mt-2">
                        <p class="text-xs text-yellow-400/80">
                            {{ __('Email belum diverifikasi.') }}
                            <button form="send-verification" class="font-bold underline hover:text-yellow-300">
                                {{ __('Kirim Ulang') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <x-input-label for="phone" :value="__('Nomor WhatsApp / HP')" class="text-white/60 ml-1 text-xs uppercase tracking-widest" />
            <input id="phone" name="phone" type="text" class="block w-full bg-white/[0.03] border-white/10 rounded-xl text-sm text-white py-3 px-4 focus:ring-1 focus:ring-white/20 focus:border-white/30 transition placeholder-white/10" value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 08123456789" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="city" :value="__('Kota / Kabupaten Domisili')" class="text-white/60 ml-1 text-xs uppercase tracking-widest" />
            <div class="modern-select-container">
                <select id="select-city" name="city" class="mt-1 block w-full" placeholder="Ketik nama kota anda..." autocomplete="off">
                    <option value="">Pilih Kota...</option>
                    @foreach(\App\Models\City::orderBy('name')->get() as $city)
                        <option value="{{ $city->name }}" {{ old('city', $user->city) == $city->name ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-white/60 ml-1 text-xs uppercase tracking-widest" />
            <textarea id="address" name="address" rows="3" class="block w-full bg-white/[0.03] border-white/10 rounded-xl text-sm text-white py-3 px-4 focus:ring-1 focus:ring-white/20 focus:border-white/30 transition resize-none placeholder-white/10" required>{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        @if($user->role === 'seller')
            <div class="p-6 bg-gradient-to-br from-orange-500/[0.05] to-transparent border border-orange-500/20 rounded-2xl space-y-4">
                <x-input-label for="vendor_payment_info" :value="__('Upload QRIS Toko')" class="text-orange-400 font-black text-xs uppercase tracking-[0.2em]" />
                <input type="file" 
                       name="vendor_payment_info" 
                       id="vendor_payment_info"
                       accept="image/jpeg,image/png,image/jpg"
                       class="block w-full text-xs text-white/40
                              file:mr-4 file:py-2 file:px-6
                              file:rounded-xl file:border-0
                              file:text-[10px] file:font-black file:uppercase
                              file:bg-orange-500 file:text-white
                              hover:file:bg-orange-600 transition
                              cursor-pointer border-2 border-dashed border-orange-500/10 rounded-xl p-3 bg-white/5"/>
                
                @if($user->vendor_payment_info)
                    <div class="mt-4 flex flex-col items-center p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[10px] text-green-400 font-bold mb-3 uppercase">✓ QRIS Tersimpan</p>
                        <img src="{{ asset('storage/' . $user->vendor_payment_info) }}" 
                             class="h-40 w-auto border-2 border-white/10 rounded-xl shadow-2xl">
                    </div>
                @else
                    <div class="bg-red-400/5 border border-red-400/10 p-3 rounded-xl">
                        <p class="text-[10px] text-red-400 font-bold leading-tight uppercase tracking-tighter">
                            ⚠️ Upload QRIS agar pembeli dapat membayar ke toko Anda!
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center gap-6 pt-6 border-t border-white/5">
            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-white text-[#3C4142] font-black uppercase tracking-widest text-xs rounded-xl hover:bg-gray-100 transition active:scale-95 shadow-xl">
                {{ __('Simpan Perubahan') }}
            </button>

            <div class="flex items-center">
                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                       class="text-xs font-bold text-green-400 bg-green-400/10 px-4 py-2 rounded-full border border-green-400/20">
                        ✅ {{ __('Tersimpan!') }}
                    </p>
                @endif

                @if (session('status') === 'avatar-deleted')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                       class="text-xs font-bold text-red-400 bg-red-400/10 px-4 py-2 rounded-full border border-red-400/20">
                        🗑️ {{ __('Foto Dihapus!') }}
                    </p>
                @endif
            </div>
        </div>
    </form>

    <form id="delete-avatar-form" action="{{ route('profile.avatar.delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</section>

<style>
    .ts-control { background: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; border-radius: 0.75rem !important; color: white !important; padding: 0.75rem !important; }
    .ts-dropdown { background: #2b2f30 !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: white !important; border-radius: 0.75rem !important; }
    .ts-dropdown .active { background: rgba(255, 255, 255, 0.1) !important; }
    .ts-control input::placeholder { color: rgba(255, 255, 255, 0.1) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.TomSelect) {
            new TomSelect("#select-city", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: "Ketik nama kota...",
                plugins: ['dropdown_input'],
            });
        }
    });
</script>