<?php
/* 
 * 404 of theme "AvantDoc"
 * 
 * @package Default
 */       

set_meta(['title' => __('Avant - Page not found')]);
include_header(); ?>

<section class="container center">
    <h1 class="not-found-title">
        <?php _e('404') ?>
    </h1>

    <p>A página que você está procurando não está aqui...<br> Talvez seja porque o sistema está em contrução.   </p>
    <p>Melhor <a href="<?php echo BASE_URL ?>">voltar para o início</a>.</p>
</section>

<?php include_footer();
