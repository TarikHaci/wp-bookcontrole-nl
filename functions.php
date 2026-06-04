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
   2. SEO & SITEMAP
   ================================================================ */
// Verwijderd: Yoast SEO neemt dit nu over.


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
        'supports'           => ['title', 'thumbnail'],
        'publicly_queryable' => true,
        'show_ui'            => true,
        'rewrite'            => ['slug' => 'correctie'],
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
        a.bc-admin-stat:hover { background: #e2e8f0; color: #1e293b; }
    </style>

    <div class="bc-admin-info">
        💡 Vul de gegevens van het boek in. De <strong>titel</strong> stel je in via het titelveld hierboven. Voeg een <strong>boekomslag</strong> toe via "Uitgelichte afbeelding" rechts.
    </div>

    <div class="bc-admin-field">
        <label for="bc_auteur">✍️ Auteur</label>
        <input type="text" id="bc_auteur" name="auteur" value="<?php echo esc_attr($auteur); ?>"
               class="bc-admin-input" placeholder="Bijv. Imaam al-Bukhari">
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
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=correction&bc_book_id=' . $post->ID)); ?>" class="bc-admin-stat" style="text-decoration: none;">
                📝 <?php echo $correction_count; ?> correcties
            </a>
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

// Enqueue WP media library on correction edit screens
function boekcontrole_correction_admin_scripts($hook) {
    global $post_type;
    if ($post_type === 'correction' && in_array($hook, ['post.php', 'post-new.php'])) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'boekcontrole_correction_admin_scripts');
add_action('add_meta_boxes', 'boekcontrole_correction_meta_boxes');

function boekcontrole_correction_meta_box_html($post) {
    wp_nonce_field('bc_correction_meta', 'bc_correction_nonce');

    $book_id      = get_post_meta($post->ID, 'book_id', true);
    $bladzijde    = get_post_meta($post->ID, 'bladzijde', true);
    $druk         = get_post_meta($post->ID, 'druk', true);
    $type         = get_post_meta($post->ID, 'type', true);
    $beschrijving = get_post_meta($post->ID, 'beschrijving', true);

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
        /* Photo gallery admin styles */
        .bc-admin-gallery { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
        .bc-admin-gallery-item { position: relative; width: 120px; height: 120px; border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb; background: #f9fafb; }
        .bc-admin-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .bc-admin-gallery-item .bc-admin-remove-foto {
            position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; border-radius: 50%;
            background: rgba(239,68,68,0.85); color: #fff; border: none; font-size: 14px; line-height: 1;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s;
        }
        .bc-admin-gallery-item:hover .bc-admin-remove-foto { opacity: 1; }
        .bc-admin-gallery-item:hover { border-color: #059669; }
        .bc-admin-add-fotos {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
            background: #059669; color: #fff; border: none; border-radius: 6px;
            font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;
        }
        .bc-admin-add-fotos:hover { background: #047857; }
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
            <?php
            $selected_types = is_array($type) ? $type : ($type ? [$type] : []);
            ?>
            <div class="bc-admin-type-pill type-inhoudelijk">
                <input type="checkbox" name="type[]" id="type_inhoudelijk" value="inhoudelijk" <?php checked(in_array('inhoudelijk', $selected_types)); ?>>
                <label for="type_inhoudelijk">⚠️ Inhoudelijk</label>
            </div>
            <div class="bc-admin-type-pill type-typo">
                <input type="checkbox" name="type[]" id="type_typo" value="typo" <?php checked(in_array('typo', $selected_types)); ?>>
                <label for="type_typo">✏️ Typo</label>
            </div>
            <div class="bc-admin-type-pill type-misvertaling">
                <input type="checkbox" name="type[]" id="type_misvertaling" value="misvertaling" <?php checked(in_array('misvertaling', $selected_types)); ?>>
                <label for="type_misvertaling">🔄 Misvertaling</label>
            </div>
        </div>
    </div>

    <div class="bc-admin-field">
        <label for="bc_beschrijving">📝 Beschrijving</label>
        <textarea id="bc_beschrijving" name="beschrijving" class="bc-admin-textarea"
                  placeholder="Beschrijf de fout zo duidelijk mogelijk..."><?php echo esc_textarea($beschrijving); ?></textarea>
    </div>

    <!-- Afbeeldingen gallery -->
    <div class="bc-admin-field">
        <label>📷 Afbeeldingen</label>
        <p class="description" style="margin-bottom:10px;">Voeg een of meerdere afbeeldingen toe via de WordPress mediabibliotheek.</p>
        <?php
        $foto_ids = bc_get_correction_fotos($post->ID);
        ?>
        <div id="bc-admin-fotos-gallery" class="bc-admin-gallery">
            <?php foreach ($foto_ids as $fid) : ?>
                <div class="bc-admin-gallery-item" data-id="<?php echo $fid; ?>">
                    <?php echo wp_get_attachment_image($fid, 'thumbnail'); ?>
                    <button type="button" class="bc-admin-remove-foto" title="Verwijder">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="bc-admin-fotos-ids" name="correction_fotos_ids" value="<?php echo esc_attr(implode(',', $foto_ids)); ?>">
        <button type="button" id="bc-admin-add-fotos" class="bc-admin-add-fotos">📷 Afbeeldingen toevoegen</button>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var gallery = $('#bc-admin-fotos-gallery');
        var hiddenInput = $('#bc-admin-fotos-ids');

        function getIds() {
            var val = hiddenInput.val().trim();
            return val ? val.split(',').map(Number).filter(Boolean) : [];
        }

        function setIds(ids) {
            hiddenInput.val(ids.join(','));
        }

        // Remove photo
        gallery.on('click', '.bc-admin-remove-foto', function(e) {
            e.preventDefault();
            var item = $(this).closest('.bc-admin-gallery-item');
            var removeId = parseInt(item.data('id'));
            var ids = getIds().filter(function(id) { return id !== removeId; });
            setIds(ids);
            item.fadeOut(200, function() { $(this).remove(); });
        });

        // Add photos via media library
        $('#bc-admin-add-fotos').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Afbeeldingen selecteren',
                button: { text: 'Toevoegen aan correctie' },
                multiple: true,
                library: { type: 'image' }
            });

            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = getIds();
                selection.each(function(attachment) {
                    var att = attachment.toJSON();
                    if (ids.indexOf(att.id) === -1) {
                        ids.push(att.id);
                        var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                        var html = '<div class="bc-admin-gallery-item" data-id="' + att.id + '">' +
                                   '<img src="' + thumb + '" alt="">' +
                                   '<button type="button" class="bc-admin-remove-foto" title="Verwijder">&times;</button>' +
                                   '</div>';
                        gallery.append(html);
                    }
                });
                setIds(ids);
            });

            frame.open();
        });
    });
    </script>
    <?php
}

