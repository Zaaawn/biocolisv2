
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

    
    <section class="bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 text-white py-20 px-4 relative overflow-hidden">

        
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-4xl mx-auto text-center relative">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full text-sm font-medium mb-6">
                🌱 Fruits & Légumes ultra-frais, livrés près de chez vous
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                Mange local,<br>
                <span class="text-green-200">mange frais</span>
            </h1>

            <p class="text-lg md:text-xl text-green-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                Biocolis connecte les producteurs locaux et les consommateurs.
                Commande en 2 clics, reçois le meilleur des jardins près de chez toi.
            </p>

            
            <form action="<?php echo e(route('annonces.index')); ?>" method="GET"
                  class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto mb-8">
                <input type="text" name="search"
                    placeholder="Que cherchez-vous ? Tomates, fraises..."
                    class="flex-1 px-5 py-4 rounded-2xl text-gray-900 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg">
                <button type="submit"
                    class="px-6 py-4 bg-white text-green-700 font-bold rounded-2xl hover:bg-green-50 transition shadow-lg text-sm whitespace-nowrap">
                    🔍 Rechercher
                </button>
            </form>

            
            <div class="flex flex-wrap justify-center gap-2">
                <?php $__currentLoopData = [
                    ['🥕', 'Légumes', 'legume'],
                    ['🍓', 'Fruits', 'fruit'],
                    ['🌿', 'Herbes', 'herbe'],
                    ['🍄', 'Champignons', 'champignon'],
                    ['🍃', 'Bio', null, 'bio'],
                    ['📍', 'Local', null, 'local'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('annonces.index', isset($cat[3]) ? ['label' => $cat[3]] : ['type' => $cat[2]])); ?>"
                       class="flex items-center gap-1.5 px-4 py-2 bg-white/15 hover:bg-white/25 backdrop-blur rounded-full text-sm font-medium transition">
                        <?php echo e($cat[0]); ?> <?php echo e($cat[1]); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="bg-white border-b border-gray-100 py-8 px-4">
        <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <?php $__currentLoopData = [
                [$stats['nb_annonces'], 'annonces actives', '📢'],
                [$stats['nb_producteurs'], 'producteurs', '🚜'],
                [$stats['nb_commandes'], 'commandes livrées', '📦'],
                [$stats['nb_villes'], 'villes couvertes', '📍'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val, $lbl, $ico]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="text-3xl font-extrabold text-gray-900"><?php echo e(number_format($val)); ?><?php echo e($val > 0 ? '+' : ''); ?></div>
                    <div class="text-sm text-gray-500 mt-1"><?php echo e($ico); ?> <?php echo e($lbl); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <?php if($annonces_recentes->isNotEmpty()): ?>
        <section class="max-w-6xl mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Nouveautés 🌱</h2>
                    <p class="text-gray-400 text-sm mt-1">Les dernières annonces près de chez vous</p>
                </div>
                <a href="<?php echo e(route('annonces.index')); ?>"
                   class="text-sm font-medium text-green-600 hover:text-green-700 transition">
                    Tout voir →
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php $__currentLoopData = $annonces_recentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginalb4f523805c9716e9c4a2730a4c7ae139 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.annonce-card','data' => ['annonce' => $annonce]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('annonce-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['annonce' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($annonce)]); ?>
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
        </section>
    <?php endif; ?>

    
    <?php if($annonces_vedettes->isNotEmpty()): ?>
        <section class="bg-green-50 py-12 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">⭐ À la une</h2>
                        <p class="text-gray-400 text-sm mt-1">Sélectionnés par nos producteurs</p>
                    </div>
                    <a href="<?php echo e(route('annonces.index')); ?>"
                       class="text-sm font-medium text-green-600 hover:text-green-700">
                        Voir tout →
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php $__currentLoopData = $annonces_vedettes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginalb4f523805c9716e9c4a2730a4c7ae139 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4f523805c9716e9c4a2730a4c7ae139 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.annonce-card','data' => ['annonce' => $annonce]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('annonce-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['annonce' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($annonce)]); ?>
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
        </section>
    <?php endif; ?>

    
    <section class="max-w-5xl mx-auto px-4 py-16">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Comment ça marche ?</h2>
        <p class="text-gray-400 text-center text-sm mb-12">Simple, rapide et local</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php $__currentLoopData = [
                ['🔍', '1. Je cherche', 'Parcourez les annonces de producteurs près de chez vous. Filtrez par type, label ou distance.'],
                ['🛒', '2. Je commande', 'Ajoutez au panier, choisissez votre mode de livraison, payez en toute sécurité avec Stripe.'],
                ['🥕', '3. Je déguste', 'Récupérez vos produits frais récoltés le jour même. Laissez un avis pour aider la communauté.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ico, $titre, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                        <?php echo e($ico); ?>

                    </div>
                    <h3 class="font-bold text-gray-900 mb-2"><?php echo e($titre); ?></h3>
                    <p class="text-sm text-gray-500 leading-relaxed"><?php echo e($desc); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="bg-gray-900 text-white py-16 px-4">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold text-center mb-12">Pourquoi Biocolis ?</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php $__currentLoopData = [
                    ['⚡', 'Ultra-frais', 'Récolté dans les 24h'],
                    ['📍', 'Circuit court', 'Zéro intermédiaire'],
                    ['🔒', 'Paiement sécurisé', 'Stripe certifié'],
                    ['🌿', 'Éco-responsable', 'Emballages recyclables'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ico, $titre, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-center">
                        <div class="text-3xl mb-3"><?php echo e($ico); ?></div>
                        <div class="font-semibold mb-1"><?php echo e($titre); ?></div>
                        <div class="text-sm text-gray-400"><?php echo e($desc); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <?php if(auth()->guard()->guest()): ?>
        <section class="max-w-4xl mx-auto px-4 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Vous avez un jardin ou une ferme ?</h2>
            <p class="text-gray-500 mb-8">Rejoignez des centaines de producteurs qui vendent directement à leur communauté</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?php echo e(route('register')); ?>"
                   class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl transition text-sm">
                    🚜 Vendre mes produits
                </a>
                <a href="<?php echo e(route('annonces.index')); ?>"
                   class="px-8 py-4 border-2 border-gray-200 hover:border-green-400 text-gray-700 font-semibold rounded-2xl transition text-sm">
                    🥕 Acheter local
                </a>
            </div>
        </section>
    <?php endif; ?>

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
<?php /**PATH C:\laragon\www\biocolis_nextgent\resources\views/welcome.blade.php ENDPATH**/ ?>