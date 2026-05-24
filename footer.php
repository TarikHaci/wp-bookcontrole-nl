    </main>

    <!-- ── FOOTER ── -->
    <footer class="bg-[#0f172a] text-gray-400 mt-auto">
        <div class="gradient-accent"></div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 py-10">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-xs">📖</div>
                        <span class="text-white font-bold">BoekControle.nl</span>
                    </div>
                    <p class="text-[13px] leading-relaxed text-gray-500">
                        Een platform voor correctiemeldingen in Islamitische boeken. Samen werken we aan betrouwbare kennis.
                    </p>
                </div>
                <div>
                    <h4 class="text-gray-300 text-xs font-semibold uppercase tracking-[0.1em] mb-4">Navigatie</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo home_url(); ?>" class="text-[13px] text-gray-500 hover:text-emerald-400 transition-colors no-underline">Boeken overzicht</a></li>
                        <li><a href="<?php echo site_url('/formulier'); ?>" class="text-[13px] text-gray-500 hover:text-emerald-400 transition-colors no-underline">Correctie melden</a></li>
                        <li><a href="<?php echo site_url('/over-ons'); ?>" class="text-[13px] text-gray-500 hover:text-emerald-400 transition-colors no-underline">Over ons</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-gray-300 text-xs font-semibold uppercase tracking-[0.1em] mb-4">Gemaakt door</h4>
                    <a href="https://websiteexpert.nl" target="_blank" rel="noopener" class="text-[13px] text-gray-500 hover:text-emerald-400 transition-colors no-underline">WebsiteExpert.nl ↗</a>
                </div>
            </div>
            <div class="border-t border-gray-800/60 py-5 text-center text-xs text-gray-600">
                Gerealiseerd door <span class="text-emerald-500/80">Allaah</span> – Alhamdulillaah.
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div id="bc-lightbox" class="lightbox">
        <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 backdrop-blur text-white text-xl cursor-pointer flex items-center justify-center hover:bg-white/25 transition-colors border-none" aria-label="Sluiten">✕</button>
        <img id="bc-lightbox-img" src="" alt="">
    </div>

    <?php wp_footer(); ?>
</body>
</html>
