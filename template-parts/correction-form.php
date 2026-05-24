<?php
/**
 * Correction Form Template Part
 * Handles both AJAX (preferred) and traditional POST fallback
 */

// Traditional POST fallback (when JS is disabled)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving']) && !wp_doing_ajax()) {
    // Verify nonce
    if (!isset($_POST['_bc_nonce']) || !wp_verify_nonce($_POST['_bc_nonce'], 'bc_correction_nonce')) {
        echo '<div class="bc-alert bc-alert--error">❌ Beveiligingscontrole mislukt. Vernieuw de pagina.</div>';
    } else {
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

        foreach (['bladzijde', 'druk', 'type', 'beschrijving'] as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($correction_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        update_post_meta($correction_id, 'book_id', $book_id);

        // Handle upload
        if (!empty($_FILES['foto']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $attachment_id = media_handle_upload('foto', $correction_id);
            if (!is_wp_error($attachment_id)) {
                update_post_meta($correction_id, 'foto', $attachment_id);
            }
        }

        echo '<div class="bc-alert bc-alert--success">✅ Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.</div>';
    }
}
?>

<form id="bc-correction-form" method="POST" enctype="multipart/form-data">
    <?php wp_nonce_field('bc_correction_nonce', '_bc_nonce'); ?>

    <!-- Boek selectie -->
    <div class="bc-form-group">
        <label for="book_select" class="bc-label">
            <span class="bc-label__icon">📘</span> Kies een boek
        </label>
        <select name="book_select" id="book_select" class="bc-select" required>
            <option value="">— Selecteer een boek —</option>
            <?php
            $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish', 'pending']]);
            foreach ($books as $book) {
                echo "<option value='{$book->ID}'>" . esc_html($book->post_title) . "</option>";
            }
            ?>
            <option value="nieuw">➕ Nieuw boek toevoegen</option>
        </select>
    </div>

    <!-- Nieuw boek velden (slide-down) -->
    <div id="bc-new-book-fields" class="bc-slide-content">
        <div class="bc-form-group">
            <label class="bc-label">Titel van nieuw boek</label>
            <input type="text" name="nieuw_boek_titel" class="bc-input" placeholder="Bijv. Riyaad as-Saalihien">
        </div>
        <div class="bc-form-group">
            <label class="bc-label">Auteur</label>
            <input type="text" name="nieuw_boek_auteur" class="bc-input" placeholder="Bijv. Imaam an-Nawawie">
        </div>
    </div>

    <hr style="border:none;border-top:1px solid var(--clr-gray-200);margin:var(--space-6) 0;">

    <!-- Druk -->
    <div class="bc-form-group">
        <label class="bc-label">
            <span class="bc-label__icon">📚</span> Druk / Editie
        </label>
        <input type="text" name="druk" class="bc-input" placeholder="Bijv. 2e druk, 2024" required>
    </div>

    <!-- Bladzijde -->
    <div class="bc-form-group">
        <label class="bc-label">
            <span class="bc-label__icon">📄</span> Bladzijde
        </label>
        <input type="text" name="bladzijde" class="bc-input" placeholder="Bijv. 42" required>
    </div>

    <!-- Type fout -->
    <div class="bc-form-group">
        <label class="bc-label">
            <span class="bc-label__icon">🚩</span> Type fout
        </label>
        <div class="bc-type-selector">
            <div class="bc-type-option bc-type-option--aqidah">
                <input type="radio" name="type" id="type-aqidah" value="aqidah" required>
                <label for="type-aqidah">⚠️ Aqidah</label>
            </div>
            <div class="bc-type-option bc-type-option--typo">
                <input type="radio" name="type" id="type-typo" value="typo">
                <label for="type-typo">✏️ Typo</label>
            </div>
            <div class="bc-type-option">
                <input type="radio" name="type" id="type-misvertaling" value="misvertaling">
                <label for="type-misvertaling">🔄 Misvertaling</label>
            </div>
            <div class="bc-type-option">
                <input type="radio" name="type" id="type-overig" value="overig">
                <label for="type-overig">📝 Overig</label>
            </div>
        </div>
    </div>

    <!-- Beschrijving -->
    <div class="bc-form-group">
        <label class="bc-label">
            <span class="bc-label__icon">📝</span> Beschrijving van de fout
        </label>
        <textarea name="beschrijving" class="bc-textarea" rows="4" placeholder="Beschrijf de fout zo duidelijk mogelijk. Vermeld eventueel de juiste tekst." required></textarea>
    </div>

    <!-- Afbeelding upload -->
    <div class="bc-form-group">
        <label class="bc-label">
            <span class="bc-label__icon">📷</span> Afbeelding (optioneel)
        </label>
        <div id="bc-upload-zone" class="bc-upload">
            <span class="bc-upload__icon">📷</span>
            <p class="bc-upload__text">
                <strong>Klik om te uploaden</strong> of sleep een bestand hierheen
            </p>
            <input type="file" name="foto" id="bc-upload-input" accept="image/*" style="display:none;">
            <div id="bc-upload-preview" class="bc-upload__preview">
                <img id="bc-upload-preview-img" src="" alt="Preview">
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div style="padding-top:var(--space-4);">
        <button type="submit" class="bc-btn bc-btn--primary bc-btn--lg" style="width:100%;">
            ✅ Correctie insturen
        </button>
    </div>
</form>
