<?php get_template_part('template/components/header'); ?>

<div class="container">
    <section class="archive-shopnews">
        <?php if (!empty($shopnews)): ?>
            <ul>
                <?php foreach ($shopnews as $news): ?>
                    <li>
                        <h3>
                            <a href="<?= esc_url($news->url) ?>">
                                <?= esc_html($news->title) ?>
                            </a>
                        </h3>
                        <small><?= esc_html($news->date) ?></small>

                        <?php if (!empty($news->acf['related_shop_data'])): 
                            $shop = $news->acf['related_shop_data'];
                        ?>
                            <div class="related-shop">
                                <strong>Shop: </strong>
                                <a href="<?= esc_url($shop['url']) ?>">
                                    <?= esc_html($shop['title']) ?>
                                </a>
                                <span> | Category: <?= esc_html($shop['category']) ?></span>
                                <span> | Floor: <?= esc_html($shop['floor']) ?></span>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?= $shopnews->pagination ?? '' ?>

        <?php else: ?>
            <p>No shop news found.</p>
        <?php endif; ?>
    </section>
</div>

<?php get_template_part('template/components/footer'); ?>
