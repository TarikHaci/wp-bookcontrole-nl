<?php

add_theme_support('post-thumbnails');

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
   2. CUSTOM POST TYPES
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
            'new_item'           => 'Nieuwe correctie',
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
   3. ADMIN META BOXES — Boeken
   ================================================================ */

function boekcontrole_book_meta_boxes() {
    add_meta_box(
        'book_details',
        '📘 Boekgegevens',
        'boekcontrole_book_meta_box_html',
        'book',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'boekcontrole_book_meta_boxes');

function boekcontrole_book_meta_box_html($post) {
    wp_nonce_field('bc_book_meta', 'bc_book_nonce');
    $auteur = get_post_meta($post->ID, 'auteur', true);
    ?>
    <style>
        .bc-admin-field { margin-bottom: 16px; }
        .bc-admin-field label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px; color: #1e293b; }
        .bc-admin-field .description { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .bc-admin-input { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.2s; }
        .bc-admin-input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5,150,105,0.15); }
        .bc-admin-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: #166534; }
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
    <?php
}

function boekcontrole_save_book_meta($post_id) {
    if (!isset($_POST['bc_book_nonce']) || !wp_verify_nonce($_POST['bc_book_nonce'], 'bc_book_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['auteur'])) {
        update_post_meta($post_id, 'auteur', sanitize_text_field($_POST['auteur']));
    }
}
add_action('save_post_book', 'boekcontrole_save_book_meta');


/* ================================================================
   4. ADMIN META BOXES — Correcties
   ================================================================ */

function boekcontrole_correction_meta_boxes() {
    add_meta_box(
        'correction_details',
        '📝 Correctie Details',
        'boekcontrole_correction_meta_box_html',
        'correction',
        'normal',
        'high'
    );
    // Remove default editor since we don't use it
    remove_post_type_support('correction', 'editor');
}
add_action('add_meta_boxes', 'boekcontrole_correction_meta_boxes');

function boekcontrole_correction_meta_box_html($post) {
    wp_nonce_field('bc_correction_meta', 'bc_correction_nonce');

    $book_id     = get_post_meta($post->ID, 'book_id', true);
    $bladzijde   = get_post_meta($post->ID, 'bladzijde', true);
    $druk        = get_post_meta($post->ID, 'druk', true);
    $type        = get_post_meta($post->ID, 'type', true);
    $beschrijving = get_post_meta($post->ID, 'beschrijving', true);
    $foto_id     = get_post_meta($post->ID, 'foto', true);

    $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish', 'pending', 'draft'], 'orderby' => 'title', 'order' => 'ASC']);
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
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border: 2px solid #e5e7eb; border-radius: 8px;
            font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;
            background: #fff;
        }
        .bc-admin-type-pill label:hover { border-color: #d1d5db; background: #f9fafb; }
        .bc-admin-type-pill input:checked + label { border-color: #059669; background: #ecfdf5; color: #065f46; font-weight: 600; }
        .bc-admin-type-pill.type-inhoudelijk input:checked + label { border-color: #ef4444; background: #fef2f2; color: #991b1b; }
        .bc-admin-type-pill.type-typo input:checked + label { border-color: #f59e0b; background: #fffbeb; color: #92400e; }
        .bc-admin-type-pill.type-misvertaling input:checked + label { border-color: #8b5cf6; background: #f5f3ff; color: #5b21b6; }
        .bc-admin-foto-preview { margin-top: 8px; }
        .bc-admin-foto-preview img { max-width: 200px; height: auto; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>

    <div class="bc-admin-info">
        💡 Vul de correctiegegevens hieronder in. De <strong>titel</strong> wordt automatisch gegenereerd op basis van de beschrijving.
    </div>

    <!-- Boek selectie -->
    <div class="bc-admin-field">
        <label for="bc_book_id">📘 Boek</label>
        <select id="bc_book_id" name="book_id" class="bc-admin-select">
            <option value="">— Selecteer een boek —</option>
            <?php foreach ($books as $b) : ?>
                <option value="<?php echo $b->ID; ?>" <?php selected($book_id, $b->ID); ?>>
                    <?php echo esc_html($b->post_title); ?>
                    <?php
                    $b_auteur = get_post_meta($b->ID, 'auteur', true);
                    if ($b_auteur) echo ' — ' . esc_html($b_auteur);
                    ?>
                    <?php if ($b->post_status !== 'publish') echo ' [concept]'; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">Selecteer het boek waarvoor deze correctie geldt.</p>
    </div>

    <!-- Druk + Bladzijde naast elkaar -->
    <div class="bc-admin-row">
        <div class="bc-admin-field">
            <label for="bc_druk">📚 Druk / Editie</label>
            <input type="text" id="bc_druk" name="druk" value="<?php echo esc_attr($druk); ?>"
                   class="bc-admin-input" placeholder="Bijv. 2e druk, 2024">
        </div>
        <div class="bc-admin-field">
            <label for="bc_bladzijde">📄 Bladzijde</label>
            <input type="number" id="bc_bladzijde" name="bladzijde" value="<?php echo esc_attr($bladzijde); ?>"
                   class="bc-admin-input" placeholder="Bijv. 42" min="1">
        </div>
    </div>

    <!-- Type fout — visuele pill knoppen -->
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
        <p class="description">Kies het type fout dat in het boek is gevonden.</p>
    </div>

    <!-- Beschrijving -->
    <div class="bc-admin-field">
        <label for="bc_beschrijving">📝 Beschrijving</label>
        <textarea id="bc_beschrijving" name="beschrijving" class="bc-admin-textarea"
                  placeholder="Beschrijf de fout zo duidelijk mogelijk. Vermeld eventueel de juiste tekst."><?php echo esc_textarea($beschrijving); ?></textarea>
        <p class="description">Geef een duidelijke beschrijving van de gevonden fout en eventueel de correcte tekst.</p>
    </div>

    <!-- Foto preview -->
    <?php if ($foto_id) : ?>
    <div class="bc-admin-field">
        <label>📷 Bijgevoegde afbeelding</label>
        <div class="bc-admin-foto-preview">
            <?php echo wp_get_attachment_image($foto_id, 'medium'); ?>
        </div>
        <p class="description">Deze afbeelding is bij de melding gevoegd. Beheer via Media bibliotheek.</p>
    </div>
    <?php endif; ?>

    <?php
}

function boekcontrole_save_correction_meta($post_id) {
    if (!isset($_POST['bc_correction_nonce']) || !wp_verify_nonce($_POST['bc_correction_nonce'], 'bc_correction_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['bladzijde', 'druk', 'type', 'beschrijving', 'book_id'] as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Auto-generate title from beschrijving if needed
    if (isset($_POST['beschrijving']) && !empty($_POST['beschrijving'])) {
        $desc = wp_strip_all_tags($_POST['beschrijving']);
        $title = 'Correctie: ' . wp_trim_words($desc, 10, '...');
        wp_update_post(['ID' => $post_id, 'post_title' => $title]);
    }
}
add_action('save_post_correction', 'boekcontrole_save_correction_meta');


/* ================================================================
   5. ADMIN COLUMNS — Boeken lijstweergave
   ================================================================ */

function boekcontrole_book_columns($columns) {
    $new = [];
    $new['cb'] = $columns['cb'];
    $new['title'] = 'Boektitel';
    $new['auteur'] = 'Auteur';
    $new['correcties_count'] = 'Correcties';
    $new['thumbnail'] = 'Omslag';
    $new['date'] = 'Datum';
    return $new;
}
add_filter('manage_book_posts_columns', 'boekcontrole_book_columns');

function boekcontrole_book_column_data($column, $post_id) {
    switch ($column) {
        case 'auteur':
            $auteur = get_post_meta($post_id, 'auteur', true);
            echo $auteur ? esc_html($auteur) : '<span style="color:#94a3b8;">—</span>';
            break;
        case 'correcties_count':
            $count = count(get_posts([
                'post_type' => 'correction', 'numberposts' => -1,
                'post_status' => ['publish', 'pending'],
                'meta_key' => 'book_id', 'meta_value' => $post_id, 'fields' => 'ids',
            ]));
            $bg = $count > 0 ? '#059669' : '#d1d5db';
            echo "<span style='background:{$bg};color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;font-weight:600;'>{$count}</span>";
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
   6. ADMIN COLUMNS — Correcties lijstweergave
   ================================================================ */

function boekcontrole_correction_columns($columns) {
    $new = [];
    $new['cb'] = $columns['cb'];
    $new['title'] = 'Correctie';
    $new['boek'] = '📘 Boek';
    $new['type_fout'] = '🚩 Type';
    $new['bladzijde'] = '📄 Blz.';
    $new['druk'] = '📚 Druk';
    $new['beschrijving_kort'] = '📝 Beschrijving';
    $new['date'] = 'Datum';
    return $new;
}
add_filter('manage_correction_posts_columns', 'boekcontrole_correction_columns');

function boekcontrole_correction_column_data($column, $post_id) {
    switch ($column) {
        case 'boek':
            $book_id = get_post_meta($post_id, 'book_id', true);
            if ($book_id && get_post($book_id)) {
                $edit_link = get_edit_post_link($book_id);
                echo '<a href="' . esc_url($edit_link) . '" style="font-weight:500;">' . esc_html(get_the_title($book_id)) . '</a>';
            } else {
                echo '<span style="color:#94a3b8;">Onbekend</span>';
            }
            break;
        case 'type_fout':
            $type = get_post_meta($post_id, 'type', true);
            $colors = [
                'inhoudelijk'  => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fecaca'],
                'typo'         => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#fde68a'],
                'misvertaling' => ['bg' => '#f5f3ff', 'text' => '#5b21b6', 'border' => '#ddd6fe'],
            ];
            $c = $colors[$type] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'border' => '#e5e7eb'];
            echo "<span style='background:{$c['bg']};color:{$c['text']};border:1px solid {$c['border']};padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;white-space:nowrap;'>" . ucfirst(esc_html($type ?: '—')) . "</span>";
            break;
        case 'bladzijde':
            $val = get_post_meta($post_id, 'bladzijde', true);
            echo $val ? esc_html($val) : '<span style="color:#94a3b8;">—</span>';
            break;
        case 'druk':
            $val = get_post_meta($post_id, 'druk', true);
            echo $val ? esc_html($val) : '<span style="color:#94a3b8;">—</span>';
            break;
        case 'beschrijving_kort':
            $desc = get_post_meta($post_id, 'beschrijving', true);
            echo $desc ? '<span style="color:#4b5563;">' . esc_html(wp_trim_words($desc, 8, '...')) . '</span>' : '<span style="color:#94a3b8;">—</span>';
            break;
    }
}
add_action('manage_correction_posts_custom_column', 'boekcontrole_correction_column_data', 10, 2);

// Maak kolommen sorteerbaar
function boekcontrole_correction_sortable_columns($columns) {
    $columns['boek'] = 'boek';
    $columns['type_fout'] = 'type_fout';
    $columns['bladzijde'] = 'bladzijde';
    return $columns;
}
add_filter('manage_edit-correction_sortable_columns', 'boekcontrole_correction_sortable_columns');


/* ================================================================
   7. ADMIN STYLING — Extra CSS in admin
   ================================================================ */

function boekcontrole_admin_styles() {
    $screen = get_current_screen();
    if (!$screen) return;
    if (in_array($screen->post_type, ['book', 'correction'])) {
        echo '<style>
            #titlediv #title-prompt-text { color: #94a3b8; }
            .column-thumbnail { width: 60px; }
            .column-correcties_count { width: 80px; text-align: center; }
            .column-bladzijde { width: 60px; }
            .column-druk { width: 100px; }
            .column-type_fout { width: 110px; }
        </style>';
    }
}
add_action('admin_head', 'boekcontrole_admin_styles');

// Custom placeholder voor titelveld
function boekcontrole_title_placeholder($title, $post) {
    if ($post->post_type === 'book') return 'Voer de boektitel in...';
    if ($post->post_type === 'correction') return 'Wordt automatisch ingevuld...';
    return $title;
}
add_filter('enter_title_here', 'boekcontrole_title_placeholder', 10, 2);


/* ================================================================
   8. AJAX HANDLER — Frontend formulier
   ================================================================ */

function boekcontrole_handle_correction_ajax() {
    if (!isset($_POST['bc_nonce']) || !wp_verify_nonce($_POST['bc_nonce'], 'bc_correction_nonce')) {
        wp_send_json_error(['message' => 'Beveiligingscontrole mislukt. Vernieuw de pagina.']);
    }

    if ($_POST['book_select'] === 'nieuw') {
        $book_id = wp_insert_post([
            'post_type' => 'book', 'post_status' => 'pending',
            'post_title' => sanitize_text_field($_POST['nieuw_boek_titel']),
            'meta_input' => ['auteur' => sanitize_text_field($_POST['nieuw_boek_auteur'])]
        ]);
    } else {
        $book_id = intval($_POST['book_select']);
    }

    $correction_id = wp_insert_post([
        'post_type' => 'correction', 'post_status' => 'pending',
        'post_title' => 'Correctie: ' . wp_trim_words(wp_strip_all_tags($_POST['beschrijving']), 10, '...'),
    ]);

    if (is_wp_error($correction_id)) {
        wp_send_json_error(['message' => 'Er ging iets mis bij het opslaan.']);
    }

    foreach (['bladzijde', 'druk', 'type', 'beschrijving'] as $f) {
        if (isset($_POST[$f])) update_post_meta($correction_id, $f, sanitize_text_field($_POST[$f]));
    }
    update_post_meta($correction_id, 'book_id', $book_id);

    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $aid = media_handle_upload('foto', $correction_id);
        if (!is_wp_error($aid)) update_post_meta($correction_id, 'foto', $aid);
    }

    wp_send_json_success(['message' => 'Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.']);
}
add_action('wp_ajax_bc_submit_correction', 'boekcontrole_handle_correction_ajax');
add_action('wp_ajax_nopriv_bc_submit_correction', 'boekcontrole_handle_correction_ajax');


/* ================================================================
   9. HELPERS — Frontend template functions
   ================================================================ */

function boekcontrole_get_stats() {
    $books       = wp_count_posts('book');
    $corrections = wp_count_posts('correction');
    return [
        'books'       => $books->publish ?? 0,
        'corrections' => $corrections->publish ?? 0,
    ];
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