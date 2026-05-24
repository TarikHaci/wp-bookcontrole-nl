<?php get_header(); ?>

<?php
$book_id = get_the_ID();
$title   = get_the_title();
$author  = get_post_meta($book_id, 'auteur', true);

$corrections = get_posts([
    'post_type' => 'correction', 'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [['key' => 'book_id', 'value' => $book_id, 'compare' => '=']]
]);

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

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <!-- Back -->
    <a href="<?php echo home_url(); ?>"
       class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-600 transition-colors mb-6 no-underline">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Terug naar overzicht
    </a>

    <!-- ── Book Header ── -->
    <div class="flex flex-col sm:flex-row gap-5 sm:gap-6 mb-8 pb-6 border-b border-gray-200">
        <?php if (has_post_thumbnail($book_id)) : ?>
        <div class="flex-shrink-0 w-28 sm:w-32 bg-gradient-to-b from-gray-50 to-gray-100 rounded-lg overflow-hidden shadow-md self-start">
            <?php the_post_thumbnail('medium', ['class' => 'w-full h-auto']); ?>
        </div>
        <?php endif; ?>

        <div class="flex-1 min-w-0">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-1 leading-tight"><?php echo esc_html($title); ?></h1>
            <p class="text-sm text-gray-400 mb-4"><?php echo esc_html($author ?: 'Auteur onbekend'); ?></p>

            <div class="flex flex-wrap gap-1.5 mb-4">
                <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?php echo count($corrections); ?> correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?>
                </span>
                <?php foreach ($type_counts as $t => $cnt) : ?>
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold <?php echo bc_type_badge_classes($t); ?>">
                        <?php echo ucfirst(esc_html($t)); ?> (<?php echo $cnt; ?>)
                    </span>
                <?php endforeach; ?>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo site_url('/formulier'); ?>"
                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all no-underline">
                    ✍️ Correctie melden
                </a>
                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link naar dit boek gekopieerd!');"
                        class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Deel boek
                </button>
            </div>
        </div>
    </div>

    <?php if ($corrections) : ?>

        <!-- Filter -->
        <?php if (count($types_available) > 1) : ?>
        <div id="bc-filter-bar" class="flex flex-wrap gap-1.5 mb-5">
            <button data-filter="alle"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition-all border bg-white shadow-sm text-emerald-700 border-emerald-300">Alle</button>
            <?php foreach ($types_available as $ft) : ?>
                <button data-filter="<?php echo esc_attr($ft); ?>"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition-all border border-transparent text-gray-500 hover:bg-white hover:shadow-sm hover:border-gray-200">
                    <?php echo ucfirst(esc_html($ft)); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-3">
            <?php foreach ($corrections as $idx => $correction) :
                $type = get_post_meta($correction->ID, 'type', true);
                $druk = get_post_meta($correction->ID, 'druk', true);
                $blz  = get_post_meta($correction->ID, 'bladzijde', true);
                $desc = get_post_meta($correction->ID, 'beschrijving', true);
                $foto_meta = get_post_meta($correction->ID, 'foto', true);
                $foto = has_post_thumbnail($correction->ID) ? get_post_thumbnail_id($correction->ID) : $foto_meta;
                $permalink = get_permalink($correction->ID);
            ?>
                <div id="correctie-<?php echo $correction->ID; ?>" 
                     class="group bg-white rounded-lg border border-gray-200 p-4 shadow-sm border-l-[3px] <?php echo bc_type_border_class($type); ?> animate-fade-in-up target:ring-2 target:ring-emerald-500 target:bg-emerald-50 transition-colors relative"
                     data-correction-type="<?php echo esc_attr($type); ?>"
                     style="animation-delay:<?php echo $idx * 50; ?>ms">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold <?php echo bc_type_badge_classes($type); ?>">
                            <?php echo ucfirst(esc_html($type)); ?>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">p. <?php echo esc_html($blz); ?> · druk <?php echo esc_html($druk); ?></span>
                            <a href="<?php echo esc_url($permalink); ?>" 
                               title="Bekijk op aparte pagina"
                               class="text-gray-300 hover:text-emerald-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <button title="Kopieer link naar deze correctie"
                               class="text-gray-300 hover:text-emerald-600 transition-colors cursor-pointer bg-transparent border-none p-0"
                               onclick="navigator.clipboard.writeText('<?php echo esc_url($permalink); ?>'); alert('Link naar correctie gekopieerd!'); return false;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed"><?php echo esc_html($desc); ?></p>
                    <?php if ($foto) :
                        $full = wp_get_attachment_url($foto);
                    ?>
                        <div class="mt-2">
                            <?php echo wp_get_attachment_image($foto, 'medium', false, [
                                'class' => 'rounded-md w-full h-auto max-h-40 object-cover cursor-pointer hover:opacity-80 transition',
                                'data-lightbox' => 'true', 'data-full' => $full,
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Druk</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Blz.</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Type</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Beschrijving</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($corrections as $correction) :
                        $type = get_post_meta($correction->ID, 'type', true);
                        $druk = get_post_meta($correction->ID, 'druk', true);
                        $blz  = get_post_meta($correction->ID, 'bladzijde', true);
                        $desc = get_post_meta($correction->ID, 'beschrijving', true);
                        $foto_meta = get_post_meta($correction->ID, 'foto', true);
                        $foto = has_post_thumbnail($correction->ID) ? get_post_thumbnail_id($correction->ID) : $foto_meta;
                        $permalink = get_permalink($correction->ID);
                    ?>
                        <tr id="correctie-<?php echo $correction->ID; ?>" class="hover:bg-gray-50/60 transition-colors target:bg-emerald-50 group" data-correction-type="<?php echo esc_attr($type); ?>">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                <button title="Kopieer link"
                                   class="inline-block mr-1 text-gray-300 hover:text-emerald-600 transition-colors opacity-0 group-hover:opacity-100 target:opacity-100 bg-transparent border-none p-0 cursor-pointer"
                                   onclick="navigator.clipboard.writeText('<?php echo esc_url($permalink); ?>'); alert('Link gekopieerd!'); return false;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </button>
                                <a href="<?php echo esc_url($permalink); ?>" 
                                   title="Bekijk op aparte pagina"
                                   class="inline-block mr-1 text-gray-300 hover:text-emerald-600 transition-colors opacity-0 group-hover:opacity-100 target:opacity-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <?php echo esc_html($druk); ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600"><?php echo esc_html($blz); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold <?php echo bc_type_badge_classes($type); ?>">
                                    <?php echo ucfirst(esc_html($type)); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-sm"><?php echo esc_html($desc); ?></td>
                            <td class="px-4 py-3">
                                <?php if ($foto) :
                                    $full = wp_get_attachment_url($foto);
                                    echo wp_get_attachment_image($foto, 'thumbnail', false, [
                                        'class' => 'w-10 h-10 rounded object-cover cursor-pointer hover:scale-110 transition-transform shadow-sm',
                                        'data-lightbox' => 'true', 'data-full' => $full,
                                    ]);
                                else : ?>
                                    <span class="text-gray-300">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else : ?>
        <div class="text-center py-12 text-gray-400">
            <p class="text-sm">Nog geen goedgekeurde correcties voor dit boek.</p>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
