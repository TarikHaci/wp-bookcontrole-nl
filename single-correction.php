<?php get_header(); ?>

<?php
$correction_id = get_the_ID();
$book_id       = get_post_meta($correction_id, 'book_id', true);
$type          = get_post_meta($correction_id, 'type', true);
$druk          = get_post_meta($correction_id, 'druk', true);
$blz           = get_post_meta($correction_id, 'bladzijde', true);
$desc          = get_post_meta($correction_id, 'beschrijving', true);

$book_title = $book_id ? get_the_title($book_id) : 'Boek onbekend';
$book_link  = $book_id ? get_permalink($book_id) : home_url();
$author     = $book_id ? get_post_meta($book_id, 'auteur', true) : '';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- Terug navigatie -->
    <a href="<?php echo esc_url($book_link); ?>"
       class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-600 transition-colors mb-6 no-underline">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Terug naar alle correcties voor "<?php echo esc_html(wp_trim_words($book_title, 5, '...')); ?>"
    </a>

    <!-- Detailkaart -->
    <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <?php if ($book_id && has_post_thumbnail($book_id)) : ?>
                    <div class="hidden sm:block flex-shrink-0 w-12 h-16 bg-white rounded shadow-sm overflow-hidden border border-gray-200">
                        <?php echo get_the_post_thumbnail($book_id, 'thumbnail', ['class' => 'w-full h-full object-cover']); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 mb-1 leading-tight">
                        Correctiemelding
                    </h1>
                    <p class="text-sm text-gray-500">
                        Gekoppeld aan: <a href="<?php echo esc_url($book_link); ?>" class="font-semibold text-emerald-600 hover:underline"><?php echo esc_html($book_title); ?></a>
                        <?php if ($author) echo ' <span class="opacity-60">— ' . esc_html($author) . '</span>'; ?>
                    </p>
                </div>
            </div>
            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link gekopieerd!');"
                    class="inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-50 hover:text-emerald-600 transition-colors cursor-pointer shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Kopieer link
            </button>
        </div>

        <div class="p-6 sm:p-8">
            
            <!-- Metadata tags -->
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Type fout</span>
                    <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold self-start <?php echo bc_type_badge_classes($type); ?>">
                        <?php echo ucfirst(esc_html($type ?: 'Onbekend')); ?>
                    </span>
                </div>
                <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bladzijde</span>
                    <span class="text-sm font-semibold text-gray-800"><?php echo esc_html($blz ?: '—'); ?></span>
                </div>
                <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Druk / Editie</span>
                    <span class="text-sm font-semibold text-gray-800"><?php echo esc_html($druk ?: '—'); ?></span>
                </div>
                <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Weergaven</span>
                    <span class="text-sm font-semibold text-gray-800 font-medium text-emerald-700">👁️ <?php echo number_format_i18n(boekcontrole_get_views($correction_id)); ?></span>
                </div>
            </div>

            <!-- Beschrijving -->
            <div class="mb-8">
                <h2 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Beschrijving van de fout</h2>
                <div class="prose prose-sm prose-emerald max-w-none text-gray-700 leading-relaxed bg-gray-50/50 p-5 rounded-lg border border-gray-100">
                    <?php echo nl2br(esc_html($desc)); ?>
                </div>
            </div>

            <!-- Afbeeldingen -->
            <?php $fotos = bc_get_correction_fotos($correction_id); if (!empty($fotos)) : ?>
            <div>
                <h2 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Bijgevoegde afbeelding<?php echo count($fotos) > 1 ? 'en' : ''; ?> (<?php echo count($fotos); ?>)
                </h2>
                <div class="grid gap-3 <?php echo count($fotos) > 1 ? 'grid-cols-2' : 'grid-cols-1'; ?>">
                    <?php foreach ($fotos as $foto_id) :
                        $full_url = wp_get_attachment_url($foto_id);
                    ?>
                        <div class="border border-gray-200 p-2 rounded-xl inline-block shadow-sm">
                            <?php echo wp_get_attachment_image($foto_id, 'large', false, [
                                'class' => 'rounded-lg max-h-[500px] w-auto cursor-zoom-in hover:opacity-90 transition-opacity',
                                'data-lightbox' => 'true',
                                'data-full' => $full_url,
                            ]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </article>

</div>

<?php get_footer(); ?>
