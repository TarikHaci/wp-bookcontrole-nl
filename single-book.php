<?php get_header(); ?>

<?php
$book_id = get_the_ID();
$title   = get_the_title();
$author  = get_post_meta($book_id, 'auteur', true);

$corrections = get_posts([
    'post_type'   => 'correction',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'book_id',
        'value'   => $book_id,
        'compare' => '='
    ]]
]);

// Collect unique types for filter
$types_available = [];
$type_counts = [];
foreach ($corrections as $c) {
    $t = get_post_meta($c->ID, 'type', true);
    if ($t) {
        if (!in_array($t, $types_available)) $types_available[] = $t;
        if (!isset($type_counts[$t])) $type_counts[$t] = 0;
        $type_counts[$t]++;
    }
}
?>

<div class="max-w-4xl mx-auto">

    <!-- Back link -->
    <a href="<?php echo home_url(); ?>"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors mb-6 no-underline">
        ← Terug naar overzicht
    </a>

    <!-- ── Book Header ── -->
    <div class="flex flex-col sm:flex-row gap-6 sm:gap-8 mb-8 pb-8 border-b border-gray-200">
        <?php if (has_post_thumbnail($book_id)) : ?>
        <div class="flex-shrink-0 w-40 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl overflow-hidden shadow-lg">
            <?php the_post_thumbnail('medium', ['class' => 'w-full h-auto']); ?>
        </div>
        <?php endif; ?>

        <div class="flex-1">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2 leading-tight"><?php echo esc_html($title); ?></h1>
            <p class="text-base text-gray-500 mb-4">Auteur: <?php echo esc_html($author ?: 'Onbekend'); ?></p>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mb-5">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?php echo count($corrections); ?> correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?>
                </span>
                <?php foreach ($type_counts as $t => $cnt) : ?>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold <?php echo bc_type_badge_classes($t); ?>">
                        <?php echo ucfirst(esc_html($t)); ?>: <?php echo $cnt; ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo site_url('/formulier'); ?>"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-600 hover:to-emerald-500 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-md shadow-emerald-600/25 transition-all hover:-translate-y-0.5 no-underline">
                    ✍️ Meld een correctie
                </a>
                <a href="<?php echo home_url(); ?>"
                   class="inline-flex items-center gap-2 bg-white text-emerald-700 border-2 border-emerald-200 hover:border-emerald-400 hover:bg-emerald-50 px-5 py-2.5 rounded-full text-sm font-semibold shadow-sm transition-all no-underline">
                    📚 Alle boeken
                </a>
            </div>
        </div>
    </div>

    <!-- ── Section Heading ── -->
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-900">Correcties</h2>
        <span class="text-sm text-gray-500 font-medium"><?php echo count($corrections); ?> resultaten</span>
    </div>

    <?php if ($corrections) : ?>

        <!-- Filter bar -->
        <?php if (count($types_available) > 1) : ?>
        <div id="bc-filter-bar" class="flex flex-wrap gap-2 mb-6 p-3 bg-gray-50 rounded-xl border border-gray-200">
            <button data-filter="alle"
                    class="px-4 py-2 rounded-full text-xs font-semibold cursor-pointer transition-all border border-transparent bg-white shadow-sm text-emerald-700 border-emerald-300">
                Alle
            </button>
            <?php foreach ($types_available as $ft) : ?>
                <button data-filter="<?php echo esc_attr($ft); ?>"
                        class="px-4 py-2 rounded-full text-xs font-semibold cursor-pointer transition-all border border-transparent text-gray-500 hover:bg-white hover:shadow-sm">
                    <?php echo ucfirst(esc_html($ft)); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ── Mobile: Cards ── -->
        <div class="sm:hidden space-y-4">
            <?php foreach ($corrections as $i => $correction) :
                $type        = get_post_meta($correction->ID, 'type', true);
                $druk        = get_post_meta($correction->ID, 'druk', true);
                $bladzijde   = get_post_meta($correction->ID, 'bladzijde', true);
                $beschrijving = get_post_meta($correction->ID, 'beschrijving', true);
                $foto_id     = get_post_meta($correction->ID, 'foto', true);
            ?>
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm border-l-4 <?php echo bc_type_border_class($type); ?> animate-fade-in-up"
                     data-correction-type="<?php echo esc_attr($type); ?>"
                     style="animation-delay:<?php echo $i * 60; ?>ms">

                    <div class="flex gap-3 mb-2 text-sm">
                        <span class="font-semibold text-gray-500 min-w-[5.5rem] flex-shrink-0">Druk</span>
                        <span class="text-gray-800"><?php echo esc_html($druk); ?></span>
                    </div>
                    <div class="flex gap-3 mb-2 text-sm">
                        <span class="font-semibold text-gray-500 min-w-[5.5rem] flex-shrink-0">Bladzijde</span>
                        <span class="text-gray-800"><?php echo esc_html($bladzijde); ?></span>
                    </div>
                    <div class="flex gap-3 mb-2 text-sm">
                        <span class="font-semibold text-gray-500 min-w-[5.5rem] flex-shrink-0">Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo bc_type_badge_classes($type); ?>">
                            <?php echo ucfirst(esc_html($type)); ?>
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 leading-relaxed">
                        <strong class="text-gray-500 block mb-1">Beschrijving</strong>
                        <?php echo esc_html($beschrijving); ?>
                    </div>

                    <?php if ($foto_id) :
                        $full_url = wp_get_attachment_url($foto_id);
                    ?>
                        <div class="mt-3">
                            <?php echo wp_get_attachment_image($foto_id, 'medium', false, [
                                'class'         => 'rounded-lg shadow-sm w-full h-auto max-h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity',
                                'data-lightbox'  => 'true',
                                'data-full'      => $full_url,
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ── Desktop: Table ── -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Druk</th>
                        <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Bladzijde</th>
                        <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Type</th>
                        <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-gray-500 border-b-2 border-gray-200">Beschrijving</th>
                        <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Afbeelding</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($corrections as $correction) :
                        $type        = get_post_meta($correction->ID, 'type', true);
                        $druk        = get_post_meta($correction->ID, 'druk', true);
                        $bladzijde   = get_post_meta($correction->ID, 'bladzijde', true);
                        $beschrijving = get_post_meta($correction->ID, 'beschrijving', true);
                        $foto_id     = get_post_meta($correction->ID, 'foto', true);
                    ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50/80 transition-colors"
                            data-correction-type="<?php echo esc_attr($type); ?>">
                            <td class="px-4 py-3 text-gray-700"><?php echo esc_html($druk); ?></td>
                            <td class="px-4 py-3 text-gray-700"><?php echo esc_html($bladzijde); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo bc_type_badge_classes($type); ?>">
                                    <?php echo ucfirst(esc_html($type)); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 max-w-md"><?php echo esc_html($beschrijving); ?></td>
                            <td class="px-4 py-3">
                                <?php if ($foto_id) :
                                    $full_url = wp_get_attachment_url($foto_id);
                                    echo wp_get_attachment_image($foto_id, 'thumbnail', false, [
                                        'class'         => 'w-12 h-12 rounded-lg object-cover shadow-sm cursor-pointer hover:scale-110 transition-transform',
                                        'data-lightbox'  => 'true',
                                        'data-full'      => $full_url,
                                    ]);
                                else : ?>
                                    <span class="text-gray-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else : ?>
        <div class="text-center py-16 text-gray-400">
            <span class="block text-4xl mb-4">✅</span>
            <p class="text-base">Er zijn nog geen goedgekeurde correcties voor dit boek.</p>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
