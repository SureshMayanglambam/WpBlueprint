<?php get_template_part('template/components/header'); ?>

<div class="container">
    <article class="single-shopnews">
        <h1><?= esc_html($news->title) ?></h1>
        <small><?= esc_html($news->date) ?></small>
        <div class="content">
            <?= $news->content ?>
        </div>

        <?php if (!empty($news->acf['related_shop_data'])):
            $shop = $news->acf['related_shop_data'];
        ?>
            <div class="related-shop">
                <h2>Related Shop</h2>
                <a href="<?= esc_url($shop['url']) ?>">
                    <?= esc_html($shop['title']) ?>
                </a>
                <div>
                    <span>Category: <?= esc_html($shop['category']) ?></span>
                    <span> | Floor: <?= esc_html($shop['floor']) ?></span>
                </div>
                <?php if (!empty($shop['image'])): ?>
                    <div class="shop-image">
                        <img src="<?= esc_url($shop['image']) ?>" alt="">
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </article>
</div>

<?php get_template_part('template/components/footer'); ?>
