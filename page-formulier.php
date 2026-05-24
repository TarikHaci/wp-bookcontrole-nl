<?php
/* Template Name: Correctieformulier */
get_header();
?>

<div class="max-w-lg mx-auto px-4 sm:px-6 py-8">

    <!-- Form Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <!-- Green top bar -->
        <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-700"></div>

        <div class="p-5 sm:p-7">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-11 h-11 rounded-lg bg-emerald-600 flex items-center justify-center mx-auto mb-3 text-xl shadow-md shadow-emerald-500/20">✍️</div>
                <h1 class="text-xl font-extrabold text-gray-900 mb-1">Correctie melden</h1>
                <p class="text-xs text-gray-400">Heeft u een fout gevonden? Meld het hieronder.</p>
            </div>

            <?php get_template_part('template-parts/correction-form'); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>