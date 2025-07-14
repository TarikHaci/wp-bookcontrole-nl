<?php get_header(); ?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold"><?php the_title(); ?></h1>
    <p>Auteur: <?php echo get_post_meta(get_the_ID(), 'auteur', true); ?></p>
    <h2 class="text-2xl mt-6 mb-2">Correcties</h2>
    <table class="table-auto w-full">
        <thead><tr class="bg-gray-100">
            <th>Druk</th><th>Bladzijde</th><th>Type</th><th>Beschrijving</th>
        </tr></thead>
        <tbody>
        <?php
        $corrections = get_posts([
            'post_type' => 'correction',
            'meta_key' => 'book_id',
            'meta_value' => get_the_ID(),
            'post_status' => 'publish'
        ]);
        foreach ($corrections as $c): ?>
            <tr class="border-b">
                <td><?php echo get_post_meta($c->ID, 'druk', true); ?></td>
                <td><?php echo get_post_meta($c->ID, 'bladzijde', true); ?></td>
                <td class="<?php echo get_post_meta($c->ID, 'type', true) === 'aqidah' ? 'text-red-600' : ''; ?>">
                    <?php echo ucfirst(get_post_meta($c->ID, 'type', true)); ?>
                </td>
                <td><?php echo get_post_meta($c->ID, 'beschrijving', true); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php get_footer(); ?>