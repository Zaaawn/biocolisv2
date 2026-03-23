{{-- resources/views/components/signaler-btn.blade.php --}}
@auth
@props(['type', 'id'])
<button onclick="ouvrirSignalement('{{ $type }}', {{ $id }})" type="button"
    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-red-500 transition">
    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
    </svg>
    Signaler
</button>
@endauth
