<?php
function boekcontrole_enqueue_styles() {
    wp_enqueue_style('tailwind', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', [], null);
    wp_enqueue_style('boekcontrole-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'boekcontrole_enqueue_styles');

function islambieb_enqueue_tailwind() {
    echo '<script src="https://cdn.tailwindcss.com"></script>';
}
add_action('wp_head', 'islambieb_enqueue_tailwind');

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
        'supports' => ['title'],
        'publicly_queryable' => false,
        'show_ui' => true
    ]);
}
add_action('init', 'boekcontrole_register_post_types');

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