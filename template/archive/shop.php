<?php
use function WpBlueprint\breadcrumb;
?>
<?php get_template_part('template/components/header'); ?>

    <div class="container">
        <section class="archive-shop">
            <?php if (!empty($categories)): ?>
                <?php $base = remove_query_arg(['acf_category', 'page']); ?>
                <ul class="category-filter">
                    <li<?= $current === '' ? ' class="active"' : '' ?>>
                        <a href="<?= esc_url($base) ?>">All</a>
                    </li>
                    <?php foreach ($categories as $value => $label): ?>
                        <li<?= $current === $value ? ' class="active"' : '' ?>>
                            <a href="<?= esc_url(add_query_arg('acf_category', $value, $base)) ?>"><?= esc_html($label) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($shops)): ?>
                <ul class="shop-list">
                    <?php foreach ($shops as $post): ?>
                        <li class="shop-list__item">
                            <a href="<?= esc_url($post->url) ?>">
                                <div class="shop-card">
                                    <div class="img-area">
                                        <img src="<?= esc_html($post->acf['acf_image']) ?>" alt="<?= esc_html($post->title) ?>">
                                    </div>
                                    <div class="text-area">
                                        <p class="shop-ttl"><span class="floor"><?= esc_html($post->acf['acf_floor']) ?></span><?= esc_html($post->title) ?></p>
                                        <small class="cat"><?= esc_html($post->acf['acf_category']['label'] ?? '') ?></small>
                                        <div class="desc">
                                            <?= $post->content ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?= $shops->pagination ?? '' ?>
            <?php else: ?>
                <p>No shops found.</p>
            <?php endif; ?>
        </section>
    </div>
    <p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>

<?php get_template_part('template/components/footer'); ?>
