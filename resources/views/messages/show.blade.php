{{-- resources/views/messages/show.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- Header conversation --}}
        <div class="bg-white rounded-2xl border border-gray-100 mb-4 p-4 flex items-center gap-4">
            <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <img src="{{ $autreParticipant->photo_profil_url }}"
                 class="w-10 h-10 rounded-full object-cover">

            <div class="flex-1">
                <div class="font-semibold text-gray-900">{{ $autreParticipant->nom_complet }}</div>
                <a href="{{ route('annonces.show', $annonce->slug) }}"
                   class="text-xs text-green-600 hover:underline truncate block">
                    🌱 {{ $annonce->titre }}
                </a>
            </div>

            {{-- Photo de l'annonce --}}
            @if($annonce->photos)
                <a href="{{ route('annonces.show', $annonce->slug) }}">
                    <img src="{{ asset('storage/' . $annonce->photos[0]) }}"
                         class="w-12 h-12 rounded-xl object-cover">
                </a>
            @endif

            {{-- Prix annonce --}}
            <div class="text-right">
                <div class="font-bold text-green-600">{{ number_format($annonce->prix, 2) }}€</div>
                <div class="text-xs text-gray-400">/ {{ $annonce->unite_prix }}</div>
            </div>

            {{-- Ajouter au panier --}}
            @if($annonce->isDisponible() && $annonce->user_id !== Auth::id())
                <a href="{{ route('annonces.show', $annonce->slug) }}"
                   class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-semibold transition">
                    Commander
                </a>
            @endif
        </div>

        {{-- Zone messages --}}
        <div class="bg-white rounded-2xl border border-gray-100 flex flex-col"
             style="height: calc(100vh - 300px);">

            {{-- Messages --}}
            <div id="messages-container"
                 class="flex-1 overflow-y-auto p-4 space-y-3 scroll-smooth">

                @forelse($messages as $message)
                    @include('messages.partials.message', ['message' => $message])
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-4xl mb-3">💬</div>
                        <p class="text-sm">Envoyez le premier message !</p>
                    </div>
                @endforelse

                {{-- Ancre pour scroll auto --}}
                <div id="messages-bottom"></div>
            </div>

            {{-- Zone de saisie --}}
            <div class="border-t border-gray-100 p-4"
                 x-data="messageForm({{ $conversation->id }}, {{ $messages->last()?->id ?? 0 }})">

                {{-- Prévisualisation images --}}
                <div id="images-preview" class="hidden flex gap-2 mb-3 overflow-x-auto pb-1"></div>

                <form @submit.prevent="envoyer" class="flex gap-3 items-end">
                    {{-- Bouton image --}}
                    <label class="flex-shrink-0 cursor-pointer">
                        <input type="file" id="images-input" multiple accept="image/*"
                               class="hidden" @change="previewImages($event)">
                        <div class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center text-gray-400 hover:border-green-400 hover:text-green-500 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </label>

                    {{-- Input texte --}}
                    <div class="flex-1 relative">
                        <textarea
                            x-model="contenu"
                            @keydown.enter.prevent="!$event.shiftKey && envoyer()"
                            id="message-input"
                            rows="1"
                            placeholder="Écrire un message... (Entrée pour envoyer)"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none"
                            style="max-height: 120px;"
                            @input="autoResize($event.target)"
                        ></textarea>
                    </div>

                    {{-- Bouton envoyer --}}
                    <button type="submit"
                        :disabled="envoi"
                        class="flex-shrink-0 w-10 h-10 bg-green-600 hover:bg-green-700 text-white rounded-xl flex items-center justify-center transition disabled:opacity-50">
                        <svg x-show="!envoi" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg x-show="envoi" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </button>
                </form>

                {{-- Erreur --}}
                <p x-show="erreur" x-text="erreur" class="text-red-500 text-xs mt-2"></p>
            </div>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // ── Suppression message via event delegation ─────────────────────────
        document.getElementById('messages-container')?.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-supprimer-message]');
            if (!btn) return;
            if (!confirm('Supprimer ce message ?')) return;
            const id = btn.dataset.supprimerMessage;
            try {
                const res = await fetch(`/messages/message/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF }
                });
                const data = await res.json();
                if (data.success) document.getElementById(`msg-${id}`)?.remove();
            } catch(e) {}
        });

        function messageForm(conversationId, dernierMsgId) {
            return {
                contenu:    '',
                envoi:      false,
                erreur:     '',
                dernierMsgId,
                pollingInterval: null,
                fichiers:   [],

                init() {
                    this.scrollBas();
                    // Polling toutes les 3 secondes
                    this.pollingInterval = setInterval(() => this.polling(), 3000);
                },

                destroy() {
                    clearInterval(this.pollingInterval);
                },

                async envoyer() {
                    if (this.envoi) return;
                    if (!this.contenu.trim() && this.fichiers.length === 0) return;

                     // ✅ Vérif taille max 5Mo par image
    for (const f of this.fichiers) {
        if (f.size > 5 * 1024 * 1024) {
            this.erreur = `"${f.name}" dépasse 5 Mo. Choisissez une image plus petite.`;
            return;
        }
    }
                    this.envoi  = true;
                    this.erreur = '';

                    const formData = new FormData();
                    formData.append('conversation_id', conversationId);
                    if (this.contenu.trim()) formData.append('contenu', this.contenu);
                    this.fichiers.forEach(f => formData.append('images[]', f));

                    try {
                        const res = await fetch('{{ route("messages.envoyer") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF },
                            body:   formData,
                        });
                        const data = await res.json();

                        if (data.success) {
                            document.getElementById('messages-container')
                                .insertAdjacentHTML('beforeend', data.message);
                            this.dernierMsgId = data.id;
                            this.contenu      = '';
                            this.fichiers     = [];
                            document.getElementById('images-preview').innerHTML = '';
                            document.getElementById('images-preview').classList.add('hidden');
                            document.getElementById('images-input').value = '';
                            document.getElementById('message-input').style.height = 'auto';
                            this.scrollBas();
                        } else {
                            this.erreur = data.message;
                        }
                    } catch(e) {
                        console.error('Erreur envoi:', e);
                        this.erreur = 'Erreur réseau. Réessayez.';
                    } finally {
                        this.envoi = false;
                    }
                },

                async polling() {
                    try {
                        const res = await fetch(
                            `{{ route("messages.polling", $conversation->id) }}?depuis_id=${this.dernierMsgId}`,
                            { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }
                        );
                        const data = await res.json();

                        if (data.success && data.html) {
                            document.getElementById('messages-container')
                                .insertAdjacentHTML('beforeend', data.html);
                            this.dernierMsgId = data.dernier_id;
                            this.scrollBas();
                        }
                    } catch(e) {}
                },

                previewImages(event) {
                    this.fichiers = Array.from(event.target.files);
                    const preview = document.getElementById('images-preview');
                    preview.innerHTML = '';
                    preview.classList.remove('hidden');

                    this.fichiers.forEach((f, i) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            preview.insertAdjacentHTML('beforeend', `
                                <div class="relative flex-shrink-0">
                                    <img src="${e.target.result}" class="w-16 h-16 rounded-xl object-cover">
                                    <button type="button" onclick="removeImage(${i})"
                                        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">×</button>
                                </div>
                            `);
                        };
                        reader.readAsDataURL(f);
                    });
                },

                autoResize(el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
                },

                scrollBas() {
                    this.$nextTick(() => {
                        const el = document.getElementById('messages-bottom');
                        el?.scrollIntoView({ behavior: 'smooth' });
                    });
                }
            };
        }

        // ── Supprimer un message ─────────────────────────────────────────────
        async function supprimerMessage(id) {
            if (!confirm('Supprimer ce message ?')) return;
            try {
                const res = await fetch(`/messages/message/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById(`msg-${id}`)?.remove();
                }
            } catch(e) { console.error(e); }
        }
    </script>
</x-app-layout>
