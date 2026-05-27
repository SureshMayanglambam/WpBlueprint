<?php
use function WpBlueprint\breadcrumb;
?>
<?php get_template_part('template/components/header'); ?>

    <div class="container">
        <article class="single-shop">
            <div class="single-shop__inner">
                <h1 class="shop_ttl"><?= esc_html($shop->title) ?></h1>
                <p class="info"><span class="floor"><?= esc_html($shop->acf['acf_floor']) ?></span><span class="cat"><?= esc_html($shop->acf['acf_category']['label'] ?? '') ?></span></p>
                <div class="img">
                    <img src="<?= esc_html($shop->acf['acf_image']) ?>" alt="">
                </div>
                <div class="content"><?= $shop->content ?></div>
            </div>
        </article>
    </div>
    <p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>

<?php get_template_part('template/components/footer'); ?>
