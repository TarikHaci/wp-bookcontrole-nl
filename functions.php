<?php

add_theme_support('post-thumbnails');

/**
 * Enqueue Tailwind CDN + theme stylesheet + custom JS
 */
function boekcontrole_enqueue_assets() {
    // Theme stylesheet (includes @import for Google Fonts + custom animations)
    wp_enqueue_style('boekcontrole-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));

    // Custom JS
    wp_enqueue_script('boekcontrole-js', get_template_directory_uri() . '/js/boekcontrole.js', [], wp_get_theme()->get('Version'), true);

    // Localize for AJAX
    wp_localize_script('boekcontrole-js', 'bcAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bc_correction_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'boekcontrole_enqueue_assets');

/**
 * Inject Tailwind CDN via <script> tag in <head>
 */
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

/**
 * Register custom post types
 */
function boekcontrole_register_post_types() {
    register_post_type('book', [
        'labels' => [
            'name'          => 'Boeken',
            'singular_name' => 'Boek'
        ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => ['title', 'editor', 'thumbnail'],
        'rewrite'      => ['slug' => 'boeken'],
        'menu_icon'    => 'dashicons-book',
    ]);

    register_post_type('correction', [
        'labels' => [
            'name'          => 'Correcties',
            'singular_name' => 'Correctie'
        ],
        'public'             => true,
        'has_archive'        => false,
        'supports'           => ['title', 'thumbnail'],
        'publicly_queryable' => false,
        'show_ui'            => true,
        'menu_icon'          => 'dashicons-edit',
    ]);
}
add_action('init', 'boekcontrole_register_post_types');

/**
 * Admin meta boxes for corrections
 */
function boekcontrole_add_meta_boxes() {
    add_meta_box('correction_meta', 'Correctie Details', 'boekcontrole_correction_meta_box', 'correction', 'normal');
}
add_action('add_meta_boxes', 'boekcontrole_add_meta_boxes');

function boekcontrole_correction_meta_box($post) {
    $fields = ['bladzijde', 'druk', 'type', 'beschrijving', 'book_id'];
    foreach ($fields as $field) {
        $value = get_post_meta($post->ID, $field, true);
        echo "<p><label for='$field'>" . ucfirst($field) . ":</label><br>
        <input type='text' name='$field' value='" . esc_attr($value) . "' style='width:100%'></p>";
    }
}

function boekcontrole_save_meta($post_id) {
    foreach (['bladzijde', 'druk', 'type', 'beschrijving', 'book_id'] as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_correction', 'boekcontrole_save_meta');

/**
 * AJAX handler for correction form
 */
function boekcontrole_handle_correction_ajax() {
    if (!isset($_POST['bc_nonce']) || !wp_verify_nonce($_POST['bc_nonce'], 'bc_correction_nonce')) {
        wp_send_json_error(['message' => 'Beveiligingscontrole mislukt. Vernieuw de pagina.']);
    }

    // Handle new book
    if ($_POST['book_select'] === 'nieuw') {
        $new_book_id = wp_insert_post([
            'post_type'   => 'book',
            'post_status' => 'pending',
            'post_title'  => sanitize_text_field($_POST['nieuw_boek_titel']),
            'meta_input'  => [
                'auteur' => sanitize_text_field($_POST['nieuw_boek_auteur']),
            ]
        ]);
        $book_id = $new_book_id;
    } else {
        $book_id = intval($_POST['book_select']);
    }

    // Create correction
    $correction_id = wp_insert_post([
        'post_type'   => 'correction',
        'post_status' => 'pending',
        'post_title'  => 'Correctie: ' . wp_strip_all_tags($_POST['beschrijving']),
    ]);

    if (is_wp_error($correction_id)) {
        wp_send_json_error(['message' => 'Er ging iets mis bij het opslaan.']);
    }

    foreach (['bladzijde', 'druk', 'type', 'beschrijving'] as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($correction_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    update_post_meta($correction_id, 'book_id', $book_id);

    // Handle file upload
    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_id = media_handle_upload('foto', $correction_id);
        if (!is_wp_error($attachment_id)) {
            update_post_meta($correction_id, 'foto', $attachment_id);
        }
    }

    wp_send_json_success(['message' => 'Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.']);
}
add_action('wp_ajax_bc_submit_correction', 'boekcontrole_handle_correction_ajax');
add_action('wp_ajax_nopriv_bc_submit_correction', 'boekcontrole_handle_correction_ajax');

/**
 * Helper: Get total stats for hero section
 */
function boekcontrole_get_stats() {
    $books       = wp_count_posts('book');
    $corrections = wp_count_posts('correction');

    return [
        'books'       => $books->publish ?? 0,
        'corrections' => $corrections->publish ?? 0,
    ];
}

/**
 * Helper: Get badge classes for correction type
 */
function bc_type_badge_classes($type) {
    switch ($type) {
        case 'inhoudelijk':
            return 'bg-red-50 text-red-700 border border-red-200';
        case 'typo':
            return 'bg-amber-50 text-amber-700 border border-amber-200';
        case 'misvertaling':
            return 'bg-violet-50 text-violet-700 border border-violet-200';
        default:
            return 'bg-gray-100 text-gray-600 border border-gray-200';
    }
}

/**
 * Helper: Get border color for correction card (mobile)
 */
function bc_type_border_class($type) {
    switch ($type) {
        case 'inhoudelijk':  return 'border-l-red-500';
        case 'typo':         return 'border-l-amber-500';
        case 'misvertaling': return 'border-l-violet-500';
        default:             return 'border-l-gray-300';
    }
}