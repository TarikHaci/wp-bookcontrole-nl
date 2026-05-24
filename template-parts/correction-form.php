<?php
/**
 * Correction Form Template Part
 * AJAX submit (preferred) + traditional POST fallback
 */

// Traditional POST fallback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving']) && !wp_doing_ajax()) {
    if (!isset($_POST['_bc_nonce']) || !wp_verify_nonce($_POST['_bc_nonce'], 'bc_correction_nonce')) {
        echo '<div class="p-4 rounded-xl text-sm font-medium mb-6 bg-red-50 text-red-700 border border-red-200 animate-slide-down">❌ Beveiligingscontrole mislukt. Vernieuw de pagina.</div>';
    } else {
        if ($_POST['book_select'] === 'nieuw') {
            $new_book_id = wp_insert_post([
                'post_type' => 'book', 'post_status' => 'pending',
                'post_title' => sanitize_text_field($_POST['nieuw_boek_titel']),
                'meta_input' => ['auteur' => sanitize_text_field($_POST['nieuw_boek_auteur'])]
            ]);
            $book_id = $new_book_id;
        } else {
            $book_id = intval($_POST['book_select']);
        }

        $correction_id = wp_insert_post([
            'post_type' => 'correction', 'post_status' => 'pending',
            'post_title' => 'Correctie: ' . wp_strip_all_tags($_POST['beschrijving']),
        ]);

        foreach (['bladzijde', 'druk', 'type', 'beschrijving'] as $field) {
            if (isset($_POST[$field])) update_post_meta($correction_id, $field, sanitize_text_field($_POST[$field]));
        }
        update_post_meta($correction_id, 'book_id', $book_id);

        if (!empty($_FILES['foto']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_id = media_handle_upload('foto', $correction_id);
            if (!is_wp_error($attachment_id)) update_post_meta($correction_id, 'foto', $attachment_id);
        }

        echo '<div class="p-4 rounded-xl text-sm font-medium mb-6 bg-emerald-50 text-emerald-800 border border-emerald-200 animate-slide-down">✅ Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.</div>';
    }
}
?>

<form id="bc-correction-form" method="POST" enctype="multipart/form-data" class="space-y-5">
    <?php wp_nonce_field('bc_correction_nonce', '_bc_nonce'); ?>

    <!-- Boek selectie -->
    <div>
        <label for="book_select" class="block text-sm font-semibold text-gray-700 mb-2">
            📘 Kies een boek
        </label>
        <select name="book_select" id="book_select"
                class="custom-select w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white text-gray-900 shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
                required>
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

    <!-- Nieuw boek (slide-down) -->
    <div id="bc-new-book-fields" class="slide-content bg-gray-50 rounded-xl px-5 border border-gray-200 space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Titel van nieuw boek</label>
            <input type="text" name="nieuw_boek_titel"
                   class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
                   placeholder="Bijv. Riyaad as-Saalihien">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Auteur</label>
            <input type="text" name="nieuw_boek_auteur"
                   class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
                   placeholder="Bijv. Imaam an-Nawawie">
        </div>
    </div>

    <!-- Divider -->
    <hr class="border-gray-200">

    <!-- Druk -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">📚 Druk / Editie</label>
        <input type="text" name="druk"
               class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
               placeholder="Bijv. 2e druk, 2024" required>
    </div>

    <!-- Bladzijde -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">📄 Bladzijde</label>
        <input type="text" name="bladzijde"
               class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
               placeholder="Bijv. 42" required>
    </div>

    <!-- Type fout — pill radio buttons -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-3">🚩 Type fout</label>
        <div class="flex flex-wrap gap-2">
            <!-- Inhoudelijk -->
            <div class="relative">
                <input type="radio" name="type" id="type-inhoudelijk" value="inhoudelijk"
                       class="type-radio type-radio-inhoudelijk absolute opacity-0 w-0 h-0" required>
                <label for="type-inhoudelijk"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-full text-sm font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    ⚠️ Inhoudelijk
                </label>
            </div>
            <!-- Typo -->
            <div class="relative">
                <input type="radio" name="type" id="type-typo" value="typo"
                       class="type-radio type-radio-typo absolute opacity-0 w-0 h-0">
                <label for="type-typo"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-full text-sm font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    ✏️ Typo
                </label>
            </div>
            <!-- Misvertaling -->
            <div class="relative">
                <input type="radio" name="type" id="type-misvertaling" value="misvertaling"
                       class="type-radio type-radio-misvertaling absolute opacity-0 w-0 h-0">
                <label for="type-misvertaling"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-full text-sm font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    🔄 Misvertaling
                </label>
            </div>
        </div>
    </div>

    <!-- Beschrijving -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">📝 Beschrijving van de fout</label>
        <textarea name="beschrijving" rows="4"
                  class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-base bg-white shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 resize-y min-h-[7rem]"
                  placeholder="Beschrijf de fout zo duidelijk mogelijk. Vermeld eventueel de juiste tekst." required></textarea>
    </div>

    <!-- Afbeelding upload -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">📷 Afbeelding (optioneel)</label>
        <div id="bc-upload-zone"
             class="upload-zone border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer transition-all bg-gray-50 hover:border-emerald-400 hover:bg-emerald-50/50">
            <span class="block text-3xl mb-2">📷</span>
            <p class="text-sm text-gray-500">
                <strong class="text-emerald-600">Klik om te uploaden</strong> of sleep een bestand hierheen
            </p>
            <input type="file" name="foto" id="bc-upload-input" accept="image/*" class="hidden">
            <div id="bc-upload-preview" class="upload-preview mt-4">
                <img id="bc-upload-preview-img" src="" alt="Preview" class="max-h-40 mx-auto rounded-lg shadow-md">
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="pt-2">
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-600 hover:to-emerald-500 text-white px-6 py-3.5 rounded-full text-base font-semibold shadow-lg shadow-emerald-600/25 hover:shadow-xl hover:shadow-emerald-600/30 transition-all hover:-translate-y-0.5 cursor-pointer">
            ✅ Correctie insturen
        </button>
    </div>
</form>
