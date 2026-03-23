{{-- resources/views/pages/confidentialite.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-12">

        <div class="mb-8">
            <div class="text-xs text-gray-400 mb-2">Mis à jour le {{ date('d/m/Y') }}</div>
            <h1 class="text-3xl font-bold text-gray-900">Politique de Confidentialité</h1>
            <p class="text-gray-500 mt-2">Comment nous collectons et utilisons vos données personnelles.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-8 space-y-8 text-sm text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">1. Responsable du traitement</h2>
                <p>Le responsable du traitement des données personnelles collectées via Biocolis est la société Biocolis, joignable à l'adresse <a href="mailto:privacy@biocolis.fr" class="text-green-600 underline">privacy@biocolis.fr</a>.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">2. Données collectées</h2>
                <p>Nous collectons les données suivantes :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li><strong>Données d'identité :</strong> prénom, nom, nom d'utilisateur, photo de profil</li>
                    <li><strong>Données de contact :</strong> adresse email, numéro de téléphone, adresse postale</li>
                    <li><strong>Données de localisation :</strong> coordonnées GPS (avec votre consentement)</li>
                    <li><strong>Données commerciales :</strong> historique des commandes, annonces publiées</li>
                    <li><strong>Données financières :</strong> traitées par Stripe (nous ne stockons pas vos données bancaires)</li>
                    <li><strong>Données de navigation :</strong> adresse IP, type de navigateur, pages visitées</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">3. Finalités du traitement</h2>
                <p>Vos données sont utilisées pour :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li>Gérer votre compte et vous authentifier</li>
                    <li>Traiter vos commandes et paiements</li>
                    <li>Vous mettre en relation avec d'autres utilisateurs</li>
                    <li>Améliorer nos services et personnaliser votre expérience</li>
                    <li>Vous envoyer des notifications liées à votre activité</li>
                    <li>Respecter nos obligations légales</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">4. Base légale</h2>
                <p>Le traitement de vos données repose sur :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li>L'exécution du contrat (CGU) pour les données nécessaires au service</li>
                    <li>Votre consentement pour la géolocalisation et les communications marketing</li>
                    <li>L'intérêt légitime pour l'amélioration du service et la sécurité</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">5. Partage des données</h2>
                <p>Nous partageons vos données uniquement avec :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li><strong>Stripe</strong> : traitement des paiements (conforme PCI-DSS)</li>
                    <li><strong>Nominatim/OpenStreetMap</strong> : géocodage des adresses</li>
                    <li>Les autres utilisateurs de la plateforme (prénom, note, annonces) dans le cadre normal du service</li>
                </ul>
                <p class="mt-2">Nous ne vendons jamais vos données à des tiers.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">6. Vos droits (RGPD)</h2>
                <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li><strong>Accès :</strong> obtenir une copie de vos données</li>
                    <li><strong>Rectification :</strong> corriger des données inexactes</li>
                    <li><strong>Suppression :</strong> demander la suppression de votre compte et données</li>
                    <li><strong>Portabilité :</strong> recevoir vos données dans un format structuré</li>
                    <li><strong>Opposition :</strong> vous opposer au traitement pour motif légitime</li>
                </ul>
                <p class="mt-2">Pour exercer ces droits, contactez-nous : <a href="mailto:privacy@biocolis.fr" class="text-green-600 underline">privacy@biocolis.fr</a></p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">7. Cookies</h2>
                <p>Biocolis utilise des cookies strictement nécessaires au fonctionnement du service (session, sécurité CSRF). Aucun cookie publicitaire ou de tracking tiers n'est utilisé.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">8. Conservation des données</h2>
                <p>Vos données sont conservées pendant la durée de votre inscription. En cas de suppression de compte, les données sont anonymisées sous 30 jours, sauf obligations légales contraires (données de facturation conservées 10 ans).</p>
            </section>

        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('pages.cgu') }}" class="text-sm text-green-600 hover:underline">← CGU</a>
            <a href="{{ route('pages.contact') }}" class="text-sm text-green-600 hover:underline">Nous contacter →</a>
        </div>
    </div>
</x-app-layout>
