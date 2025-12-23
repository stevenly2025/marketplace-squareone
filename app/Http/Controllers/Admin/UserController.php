<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function toggleBan(User $user)
    {
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Admin utama tidak bisa di-ban!');
        }

        $user->update([
            'is_banned' => !$user->is_banned
        ]);

        $status = $user->is_banned ? 'diblokir' : 'diaktifkan kembali';
        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }
}