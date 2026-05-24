<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BoekControle.nl — Correctiemeldingen voor Islamitische boeken.">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-[#f8fafb] text-gray-900 font-sans'); ?>>

    <!-- ── HEADER ── -->
    <header class="bg-gradient-to-r from-[#022c22] via-[#064e3b] to-[#065f46] sticky top-0 z-50 shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-[60px]">

                <!-- Logo -->
                <a href="<?php echo home_url(); ?>" class="flex items-center gap-2.5 no-underline group">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-shadow">
                        <span class="text-sm">📖</span>
                    </div>
                    <span class="text-white text-[17px] font-bold tracking-tight">
                        Boek<span class="text-amber-400">Controle</span><span class="font-normal text-white/50 text-sm">.nl</span>
                    </span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden sm:flex items-center gap-1">
                    <a href="<?php echo home_url(); ?>"
                       class="text-white/70 hover:text-white px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors no-underline hover:bg-white/[0.08]">
                        Boeken
                    </a>
                    <a href="<?php echo site_url('/over-ons'); ?>"
                       class="text-white/70 hover:text-white px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors no-underline hover:bg-white/[0.08]">
                        Over ons
                    </a>
                    <a href="<?php echo site_url('/formulier'); ?>"
                       class="ml-3 inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-white px-4 py-[7px] rounded-lg text-[13px] font-semibold shadow-md shadow-amber-500/30 hover:shadow-lg transition-all no-underline">
                        ✍️ Correctie melden
                    </a>
                </nav>

                <!-- Mobile toggle -->
                <button id="bc-menu-toggle"
                        class="sm:hidden w-9 h-9 rounded-lg bg-white/10 border border-white/15 text-white text-base flex items-center justify-center hover:bg-white/20 transition-colors"
                        aria-label="Menu" aria-expanded="false">☰</button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div id="bc-mobile-nav" class="mobile-nav sm:hidden border-t border-white/10">
            <div class="px-4 py-2 space-y-0.5">
                <a href="<?php echo home_url(); ?>" class="block px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 text-sm font-medium transition-colors no-underline">📚 Boeken</a>
                <a href="<?php echo site_url('/over-ons'); ?>" class="block px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 text-sm font-medium transition-colors no-underline">ℹ️ Over ons</a>
                <a href="<?php echo site_url('/formulier'); ?>" class="block px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 text-sm font-medium transition-colors no-underline">✍️ Correctie melden</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
