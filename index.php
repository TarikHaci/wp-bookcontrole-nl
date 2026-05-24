<?php get_header(); ?>

<?php
// Get statistics
$stats = boekcontrole_get_stats();
$books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => 'publish']);
?>

<!-- Hero Section -->
<section class="bc-hero">
    <div style="position:relative;z-index:1;">
        <h1 class="bc-hero__title">Correcties in Islamitische boeken</h1>
        <p class="bc-hero__subtitle">Help mee om fouten in vertalingen en publicaties te signaleren. Samen zorgen we voor betrouwbare Islamitische literatuur.</p>

        <div class="bc-hero__stats">
            <div class="bc-hero__stat">
                <span class="bc-hero__stat-number"><?php echo intval($stats['books']); ?></span>
                <span class="bc-hero__stat-label">Boeken</span>
            </div>
            <div class="bc-hero__stat">
                <span class="bc-hero__stat-number"><?php echo intval($stats['corrections']); ?></span>
                <span class="bc-hero__stat-label">Correcties</span>
            </div>
            <?php if ($stats['aqidah'] > 0) : ?>
            <div class="bc-hero__stat">
                <span class="bc-hero__stat-number" style="color:var(--clr-red-500);"><?php echo intval($stats['aqidah']); ?></span>
                <span class="bc-hero__stat-label">Aqidah fouten</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Search & Book Grid -->
<section style="margin-top:var(--space-8);">
    <div class="bc-section-heading">
        <h2>📚 Overzicht van boeken</h2>
        <a href="<?php echo site_url('/formulier'); ?>" class="bc-btn bc-btn--primary">
            ✍️ Meld een correctie
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bc-search">
        <span class="bc-search__icon">🔍</span>
        <input type="text" id="bc-search" class="bc-search__input" placeholder="Zoek op boektitel of auteur..." autocomplete="off">
        <button id="bc-search-clear" class="bc-search__clear" aria-label="Wis zoekopdracht">✕</button>
    </div>

    <?php if ($books) : ?>
    <div id="bc-book-grid" class="bc-grid">
        <?php
        foreach ($books as $book) :
            setup_postdata($book);
            $book_id = $book->ID;
            $title = get_the_title($book_id);
            $author = get_post_meta($book_id, 'auteur', true);

            $corrections = get_posts([
                'post_type'   => 'correction',
                'numberposts' => -1,
                'post_status' => 'publish',
                'meta_key'    => 'book_id',
                'meta_value'  => $book_id,
            ]);

            // Categorize types
            $types = [];
            foreach ($corrections as $correction) {
                $type = get_post_meta($correction->ID, 'type', true);
                if (!isset($types[$type])) $types[$type] = 0;
                $types[$type]++;
            }

            $has_aqidah = isset($types['aqidah']);
        ?>
        <article class="bc-card<?php echo $has_aqidah ? ' bc-card--aqidah' : ''; ?>"
                 data-title="<?php echo esc_attr($title); ?>"
                 data-author="<?php echo esc_attr($author); ?>">

            <div class="bc-card__cover">
                <?php if (has_post_thumbnail($book_id)) : ?>
                    <?php echo get_the_post_thumbnail($book_id, 'medium', ['class' => '']); ?>
                <?php else : ?>
                    <span class="bc-card__cover-placeholder">📖</span>
                <?php endif; ?>
            </div>

            <div class="bc-card__body">
                <h3 class="bc-card__title"><?php echo esc_html($title); ?></h3>
                <p class="bc-card__author"><?php echo esc_html($author ?: 'Auteur onbekend'); ?></p>

                <p class="bc-card__stats">
                    <strong><?php echo count($corrections); ?></strong> correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?> gemeld
                </p>

                <?php if (!empty($types)) : ?>
                <div class="bc-card__badges">
                    <?php foreach ($types as $type => $count) : ?>
                        <span class="bc-badge bc-badge--<?php echo esc_attr($type); ?>">
                            <?php echo ucfirst(esc_html($type)); ?>: <?php echo $count; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="bc-card__action">
                    <a href="<?php echo get_permalink($book_id); ?>" class="bc-card__link">
                        Bekijk correcties <span>→</span>
                    </a>
                </div>
            </div>
        </article>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>

    <!-- No results message -->
    <div id="bc-no-results" class="bc-no-results">
        <span class="bc-no-results__icon">🔎</span>
        <p>Geen boeken gevonden voor deze zoekopdracht.</p>
    </div>

    <?php else : ?>
    <div class="bc-empty-state">
        <span class="bc-empty-state__icon">📚</span>
        <p class="bc-empty-state__text">Er zijn nog geen boeken toegevoegd.</p>
    </div>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
