    </main>

    <footer class="bc-footer">
        <div class="bc-container bc-footer__inner">
            <div class="bc-footer__brand">
                <h3>📖 BoekControle.nl</h3>
                <p>Een platform voor het melden en bijhouden van correcties in Islamitische boeken. Samen werken we aan nauwkeurigere vertalingen en publicaties.</p>
            </div>
            <div class="bc-footer__links">
                <h4>Navigatie</h4>
                <ul>
                    <li><a href="<?php echo home_url(); ?>">Boeken overzicht</a></li>
                    <li><a href="<?php echo site_url('/formulier'); ?>">Correctie melden</a></li>
                </ul>
            </div>
            <div class="bc-footer__links">
                <h4>Info</h4>
                <ul>
                    <li><a href="https://websiteexpert.nl" target="_blank" rel="noopener">WebsiteExpert.nl</a></li>
                </ul>
            </div>
        </div>
        <div class="bc-container bc-footer__bottom">
            Gerealiseerd door <span>Allaah</span> – WebsiteExpert.nl – Alhamdulillaah.
        </div>
    </footer>

    <!-- Lightbox -->
    <div id="bc-lightbox" class="bc-lightbox">
        <button class="bc-lightbox__close" aria-label="Sluiten">✕</button>
        <img id="bc-lightbox-img" src="" alt="Afbeelding vergroot">
    </div>

    <?php wp_footer(); ?>
</body>
</html>
