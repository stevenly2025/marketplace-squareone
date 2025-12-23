<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1️⃣ Data utama (otomatis dari ProfileUpdateRequest)
        $user->fill($request->validated());

        // 2️⃣ Field tambahan marketplace
        $user->phone = $request->phone;
        $user->city = $request->city;
        $user->address = $request->address;

        // 3️⃣ Upload AVATAR (Foto Profil)
        if ($request->hasFile('avatar')) {
            // Validasi file gambar
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Hapus foto lama jika ada (biar server gak penuh)
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 4️⃣ Upload QRIS (khusus seller)
        if ($request->hasFile('vendor_payment_info')) {
            // Validasi file gambar QRIS
            $request->validate([
                'vendor_payment_info' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Hapus QRIS lama jika ada
            if ($user->vendor_payment_info) {
                Storage::disk('public')->delete($user->vendor_payment_info);
            }

            // Simpan QRIS baru
            $path = $request->file('vendor_payment_info')->store('qris', 'public');
            $user->vendor_payment_info = $path;
        }

        // 5️⃣ Reset verifikasi email jika berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus avatar jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Hapus QRIS jika ada
        if ($user->vendor_payment_info) {
            Storage::disk('public')->delete($user->vendor_payment_info);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Delete avatar only (tanpa hapus akun)
     * 🔥 Method ini dipanggil dari route 'profile.avatar.delete'
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            // Hapus file dari storage
            Storage::disk('public')->delete($user->avatar);
            
            // Set kolom avatar jadi null
            $user->forceFill([
                'avatar' => null,
            ])->save();

            return Redirect::route('profile.edit')->with('status', 'avatar-deleted');
        }

        return Redirect::route('profile.edit')->with('error', 'Tidak ada avatar untuk dihapus.');
    }
}