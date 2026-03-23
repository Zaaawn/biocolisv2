{{-- resources/views/pages/cgu.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-12">

        <div class="mb-8">
            <div class="text-xs text-gray-400 mb-2">Mis à jour le {{ date('d/m/Y') }}</div>
            <h1 class="text-3xl font-bold text-gray-900">Conditions Générales d'Utilisation</h1>
            <p class="text-gray-500 mt-2">En utilisant Biocolis, vous acceptez les présentes conditions.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-8 space-y-8 text-sm text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">1. Objet</h2>
                <p>Biocolis est une marketplace mettant en relation des producteurs locaux (particuliers ou professionnels) avec des acheteurs souhaitant se procurer des fruits, légumes et produits frais en circuit court.</p>
                <p class="mt-2">Les présentes CGU régissent l'utilisation de la plateforme accessible à l'adresse biocolis.fr et de ses applications mobiles.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">2. Inscription et compte utilisateur</h2>
                <p>L'inscription sur Biocolis est gratuite et ouverte à toute personne physique majeure ou personne morale. Lors de l'inscription, vous vous engagez à fournir des informations exactes et à les maintenir à jour.</p>
                <p class="mt-2">Vous êtes seul responsable de la confidentialité de vos identifiants de connexion. Toute utilisation de votre compte est présumée être faite par vous.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">3. Rôle de Biocolis</h2>
                <p>Biocolis agit en qualité d'intermédiaire entre acheteurs et vendeurs. Biocolis n'est pas vendeur des produits listés sur la plateforme et ne participe pas aux transactions au-delà de la mise en relation et du traitement sécurisé des paiements.</p>
                <p class="mt-2">Les vendeurs sont seuls responsables de la qualité, de la conformité et de la légalité des produits qu'ils proposent.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">4. Annonces et produits</h2>
                <p>Les vendeurs s'engagent à :</p>
                <ul class="mt-2 space-y-1 list-disc list-inside text-gray-600">
                    <li>Proposer uniquement des produits alimentaires frais (fruits, légumes, herbes, champignons)</li>
                    <li>Renseigner des informations exactes sur les produits (prix, quantité, date de récolte)</li>
                    <li>Respecter les réglementations sanitaires en vigueur</li>
                    <li>Honorer les commandes passées dans les délais convenus</li>
                    <li>Ne pas proposer de produits interdits à la vente</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">5. Commandes et paiements</h2>
                <p>Les paiements sont traités de manière sécurisée par Stripe. Biocolis perçoit une commission de 12% sur chaque transaction pour couvrir les frais de service et de paiement.</p>
                <p class="mt-2">Le vendeur reçoit 88% du montant hors frais de livraison, versé directement sur son compte bancaire via Stripe Connect.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">6. Annulation et remboursement</h2>
                <p>L'acheteur peut annuler une commande tant qu'elle n'est pas en cours de préparation. En cas d'annulation après paiement, le remboursement est effectué sous 5 à 10 jours ouvrés sur le moyen de paiement utilisé.</p>
                <p class="mt-2">En cas de litige, contactez notre support à <a href="mailto:support@biocolis.fr" class="text-green-600 underline">support@biocolis.fr</a>.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">7. Suspension et résiliation</h2>
                <p>Biocolis se réserve le droit de suspendre ou supprimer tout compte ne respectant pas les présentes CGU, notamment en cas de fraude, d'abus ou de comportement inapproprié.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-3">8. Droit applicable</h2>
                <p>Les présentes CGU sont soumises au droit français. En cas de litige, les parties s'engagent à rechercher une solution amiable avant tout recours judiciaire.</p>
            </section>

        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('pages.confidentialite') }}"
               class="text-sm text-green-600 hover:underline">Politique de confidentialité →</a>
            <a href="{{ route('pages.contact') }}"
               class="text-sm text-green-600 hover:underline">Nous contacter →</a>
        </div>
    </div>
</x-app-layout>
