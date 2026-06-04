<?php get_header(); ?>

<div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">📚 Boeken</h1>
            <p class="text-sm text-gray-500 mt-1">Overzicht van alle boeken met correctiemeldingen</p>
        </div>
        <a href="<?php echo site_url('/formulier'); ?>"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-600 hover:to-emerald-500 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-md shadow-emerald-600/25 transition-all hover:-translate-y-0.5 no-underline self-start">
            ✍️ Meld een correctie
        </a>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $card_index = 0;
        while (have_posts()) : the_post();
            $card_index++;
            $book_id = get_the_ID();
            $title   = get_the_title();
            $author  = get_post_meta($book_id, 'auteur', true);

            $corrections = get_posts([
                'post_type'   => 'correction',
                'meta_key'    => 'book_id',
                'meta_value'  => $book_id,
                'post_status' => 'publish',
                'numberposts' => -1,
            ]);

            $types = [];
            foreach ($corrections as $c) {
                $t = get_post_meta($c->ID, 'type', true);
                $c_types = is_array($t) ? $t : ($t ? [$t] : []);
                foreach ($c_types as $tt) {
                    if (!isset($types[$tt])) $types[$tt] = 0;
                    $types[$tt]++;
                }
            }

            $has_inhoudelijk = isset($types['inhoudelijk']);
        ?>
        <article class="group bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-xl hover:shadow-emerald-500/[0.07] hover:-translate-y-1 transition-all duration-300 animate-fade-in-up stagger-<?php echo min($card_index, 12); ?>">

            <!-- Cover -->
            <div class="relative bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center p-6 min-h-[12rem]">
                <?php if (has_post_thumbnail($book_id)) : ?>
                    <?php the_post_thumbnail('medium', [
                        'class' => 'max-h-44 w-auto object-contain rounded shadow-md group-hover:scale-[1.03] transition-transform duration-300'
                    ]); ?>
                <?php else : ?>
                    <span class="text-5xl opacity-25">📖</span>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="p-5 flex-1 flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 mb-1 leading-snug"><?php echo esc_html($title); ?></h2>
                <p class="text-sm text-gray-500 mb-3"><?php echo esc_html($author ?: 'Auteur onbekend'); ?></p>

                <p class="text-sm text-gray-600 mb-3 font-medium">
                    <strong class="text-gray-900"><?php echo count($corrections); ?></strong>
                    correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?>
                </p>

                <?php if (!empty($types)) : ?>
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <?php foreach ($types as $type => $count) : ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo bc_type_badge_classes($type); ?>">
                            <?php echo ucfirst(esc_html($type)); ?>: <?php echo $count; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="mt-auto pt-4 border-t border-gray-100">
                    <a href="<?php the_permalink(); ?>"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-500 hover:gap-3 transition-all no-underline">
                        Bekijk correcties <span>→</span>
                    </a>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>

    <?php if (!have_posts()) : ?>
    <div class="text-center py-20 text-gray-400">
        <span class="block text-5xl mb-4">📚</span>
        <p class="text-base">Er zijn nog geen boeken toegevoegd.</p>
    </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