function boekcontrole_save_correction_meta($post_id) {
    if (!isset($_POST['bc_correction_nonce']) || !wp_verify_nonce($_POST['bc_correction_nonce'], 'bc_correction_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['bladzijde', 'druk', 'beschrijving', 'book_id'] as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
    }

    if (isset($_POST['type'])) {
        $type_val = is_array($_POST['type']) ? array_map('sanitize_text_field', $_POST['type']) : sanitize_text_field($_POST['type']);
        update_post_meta($post_id, 'type', $type_val);
    }

    if (isset($_POST['beschrijving']) && !empty($_POST['beschrijving'])) {
        $title = 'Correctie: ' . wp_trim_words(wp_strip_all_tags($_POST['beschrijving']), 10, '...');
        remove_action('save_post_correction', 'boekcontrole_save_correction_meta');
        wp_update_post(['ID' => $post_id, 'post_title' => $title]);
        add_action('save_post_correction', 'boekcontrole_save_correction_meta');
    }

    // Save correction photos from admin gallery
    if (isset($_POST['correction_fotos_ids'])) {
        $raw = sanitize_text_field($_POST['correction_fotos_ids']);
        $ids = array_filter(array_map('intval', explode(',', $raw)));
        if (!empty($ids)) {
            update_post_meta($post_id, 'correction_fotos', json_encode(array_values($ids)));
            // Set first image as post thumbnail for backward compat
            set_post_thumbnail($post_id, $ids[0]);
        } else {
            delete_post_meta($post_id, 'correction_fotos');
            delete_post_thumbnail($post_id);
        }
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
            $link = admin_url('edit.php?post_type=correction&bc_book_id=' . $post_id);
            echo "<a href='" . esc_url($link) . "' title='Bekijk correcties voor dit boek' class='bc-admin-badge' style='text-decoration:none;'><span style='background:{$bg};color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;font-weight:600;display:inline-block;transition:all 0.1s ease-in-out;'>{$c}</span></a>";
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
            $types = is_array($t) ? $t : ($t ? [$t] : []);
            if (empty($types)) {
                echo "<span style='background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;'>—</span>";
            } else {
                $colors = [
                    'inhoudelijk' => ['#fef2f2','#991b1b','#fecaca'],
                    'typo' => ['#fffbeb','#92400e','#fde68a'],
                    'misvertaling' => ['#f5f3ff','#5b21b6','#ddd6fe'],
                ];
                foreach ($types as $type) {
                    $c = $colors[$type] ?? ['#f3f4f6','#6b7280','#e5e7eb'];
                    echo "<span style='background:{$c[0]};color:{$c[1]};border:1px solid {$c[2]};padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;margin-right:4px;display:inline-block;margin-bottom:4px;'>" . ucfirst(esc_html($type)) . "</span>";
                }
            }
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
        .bc-admin-badge:hover span { opacity: 0.85; transform: scale(1.05); }
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
   9b. ADMIN FILTERS & ROW ACTIONS
   ================================================================ */

// Add "Correcties bekijken" hover action under book titles
function boekcontrole_book_row_actions($actions, $post) {
    if ($post->post_type === 'book') {
        $link = admin_url('edit.php?post_type=correction&bc_book_id=' . $post->ID);
        $actions['view_corrections'] = '<a href="' . esc_url($link) . '" aria-label="Bekijk correcties voor dit boek">🔍 Correcties bekijken</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'boekcontrole_book_row_actions', 10, 2);

// Add a book dropdown filter to the corrections list
function boekcontrole_admin_corrections_filter_dropdown() {
    global $typenow;
    if ($typenow === 'correction') {
        $selected = isset($_GET['bc_book_id']) ? intval($_GET['bc_book_id']) : 0;
        $books = get_posts([
            'post_type'   => 'book',
            'numberposts' => -1,
            'post_status' => ['publish', 'pending', 'draft'],
            'orderby'     => 'title',
            'order'       => 'ASC'
        ]);
        
        echo '<select name="bc_book_id">';
        echo '<option value="">' . esc_html__('Alle boeken', 'boekcontrole') . '</option>';
        foreach ($books as $b) {
            $author = get_post_meta($b->ID, 'auteur', true);
            $suffix = $author ? ' (' . $author . ')' : '';
            echo '<option value="' . $b->ID . '" ' . selected($selected, $b->ID, false) . '>' . esc_html($b->post_title . $suffix) . '</option>';
        }
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'boekcontrole_admin_corrections_filter_dropdown');

// Apply the book filter query to corrections list
function boekcontrole_filter_corrections_by_book($query) {
    global $pagenow;
    if (is_admin() && $pagenow === 'edit.php' && $query->is_main_query() && isset($_GET['post_type']) && $_GET['post_type'] === 'correction') {
        if (isset($_GET['bc_book_id']) && !empty($_GET['bc_book_id'])) {
            $book_id = intval($_GET['bc_book_id']);
            $meta_query = $query->get('meta_query') ?: [];
            $meta_query[] = [
                'key'     => 'book_id',
                'value'   => $book_id,
                'compare' => '='
            ];
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'boekcontrole_filter_corrections_by_book');


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

    foreach (['bladzijde','druk','beschrijving'] as $f) {
        if (isset($_POST[$f])) update_post_meta($cid, $f, sanitize_text_field($_POST[$f]));
    }
    if (isset($_POST['type'])) {
        $type_val = is_array($_POST['type']) ? array_map('sanitize_text_field', $_POST['type']) : sanitize_text_field($_POST['type']);
        update_post_meta($cid, 'type', $type_val);
    }
    update_post_meta($cid, 'book_id', $book_id);

    if (!empty($_FILES['foto']['name'][0])) {
        require_once(ABSPATH.'wp-admin/includes/file.php');
        require_once(ABSPATH.'wp-admin/includes/media.php');
        require_once(ABSPATH.'wp-admin/includes/image.php');
        $foto_ids = [];
        $file_count = count($_FILES['foto']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if (empty($_FILES['foto']['name'][$i])) continue;
            $_FILES['foto_single'] = [
                'name'     => $_FILES['foto']['name'][$i],
                'type'     => $_FILES['foto']['type'][$i],
                'tmp_name' => $_FILES['foto']['tmp_name'][$i],
                'error'    => $_FILES['foto']['error'][$i],
                'size'     => $_FILES['foto']['size'][$i],
            ];
            $aid = media_handle_upload('foto_single', $cid);
            if (!is_wp_error($aid)) {
                $foto_ids[] = $aid;
                if (count($foto_ids) === 1) set_post_thumbnail($cid, $aid);
            }
        }
        if (!empty($foto_ids)) update_post_meta($cid, 'correction_fotos', json_encode($foto_ids));
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

/**
 * Get all photo attachment IDs for a correction.
 * Supports new multi-photo meta and falls back to legacy single thumbnail.
 */
function bc_get_correction_fotos($correction_id) {
    // New multi-photo meta
    $fotos_json = get_post_meta($correction_id, 'correction_fotos', true);
    if ($fotos_json) {
        $ids = json_decode($fotos_json, true);
        if (is_array($ids) && !empty($ids)) return $ids;
    }
    // Legacy: single thumbnail or foto meta
    if (has_post_thumbnail($correction_id)) {
        return [get_post_thumbnail_id($correction_id)];
    }
    $foto_meta = get_post_meta($correction_id, 'foto', true);
    if ($foto_meta) return [(int)$foto_meta];
    return [];
}