<?php
/* Template Name: Over ons */
get_header();
?>

<div class="max-w-3xl mx-auto">

    <!-- Page Header -->
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center mx-auto mb-5 text-3xl shadow-lg shadow-emerald-500/25">
            ℹ️
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3 tracking-tight">Over ons</h1>
        <p class="text-gray-500 text-base max-w-lg mx-auto">Leer meer over het doel van BoekControle en de persoon achter dit initiatief.</p>
    </div>

    <!-- Main content card -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-8">

        <!-- Gradient accent -->
        <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-amber-400 to-emerald-500"></div>

        <div class="p-6 sm:p-10">

            <!-- About section -->
            <div class="mb-10">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-lg">🎯</span>
                    Het doel
                </h2>
                <div class="text-gray-600 text-sm sm:text-base leading-relaxed space-y-4 pl-0 sm:pl-[3.25rem]">
                    <p>
                        Deze website is een centrale plek waar men vergissingen en correcties in Islamitische boeken kan terugvinden. Het doel is om als ummah samen te werken aan het verspreiden van correcte kennis van onze mooie religie.
                    </p>
                    <p>
                        Iedereen kan via deze website een correctie melden. Elke melding wordt eerst beoordeeld voordat deze wordt gepubliceerd, zodat de kwaliteit gewaarborgd blijft.
                    </p>
                </div>
            </div>

            <hr class="border-gray-100 my-8">

            <!-- About Bilal Abu Yunus -->
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-lg">👤</span>
                    Bilal Abu Yunus
                </h2>
                <div class="text-gray-600 text-sm sm:text-base leading-relaxed space-y-4 pl-0 sm:pl-[3.25rem]">
                    <p>
                        Deze website staat onder toezicht van <strong class="text-gray-800">Bilal Abu Yunus</strong>. Hij is ruim tien jaar werkzaam als vertaler en controleur van islamitische boeken.
                    </p>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Achtergrond</h3>
                        <div class="flex items-start gap-3">
                            <span class="text-emerald-500 mt-0.5">🎓</span>
                            <p class="text-sm text-gray-600">
                                Abu Yunus heeft in 2017 een <strong class="text-gray-700">masteropleiding</strong> afgerond in <strong class="text-gray-700">Linguistics (Taalkunde)</strong> aan de universiteit van Leiden. Hij heeft aan dezelfde universiteit een minor in <strong class="text-gray-700">Vertaalwetenschappen</strong> afgerond.
                            </p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-emerald-500 mt-0.5">📖</span>
                            <p class="text-sm text-gray-600">
                                Sinds 2017 studeert hij in het buitenland <strong class="text-gray-700">islamitische wetenschappen</strong> bij verschillende geleerden in o.a. Saoedi-Arabië, Egypte, Koeweit en Marokko.
                            </p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-emerald-500 mt-0.5">🏛️</span>
                            <p class="text-sm text-gray-600">
                                Hij heeft in deze periode tevens een vierjarige <strong class="text-gray-700">bacheloropleiding</strong> afgerond in de <strong class="text-gray-700">Arabische taalwetenschappen</strong> aan de Imam Muhammad universiteit te Riyad.
                            </p>
                        </div>
                    </div>

                    <blockquote class="border-l-4 border-emerald-400 pl-4 py-2 text-gray-500 italic text-sm bg-emerald-50/50 rounded-r-lg pr-4">
                        "Moge Allaah onze intenties zuiveren en onze daden accepteren."
                    </blockquote>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center">
        <a href="<?php echo site_url('/formulier'); ?>"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-600 hover:to-emerald-500 text-white px-6 py-3 rounded-full text-sm font-semibold shadow-md shadow-emerald-600/25 transition-all hover:-translate-y-0.5 no-underline">
            ✍️ Correctie melden
        </a>
    </div>
</div>

<?php get_footer(); ?>
