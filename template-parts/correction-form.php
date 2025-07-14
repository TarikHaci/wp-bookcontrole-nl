<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving'])) {
    $correction = [
        'post_type' => 'correction',
        'post_status' => 'pending',
        'post_title' => 'Correctie: ' . wp_strip_all_tags($_POST['beschrijving'])
    ];
    $id = wp_insert_post($correction);
    foreach (['bladzijde', 'druk', 'type', 'beschrijving', 'book_id'] as $field) {
        update_post_meta($id, $field, sanitize_text_field($_POST[$field]));
    }
    echo '<div class="bg-green-100 text-green-800 p-4 mb-4">Bedankt! Uw correctie is ontvangen.</div>';
}
?>
<form method="POST" class="space-y-4">
    <label class="block">Boek ID:
        <input type="text" name="book_id" class="border p-2 w-full">
    </label>
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
        <textarea name="beschrijving" class="border p-2 w-full"></textarea>
    </label>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Verzenden</button>
</form>
