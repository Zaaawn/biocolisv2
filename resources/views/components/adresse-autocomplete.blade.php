{{--
    resources/views/components/adresse-autocomplete.blade.php
    
    Composant universel d'autocomplétion d'adresse via API Adresse (gouv.fr)
    Usage : <x-adresse-autocomplete />
    S'active automatiquement sur tous les champs avec data-adresse
--}}

<style>
.adresse-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 9999;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-top: none;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    max-height: 280px;
    overflow-y: auto;
}
.adresse-item {
    padding: 10px 14px;
    cursor: pointer;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.1s;
}
.adresse-item:last-child { border-bottom: none; }
.adresse-item:hover, .adresse-item.active { background: #f0fdf4; }
.adresse-item-icon { color: #118501; font-size: 14px; margin-top: 1px; flex-shrink: 0; }
.adresse-item-main { font-size: 14px; font-weight: 500; color: #1a1a1a; }
.adresse-item-sub { font-size: 12px; color: #6b7280; margin-top: 1px; }
.adresse-loading { padding: 12px 14px; text-align: center; color: #9ca3af; font-size: 13px; }
</style>

<script>
(function() {
    // ── Configuration ──────────────────────────────────────────────────────
    const API = 'https://api-adresse.data.gouv.fr/search/';

    // ── Initialise l'autocomplétion sur un champ ───────────────────────────
    function initAdresseAuto(input) {
        if (input._adresseInit) return;
        input._adresseInit = true;

        // Wrapper pour le dropdown
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const dropdown = document.createElement('div');
        dropdown.className = 'adresse-dropdown';
        dropdown.style.display = 'none';
        wrapper.appendChild(dropdown);

        let timer = null;
        let activeIndex = -1;
        let suggestions = [];

        // ── Récupérer les suggestions ──────────────────────────────────────
        async function fetchSuggestions(query) {
            if (query.length < 3) { hide(); return; }

            dropdown.innerHTML = '<div class="adresse-loading">🔍 Recherche...</div>';
            dropdown.style.display = 'block';

            try {
                const res  = await fetch(`${API}?q=${encodeURIComponent(query)}&countrycodes=fr&limit=6`);
                const data = await res.json();
                suggestions = data.features || [];
                renderSuggestions();
            } catch(e) {
                hide();
            }
        }

        // ── Afficher les suggestions ───────────────────────────────────────
        function renderSuggestions() {
            if (!suggestions.length) { hide(); return; }

            dropdown.innerHTML = '';
            activeIndex = -1;

            suggestions.forEach((feature, i) => {
                const props = feature.properties;
                const item  = document.createElement('div');
                item.className = 'adresse-item';
                item.dataset.index = i;

                const ico = props.type === 'street' ? '🛣️'
                    : props.type === 'municipality' ? '🏙️'
                    : '📍';

                item.innerHTML = `
                    <span class="adresse-item-icon">${ico}</span>
                    <div>
                        <div class="adresse-item-main">${props.label}</div>
                        <div class="adresse-item-sub">${props.postcode} ${props.city}</div>
                    </div>
                `;

                item.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    selectSuggestion(i);
                });

                dropdown.appendChild(item);
            });

            dropdown.style.display = 'block';
        }

        // ── Sélectionner une suggestion ────────────────────────────────────
        function selectSuggestion(index) {
            const feature = suggestions[index];
            if (!feature) return;

            const props = feature.properties;
            const coords = feature.geometry.coordinates; // [lng, lat]

            // Remplir le champ principal
            input.value = props.label;

            // Remplir les champs cachés selon les data-attributes
            const form = input.closest('form');
            if (!form) { hide(); return; }

            // data-fill-ville
            const villeTarget = input.dataset.fillVille || input.id.replace(/localisation|adresse/, 'ville');
            const villeEl = form.querySelector(`[name="${villeTarget}"], #${villeTarget}`);
            if (villeEl) villeEl.value = props.city || '';

            // data-fill-cp
            const cpTarget = input.dataset.fillCp || input.id.replace(/localisation|adresse/, 'code_postal');
            const cpEl = form.querySelector(`[name="${cpTarget}"], #${cpTarget}`);
            if (cpEl) cpEl.value = props.postcode || '';

            // data-fill-lat
            const latTarget = input.dataset.fillLat || input.id.replace(/localisation|adresse/, 'latitude').replace(/adresse/, 'lat');
            const latEl = form.querySelector(`[name="${latTarget}"], #${latTarget}`);
            if (latEl) latEl.value = coords[1]; // lat

            // data-fill-lng
            const lngTarget = input.dataset.fillLng || input.id.replace(/localisation|adresse/, 'longitude').replace(/adresse/, 'lng');
            const lngEl = form.querySelector(`[name="${lngTarget}"], #${lngTarget}`);
            if (lngEl) lngEl.value = coords[0]; // lng

            hide();
            input.dispatchEvent(new Event('adresse-selected', { bubbles: true }));
        }

        // ── Navigation clavier ─────────────────────────────────────────────
        input.addEventListener('keydown', (e) => {
            const items = dropdown.querySelectorAll('.adresse-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActive(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActive(items);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                selectSuggestion(activeIndex);
            } else if (e.key === 'Escape') {
                hide();
            }
        });

        function updateActive(items) {
            items.forEach((el, i) => {
                el.classList.toggle('active', i === activeIndex);
            });
        }

        // ── Input event ────────────────────────────────────────────────────
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => fetchSuggestions(input.value), 250);
        });

        // ── Fermer au clic extérieur ───────────────────────────────────────
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) hide();
        });

        function hide() {
            dropdown.style.display = 'none';
            suggestions = [];
            activeIndex = -1;
        }
    }


    // ── Bouton 📍 Ma position ─────────────────────────────────────────────────
    function ajouterBoutonPosition(input) {
        if (input._positionBtn) return;
        input._positionBtn = true;

        const parent = input.parentNode;
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'display:flex;gap:8px;align-items:stretch;';
        parent.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        input.style.flex = '1';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.title = 'Utiliser ma position actuelle';
        btn.innerHTML = '📍';
        btn.style.cssText = 'flex-shrink:0;padding:0 14px;border:1px solid #e5e7eb;border-radius:12px;font-size:16px;cursor:pointer;background:white;transition:all 0.15s;line-height:1;';
        btn.addEventListener('mouseenter', () => { btn.style.borderColor='#118501'; btn.style.background='#f0fdf4'; });
        btn.addEventListener('mouseleave', () => { btn.style.borderColor='#e5e7eb'; btn.style.background='white'; });
        btn.addEventListener('click', () => remplirAvecPosition(input, btn));
        wrapper.appendChild(btn);
    }

    function remplirAvecPosition(input, btn) {
        if (!navigator.geolocation) {
            alert('Géolocalisation non supportée par votre navigateur.');
            return;
        }
        btn.innerHTML = '⏳';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const { latitude: lat, longitude: lng } = pos.coords;
                try {
                    const res  = await fetch('https://api-adresse.data.gouv.fr/reverse/?lon=' + lng + '&lat=' + lat);
                    const data = await res.json();
                    const feature = data.features?.[0];
                    if (feature) {
                        const props = feature.properties;
                        input.value = props.label;
                        btn.innerHTML = '✅';
                        setTimeout(() => { btn.innerHTML = '📍'; btn.disabled = false; }, 2000);
                        const form = input.closest('form');
                        const fill = (names, val) => names.forEach(n => {
                            const el = form?.querySelector('[name="' + n + '"], #' + n);
                            if (el) el.value = val;
                        });
                        fill(['latitude', 'lat'],   lat);
                        fill(['longitude', 'lng'],  lng);
                        fill(['ville'],              props.city || '');
                        fill(['code_postal'],        props.postcode || '');
                    } else {
                        btn.innerHTML = '📍'; btn.disabled = false;
                        alert('Adresse introuvable pour votre position.');
                    }
                } catch(e) { btn.innerHTML = '📍'; btn.disabled = false; }
            },
            (err) => {
                btn.innerHTML = '📍'; btn.disabled = false;
                const msgs = {1:'Accès refusé. Autorisez la localisation.', 2:'Position introuvable.', 3:'Délai expiré.'};
                alert(msgs[err.code] ?? 'Erreur géolocalisation.');
            },
            { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
        );
    }

    // ── Scanner tous les champs éligibles ──────────────────────────────────
    function scanChamps() {
        // Champs avec data-adresse
        document.querySelectorAll('[data-adresse]').forEach(el => {
            initAdresseAuto(el);
            ajouterBoutonPosition(el);
        });

        // Champs connus par leur name/id
        const selecteurs = [
            'input[name="localisation"]',
            'input[name="adresse"]',
            'input[name="adresse_livraison"]',
            'input[id="localisation"]',
            'input[id="adresse"]',
        ];
        document.querySelectorAll(selecteurs.join(',')).forEach(el => {
            initAdresseAuto(el);
            ajouterBoutonPosition(el);
        });
    }

    // Init au chargement + pour les éléments dynamiques
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scanChamps);
    } else {
        scanChamps();
    }

    // Observer pour les formulaires chargés dynamiquement (Alpine.js etc.)
    const observer = new MutationObserver(() => scanChamps());
    observer.observe(document.body, { childList: true, subtree: true });

    // Exposer pour usage manuel
    window.initAdresseAuto = initAdresseAuto;
    window.remplirAvecPosition = remplirAvecPosition;
})();
</script>
