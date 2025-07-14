<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> class="bg-gray-50 text-gray-900">
    <header class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="<?php echo home_url(); ?>" class="text-xl font-bold text-gray-800">
                📚 BoekControle.nl
            </a>
            <a href="<?php echo site_url('/formulier'); ?>"
               class="text-sm sm:text-base bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
               ✍️ Correctie melden
            </a>
        </div>
    </header>
    <main class="px-4 pt-6 pb-16">
