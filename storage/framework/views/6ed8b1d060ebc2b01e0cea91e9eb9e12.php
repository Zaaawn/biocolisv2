

<?php if(auth()->guard()->check()): ?>
<div id="signalement-modal"
     style="display:none"
     class="fixed inset-0 z-50 items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="fermerSignalement()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900 text-lg">⚠️ Signaler</h3>
            <button onclick="fermerSignalement()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        </div>
        <div id="signalement-succes" style="display:none" class="text-center py-6">
            <div class="text-5xl mb-3">✅</div>
            <p class="font-semibold text-gray-900 mb-1">Signalement envoyé</p>
            <p id="signalement-msg" class="text-sm text-gray-500 mb-4"></p>
            <button onclick="fermerSignalement()" class="px-6 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold">Fermer</button>
        </div>
        <div id="signalement-form-wrapper">
            <div id="signalement-erreur" style="display:none" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm"></div>
            <p class="text-sm text-gray-500 mb-4">Aidez-nous à maintenir la qualité de Biocolis.</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motif *</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                            <input type="radio" name="signalement_motif" value="contenu_inapproprie" class="text-red-500">
                            <span>🚫</span><span class="text-sm text-gray-700">Contenu inapproprié</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                            <input type="radio" name="signalement_motif" value="arnaque" class="text-red-500">
                            <span>💸</span><span class="text-sm text-gray-700">Arnaque ou fraude</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                            <input type="radio" name="signalement_motif" value="faux_produit" class="text-red-500">
                            <span>🥦</span><span class="text-sm text-gray-700">Produit ou description trompeur</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                            <input type="radio" name="signalement_motif" value="spam" class="text-red-500">
                            <span>📨</span><span class="text-sm text-gray-700">Spam ou publicité</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50 transition">
                            <input type="radio" name="signalement_motif" value="autre" class="text-red-500">
                            <span>❓</span><span class="text-sm text-gray-700">Autre</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Détails <span class="text-gray-400 font-normal">(optionnel)</span></label>
                    <textarea id="signalement-description" rows="3"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-400 outline-none text-sm resize-none"
                        placeholder="Décrivez le problème..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="fermerSignalement()"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-gray-300 transition">
                        Annuler
                    </button>
                    <button type="button" id="signalement-submit-btn" onclick="envoyerSignalement()"
                        class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm transition">
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var _signalerType = '';
var _signalerId = 0;

window.ouvrirSignalement = function(type, id) {
    _signalerType = type;
    _signalerId = id;
    document.querySelectorAll('input[name="signalement_motif"]').forEach(function(r) { r.checked = false; });
    document.getElementById('signalement-description').value = '';
    document.getElementById('signalement-succes').style.display = 'none';
    document.getElementById('signalement-form-wrapper').style.display = 'block';
    document.getElementById('signalement-submit-btn').disabled = false;
    document.getElementById('signalement-submit-btn').textContent = 'Envoyer';
    var modal = document.getElementById('signalement-modal');
    modal.style.display = 'flex';
};

window.fermerSignalement = function() {
    document.getElementById('signalement-modal').style.display = 'none';
};

window.envoyerSignalement = function() {
    var motifEl = document.querySelector('input[name="signalement_motif"]:checked');
    if (!motifEl) { alert('Choisissez un motif.'); return; }
    var btn = document.getElementById('signalement-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Envoi...';
    var fd = new FormData();
    fd.append('cible_type', _signalerType);
    fd.append('cible_id', _signalerId);
    fd.append('motif', motifEl.value);
    fd.append('description', document.getElementById('signalement-description').value);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch('/signalements', {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: fd,
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.textContent = 'Envoyer';
        if (data.success) {
            document.getElementById('signalement-form-wrapper').style.display = 'none';
            document.getElementById('signalement-msg').textContent = data.message;
            document.getElementById('signalement-succes').style.display = 'block';
        } else {
            var errDiv = document.getElementById('signalement-erreur');
            errDiv.textContent = data.message;
            errDiv.style.display = 'block';
        }
    })
    .catch(function(e) {
        btn.disabled = false;
        btn.textContent = 'Envoyer';
        alert('Erreur: ' + e.message);
    });
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.fermerSignalement();
});
</script>
<?php endif; ?><?php /**PATH C:\laragon\www\biocolis_nextgent\resources\views/components/signalement-modal.blade.php ENDPATH**/ ?>