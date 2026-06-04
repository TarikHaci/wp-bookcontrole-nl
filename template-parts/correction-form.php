<?php
/**
 * Correction Form — AJAX + POST fallback
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beschrijving']) && !wp_doing_ajax()) {
    if (!isset($_POST['_bc_nonce']) || !wp_verify_nonce($_POST['_bc_nonce'], 'bc_correction_nonce')) {
        echo '<div class="p-3 rounded-lg text-xs font-medium mb-4 bg-red-50 text-red-600 border border-red-200 animate-slide-down">❌ Beveiligingscontrole mislukt.</div>';
    } else {
        if ($_POST['book_select'] === 'nieuw') {
            $book_id = wp_insert_post(['post_type'=>'book','post_status'=>'pending','post_title'=>sanitize_text_field($_POST['nieuw_boek_titel']),'meta_input'=>['auteur'=>sanitize_text_field($_POST['nieuw_boek_auteur'])]]);
        } else {
            $book_id = intval($_POST['book_select']);
        }
        $cid = wp_insert_post(['post_type'=>'correction','post_status'=>'pending','post_title'=>'Correctie: '.wp_strip_all_tags($_POST['beschrijving'])]);
        foreach (['bladzijde','druk','beschrijving'] as $f) { if (isset($_POST[$f])) update_post_meta($cid,$f,sanitize_text_field($_POST[$f])); }
        if (isset($_POST['type'])) {
            $type_val = is_array($_POST['type']) ? array_map('sanitize_text_field', $_POST['type']) : sanitize_text_field($_POST['type']);
            update_post_meta($cid, 'type', $type_val);
        }
        update_post_meta($cid,'book_id',$book_id);
        if (!empty($_FILES['foto']['name'][0])) {
            require_once(ABSPATH.'wp-admin/includes/file.php'); require_once(ABSPATH.'wp-admin/includes/media.php'); require_once(ABSPATH.'wp-admin/includes/image.php');
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
        echo '<div class="p-3 rounded-lg text-xs font-medium mb-4 bg-emerald-50 text-emerald-700 border border-emerald-200 animate-slide-down">✅ Jazaak Allaahu khayran! Uw correctie is ontvangen en wordt beoordeeld.</div>';
    }
}
?>

<form id="bc-correction-form" method="POST" enctype="multipart/form-data" class="space-y-4">
    <?php wp_nonce_field('bc_correction_nonce', '_bc_nonce'); ?>

    <!-- Boek -->
    <div>
        <label for="book_select" class="block text-xs font-semibold text-gray-600 mb-1.5">Boek *</label>
        <select name="book_select" id="book_select"
                class="custom-select w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white shadow-sm transition focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                required>
            <option value="">Selecteer een boek</option>
            <?php
            $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => ['publish','pending']]);
            foreach ($books as $b) echo "<option value='{$b->ID}'>".esc_html($b->post_title)."</option>";
            ?>
            <option value="nieuw">+ Nieuw boek toevoegen</option>
        </select>
    </div>

    <!-- Nieuw boek -->
    <div id="bc-new-book-fields" class="slide-content bg-gray-50 rounded-lg px-4 border border-gray-200 space-y-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Boektitel</label>
            <input type="text" name="nieuw_boek_titel" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Bijv. Cursus Arabisch 1">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Auteur</label>
            <input type="text" name="nieuw_boek_auteur" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Bijv. v Abdur-Rahiem">
        </div>
    </div>

    <div class="border-t border-gray-100 pt-4"></div>

    <!-- Druk + Bladzijde inline -->
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Druk / Editie *</label>
            <input type="text" name="druk" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="2e druk" required>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bladzijde *</label>
            <input type="text" name="bladzijde" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="42" required>
        </div>
    </div>

    <!-- Type -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-2">Type fout *</label>
        <div class="flex flex-wrap gap-2">
            <div class="relative">
                <input type="checkbox" name="type[]" id="type-inhoudelijk" value="inhoudelijk" class="type-radio type-radio-inhoudelijk absolute opacity-0 w-0 h-0">
                <label for="type-inhoudelijk" class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-gray-200 rounded-lg text-xs font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    ⚠️ Inhoudelijk
                </label>
            </div>
            <div class="relative">
                <input type="checkbox" name="type[]" id="type-typo" value="typo" class="type-radio type-radio-typo absolute opacity-0 w-0 h-0">
                <label for="type-typo" class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-gray-200 rounded-lg text-xs font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    ✏️ Typo
                </label>
            </div>
            <div class="relative">
                <input type="checkbox" name="type[]" id="type-misvertaling" value="misvertaling" class="type-radio type-radio-misvertaling absolute opacity-0 w-0 h-0">
                <label for="type-misvertaling" class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-gray-200 rounded-lg text-xs font-medium cursor-pointer transition-all bg-white hover:border-gray-300">
                    🔄 Misvertaling
                </label>
            </div>
        </div>
    </div>

    <!-- Beschrijving -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Beschrijving *</label>
        <textarea name="beschrijving" rows="3"
                  class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 resize-y"
                  placeholder="Beschrijf de fout zo duidelijk mogelijk..." required></textarea>
    </div>

    <!-- Upload (meerdere afbeeldingen) -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Afbeeldingen (optioneel)</label>
        <div id="bc-upload-zone"
             class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-5 text-center cursor-pointer transition-all bg-gray-50 hover:border-emerald-400 hover:bg-emerald-50/30">
            <span class="block text-2xl mb-1 opacity-50">📷</span>
            <p class="text-xs text-gray-400"><strong class="text-emerald-600">Klik</strong> of sleep bestanden · meerdere afbeeldingen mogelijk</p>
            <input type="file" name="foto[]" id="bc-upload-input" accept="image/*" multiple class="hidden">
        </div>
        <div id="bc-upload-preview" class="upload-preview mt-3">
            <!-- Preview thumbnails worden hier dynamisch toegevoegd -->
        </div>
    </div>

    <!-- Submit -->
    <button type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-lg text-sm font-semibold shadow-md shadow-emerald-500/20 hover:shadow-lg transition-all cursor-pointer">
        ✅ Correctie insturen
    </button>
</form>
