<?php

add_theme_support('post-thumbnails');

/**
 * Enqueue styles & scripts
 */
function boekcontrole_enqueue_assets() {
    // Google Font: Inter
    wp_enqueue_style('google-fonts-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', [], null);

    // Tailwind CSS v4 Browser CDN (single import)
    wp_enqueue_style('tailwind', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', [], null);

    // Theme stylesheet
    wp_enqueue_style('boekcontrole-style', get_stylesheet_uri(), ['google-fonts-inter', 'tailwind'], wp_get_theme()->get('Version'));

    // Custom JS
    wp_enqueue_script('boekcontrole-js', get_template_directory_uri() . '/js/boekcontrole.js', [], wp_get_theme()->get('Version'), true);

    // Localize script for AJAX
    wp_localize_script('boekcontrole-js', 'bcAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bc_correction_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'boekcontrole_enqueue_assets');

/**
 * Register custom post types
 */
function boekcontrole_register_post_types() {
    register_post_type('book', [
        'labels' => [
            'name' => 'Boeken',
            'singular_name' => 'Boek'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'boeken'],
    ]);

    register_post_type('correction', [
        'labels' => [
            'name' => 'Correcties',
            'singular_name' => 'Correctie'
        ],
        'public' => true,
        'has_archive' => false,
        'supports' => ['title', 'thumbnail'],
        'publicly_queryable' => false,
        'show_ui' => true
    ]);
}
add_action('init', 'boekcontrole_register_post_types');

/**
 * Admin meta boxes
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
    // Verify nonce
    if (!isset($_POST['bc_nonce']) || !wp_verify_nonce($_POST['bc_nonce'], 'bc_correction_nonce')) {
        wp_send_json_error(['message' => 'Beveiligingscontrole mislukt. Vernieuw de pagina.']);
    }

    // Handle new book if needed
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
 * Helper: Get total correction stats
 */
function boekcontrole_get_stats() {
    $books = wp_count_posts('book');
    $corrections = wp_count_posts('correction');

    $aqidah_count = 0;
    $aqidah_corrections = get_posts([
        'post_type'   => 'correction',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_key'    => 'type',
        'meta_value'  => 'aqidah',
        'fields'      => 'ids',
    ]);
    $aqidah_count = count($aqidah_corrections);

    return [
        'books'       => $books->publish ?? 0,
        'corrections' => $corrections->publish ?? 0,
        'aqidah'      => $aqidah_count,
    ];
}