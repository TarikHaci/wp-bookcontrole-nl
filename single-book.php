<?php get_header(); ?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-2"><?php the_title(); ?></h1>
    <p class="text-gray-600 mb-4">Auteur: <?php echo esc_html(get_post_meta(get_the_ID(), 'auteur', true)); ?></p>

    <a href="<?php echo site_url('/formulier'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-6">
        ✍️ Meld een correctie voor dit boek
    </a>

    <h2 class="text-2xl font-semibold mb-4">Correcties voor dit boek</h2>

    <?php
    $corrections = get_posts([
        'post_type' => 'correction',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => [[
            'key' => 'book_id',
            'value' => get_the_ID(),
            'compare' => '='
        ]]
    ]);

    if ($corrections) :
    ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2">Druk</th>
                        <th class="px-4 py-2">Bladzijde</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Beschrijving</th>
                        <th class="px-4 py-2">Afbeelding</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($corrections as $correction) : 
                        $type = get_post_meta($correction->ID, 'type', true);
                        $type_class = $type === 'aqidah' ? 'text-red-600 font-bold' : '';
                    ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo esc_html(get_post_meta($correction->ID, 'druk', true)); ?></td>
                            <td class="px-4 py-2"><?php echo esc_html(get_post_meta($correction->ID, 'bladzijde', true)); ?></td>
                            <td class="px-4 py-2 <?php echo $type_class; ?>"><?php echo ucfirst(esc_html($type)); ?></td>
                            <td class="px-4 py-2"><?php echo esc_html(get_post_meta($correction->ID, 'beschrijving', true)); ?></td>
                            <td class="px-4 py-2">
                                <?php
                                $attachment_id = get_post_meta($correction->ID, 'foto', true);
                                if ($attachment_id) {
                                    echo wp_get_attachment_image($attachment_id, 'thumbnail', false, ['class' => 'rounded shadow']);
                                } else {
                                    echo '<span class="text-gray-400 text-sm">Geen afbeelding</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p class="text-gray-600 italic">Er zijn nog geen goedgekeurde correcties voor dit boek.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
