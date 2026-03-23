
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
    <div class="max-w-5xl mx-auto px-4 py-12" x-data="{ periodicite: 'mensuel' }">

        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Plans B2B Biocolis</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                Commissions réduites, plus de visibilité, outils pro. Développez votre activité.
            </p>

            
            <div class="flex items-center justify-center gap-4 mt-6">
                <span :class="periodicite === 'mensuel' ? 'text-gray-900 font-semibold' : 'text-gray-400'" class="text-sm">Mensuel</span>
                <button @click="periodicite = periodicite === 'mensuel' ? 'annuel' : 'mensuel'"
                    class="relative w-12 h-6 rounded-full transition-colors"
                    :class="periodicite === 'annuel' ? 'bg-green-600' : 'bg-gray-300'">
                    <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                          :class="periodicite === 'annuel' ? 'translate-x-6' : ''"></span>
                </button>
                <span :class="periodicite === 'annuel' ? 'text-gray-900 font-semibold' : 'text-gray-400'" class="text-sm">
                    Annuel <span class="text-green-600 font-bold text-xs ml-1">-17%</span>
                </span>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $estPopulaire = $plan->slug === 'business';
                    $estActif = $abonnementActif?->plan_id === $plan->id;
                ?>

                <div class="bg-white rounded-2xl border <?php echo e($estPopulaire ? 'border-green-500 shadow-lg shadow-green-100' : 'border-gray-200'); ?> p-6 relative flex flex-col">

                    <?php if($estPopulaire): ?>
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="bg-green-600 text-white text-xs font-bold px-4 py-1 rounded-full">⭐ Populaire</span>
                        </div>
                    <?php endif; ?>

                    <?php if($estActif): ?>
                        <div class="absolute top-4 right-4">
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">✅ Actif</span>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo e($plan->nom); ?></h3>
                        <div class="mt-3">
                            <div x-show="periodicite === 'mensuel'" class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900"><?php echo e(number_format($plan->prix_mensuel, 0)); ?>€</span>
                                <span class="text-gray-400 text-sm">/mois</span>
                            </div>
                            <div x-show="periodicite === 'annuel'" x-cloak class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900"><?php echo e(number_format($plan->prix_annuel / 12, 0)); ?>€</span>
                                <span class="text-gray-400 text-sm">/mois</span>
                            </div>
                            <div x-show="periodicite === 'annuel'" x-cloak class="text-xs text-green-600 mt-1">
                                Facturé <?php echo e(number_format($plan->prix_annuel, 0)); ?>€/an
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-green-700 font-semibold bg-green-50 px-3 py-1 rounded-lg inline-block">
                            Commission <?php echo e($plan->commission_pct); ?>% seulement
                        </div>
                    </div>

                    <ul class="space-y-2 mb-6 flex-1">
                        <?php $__currentLoopData = $plan->fonctionnalites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <span class="text-green-500 flex-shrink-0 mt-0.5">✓</span>
                                <?php echo e($feature); ?>

                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    <?php if(auth()->guard()->check()): ?>
                        <?php if($estActif): ?>
                            <a href="<?php echo e(route('abonnements.mon-abonnement')); ?>"
                               class="w-full py-3 text-center border-2 border-green-500 text-green-600 font-semibold rounded-xl text-sm transition hover:bg-green-50">
                                Gérer mon abonnement
                            </a>
                        <?php else: ?>
                            <form action="<?php echo e(route('abonnements.souscrire', $plan->slug)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="periodicite" :value="periodicite">
                                <button type="submit"
                                    class="w-full py-3 <?php echo e($estPopulaire ? 'bg-green-600 hover:bg-green-700 text-white' : 'border-2 border-gray-200 hover:border-green-400 text-gray-700'); ?> font-semibold rounded-xl text-sm transition">
                                    Commencer avec <?php echo e($plan->nom); ?>

                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo e(route('register')); ?>"
                           class="w-full py-3 text-center <?php echo e($estPopulaire ? 'bg-green-600 hover:bg-green-700 text-white' : 'border-2 border-gray-200 hover:border-green-400 text-gray-700'); ?> font-semibold rounded-xl text-sm transition block">
                            Commencer avec <?php echo e($plan->nom); ?>

                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="bg-gradient-to-br from-green-600 to-emerald-500 rounded-2xl p-8 text-white text-center mb-8">
            <h2 class="text-2xl font-bold mb-2">Économisez sur chaque vente</h2>
            <p class="text-green-100 mb-6">vs. la commission standard de 12%</p>
            <div class="grid grid-cols-4 gap-4">
                <?php $__currentLoopData = [['Standard', '12%', 'Sans abonnement'], ['Starter', '10%', '-2%'], ['Business', '8%', '-4%'], ['Premium', '5%', '-7%']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$nom, $pct, $eco]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white/10 rounded-xl p-4">
                        <div class="text-2xl font-bold"><?php echo e($pct); ?></div>
                        <div class="text-sm font-semibold mt-1"><?php echo e($nom); ?></div>
                        <div class="text-xs text-green-200 mt-0.5"><?php echo e($eco); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-lg mb-4">Questions fréquentes</h2>
            <div class="space-y-4">
                <?php $__currentLoopData = [
                    ['Puis-je changer de plan ?', 'Oui, vous pouvez upgrader ou downgrader à tout moment. Le nouveau tarif s\'applique immédiatement.'],
                    ['Comment fonctionne la commission réduite ?', 'Dès l\'activation de votre plan, la commission Biocolis est automatiquement réduite sur toutes vos ventes.'],
                    ['Puis-je annuler ?', 'Oui, à tout moment. Votre abonnement reste actif jusqu\'à la fin de la période payée.'],
                    ['Y a-t-il une période d\'essai ?', 'Contactez-nous à support@biocolis.fr pour discuter d\'une démo ou d\'un essai gratuit.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$q, $r]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left py-3 border-b border-gray-100">
                            <span class="font-medium text-gray-900 text-sm"><?php echo e($q); ?></span>
                            <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 text-gray-400 transition-transform flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="py-3 text-sm text-gray-500"><?php echo e($r); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\laragon\www\biocolis_nextgent\resources\views/abonnements/tarifs.blade.php ENDPATH**/ ?>