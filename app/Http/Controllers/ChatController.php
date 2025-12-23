<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Menampilkan daftar chat
    public function index()
    {
        $chats = Chat::where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->latest()
            ->get();
        return view('chats.index', compact('chats'));
    }

    // Kirim pesan baru
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Chat::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Pesan terkirim!');
    }
}