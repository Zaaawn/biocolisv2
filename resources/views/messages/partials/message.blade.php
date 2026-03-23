{{-- resources/views/messages/partials/message.blade.php --}}
@php $estMoi = $message->sender_id === Auth::id(); @endphp

<div class="flex {{ $estMoi ? 'justify-end' : 'justify-start' }} gap-2 group"
     id="msg-{{ $message->id }}">

    @if(!$estMoi)
        <img src="{{ $message->sender->photo_profil_url }}"
             class="w-7 h-7 rounded-full object-cover flex-shrink-0 self-end">
    @endif

    <div class="max-w-xs lg:max-w-md">

        @if($message->type === 'systeme')
            <div class="text-center my-2">
                <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                    🔔 {{ $message->contenu ?? $message->systeme_type }}
                </span>
            </div>
        @else
            <div class="relative">

                {{-- Images --}}
                @if(!empty($message->images) && count($message->images) > 0)
                    <div class="grid {{ count($message->images) > 1 ? 'grid-cols-2' : 'grid-cols-1' }} gap-1 mb-1">
                        @foreach($message->images as $img)
                            <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                <img src="{{ asset('storage/' . $img) }}"
                                     class="rounded-xl w-full object-cover max-h-48 hover:opacity-90 transition"
                                     loading="lazy">
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Texte --}}
                @if($message->contenu)
                    <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed
                        {{ $estMoi ? 'bg-green-600 text-white rounded-br-sm' : 'bg-gray-100 text-gray-800 rounded-bl-sm' }}">
                        {!! nl2br(e($message->contenu)) !!}
                    </div>
                @endif

                {{-- Heure --}}
                <div class="flex items-center gap-1 mt-1 {{ $estMoi ? 'justify-end' : 'justify-start' }}">
                    <span class="text-xs text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                    @if($estMoi)
                        @if($message->is_read)
                            <span class="text-xs text-green-500">✓✓</span>
                        @else
                            <span class="text-xs text-gray-300">✓</span>
                        @endif
                    @endif
                </div>

                {{-- Supprimer (data-attribute, pas de JS inline) --}}
                @if($estMoi && $message->created_at->diffInMinutes(now()) <= 10)
                    <button data-supprimer-message="{{ $message->id }}"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        ×
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
