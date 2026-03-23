{{-- resources/views/components/notification-bell.blade.php --}}
{{-- À insérer dans layouts/app.blade.php AVANT le panier --}}

@auth
<div class="relative" x-data="notifBell()" x-init="init()">

    {{-- Bouton cloche --}}
    <button @click="toggle()" @click.outside="open = false"
        class="relative p-2 text-gray-500 hover:text-green-600 transition">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        {{-- Badge --}}
        <span x-show="nbNonLus > 0" x-cloak
            class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"
            x-text="nbNonLus > 9 ? '9+' : nbNonLus">
        </span>
    </button>

    {{-- Dropdown notifications --}}
    <div x-show="open" x-transition x-cloak
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="font-semibold text-gray-900 text-sm">Notifications</span>
            <button @click="toutLire()" x-show="nbNonLus > 0"
                class="text-xs text-green-600 hover:underline">
                Tout marquer comme lu
            </button>
        </div>

        {{-- Liste --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            <template x-if="notifications.length === 0">
                <div class="text-center py-8 text-gray-400">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-sm">Aucune notification</p>
                </div>
            </template>

            <template x-for="notif in notifications" :key="notif.id">
                <a :href="notif.url"
                   @click="marquerLu(notif.id)"
                   :class="notif.lu ? 'bg-white' : 'bg-green-50'"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition block">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900" x-text="notif.titre"></div>
                        <div class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="notif.message"></div>
                        <div class="text-xs text-gray-400 mt-1" x-text="notif.date"></div>
                    </div>
                    <div x-show="!notif.lu" class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0 mt-1.5"></div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 px-4 py-2.5">
            <a href="{{ route('notifications.index') }}"
               class="text-xs text-green-600 hover:underline font-medium">
                Voir toutes les notifications →
            </a>
        </div>
    </div>
</div>

<script>
function notifBell() {
    return {
        open: false,
        nbNonLus: 0,
        notifications: [],
        pollingInterval: null,

        init() {
            this.charger();
            // Polling toutes les 30 secondes
            this.pollingInterval = setInterval(() => this.charger(), 30000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.charger();
        },

        async charger() {
            try {
                const res  = await fetch('{{ route("notifications.dernieres") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.notifications = data.notifications;
                this.nbNonLus      = data.nb_non_lus;
            } catch(e) {}
        },

        async toutLire() {
            await fetch('{{ route("notifications.tout-lire") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            this.nbNonLus = 0;
            this.notifications = this.notifications.map(n => ({ ...n, lu: true }));
        },

        async marquerLu(id) {
            // Juste mettre à jour localement, la redirection fait le reste
            this.notifications = this.notifications.map(n =>
                n.id === id ? { ...n, lu: true } : n
            );
            this.nbNonLus = Math.max(0, this.nbNonLus - 1);
        },
    };
}
</script>
@endauth
