<?php
/* Template Name: Correctieformulier */
get_header();
?>

<div class="max-w-xl mx-auto">

    <!-- Steps indicator -->
    <div class="flex items-center justify-center gap-1 mb-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-2 rounded-full">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">1</span>
            <span class="hidden sm:inline">Boek kiezen</span>
        </div>
        <div class="w-6 sm:w-8 h-0.5 bg-gray-200 rounded"></div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 px-3 py-2 rounded-full">
            <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs font-bold">2</span>
            <span class="hidden sm:inline">Details invullen</span>
        </div>
        <div class="w-6 sm:w-8 h-0.5 bg-gray-200 rounded"></div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 px-3 py-2 rounded-full">
            <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs font-bold">3</span>
            <span class="hidden sm:inline">Versturen</span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-lg p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-emerald-500/25">
                ✍️
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Correctie melden</h1>
            <p class="text-sm text-gray-500">Heeft u een fout gevonden in een Islamitisch boek? Meld het hieronder.</p>
        </div>

        <?php get_template_part('template-parts/correction-form'); ?>
    </div>
</div>

<?php get_footer(); ?>