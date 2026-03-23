
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

        
        <div class="flex items-center gap-4 mb-8">
            <img src="<?php echo e($user->photo_profil_url); ?>" class="w-14 h-14 rounded-full object-cover border-2 border-green-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bonjour <?php echo e($user->prenom); ?> 👋</h1>
                <p class="text-gray-400 text-sm">Bienvenue sur votre espace Biocolis</p>
            </div>
        </div>

        
        <?php if($mes_annonces->isNotEmpty() && !auth()->user()->hasStripeAccount() && empty($user->iban)): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💳</span>
                    <div>
                        <div class="font-semibold text-amber-900 text-sm">Renseignez votre IBAN pour recevoir vos paiements</div>
                        <div class="text-xs text-amber-700 mt-0.5">Vous avez des annonces actives mais pas encore d'IBAN enregistré.</div>
                    </div>
                </div>
                <a href="<?php echo e(route('profile.edit')); ?>#iban"
                   class="flex-shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs transition whitespace-nowrap">
                    Ajouter mon IBAN →
                </a>
            </div>
        <?php endif; ?>

        
        <?php if($avis_a_laisser->isNotEmpty()): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                <h2 class="font-semibold text-amber-800 mb-3 flex items-center gap-2">
                    ⭐ Laissez un avis
                    <span class="text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full"><?php echo e($avis_a_laisser->count()); ?></span>
                </h2>
                <div class="space-y-2">
                    <?php $__currentLoopData = $avis_a_laisser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-xl p-3 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-medium text-gray-900"><?php echo e($commande->vendeur->nom_complet); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($commande->lignes->first()?->titre_annonce); ?></div>
                            </div>
                            <a href="<?php echo e(route('commandes.show', $commande->id)); ?>#avis"
                               class="flex-shrink-0 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold transition">
                                Noter →
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">💰</div>
                <div class="text-xl font-bold text-green-600"><?php echo e(number_format($stats['total_gagne'], 2)); ?>€</div>
                <div class="text-xs text-gray-400">total gagné</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">🛒</div>
                <div class="text-xl font-bold text-gray-900"><?php echo e($stats['nb_ventes']); ?></div>
                <div class="text-xs text-gray-400">ventes</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">📢</div>
                <div class="text-xl font-bold text-gray-900"><?php echo e($stats['nb_annonces']); ?></div>
                <div class="text-xs text-gray-400">annonces actives</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">⏳</div>
                <div class="text-xl font-bold text-orange-500"><?php echo e(number_format($stats['solde_en_attente'], 2)); ?>€</div>
                <div class="text-xs text-gray-400">en attente</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                
                <?php if($ventes_recentes->isNotEmpty()): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-gray-900">Mes ventes récentes</h2>
                            <a href="<?php echo e(route('stripe.dashboard')); ?>" class="text-xs text-green-600 hover:underline">Tout voir →</a>
                        </div>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $ventes_recentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('commandes.show', $commande->id)); ?>"
                                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($commande->numero); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo e($commande->acheteur->prenom); ?> · <?php echo e($commande->created_at->format('d/m/Y')); ?></div>
                                    </div>
                                    <div class="text-sm font-bold text-green-600">
                                        +<?php echo e(number_format($commande->montant_vendeur, 2)); ?>€
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-gray-900">Mes achats récents</h2>
                        <a href="<?php echo e(route('commandes.mes-commandes')); ?>" class="text-xs text-green-600 hover:underline">Tout voir →</a>
                    </div>
                    <?php if($commandes_recentes->isEmpty()): ?>
                        <div class="text-center py-8 text-gray-400">
                            <div class="text-3xl mb-2">📦</div>
                            <p class="text-sm">Aucun achat</p>
                            <a href="<?php echo e(route('annonces.index')); ?>"
                               class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-xl text-xs font-semibold hover:bg-green-700 transition">
                                Parcourir les annonces
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $commandes_recentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $colors = ['payee'=>'text-blue-600','en_preparation'=>'text-orange-600','prete'=>'text-purple-600','en_livraison'=>'text-indigo-600','livree'=>'text-teal-600','terminee'=>'text-green-600','annulee'=>'text-red-500'][$commande->statut] ?? 'text-gray-500';
                                ?>
                                <a href="<?php echo e(route('commandes.show', $commande->id)); ?>"
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                    <div class="flex gap-1">
                                        <?php $__currentLoopData = $commande->lignes->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100">
                                                <?php if($l->photo_annonce): ?>
                                                    <img src="<?php echo e(asset('storage/'.$l->photo_annonce)); ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center text-lg">🥕</div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($commande->numero); ?></div>
                                        <div class="text-xs <?php echo e($colors); ?>"><?php echo e(ucfirst(str_replace('_',' ',$commande->statut))); ?></div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900"><?php echo e(number_format($commande->total_ttc, 2)); ?>€</div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="space-y-4">
                
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Actions rapides</h2>
                    <div class="space-y-2">
                        <a href="<?php echo e(route('annonces.create')); ?>"
                           class="flex items-center gap-3 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                            <span>📢</span> Déposer une annonce
                        </a>
                        <a href="<?php echo e(route('stripe.dashboard')); ?>"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💰</span> Mes revenus
                        </a>
                        <a href="<?php echo e(route('panier.index')); ?>"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>🛒</span> Mon panier
                            <?php if($nb_panier > 0): ?>
                                <span class="ml-auto bg-green-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"><?php echo e($nb_panier); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo e(route('messages.index')); ?>"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💬</span> Messages
                            <?php if($nb_messages > 0): ?>
                                <span class="ml-auto bg-green-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"><?php echo e($nb_messages); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo e(route('profile.edit')); ?>"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-gray-300 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>⚙️</span> Mon profil
                        </a>
                    </div>
                </div>

                
                <?php if($mes_annonces->isNotEmpty()): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between mb-3">
                            <h2 class="font-semibold text-gray-900">Mes annonces</h2>
                            <a href="<?php echo e(route('annonces.mes-annonces')); ?>" class="text-xs text-green-600 hover:underline">Tout voir</a>
                        </div>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $mes_annonces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('annonces.show', $annonce->slug)); ?>"
                                   class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded-xl transition">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                        <img src="<?php echo e($annonce->premiere_photo); ?>" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate"><?php echo e($annonce->titre); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo e(number_format($annonce->prix, 2)); ?>€ / <?php echo e($annonce->unite_prix); ?></div>
                                    </div>
                                    <div class="text-xs text-gray-400"><?php echo e($annonce->nb_vues); ?> 👁</div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\biocolis_nextgent\resources\views/dashboard/particulier.blade.php ENDPATH**/ ?>