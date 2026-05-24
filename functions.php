<?php

add_theme_support('post-thumbnails');
add_theme_support('title-tag');

/* ================================================================
   1. ENQUEUE — Frontend assets
   ================================================================ */

function boekcontrole_enqueue_assets() {
    wp_enqueue_style('boekcontrole-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_script('boekcontrole-js', get_template_directory_uri() . '/js/boekcontrole.js', [], wp_get_theme()->get('Version'), true);
    wp_localize_script('boekcontrole-js', 'bcAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bc_correction_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'boekcontrole_enqueue_assets');

function boekcontrole_tailwind_cdn() {
    echo '<script src="https://cdn.tailwindcss.com"></script>';
    echo "<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                },
            }
        }
    }
    </script>";
}
add_action('wp_head', 'boekcontrole_tailwind_cdn', 1);


/* ================================================================
   2. SEO — Meta tags, Open Graph, Schema, Sitemap, Robots
   ================================================================ */

function boekcontrole_seo_meta() {
    $site_name = 'BoekControle.nl';
    $logo_url = get_template_directory_uri() . '/img/logo.png';

    // Defaults
    $title = $site_name . ' — Correcties in Islamitische boeken';
    $description = 'Centrale plek voor het melden en bijhouden van correcties in Islamitische boeken. Onder toezicht van Ustaadh Bilaal Abu Yunus (حفظه الله).';
    $url = home_url($_SERVER['REQUEST_URI'] ?? '/');
    $type = 'website';

    if (is_singular('book')) {
        $book_title = get_the_title();
        $auteur = get_post_meta(get_the_ID(), 'auteur', true);
        $title = $book_title . ' — Correcties | ' . $site_name;
        $description = 'Bekijk correcties voor ' . $book_title . ($auteur ? ' van ' . $auteur : '') . '. ' . $site_name;
        $type = 'article';
    } elseif (is_page()) {
        $page_title = get_the_title();
        $title = $page_title . ' | ' . $site_name;
    }

    echo "\n<!-- SEO Meta -->\n";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";

    // Open Graph
    echo '<meta property="og:type" content="' . $type . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . $site_name . '">' . "\n";
    echo '<meta property="og:locale" content="nl_NL">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($logo_url) . '">' . "\n";

    // Twitter
    echo '<meta name="twitter:card" content="summary">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";

    // JSON-LD Schema
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $site_name,
        'url' => home_url('/'),
        'description' => $description,
        'inLanguage' => 'nl',
    ];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'boekcontrole_seo_meta', 5);

// Dynamic XML Sitemap
function boekcontrole_sitemap() {
    if (!isset($_GET['bc_sitemap'])) return;

    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    echo '<url><loc>' . home_url('/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>' . "\n";

    // Pages
    $pages = get_pages(['post_status' => 'publish']);
    foreach ($pages as $p) {
        echo '<url><loc>' . get_permalink($p->ID) . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
    }

    // Books
    $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => 'publish']);
    foreach ($books as $b) {
        echo '<url><loc>' . get_permalink($b->ID) . '</loc><changefreq>weekly</changefreq><priority>0.9</priority></url>' . "\n";
    }

    echo '</urlset>';
    exit;
}
add_action('template_redirect', 'boekcontrole_sitemap');

// Rewrite for sitemap
function boekcontrole_sitemap_rewrite() {
    add_rewrite_rule('sitemap\.xml$', 'index.php?bc_sitemap=1', 'top');
}
add_action('init', 'boekcontrole_sitemap_rewrite');

function boekcontrole_sitemap_query_var($vars) {
    $vars[] = 'bc_sitemap';
    return $vars;
}
add_filter('query_vars', 'boekcontrole_sitemap_query_var');

// Robots.txt
function boekcontrole_robots_txt($output, $public) {
    $output .= "\nSitemap: " . home_url('/sitemap.xml') . "\n";
    $output .= "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Allow: /wp-admin/admin-ajax.php\n";
    return $output;
}
add_filter('robots_txt', 'boekcontrole_robots_txt', 10, 2);


