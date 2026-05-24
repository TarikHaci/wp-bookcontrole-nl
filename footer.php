    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 mt-auto">
        <!-- Gradient accent line -->
        <div class="gradient-accent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Footer grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 py-10">
                <!-- Brand -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-2 flex items-center gap-2">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="" class="h-6 w-6 rounded">
                        BoekControle.nl
                    </h3>
                    <p class="text-sm leading-relaxed">
                        Een platform voor het melden en bijhouden van correcties in Islamitische boeken.
                        Samen werken we aan nauwkeurigere vertalingen en publicaties.
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h4 class="text-gray-200 text-sm font-semibold uppercase tracking-wider mb-4">Navigatie</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo home_url(); ?>" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors no-underline">Boeken overzicht</a></li>
                        <li><a href="<?php echo site_url('/formulier'); ?>" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors no-underline">Correctie melden</a></li>
                        <li><a href="<?php echo site_url('/over-ons'); ?>" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors no-underline">Over ons</a></li>
                    </ul>
                </div>

                <!-- Info -->
                <div>
                    <h4 class="text-gray-200 text-sm font-semibold uppercase tracking-wider mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li><a href="https://websiteexpert.nl" target="_blank" rel="noopener" class="text-sm text-gray-400 hover:text-emerald-400 transition-colors no-underline">WebsiteExpert.nl</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-gray-800 py-6 text-center text-sm">
                Gerealiseerd door <span class="text-emerald-400">Allaah</span> – WebsiteExpert.nl – Alhamdulillaah.
            </div>
        </div>
    </footer>

    <!-- Lightbox overlay -->
    <div id="bc-lightbox" class="lightbox">
        <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/15 border-none text-white text-xl cursor-pointer flex items-center justify-center hover:bg-white/30 transition-colors" aria-label="Sluiten">✕</button>
        <img id="bc-lightbox-img" src="" alt="Afbeelding vergroot">
    </div>

    <?php wp_footer(); ?>
</body>
</html>
