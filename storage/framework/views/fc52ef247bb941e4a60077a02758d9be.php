
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="max-w-5xl mx-auto px-4 py-8">

        
        <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2">
            <a href="<?php echo e(route('annonces.index')); ?>" class="hover:text-green-600">Annonces</a>
            <span>/</span>
            <span class="text-gray-700"><?php echo e($annonce->titre); ?></span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            
            <div class="lg:col-span-2 space-y-6">

                
                <div x-data="{ photo: '<?php echo e($annonce->premiere_photo); ?>' }"
                     class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                    
                    <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
                        <img :src="photo" alt="<?php echo e($annonce->titre); ?>"
                             class="w-full h-full object-cover">
                    </div>

                    
                    <?php if(count($annonce->photos ?? []) > 1): ?>
                        <div class="flex gap-2 p-3 overflow-x-auto">
                            <?php $__currentLoopData = $annonce->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button @click="photo='<?php echo e(asset('storage/' . $p)); ?>'"
                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                                    :class="photo === '<?php echo e(asset('storage/' . $p)); ?>' ? 'border-green-500' : 'border-transparent'">
                                    <img src="<?php echo e(asset('storage/' . $p)); ?>" class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                    <?php echo e($annonce->label === 'bio' ? 'bg-green-100 text-green-700' : 'bg-blue-50 text-blue-600'); ?>">
                                    <?php echo e(match($annonce->label) { 'bio'=>'🍃 Bio', 'local'=>'📍 Local', 'raisonne'=>'♻️ Raisonné', default=>'🌾 '.ucfirst($annonce->label) }); ?>

                                </span>
                                <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                                    <?php echo e(match($annonce->type_produit) { 'fruit'=>'🍓 Fruit', 'legume'=>'🥕 Légume', 'herbe'=>'🌿 Herbe', 'champignon'=>'🍄 Champignon', default=>'📦 Autre' }); ?>

                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($annonce->titre); ?></h1>
                        </div>

                        
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->id() !== $annonce->user_id): ?>
                                <button id="like-btn" onclick="toggleLikeShow(<?php echo e($annonce->id); ?>)"
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 hover:border-red-300 transition text-sm">
                                    <svg id="like-icon" class="h-5 w-5 <?php echo e($estLike ? 'text-red-500' : 'text-gray-400'); ?>"
                                         fill="<?php echo e($estLike ? '#ef4444' : 'none'); ?>" viewBox="0 0 24 24" stroke="currentColor"
                                         stroke-width="<?php echo e($estLike ? '0' : '2'); ?>">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    <span id="nb-likes"><?php echo e($annonce->nb_likes); ?></span>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="grid grid-cols-3 gap-4 py-4 border-y border-gray-100">
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600"><?php echo e(number_format($annonce->prix, 2)); ?>€</div>
                            <div class="text-xs text-gray-400">/ <?php echo e($annonce->unite_prix); ?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900"><?php echo e($annonce->quantite_disponible); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e($annonce->unite_prix); ?> disponible(s)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900 flex items-center justify-center gap-1">
                                <span class="text-yellow-400">★</span>
                                <?php echo e($annonce->note_moyenne > 0 ? number_format($annonce->note_moyenne, 1) : '—'); ?>

                            </div>
                            <div class="text-xs text-gray-400"><?php echo e($annonce->nb_commandes); ?> commande(s)</div>
                        </div>
                    </div>

                    
                    <div>
                        <h2 class="font-semibold text-gray-900 mb-2">Description</h2>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line"><?php echo e($annonce->description); ?></p>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📅</span>
                            <span>Récolté le <?php echo e($annonce->date_recolte->format('d/m/Y')); ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📍</span>
                            <span><?php echo e($annonce->ville ?? $annonce->localisation); ?></span>
                        </div>
                        <?php if($annonce->disponible_a_partir_de): ?>
                            <div class="flex items-center gap-2 text-gray-600">
                                <span>⏰</span>
                                <span>Dispo à partir du <?php echo e($annonce->disponible_a_partir_de->format('d/m/Y')); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($annonce->quantite_min_commande): ?>
                            <div class="flex items-center gap-2 text-gray-600">
                                <span>📦</span>
                                <span>Minimum <?php echo e($annonce->quantite_min_commande); ?> <?php echo e($annonce->unite_prix); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div>
                        <h2 class="font-semibold text-gray-900 mb-2">Modes de livraison</h2>
                        <div class="flex flex-wrap gap-2">
                            <?php if($annonce->livraison_main_propre): ?>
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🤝 <span>Main propre</span> <span class="text-green-600 font-semibold">Gratuit</span>
                                </span>
                            <?php endif; ?>
                            <?php if($annonce->livraison_point_relais): ?>
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    📦 <span>Point relais</span> <span class="text-gray-500 font-semibold">3,00€</span>
                                </span>
                            <?php endif; ?>
                            <?php if($annonce->livraison_domicile): ?>
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🏠 <span>Domicile</span> <span class="text-gray-500 font-semibold">6,00€</span>
                                </span>
                            <?php endif; ?>
                            <?php if($annonce->livraison_locker): ?>
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🗄️ <span>Locker</span> <span class="text-gray-500 font-semibold">2,50€</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <?php if($annonce->ratings->count() > 0): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">
                            Avis (<?php echo e($annonce->ratings->count()); ?>)
                        </h2>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $annonce->ratings->where('is_visible', true)->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex gap-3">
                                    <img src="<?php echo e($rating->auteur->photo_profil_url); ?>"
                                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900"><?php echo e($rating->auteur->prenom); ?></span>
                                            <div class="flex text-yellow-400 text-xs">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <?php echo e($i <= $rating->note ? '★' : '☆'); ?>

                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-xs text-gray-400"><?php echo e($rating->created_at->diffForHumans()); ?></span>
                                        </div>
                                        <?php if($rating->commentaire): ?>
                                            <p class="text-sm text-gray-600"><?php echo e($rating->commentaire); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="space-y-4">

                
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="<?php echo e($annonce->user->photo_profil_url); ?>"
                             alt="<?php echo e($annonce->user->nom_complet); ?>"
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900"><?php echo e($annonce->user->nom_complet); ?></div>
                            <div class="text-xs text-gray-400 flex items-center gap-1">
                                <?php if($annonce->user->note_moyenne > 0): ?>
                                    <span class="text-yellow-400">★</span>
                                    <?php echo e(number_format($annonce->user->note_moyenne, 1)); ?>

                                    · <?php echo e($annonce->user->nb_avis); ?> avis ·
                                <?php endif; ?>
                                <?php echo e($annonce->user->nb_ventes); ?> vente(s)
                            </div>
                        </div>
                        
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->id() !== $annonce->user_id): ?>
                                <?php if (isset($component)) { $__componentOriginale7816ce1b5b317e22e74ca514e24f6bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7816ce1b5b317e22e74ca514e24f6bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.signaler-btn','data' => ['type' => 'annonce','id' => $annonce->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('signaler-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'annonce','id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($annonce->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7816ce1b5b317e22e74ca514e24f6bc)): ?>
<?php $attributes = $__attributesOriginale7816ce1b5b317e22e74ca514e24f6bc; ?>
<?php unset($__attributesOriginale7816ce1b5b317e22e74ca514e24f6bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7816ce1b5b317e22e74ca514e24f6bc)): ?>
<?php $component = $__componentOriginale7816ce1b5b317e22e74ca514e24f6bc; ?>
<?php unset($__componentOriginale7816ce1b5b317e22e74ca514e24f6bc); ?>
<?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if($annonce->user->isProfessionnel()): ?>
                        <div class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg mb-3">
                            🏢 <?php echo e($annonce->user->societe_nom); ?>

                        </div>
                    <?php endif; ?>

                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->id() !== $annonce->user_id): ?>
                            <a href="<?php echo e(route('messages.show', ['annonce' => $annonce->id, 'user' => $annonce->user_id])); ?>"
                               class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-green-500 hover:text-green-600 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contacter le vendeur
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->id() !== $annonce->user_id && $annonce->isDisponible()): ?>
                        <div class="bg-white rounded-2xl border border-green-100 p-5 sticky top-4">
                            <h3 class="font-semibold text-gray-900 mb-4">Commander</h3>

                            <form action="<?php echo e(route('panier.ajouter', $annonce->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">
                                        Quantité (<?php echo e($annonce->unite_prix); ?>)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeQty(-1)"
                                            class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:border-gray-300 transition text-gray-600">−</button>
                                        <input type="number" id="quantite" name="quantite"
                                            value="<?php echo e($annonce->quantite_min_commande ?? 1); ?>"
                                            min="<?php echo e($annonce->quantite_min_commande ?? 0.5); ?>"
                                            max="<?php echo e($annonce->quantite_disponible); ?>"
                                            step="0.01"
                                            class="flex-1 text-center py-2 rounded-lg border border-gray-200 focus:border-green-500 outline-none text-sm font-medium"
                                            oninput="updateTotal()">
                                        <button type="button" onclick="changeQty(1)"
                                            class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:border-gray-300 transition text-gray-600">+</button>
                                    </div>
                                </div>

                                
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Livraison</label>
                                    <select name="mode_livraison" id="mode_livraison" onchange="updateTotal()"
                                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                                        <?php if($annonce->livraison_main_propre): ?>
                                            <option value="main_propre" data-frais="0">🤝 Main propre — Gratuit</option>
                                        <?php endif; ?>
                                        <?php if($annonce->livraison_point_relais): ?>
                                            <option value="point_relais" data-frais="3">📦 Point relais — 3,00€</option>
                                        <?php endif; ?>
                                        <?php if($annonce->livraison_domicile): ?>
                                            <option value="domicile" data-frais="6">🏠 Domicile — 6,00€</option>
                                        <?php endif; ?>
                                        <?php if($annonce->livraison_locker): ?>
                                            <option value="locker" data-frais="2.5">🗄️ Locker — 2,50€</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                
                                <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-1.5 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Produits</span>
                                        <span id="total-produits"><?php echo e(number_format($annonce->prix * ($annonce->quantite_min_commande ?? 1), 2)); ?>€</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Livraison</span>
                                        <span id="total-livraison">0,00€</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Frais de service</span>
                                        <span>0,99€</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-gray-900 pt-1.5 border-t border-gray-200">
                                        <span>Total</span>
                                        <span id="total-final" class="text-green-600"><?php echo e(number_format($annonce->prix * ($annonce->quantite_min_commande ?? 1) + 0.99, 2)); ?>€</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                                    <?php echo e($dansPanier ? '✓ Dans le panier' : 'Ajouter au panier'); ?>

                                </button>
                            </form>
                        </div>
                    <?php elseif(auth()->id() === $annonce->user_id): ?>
                        <div class="bg-white rounded-2xl border border-gray-100 p-5">
                            <p class="text-sm text-gray-500 text-center mb-3">C'est votre annonce</p>
                            <a href="<?php echo e(route('annonces.edit', $annonce->slug)); ?>"
                               class="w-full block text-center py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                                ✏️ Modifier l'annonce
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                        <p class="text-sm text-gray-500 mb-3">Connectez-vous pour commander</p>
                        <a href="<?php echo e(route('login')); ?>"
                           class="w-full block py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                            Se connecter
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($similaires->count() > 0): ?>
            <div class="mt-12">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Vous aimerez aussi</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php $__currentLoopData = $similaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginalb4f523805c9716e9c4a2730a4c7ae139 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.annonce-card','data' => ['annonce' => $s]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('annonce-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['annonce' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($s)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139)): ?>
