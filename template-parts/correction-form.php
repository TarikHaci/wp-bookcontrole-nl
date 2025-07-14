<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving'])) {
    // Boek aanmaken indien nodig
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

    echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-6">✅ Bedankt! Uw correctie is ontvangen en wordt beoordeeld.</div>';
}
?>

<form method="POST" enctype="multipart/form-data" class="space-y-6">

    <!-- Boek keuze -->
    <div>
        <label for="book_select" class="block font-medium mb-1">📘 Kies een boek:</label>
        <select name="book_select" id="book_select" class="border p-2 w-full rounded" required onchange="toggleNieuwBoek()">
            <option value="">-- Selecteer een boek --</option>
            <?php
            $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish', 'pending']]);
            foreach ($books as $book) {
                echo "<option value='{$book->ID}'>" . esc_html($book->post_title) . "</option>";
            }
            ?>
            <option value="nieuw">➕ Nieuw boek toevoegen</option>
        </select>
    </div>

    <!-- Nieuw boek invoervelden -->
    <div id="nieuw_boek_fields" class="hidden space-y-4">
        <div>
            <label class="block font-medium mb-1">Titel van nieuw boek:</label>
            <input type="text" name="nieuw_boek_titel" class="border p-2 w-full rounded">
        </div>
        <div>
            <label class="block font-medium mb-1">Auteur:</label>
            <input type="text" name="nieuw_boek_auteur" class="border p-2 w-full rounded">
        </div>
    </div>

    <!-- Correctiegegevens -->
    <div>
        <label class="block font-medium mb-1">📚 Druk:</label>
        <input type="text" name="druk" class="border p-2 w-full rounded" required>
    </div>

    <div>
        <label class="block font-medium mb-1">📄 Bladzijde:</label>
        <input type="text" name="bladzijde" class="border p-2 w-full rounded" required>
    </div>

    <div>
        <label class="block font-medium mb-1">🚩 Type fout:</label>
        <select name="type" class="border p-2 w-full rounded" required>
            <option value="aqidah">Aqidah</option>
            <option value="typo">Typo</option>
            <option value="misvertaling">Misvertaling</option>
            <option value="overig">Overig</option>
        </select>
    </div>

    <div>
        <label class="block font-medium mb-1">📝 Beschrijving van de fout:</label>
        <textarea name="beschrijving" class="border p-2 w-full rounded" rows="4" required></textarea>
    </div>

    <div>
        <label class="block font-medium mb-1">📷 Voeg afbeelding toe (optioneel):</label>
        <input type="file" name="foto" accept="image/*" class="border p-2 w-full rounded">
    </div>

    <!-- Verstuur knop -->
    <div>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-full hover:bg-blue-700 transition">
            ✅ Correctie insturen
        </button>
    </div>
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
