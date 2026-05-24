<?php get_header(); ?>

<?php
$stats = boekcontrole_get_stats();
$books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'post_status' => 'publish']);
?>

<!-- ═══════════════════════════════════════════════════
     HERO — Full-width, immersive
     ═══════════════════════════════════════════════════ -->
<section class="hero-pattern bg-gradient-to-br from-[#022c22] via-[#064e3b] to-[#047857] text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-white/[0.08] border border-white/[0.12] rounded-full px-3 py-1 text-xs font-medium text-white/70 mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <?php echo intval($stats['corrections']); ?> correcties verwerkt
            </div>

            <h1 class="text-[2rem] sm:text-[2.75rem] font-extrabold leading-[1.15] tracking-tight mb-4">
                Correcties in<br>Islamitische boeken
            </h1>
            <p class="text-[15px] sm:text-base text-white/60 leading-relaxed mb-8 max-w-lg">
                Help mee om fouten in vertalingen en publicaties te signaleren. Samen zorgen we voor betrouwbare Islamitische literatuur.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="<?php echo site_url('/formulier'); ?>"
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-lg shadow-amber-500/25 transition-all hover:shadow-xl no-underline">
                    ✍️ Correctie melden
                </a>
                <a href="#boeken"
                   class="inline-flex items-center gap-2 bg-white/[0.08] hover:bg-white/[0.15] border border-white/[0.15] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all no-underline">
                    📚 Bekijk boeken
                </a>
            </div>
        </div>

        <!-- Stats row -->
        <div class="flex gap-8 sm:gap-12 mt-10 pt-8 border-t border-white/[0.08]">
            <div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-400"><?php echo intval($stats['books']); ?></div>
                <div class="text-xs text-white/40 mt-0.5 uppercase tracking-wider font-medium">Boeken</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-400"><?php echo intval($stats['corrections']); ?></div>
                <div class="text-xs text-white/40 mt-0.5 uppercase tracking-wider font-medium">Correcties</div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     WELKOMST — Compact, elegant quote
     ═══════════════════════════════════════════════════ -->
<div class="max-w-6xl mx-auto px-4 sm:px-6">

<section class="py-10 sm:py-12">
    <div class="relative bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden max-w-2xl">
        <!-- Green left accent -->
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-emerald-700"></div>

        <div class="px-6 sm:px-8 py-6">
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-3">Van de oprichter</p>

            <p class="text-[14px] sm:text-[15px] text-gray-600 leading-[1.75] mb-3">
                Welkom op de website van Boekcontrole. Het idee voor deze website is ontstaan nadat ik in de afgelopen jaren als vertaler en controleur van islamitische boeken soms vertaalvergissingen tegenkwam in verschillende boeken. Ik miste een goede, centrale plek waar men deze vergissingen (en correcties) kan terugvinden.
            </p>
            <p class="text-[14px] sm:text-[15px] text-gray-600 leading-[1.75] mb-4">
                Ik ben ervan overtuigd dat het merendeel van deze vergissingen concentratiefouten of typo's zijn, en ik weet zeker dat ik me ook weleens vergist heb in een boek dat ik vertaald heb. Fouten maken is menselijk. Het is echter wel belangrijk dat er een centrale plek is waar men deze vergissingen kan terugvinden, zodat men deze kan corrigeren. Op deze manier helpen we elkaar als ummah bij het verspreiden van correcte kennis van onze mooie religie.
            </p>

            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">AY</div>
                <div>
                    <div class="text-sm font-semibold text-gray-900">Abu Yunus</div>
                    <div class="text-xs text-gray-400">Oprichter</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     BOEKEN GRID
     ═══════════════════════════════════════════════════ -->
