<?php get_header(); ?>

<div class="container mx-auto p-8">
    <h1 class="text-4xl font-bold mb-4">Welkom bij BoekControle.nl</h1>
    <p class="text-lg text-gray-700 mb-6">
        Deze website helpt bij het verzamelen en tonen van gemelde fouten in islamitische boeken.
        Je kunt boeken bekijken, meldingen van correcties inzien, en zelf fouten melden via het formulier.
    </p>

    <div class="bg-blue-100 p-4 rounded-lg shadow mb-6">
        <h2 class="text-2xl font-semibold mb-2">Laatste boeken</h2>
        <ul class="space-y-2">
            <?php
            $recent_books = new WP_Query([
                'post_type' => 'book',
                'posts_per_page' => 5
            ]);
            if ($recent_books->have_posts()) :
                while ($recent_books->have_posts()) : $recent_books->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>" class="text-blue-600 hover:underline">
                            <?php the_title(); ?>
                        </a>
                    </li>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <li>Geen boeken gevonden.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div>
        <a href="<?php echo get_post_type_archive_link('book'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Bekijk alle boeken
        </a>
    </div>
</div>

<?php get_footer(); ?>
