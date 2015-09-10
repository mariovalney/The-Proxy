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

    <p><?php _e('The page you are looking for is not here...')?></p>
    <p>
        <?php _e("Maybe it's because our site is still under construction.")?><br>
        <?php _e("We hope we'll finish all the Docs and release the version Beta to download soon. :D")?>
    </p>
</section>

<?php include_footer();
