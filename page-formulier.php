<?php
/* Template Name: Correctieformulier */
get_header();
?>

<div style="max-width:40rem;margin-inline:auto;">
    <!-- Steps Indicator -->
    <div class="bc-steps">
        <div class="bc-step is-active">
            <span class="bc-step__number">1</span>
            <span class="bc-hide-mobile" style="display:inline !important;">Boek kiezen</span>
        </div>
        <span class="bc-step__connector"></span>
        <div class="bc-step">
            <span class="bc-step__number">2</span>
            <span class="bc-hide-mobile" style="display:inline !important;">Details invullen</span>
        </div>
        <span class="bc-step__connector"></span>
        <div class="bc-step">
            <span class="bc-step__number">3</span>
            <span class="bc-hide-mobile" style="display:inline !important;">Versturen</span>
        </div>
    </div>

    <div class="bc-form-card">
        <div class="bc-form-header">
            <div class="bc-form-header__icon">✍️</div>
            <h1>Correctie melden</h1>
            <p>Heeft u een fout gevonden in een Islamitisch boek? Meld het hieronder.</p>
        </div>

        <?php get_template_part('template-parts/correction-form'); ?>
    </div>
</div>

<?php get_footer(); ?>