<?php $attributes = $__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139; ?>
<?php unset($__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4f523805c9716e9c4a2730a4c7ae139)): ?>
<?php $component = $__componentOriginalb4f523805c9716e9c4a2730a4c7ae139; ?>
<?php unset($__componentOriginalb4f523805c9716e9c4a2730a4c7ae139); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const prixUnitaire = <?php echo e($annonce->prix); ?>;

        function changeQty(delta) {
            const input = document.getElementById('quantite');
            const step = parseFloat(input.step) || 0.1;
            const newVal = Math.max(parseFloat(input.min), parseFloat(input.value) + delta * step);
            input.value = Math.min(parseFloat(input.max), newVal).toFixed(2);
            updateTotal();
        }

        function updateTotal() {
            const qty   = parseFloat(document.getElementById('quantite').value) || 0;
            const sel   = document.getElementById('mode_livraison');
            const frais = sel ? parseFloat(sel.options[sel.selectedIndex].dataset.frais || 0) : 0;
            const produits = qty * prixUnitaire;
            const total  = produits + frais + 0.99;

            document.getElementById('total-produits').textContent  = produits.toFixed(2).replace('.', ',') + '€';
            document.getElementById('total-livraison').textContent = frais.toFixed(2).replace('.', ',') + '€';
            document.getElementById('total-final').textContent     = total.toFixed(2).replace('.', ',') + '€';
        }

        async function toggleLikeShow(annonceId) {
            try {
                const res = await fetch(`/annonces/${annonceId}/like`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                const icon = document.getElementById('like-icon');
                const nb   = document.getElementById('nb-likes');
                if (data.liked) {
                    icon.setAttribute('fill', '#ef4444');
                    icon.setAttribute('stroke-width', '0');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke-width', '2');
                }
                nb.textContent = data.nb_likes;
            } catch(e) {}
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\biocolis_nextgent\resources\views/annonces/show.blade.php ENDPATH**/ ?>