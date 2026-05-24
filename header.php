<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BoekControle.nl — Correctiemeldingen voor Islamitische boeken. Meld fouten, bekijk correcties en help de ummah.">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 text-gray-900 font-sans'); ?>>

    <!-- Header -->
    <header class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-800 sticky top-0 z-50 shadow-lg shadow-emerald-950/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">

                <!-- Logo -->
                <a href="<?php echo home_url(); ?>" class="flex items-center gap-3 text-white no-underline hover:opacity-90 transition-opacity">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png"
                         alt="BoekControle Logo"
                         class="h-9 w-9 rounded-lg object-contain shadow-md shadow-amber-500/20">
                    <span class="text-lg sm:text-xl font-bold tracking-tight">
                        BoekControle<span class="font-normal opacity-60">.nl</span>
                    </span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden sm:flex items-center gap-2">
                    <a href="<?php echo home_url(); ?>"
                       class="text-white/75 hover:text-white hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all no-underline">
                        Boeken
                    </a>
                    <a href="<?php echo site_url('/over-ons'); ?>"
                       class="text-white/75 hover:text-white hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all no-underline">
                        Over ons
                    </a>
                    <a href="<?php echo site_url('/formulier'); ?>"
                       class="ml-2 inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/30 transition-all hover:-translate-y-0.5 no-underline">
                        ✍️ Correctie melden
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="bc-menu-toggle"
                        class="sm:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 border border-white/20 text-white text-lg hover:bg-white/20 transition-colors"
                        aria-label="Menu" aria-expanded="false">
                    ☰
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div id="bc-mobile-nav" class="mobile-nav sm:hidden border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                <a href="<?php echo home_url(); ?>"
                   class="block px-4 py-3 rounded-lg text-white/90 hover:bg-white/10 font-medium transition-colors no-underline">
                    📚 Boeken overzicht
                </a>
                <a href="<?php echo site_url('/over-ons'); ?>"
                   class="block px-4 py-3 rounded-lg text-white/90 hover:bg-white/10 font-medium transition-colors no-underline">
                    ℹ️ Over ons
                </a>
                <a href="<?php echo site_url('/formulier'); ?>"
                   class="block px-4 py-3 rounded-lg text-white/90 hover:bg-white/10 font-medium transition-colors no-underline">
                    ✍️ Correctie melden
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
