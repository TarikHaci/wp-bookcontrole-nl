<?php get_header(); ?>

<?php
$stats = boekcontrole_get_stats();
$books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => 'publish']);
?>

<!-- ═══════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════ -->
<section class="relative bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 text-white rounded-2xl overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 px-6 sm:px-10 lg:px-14 py-12 sm:py-16 mb-10">
    <!-- Decorative glow -->
    <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-radial from-amber-500/10 to-transparent pointer-events-none" style="background:radial-gradient(ellipse at 80% 30%, rgba(245,158,11,0.1) 0%, transparent 70%)"></div>

    <div class="relative z-10 max-w-3xl">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-4">
            Correcties in<br>Islamitische boeken
        </h1>
        <p class="text-base sm:text-lg text-white/70 mb-8 max-w-2xl leading-relaxed">
            Help mee om fouten in vertalingen en publicaties te signaleren. Samen zorgen we voor betrouwbare Islamitische literatuur.
        </p>

        <!-- Stats -->
        <div class="flex flex-wrap gap-3 sm:gap-4">
            <div class="bg-white/[0.08] backdrop-blur-md border border-white/[0.12] rounded-xl px-5 sm:px-6 py-4 min-w-[7rem]">
                <span class="block text-2xl sm:text-3xl font-extrabold text-amber-400"><?php echo intval($stats['books']); ?></span>
                <span class="block text-sm text-white/60 mt-0.5">Boeken</span>
            </div>
            <div class="bg-white/[0.08] backdrop-blur-md border border-white/[0.12] rounded-xl px-5 sm:px-6 py-4 min-w-[7rem]">
                <span class="block text-2xl sm:text-3xl font-extrabold text-amber-400"><?php echo intval($stats['corrections']); ?></span>
                <span class="block text-sm text-white/60 mt-0.5">Correcties</span>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     WELCOME MESSAGE — Abu Yunus
     ═══════════════════════════════════════════════════════ -->
<section class="mb-12">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8 max-w-3xl">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-lg shadow-md shadow-emerald-500/20">
                📝
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-0.5">Welkom bij BoekControle</h2>
                <p class="text-sm text-gray-500">Een woord van de oprichter</p>
            </div>
        </div>
        <div class="text-gray-600 text-sm sm:text-base leading-relaxed space-y-4 pl-0 sm:pl-14">
            <p>
                Welkom op de website van Boekcontrole. Het idee voor deze website is ontstaan nadat ik in de afgelopen jaren als vertaler en controleur van islamitische boeken soms vertaalvergissingen tegenkwam in verschillende boeken. Ik miste een goede, centrale plek waar men deze vergissingen (en correcties) kan terugvinden.
            </p>
            <p>
                Ik ben ervan overtuigd dat het merendeel van deze vergissingen concentratiefouten of typo's zijn, en ik weet zeker dat ik me ook weleens vergist heb in een boek dat ik vertaald heb. Fouten maken is menselijk. Het is echter wel belangrijk dat er een centrale plek is waar men deze vergissingen kan terugvinden, zodat men deze kan corrigeren. Op deze manier helpen we elkaar als ummah bij het verspreiden van correcte kennis van onze mooie religie.
            </p>
            <p class="text-gray-800 font-medium">
                Jullie broeder,<br>
                Abu Yunus.
            </p>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     BOOK GRID
     ═══════════════════════════════════════════════════════ -->
<section>
    <!-- Section header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">📚 Overzicht van boeken</h2>
        <a href="<?php echo site_url('/formulier'); ?>"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-600 hover:to-emerald-500 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-md shadow-emerald-600/25 hover:shadow-lg transition-all hover:-translate-y-0.5 no-underline self-start">
            ✍️ Meld een correctie
        </a>
    </div>

    <!-- Search -->
    <div class="relative max-w-md mb-8">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">🔍</span>
        <input type="text" id="bc-search"
               class="w-full pl-11 pr-10 py-3 border-2 border-gray-200 rounded-xl text-base bg-white text-gray-900 shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 placeholder:text-gray-400"
               placeholder="Zoek op boektitel of auteur..." autocomplete="off">
        <button id="bc-search-clear"
                class="search-clear absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200 items-center justify-center text-xs text-gray-500 hover:text-gray-700 transition-colors border-none cursor-pointer"
                aria-label="Wis zoekopdracht">✕</button>
    </div>

    <?php if ($books) : ?>
    <!-- Grid -->
    <div id="bc-book-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $card_index = 0;
        foreach ($books as $book) :
            setup_postdata($book);
            $card_index++;
            $book_id = $book->ID;
            $title   = get_the_title($book_id);
            $author  = get_post_meta($book_id, 'auteur', true);

            $corrections = get_posts([
                'post_type'   => 'correction',
                'numberposts' => -1,
                'post_status' => 'publish',
                'meta_key'    => 'book_id',
                'meta_value'  => $book_id,
            ]);

            $types = [];
            foreach ($corrections as $correction) {
                $type = get_post_meta($correction->ID, 'type', true);
                if (!isset($types[$type])) $types[$type] = 0;
                $types[$type]++;
            }

            $has_inhoudelijk = isset($types['inhoudelijk']);
        ?>
        <article class="group bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-xl hover:shadow-emerald-500/[0.07] hover:-translate-y-1 transition-all duration-300 animate-fade-in-up stagger-<?php echo min($card_index, 12); ?> <?php echo $has_inhoudelijk ? 'border-l-4 border-l-red-400' : ''; ?>"
                 data-title="<?php echo esc_attr($title); ?>"
                 data-author="<?php echo esc_attr($author); ?>">

            <!-- Cover -->
            <div class="relative bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center p-6 min-h-[12rem]">
                <?php if (has_post_thumbnail($book_id)) : ?>
                    <?php echo get_the_post_thumbnail($book_id, 'medium', [
                        'class' => 'max-h-44 w-auto object-contain rounded shadow-md group-hover:scale-[1.03] transition-transform duration-300'
                    ]); ?>
                <?php else : ?>
                    <span class="text-5xl opacity-25">📖</span>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="text-lg font-bold text-gray-900 mb-1 leading-snug"><?php echo esc_html($title); ?></h3>
                <p class="text-sm text-gray-500 mb-3"><?php echo esc_html($author ?: 'Auteur onbekend'); ?></p>

                <p class="text-sm text-gray-600 mb-3 font-medium">
                    <strong class="text-gray-900"><?php echo count($corrections); ?></strong>
                    correctie<?php echo count($corrections) !== 1 ? 's' : ''; ?> gemeld
                </p>

                <?php if (!empty($types)) : ?>
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <?php foreach ($types as $type => $count) : ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo bc_type_badge_classes($type); ?>">
                            <?php echo ucfirst(esc_html($type)); ?>: <?php echo $count; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Action -->
                <div class="mt-auto pt-4 border-t border-gray-100">
                    <a href="<?php echo get_permalink($book_id); ?>"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-500 hover:gap-3 transition-all no-underline">
                        Bekijk correcties <span>→</span>
                    </a>
                </div>
            </div>
        </article>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>

    <!-- No search results -->
    <div id="bc-no-results" class="no-results text-center py-16 text-gray-400">
        <span class="block text-4xl mb-3">🔎</span>
        <p class="text-base">Geen boeken gevonden voor deze zoekopdracht.</p>
    </div>

    <?php else : ?>
    <!-- Empty state -->
    <div class="text-center py-20 text-gray-400">
        <span class="block text-5xl mb-4">📚</span>
        <p class="text-base">Er zijn nog geen boeken toegevoegd.</p>
    </div>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
