<?php get_header(); ?>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Boeken</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php while (have_posts()) : the_post(); ?>
            <div class="border rounded-xl p-4 bg-white shadow">
                <h2 class="text-xl font-semibold"><?php the_title(); ?></h2>
                <p><?php echo get_post_meta(get_the_ID(), 'auteur', true); ?></p>
                <?php
                    $corrections = get_posts([
                        'post_type' => 'correction',
                        'meta_key' => 'book_id',
                        'meta_value' => get_the_ID(),
                        'post_status' => 'publish'
                    ]);
                    $has_aqidah = false;
                    foreach ($corrections as $c) {
                        if (get_post_meta($c->ID, 'type', true) === 'aqidah') {
                            $has_aqidah = true;
                            break;
                        }
                    }
                ?>
                <?php if ($has_aqidah): ?>
                    <span class="text-red-600 font-bold">⚠️ Aqidah fout gemeld</span>
                <?php endif; ?>
                <p><?php echo count($corrections); ?> correcties</p>
                <a class="text-blue-500 underline" href="<?php the_permalink(); ?>">Bekijk</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php get_footer(); ?>
