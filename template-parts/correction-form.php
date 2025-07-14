<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving'])) {
    // Als nieuwe boek is ingevuld, eerst het boek aanmaken
    if ($_POST['book_select'] === 'nieuw') {
        $new_book_id = wp_insert_post([
            'post_type' => 'book',
            'post_status' => 'pending',
            'post_title' => sanitize_text_field($_POST['nieuw_boek_titel']),
            'meta_input' => [
                'auteur' => sanitize_text_field($_POST['nieuw_boek_auteur']),
            ]
        ]);
        $book_id = $new_book_id;
    } else {
        $book_id = intval($_POST['book_select']);
    }

    // Correctie aanmaken
    $correction = [
        'post_type' => 'correction',
        'post_status' => 'pending',
        'post_title' => 'Correctie: ' . wp_strip_all_tags($_POST['beschrijving']),
    ];
    $correction_id = wp_insert_post($correction);

    foreach (['bladzijde', 'druk', 'type', 'beschrijving'] as $field) {
        update_post_meta($correction_id, $field, sanitize_text_field($_POST[$field]));
    }

    update_post_meta($correction_id, 'book_id', $book_id);

    // Afbeelding uploaden
    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_id = media_handle_upload('foto', $correction_id);
        if (!is_wp_error($attachment_id)) {
            update_post_meta($correction_id, 'foto', $attachment_id);
        }
    }

    echo '<div class="bg-green-100 text-green-800 p-4 mb-4">Bedankt! Uw correctie is ontvangen.</div>';
}
?>

<form method="POST" enctype="multipart/form-data" class="space-y-4">

    <label class="block">Boek kiezen:
        <select name="book_select" id="book_select" class="border p-2 w-full" onchange="toggleNieuwBoek()">
            <option value="">-- Selecteer een boek --</option>
            <?php
            $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish', 'pending']]);
            foreach ($books as $book) {
                echo "<option value='{$book->ID}'>" . esc_html($book->post_title) . "</option>";
            }
            ?>
            <option value="nieuw">➕ Nieuw boek toevoegen</option>
        </select>
    </label>

    <div id="nieuw_boek_fields" class="hidden">
        <label class="block">Nieuwe titel:
            <input type="text" name="nieuw_boek_titel" class="border p-2 w-full">
        </label>
        <label class="block">Auteur:
            <input type="text" name="nieuw_boek_auteur" class="border p-2 w-full">
        </label>
    </div>

    <label class="block">Druk:
        <input type="text" name="druk" class="border p-2 w-full">
    </label>

    <label class="block">Bladzijde:
        <input type="text" name="bladzijde" class="border p-2 w-full">
    </label>

    <label class="block">Type fout:
        <select name="type" class="border p-2 w-full">
            <option value="aqidah">Aqidah</option>
            <option value="typo">Typo</option>
            <option value="misvertaling">Misvertaling</option>
            <option value="overig">Overig</option>
        </select>
    </label>

    <label class="block">Beschrijving:
        <textarea name="beschrijving" class="border p-2 w-full" required></textarea>
    </label>

    <label class="block">Afbeelding (optioneel):
        <input type="file" name="foto" class="border p-2 w-full">
    </label>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Verzenden</button>
</form>

<script>
function toggleNieuwBoek() {
    const select = document.getElementById('book_select');
    const fields = document.getElementById('nieuw_boek_fields');
    if (select.value === 'nieuw') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>