/* ================================================================
   3. VIEW COUNTER — Track book page views
   ================================================================ */

function boekcontrole_track_view() {
    if (!is_singular('book')) return;
    if (is_admin()) return;
    if (defined('DOING_AJAX') && DOING_AJAX) return;

    // Don't count logged-in admin views
    if (current_user_can('manage_options')) return;

    $post_id = get_the_ID();
    $views = (int) get_post_meta($post_id, 'bc_views', true);
    update_post_meta($post_id, 'bc_views', $views + 1);
}
add_action('wp_head', 'boekcontrole_track_view');

function boekcontrole_get_views($post_id) {
    return (int) get_post_meta($post_id, 'bc_views', true);
}


/* ================================================================
   4. CUSTOM POST TYPES
   ================================================================ */

function boekcontrole_register_post_types() {
    register_post_type('book', [
        'labels' => [
            'name'               => 'Boeken',
            'singular_name'      => 'Boek',
            'add_new'            => 'Nieuw boek toevoegen',
            'add_new_item'       => 'Nieuw boek toevoegen',
            'edit_item'          => 'Boek bewerken',
            'new_item'           => 'Nieuw boek',
            'view_item'          => 'Boek bekijken',
            'search_items'       => 'Boeken zoeken',
            'not_found'          => 'Geen boeken gevonden',
            'not_found_in_trash' => 'Geen boeken in prullenbak',
        ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => ['title', 'thumbnail'],
        'rewrite'      => ['slug' => 'boeken'],
        'menu_icon'    => 'dashicons-book',
    ]);

    register_post_type('correction', [
        'labels' => [
            'name'               => 'Correcties',
            'singular_name'      => 'Correctie',
            'add_new'            => 'Nieuwe correctie',
            'add_new_item'       => 'Nieuwe correctie toevoegen',
            'edit_item'          => 'Correctie bewerken',
            'search_items'       => 'Correcties zoeken',
            'not_found'          => 'Geen correcties gevonden',
            'not_found_in_trash' => 'Geen correcties in prullenbak',
        ],
        'public'             => true,
        'has_archive'        => false,
        'supports'           => ['title'],
        'publicly_queryable' => false,
        'show_ui'            => true,
        'menu_icon'          => 'dashicons-edit',
    ]);
}
add_action('init', 'boekcontrole_register_post_types');


/* ================================================================
   5. ADMIN META BOXES — Boeken
   ================================================================ */

function boekcontrole_book_meta_boxes() {
    add_meta_box('book_details', '📘 Boekgegevens', 'boekcontrole_book_meta_box_html', 'book', 'normal', 'high');
}
add_action('add_meta_boxes', 'boekcontrole_book_meta_boxes');

function boekcontrole_book_meta_box_html($post) {
    wp_nonce_field('bc_book_meta', 'bc_book_nonce');
    $auteur = get_post_meta($post->ID, 'auteur', true);
    $views = boekcontrole_get_views($post->ID);
    ?>
    <style>
        .bc-admin-field { margin-bottom: 16px; }
        .bc-admin-field label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px; color: #1e293b; }
        .bc-admin-field .description { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .bc-admin-input { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.2s; }
        .bc-admin-input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5,150,105,0.15); }
        .bc-admin-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: #166534; }
        .bc-admin-stat { display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; border-radius: 6px; padding: 6px 12px; font-size: 13px; color: #475569; font-weight: 500; }
    </style>

    <div class="bc-admin-info">
        💡 Vul de gegevens van het boek in. De <strong>titel</strong> stel je in via het titelveld hierboven. Voeg een <strong>boekomslag</strong> toe via "Uitgelichte afbeelding" rechts.
    </div>

    <div class="bc-admin-field">
        <label for="bc_auteur">✍️ Auteur</label>
        <input type="text" id="bc_auteur" name="auteur" value="<?php echo esc_attr($auteur); ?>"
               class="bc-admin-input" placeholder="Bijv. Imaam an-Nawawie">
        <p class="description">De naam van de auteur of vertaler van het boek.</p>
    </div>

    <?php if ($post->ID && get_post_status($post->ID) !== 'auto-draft') : ?>
    <div class="bc-admin-field">
        <label>📊 Statistieken</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span class="bc-admin-stat">👁️ <?php echo number_format_i18n($views); ?> weergaven</span>
            <?php
            $correction_count = count(get_posts(['post_type'=>'correction','numberposts'=>-1,'post_status'=>['publish','pending'],'meta_key'=>'book_id','meta_value'=>$post->ID,'fields'=>'ids']));
            ?>
            <span class="bc-admin-stat">📝 <?php echo $correction_count; ?> correcties</span>
        </div>
    </div>
    <?php endif; ?>
    <?php
}

function boekcontrole_save_book_meta($post_id) {
    if (!isset($_POST['bc_book_nonce']) || !wp_verify_nonce($_POST['bc_book_nonce'], 'bc_book_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['auteur'])) update_post_meta($post_id, 'auteur', sanitize_text_field($_POST['auteur']));
}
add_action('save_post_book', 'boekcontrole_save_book_meta');


/* ================================================================
   6. ADMIN META BOXES — Correcties
   ================================================================ */

function boekcontrole_correction_meta_boxes() {
    add_meta_box('correction_details', '📝 Correctie Details', 'boekcontrole_correction_meta_box_html', 'correction', 'normal', 'high');
    remove_post_type_support('correction', 'editor');
}
add_action('add_meta_boxes', 'boekcontrole_correction_meta_boxes');

function boekcontrole_correction_meta_box_html($post) {
    wp_nonce_field('bc_correction_meta', 'bc_correction_nonce');

    $book_id      = get_post_meta($post->ID, 'book_id', true);
    $bladzijde    = get_post_meta($post->ID, 'bladzijde', true);
    $druk         = get_post_meta($post->ID, 'druk', true);
    $type         = get_post_meta($post->ID, 'type', true);
    $beschrijving = get_post_meta($post->ID, 'beschrijving', true);
    $foto_id      = get_post_meta($post->ID, 'foto', true);

    $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish','pending','draft'], 'orderby' => 'title', 'order' => 'ASC']);
    ?>
    <style>
        .bc-admin-field { margin-bottom: 16px; }
        .bc-admin-field label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px; color: #1e293b; }
        .bc-admin-field .description { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .bc-admin-input, .bc-admin-select, .bc-admin-textarea {
            width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 14px; font-family: inherit; transition: border-color 0.2s;
        }
        .bc-admin-input:focus, .bc-admin-select:focus, .bc-admin-textarea:focus {
            outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5,150,105,0.15);
        }
        .bc-admin-textarea { min-height: 100px; resize: vertical; }
        .bc-admin-row { display: flex; gap: 12px; }
        .bc-admin-row > div { flex: 1; }
        .bc-admin-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: #166534; }
        .bc-admin-type-pills { display: flex; gap: 8px; flex-wrap: wrap; }
        .bc-admin-type-pill { position: relative; }
        .bc-admin-type-pill input { position: absolute; opacity: 0; width: 0; height: 0; }
        .bc-admin-type-pill label {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border: 2px solid #e5e7eb;
            border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; background: #fff;
        }
        .bc-admin-type-pill label:hover { border-color: #d1d5db; background: #f9fafb; }
        .bc-admin-type-pill input:checked + label { border-color: #059669; background: #ecfdf5; color: #065f46; font-weight: 600; }
        .bc-admin-type-pill.type-inhoudelijk input:checked + label { border-color: #ef4444; background: #fef2f2; color: #991b1b; }
        .bc-admin-type-pill.type-typo input:checked + label { border-color: #f59e0b; background: #fffbeb; color: #92400e; }
        .bc-admin-type-pill.type-misvertaling input:checked + label { border-color: #8b5cf6; background: #f5f3ff; color: #5b21b6; }
        .bc-admin-foto-preview img { max-width: 200px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>

    <div class="bc-admin-info">
        💡 De <strong>titel</strong> wordt automatisch gegenereerd. Vul onderstaande velden in.
    </div>

    <div class="bc-admin-field">
        <label for="bc_book_id">📘 Boek</label>
        <select id="bc_book_id" name="book_id" class="bc-admin-select">
            <option value="">— Selecteer een boek —</option>
            <?php foreach ($books as $b) :
                $b_auteur = get_post_meta($b->ID, 'auteur', true);
            ?>
                <option value="<?php echo $b->ID; ?>" <?php selected($book_id, $b->ID); ?>>
                    <?php echo esc_html($b->post_title); ?>
                    <?php if ($b_auteur) echo ' — ' . esc_html($b_auteur); ?>
                    <?php if ($b->post_status !== 'publish') echo ' [concept]'; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="bc-admin-row">
        <div class="bc-admin-field">
            <label for="bc_druk">📚 Druk / Editie</label>
            <input type="text" id="bc_druk" name="druk" value="<?php echo esc_attr($druk); ?>" class="bc-admin-input" placeholder="Bijv. 2e druk, 2024">
        </div>
        <div class="bc-admin-field">
            <label for="bc_bladzijde">📄 Bladzijde</label>
            <input type="number" id="bc_bladzijde" name="bladzijde" value="<?php echo esc_attr($bladzijde); ?>" class="bc-admin-input" placeholder="42" min="1">
        </div>
    </div>

    <div class="bc-admin-field">
        <label>🚩 Type fout</label>
        <div class="bc-admin-type-pills">
            <div class="bc-admin-type-pill type-inhoudelijk">
                <input type="radio" name="type" id="type_inhoudelijk" value="inhoudelijk" <?php checked($type, 'inhoudelijk'); ?>>
                <label for="type_inhoudelijk">⚠️ Inhoudelijk</label>
            </div>
            <div class="bc-admin-type-pill type-typo">
                <input type="radio" name="type" id="type_typo" value="typo" <?php checked($type, 'typo'); ?>>
                <label for="type_typo">✏️ Typo</label>
            </div>
            <div class="bc-admin-type-pill type-misvertaling">
                <input type="radio" name="type" id="type_misvertaling" value="misvertaling" <?php checked($type, 'misvertaling'); ?>>
                <label for="type_misvertaling">🔄 Misvertaling</label>
            </div>
        </div>
    </div>

    <div class="bc-admin-field">
        <label for="bc_beschrijving">📝 Beschrijving</label>
        <textarea id="bc_beschrijving" name="beschrijving" class="bc-admin-textarea"
                  placeholder="Beschrijf de fout zo duidelijk mogelijk..."><?php echo esc_textarea($beschrijving); ?></textarea>
    </div>

    <!-- Foto upload/preview -->
    <div class="bc-admin-field">
        <label>📷 Afbeelding</label>
        <?php if ($foto_id && wp_get_attachment_url($foto_id)) : ?>
            <div class="bc-admin-foto-preview" style="margin-bottom:8px;">
                <?php echo wp_get_attachment_image($foto_id, 'medium'); ?>
            </div>
            <p class="description">Huidige afbeelding. Upload een nieuwe via "Uitgelichte afbeelding" rechts, of beheer via Media.</p>
        <?php else : ?>
            <p class="description">Nog geen afbeelding. Voeg toe via "Uitgelichte afbeelding" rechts →</p>
        <?php endif; ?>
    </div>
    <?php
}

function boekcontrole_save_correction_meta($post_id) {
    if (!isset($_POST['bc_correction_nonce']) || !wp_verify_nonce($_POST['bc_correction_nonce'], 'bc_correction_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['bladzijde', 'druk', 'type', 'beschrijving', 'book_id'] as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
    }

    if (isset($_POST['beschrijving']) && !empty($_POST['beschrijving'])) {
        $title = 'Correctie: ' . wp_trim_words(wp_strip_all_tags($_POST['beschrijving']), 10, '...');
        wp_update_post(['ID' => $post_id, 'post_title' => $title]);
    }
}
add_action('save_post_correction', 'boekcontrole_save_correction_meta');


/* ================================================================
   7. ADMIN COLUMNS — Boeken
   ================================================================ */

function boekcontrole_book_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'thumbnail' => 'Omslag',
        'title' => 'Boektitel',
        'auteur' => 'Auteur',
        'correcties_count' => 'Correcties',
        'views' => '👁️ Views',
        'date' => 'Datum',
    ];
}
add_filter('manage_book_posts_columns', 'boekcontrole_book_columns');

function boekcontrole_book_column_data($column, $post_id) {
    switch ($column) {
        case 'auteur':
            $a = get_post_meta($post_id, 'auteur', true);
            echo $a ? esc_html($a) : '<span style="color:#94a3b8;">—</span>';
            break;
        case 'correcties_count':
            $c = count(get_posts(['post_type'=>'correction','numberposts'=>-1,'post_status'=>['publish','pending'],'meta_key'=>'book_id','meta_value'=>$post_id,'fields'=>'ids']));
            $bg = $c > 0 ? '#059669' : '#d1d5db';
            echo "<span style='background:{$bg};color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;font-weight:600;'>{$c}</span>";
            break;
        case 'views':
            $v = boekcontrole_get_views($post_id);
            echo "<span style='color:#64748b;font-weight:500;'>" . number_format_i18n($v) . "</span>";
            break;
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [50, 70], ['style' => 'border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.1);']);
            } else {
                echo '<span style="color:#94a3b8;font-size:20px;">📖</span>';
            }
            break;
    }
}
add_action('manage_book_posts_custom_column', 'boekcontrole_book_column_data', 10, 2);


/* ================================================================
   8. ADMIN COLUMNS — Correcties
   ================================================================ */

function boekcontrole_correction_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'title' => 'Correctie',
        'boek' => '📘 Boek',
        'type_fout' => '🚩 Type',
        'bladzijde' => '📄 Blz.',
        'druk' => '📚 Druk',
        'beschrijving_kort' => '📝 Beschrijving',
        'date' => 'Datum',
    ];
}
add_filter('manage_correction_posts_columns', 'boekcontrole_correction_columns');

function boekcontrole_correction_column_data($column, $post_id) {
    switch ($column) {
        case 'boek':
            $bid = get_post_meta($post_id, 'book_id', true);
            if ($bid && get_post($bid)) {
                echo '<a href="' . esc_url(get_edit_post_link($bid)) . '" style="font-weight:500;">' . esc_html(get_the_title($bid)) . '</a>';
            } else echo '<span style="color:#94a3b8;">—</span>';
            break;
        case 'type_fout':
            $t = get_post_meta($post_id, 'type', true);
            $colors = [
                'inhoudelijk' => ['#fef2f2','#991b1b','#fecaca'],
                'typo' => ['#fffbeb','#92400e','#fde68a'],
                'misvertaling' => ['#f5f3ff','#5b21b6','#ddd6fe'],
            ];
            $c = $colors[$t] ?? ['#f3f4f6','#6b7280','#e5e7eb'];
            echo "<span style='background:{$c[0]};color:{$c[1]};border:1px solid {$c[2]};padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;'>" . ucfirst(esc_html($t ?: '—')) . "</span>";
            break;
        case 'bladzijde':
            echo esc_html(get_post_meta($post_id, 'bladzijde', true) ?: '—');
            break;
        case 'druk':
            echo esc_html(get_post_meta($post_id, 'druk', true) ?: '—');
            break;
        case 'beschrijving_kort':
            $d = get_post_meta($post_id, 'beschrijving', true);
            echo $d ? '<span style="color:#4b5563;">' . esc_html(wp_trim_words($d, 8, '...')) . '</span>' : '—';
            break;
    }
}
add_action('manage_correction_posts_custom_column', 'boekcontrole_correction_column_data', 10, 2);

function boekcontrole_correction_sortable_columns($columns) {
    $columns['boek'] = 'boek'; $columns['type_fout'] = 'type_fout'; $columns['bladzijde'] = 'bladzijde';
    return $columns;
}
add_filter('manage_edit-correction_sortable_columns', 'boekcontrole_correction_sortable_columns');


/* ================================================================
   9. ADMIN STYLING
   ================================================================ */

function boekcontrole_admin_styles() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, ['book', 'correction'])) return;
    echo '<style>
        .column-thumbnail { width: 60px; }
        .column-correcties_count, .column-views { width: 80px; text-align: center; }
        .column-bladzijde { width: 60px; }
        .column-druk { width: 100px; }
        .column-type_fout { width: 110px; }
    </style>';
}
add_action('admin_head', 'boekcontrole_admin_styles');

function boekcontrole_title_placeholder($title, $post) {
    if ($post->post_type === 'book') return 'Voer de boektitel in...';
    if ($post->post_type === 'correction') return 'Wordt automatisch ingevuld...';
    return $title;
}
add_filter('enter_title_here', 'boekcontrole_title_placeholder', 10, 2);


/* ================================================================
   10. AJAX HANDLER — Frontend form
   ================================================================ */

function boekcontrole_handle_correction_ajax() {
    if (!isset($_POST['bc_nonce']) || !wp_verify_nonce($_POST['bc_nonce'], 'bc_correction_nonce')) {
        wp_send_json_error(['message' => 'Beveiligingscontrole mislukt.']);
    }

    if ($_POST['book_select'] === 'nieuw') {
        $book_id = wp_insert_post(['post_type'=>'book','post_status'=>'pending','post_title'=>sanitize_text_field($_POST['nieuw_boek_titel']),'meta_input'=>['auteur'=>sanitize_text_field($_POST['nieuw_boek_auteur'])]]);
    } else {
        $book_id = intval($_POST['book_select']);
    }

    $cid = wp_insert_post(['post_type'=>'correction','post_status'=>'pending','post_title'=>'Correctie: '.wp_trim_words(wp_strip_all_tags($_POST['beschrijving']),10,'...')]);
    if (is_wp_error($cid)) wp_send_json_error(['message' => 'Er ging iets mis.']);

    foreach (['bladzijde','druk','type','beschrijving'] as $f) {
        if (isset($_POST[$f])) update_post_meta($cid, $f, sanitize_text_field($_POST[$f]));
    }
    update_post_meta($cid, 'book_id', $book_id);

    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH.'wp-admin/includes/file.php');
        require_once(ABSPATH.'wp-admin/includes/media.php');
        require_once(ABSPATH.'wp-admin/includes/image.php');
        $aid = media_handle_upload('foto', $cid);
        if (!is_wp_error($aid)) update_post_meta($cid, 'foto', $aid);
    }

    wp_send_json_success(['message' => 'Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.']);
}
add_action('wp_ajax_bc_submit_correction', 'boekcontrole_handle_correction_ajax');
add_action('wp_ajax_nopriv_bc_submit_correction', 'boekcontrole_handle_correction_ajax');


/* ================================================================
   11. HELPERS — Frontend template functions
   ================================================================ */

function boekcontrole_get_stats() {
    $b = wp_count_posts('book'); $c = wp_count_posts('correction');
    return ['books' => $b->publish ?? 0, 'corrections' => $c->publish ?? 0];
}

function bc_type_badge_classes($type) {
    switch ($type) {
        case 'inhoudelijk':  return 'bg-red-50 text-red-700 border border-red-200';
        case 'typo':         return 'bg-amber-50 text-amber-700 border border-amber-200';
        case 'misvertaling': return 'bg-violet-50 text-violet-700 border border-violet-200';
        default:             return 'bg-gray-100 text-gray-600 border border-gray-200';
    }
}

function bc_type_border_class($type) {
    switch ($type) {
        case 'inhoudelijk':  return 'border-l-red-500';
        case 'typo':         return 'border-l-amber-500';
        case 'misvertaling': return 'border-l-violet-500';
        default:             return 'border-l-gray-300';
    }
}