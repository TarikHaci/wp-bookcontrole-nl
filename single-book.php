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

// Collect unique types for filter bar
$types_available = [];
foreach ($corrections as $c) {
    $t = get_post_meta($c->ID, 'type', true);
    if ($t && !in_array($t, $types_available)) $types_available[] = $t;
}
?>

<div style="max-width:56rem;margin-inline:auto;">

    <!-- Back link -->
    <a href="<?php echo home_url(); ?>" class="bc-back-link">
        ← Terug naar overzicht
    </a>

    <!-- Book Header -->
    <div class="bc-book-header">
        <?php if (has_post_thumbnail($book_id)) : ?>
        <div class="bc-book-header__cover">
            <?php the_post_thumbnail('medium'); ?>
        </div>
        <?php endif; ?>

        <div class="bc-book-header__info">
            <h1 class="bc-book-header__title"><?php echo esc_html($title); ?></h1>
            <p class="bc-book-header__author">Auteur: <?php echo esc_html($author ?: 'Onbekend'); ?></p>

            <div class="bc-book-header__meta">
                <span class="bc-badge bc-badge--count">
                    <?php echo count($corrections); ?> correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?>
                </span>
                <?php
                $type_counts = [];
                foreach ($corrections as $c) {
                    $t = get_post_meta($c->ID, 'type', true);
                    if (!isset($type_counts[$t])) $type_counts[$t] = 0;
                    $type_counts[$t]++;
                }
                foreach ($type_counts as $t => $cnt) : ?>
                    <span class="bc-badge bc-badge--<?php echo esc_attr($t); ?>">
                        <?php echo ucfirst(esc_html($t)); ?>: <?php echo $cnt; ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <div class="bc-book-header__actions">
                <a href="<?php echo site_url('/formulier'); ?>" class="bc-btn bc-btn--primary">
                    ✍️ Meld een correctie
                </a>
                <a href="<?php echo home_url(); ?>" class="bc-btn bc-btn--secondary">
                    📚 Alle boeken
                </a>
            </div>
        </div>
    </div>

    <!-- Section heading with correction count -->
    <div class="bc-section-heading">
        <h2>Correcties</h2>
        <span class="bc-section-heading__count"><?php echo count($corrections); ?> resultaten</span>
    </div>

    <?php if ($corrections) : ?>

        <!-- Filter bar -->
        <?php if (count($types_available) > 1) : ?>
        <div id="bc-filter-bar" class="bc-filter-bar">
            <button class="bc-filter-btn is-active" data-filter="alle">Alle</button>
            <?php foreach ($types_available as $ft) : ?>
                <button class="bc-filter-btn" data-filter="<?php echo esc_attr($ft); ?>">
                    <?php echo ucfirst(esc_html($ft)); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Mobile: Correction Cards -->
        <div class="bc-correction-cards bc-show-mobile">
            <?php foreach ($corrections as $i => $correction) :
                $type        = get_post_meta($correction->ID, 'type', true);
                $druk        = get_post_meta($correction->ID, 'druk', true);
                $bladzijde   = get_post_meta($correction->ID, 'bladzijde', true);
                $beschrijving = get_post_meta($correction->ID, 'beschrijving', true);
                $foto_id     = get_post_meta($correction->ID, 'foto', true);
            ?>
                <div class="bc-correction-card bc-correction-card--<?php echo esc_attr($type); ?>"
                     data-correction-type="<?php echo esc_attr($type); ?>"
                     style="animation-delay:<?php echo $i * 60; ?>ms">
                    <div class="bc-correction-card__row">
                        <span class="bc-correction-card__label">Druk</span>
                        <span class="bc-correction-card__value"><?php echo esc_html($druk); ?></span>
                    </div>
                    <div class="bc-correction-card__row">
                        <span class="bc-correction-card__label">Bladzijde</span>
                        <span class="bc-correction-card__value"><?php echo esc_html($bladzijde); ?></span>
                    </div>
                    <div class="bc-correction-card__row">
                        <span class="bc-correction-card__label">Type</span>
                        <span class="bc-correction-card__value">
                            <span class="bc-badge bc-badge--<?php echo esc_attr($type); ?>"><?php echo ucfirst(esc_html($type)); ?></span>
                        </span>
                    </div>
                    <div class="bc-correction-card__desc">
                        <strong>Beschrijving:</strong><br>
                        <?php echo esc_html($beschrijving); ?>
                    </div>
                    <?php if ($foto_id) :
                        $full_url = wp_get_attachment_url($foto_id);
                    ?>
                        <div style="margin-top:var(--space-3);">
                            <?php echo wp_get_attachment_image($foto_id, 'medium', false, [
                                'class'     => 'bc-table__thumbnail',
                                'style'     => 'width:100%;height:auto;max-height:12rem;',
                                'data-full' => $full_url,
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Desktop: Table -->
        <div class="bc-table-wrapper bc-hide-mobile">
            <table class="bc-table">
                <thead>
                    <tr>
                        <th>Druk</th>
                        <th>Bladzijde</th>
                        <th>Type</th>
                        <th>Beschrijving</th>
                        <th>Afbeelding</th>
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
                        <tr data-correction-type="<?php echo esc_attr($type); ?>">
                            <td><?php echo esc_html($druk); ?></td>
                            <td><?php echo esc_html($bladzijde); ?></td>
                            <td>
                                <span class="bc-badge bc-badge--<?php echo esc_attr($type); ?>">
                                    <?php echo ucfirst(esc_html($type)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($beschrijving); ?></td>
                            <td>
                                <?php if ($foto_id) :
                                    $full_url = wp_get_attachment_url($foto_id);
                                    echo wp_get_attachment_image($foto_id, 'thumbnail', false, [
                                        'class'     => 'bc-table__thumbnail',
                                        'data-full' => $full_url,
                                    ]);
                                else : ?>
                                    <span style="color:var(--clr-gray-400);font-size:var(--text-xs);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else : ?>
        <div class="bc-empty-state">
            <span class="bc-empty-state__icon">✅</span>
            <p class="bc-empty-state__text">Er zijn nog geen goedgekeurde correcties voor dit boek.</p>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
