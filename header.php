<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BoekControle.nl — Correctiemeldingen voor Islamitische boeken. Meld fouten, bekijk correcties en help de ummah.">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 text-gray-900'); ?>>

    <header class="bc-header">
        <div class="bc-container bc-header__inner">
            <a href="<?php echo home_url(); ?>" class="bc-header__logo">
                <span class="bc-header__logo-icon">📖</span>
                BoekControle<span style="font-weight:400;opacity:0.7">.nl</span>
            </a>

            <nav class="bc-header__nav">
                <a href="<?php echo home_url(); ?>" class="bc-header__nav-link">Boeken</a>
                <a href="<?php echo site_url('/formulier'); ?>" class="bc-btn bc-btn--gold bc-btn--sm">
                    ✍️ Correctie melden
                </a>
            </nav>

            <button id="bc-menu-toggle" class="bc-header__menu-toggle" aria-label="Menu" aria-expanded="false">
                ☰
            </button>
        </div>

        <div id="bc-mobile-nav" class="bc-container bc-header__mobile-nav">
            <a href="<?php echo home_url(); ?>">📚 Boeken overzicht</a>
            <a href="<?php echo site_url('/formulier'); ?>">✍️ Correctie melden</a>
        </div>
    </header>

    <main class="bc-main bc-container">
