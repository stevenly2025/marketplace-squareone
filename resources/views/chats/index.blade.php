<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight italic">💬 Pesan SquareOne</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[70vh]">
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @forelse($chats as $chat)
                <div class="flex {{ $chat->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $chat->sender_id === auth()->id() ? 'bg-blue-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-slate-100 text-slate-800 rounded-r-2xl rounded-tl-2xl' }} p-4 shadow-sm">
                        <p class="text-sm">{{ $chat->message }}</p>
                        <p class="text-[8px] mt-1 opacity-70 uppercase font-black">{{ $chat->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-400 mt-20 italic">Belum ada percakapan.</p>
            @endforelse
        </div>

        <div class="p-4 bg-slate-50 border-t">
            <form action="{{ route('chats.store') }}" method="POST" class="flex space-x-2">
                @csrf
                <input type="hidden" name="receiver_id" value="1"> <input type="text" name="message" placeholder="Tulis pesan..." class="flex-1 rounded-full border-slate-200 text-sm px-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-full font-black text-xs uppercase tracking-widest">Kirim</button>
            </form>
        </div>
    </div>
</x-app-layout>