<section id="boeken" class="pb-16">
    <!-- Header + search -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Boeken</h2>
            <p class="text-sm text-gray-500"><?php echo count($books); ?> boek<?php echo count($books) !== 1 ? 'en' : ''; ?> met correctiemeldingen</p>
        </div>
        <div class="relative w-full sm:w-72">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="bc-search"
                   class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-900 shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 placeholder:text-gray-400"
                   placeholder="Zoeken..." autocomplete="off">
            <button id="bc-search-clear"
                    class="search-clear absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-gray-200 hover:bg-gray-300 items-center justify-center text-[10px] text-gray-500 transition-colors border-none cursor-pointer"
                    aria-label="Wis">✕</button>
        </div>
    </div>

    <?php if ($books) : ?>
    <div id="bc-book-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php
        $i = 0;
        foreach ($books as $book) :
            setup_postdata($book);
            $i++;
            $book_id = $book->ID;
            $title   = get_the_title($book_id);
            $author  = get_post_meta($book_id, 'auteur', true);

            $corrections = get_posts([
                'post_type' => 'correction', 'numberposts' => -1,
                'post_status' => 'publish',
                'meta_key' => 'book_id', 'meta_value' => $book_id,
            ]);

            $types = [];
            foreach ($corrections as $c) {
                $t = get_post_meta($c->ID, 'type', true);
                if (!isset($types[$t])) $types[$t] = 0;
                $types[$t]++;
            }
            $has_inhoudelijk = isset($types['inhoudelijk']);
        ?>
        <a href="<?php echo get_permalink($book_id); ?>"
           class="book-card group bg-white border border-gray-200/80 rounded-xl overflow-hidden flex flex-col shadow-sm hover:-translate-y-0.5 transition-all duration-200 no-underline animate-fade-in-up stagger-<?php echo min($i, 9); ?> <?php echo $has_inhoudelijk ? 'ring-1 ring-red-300' : ''; ?>"
           data-title="<?php echo esc_attr($title); ?>"
           data-author="<?php echo esc_attr($author); ?>">

            <!-- Cover -->
            <div class="relative bg-gradient-to-b from-gray-50 to-gray-100 flex items-center justify-center py-6 px-4 h-44">
                <?php if (has_post_thumbnail($book_id)) : ?>
                    <?php echo get_the_post_thumbnail($book_id, 'medium', [
                        'class' => 'max-h-36 w-auto object-contain drop-shadow-md group-hover:scale-[1.04] transition-transform duration-300'
                    ]); ?>
                <?php else : ?>
                    <div class="text-4xl opacity-20">📖</div>
                <?php endif; ?>

                <?php if ($has_inhoudelijk) : ?>
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">
                        ⚠️ Inhoudelijk
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="px-4 pt-3 pb-4 flex-1 flex flex-col">
                <h3 class="text-[15px] font-bold text-gray-900 mb-0.5 leading-snug group-hover:text-emerald-700 transition-colors line-clamp-2"><?php echo esc_html($title); ?></h3>
                <p class="text-xs text-gray-400 mb-3"><?php echo esc_html($author ?: 'Auteur onbekend'); ?></p>

                <div class="mt-auto flex items-center justify-between">
                    <div class="flex flex-wrap gap-1">
                        <?php foreach ($types as $type => $count) : ?>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold <?php echo bc_type_badge_classes($type); ?>">
                                <?php echo ucfirst(esc_html($type)); ?> <?php echo $count; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <span class="text-gray-300 group-hover:text-emerald-500 transition-colors text-sm">→</span>
                </div>
            </div>
        </a>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>

    <!-- No results -->
    <div id="bc-no-results" class="no-results text-center py-12 text-gray-400">
        <p class="text-sm">Geen boeken gevonden.</p>
    </div>

    <?php else : ?>
    <div class="text-center py-16 text-gray-400">
        <div class="text-4xl mb-3 opacity-40">📚</div>
        <p class="text-sm">Er zijn nog geen boeken toegevoegd.</p>
    </div>
    <?php endif; ?>
</section>

</div><!-- /max-w-6xl -->

<?php get_footer(); ?>
