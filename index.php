<?php get_header(); ?>

<div class="container mx-auto p-6">
    <!-- Top bar met melding knop -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">📚 Overzicht van boeken</h1>
        <a href="<?php echo site_url('/formulier'); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow hover:bg-blue-700 text-lg">
            ✍️ Meld een correctie
        </a>
    </div>

    <?php
    $books = get_posts(['post_type' => 'book', 'numberposts' => -1]);
    if ($books) :
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($books as $book) :
            $book_id = $book->ID;
            $title = get_the_title($book_id);
            $author = get_post_meta($book_id, 'auteur', true);

            $corrections = get_posts([
                'post_type' => 'correction',
                'numberposts' => -1,
                'post_status' => 'publish',
                'meta_key' => 'book_id',
                'meta_value' => $book_id,
            ]);

            // Categoriseer fouten
            $types = [];
            foreach ($corrections as $correction) {
                $type = get_post_meta($correction->ID, 'type', true);
                if (!isset($types[$type])) {
                    $types[$type] = 0;
                }
                $types[$type]++;
            }

            $has_aqidah = isset($types['aqidah']);
        ?>
        <div class="bg-white border border-gray-200 rounded-xl shadow p-5 flex flex-col justify-between h-full">
            <?php if (has_post_thumbnail($book_id)) : ?>
                <div class="mb-3">
                    <?php echo get_the_post_thumbnail($book_id, 'medium', ['class' => 'rounded']); ?>
                </div>
            <?php endif; ?>

            <div>
                <h2 class="text-xl font-bold mb-1"><?php echo esc_html($title); ?></h2>
                <p class="text-sm text-gray-500 mb-2">Auteur: <?php echo esc_html($author); ?></p>

                <p class="mb-2 text-sm text-gray-700">
                    <strong><?php echo count($corrections); ?></strong> correcties gemeld
                </p>

                <div class="mb-2 space-y-1 text-sm">
                    <?php foreach ($types as $type => $count) : ?>
                        <span class="inline-block px-2 py-1 rounded 
                            <?php echo ($type === 'aqidah') ? 'bg-red-100 text-red-700 font-semibold' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo ucfirst($type); ?>: <?php echo $count; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?php echo get_permalink($book_id); ?>" class="inline-block mt-2 text-blue-600 hover:underline font-medium">
                    📖 Bekijk correcties →
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <p class="text-gray-600">Er zijn nog geen boeken toegevoegd.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
