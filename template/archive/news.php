<?php
use function WpBlueprint\breadcrumb;
?>
<?php get_template_part('template/components/header'); ?>

    <div class="container">
        <section class="news-archive">
            <?php if (!empty($terms)): ?>
                <?php $base = remove_query_arg(['acf-news-cat', 'page']); ?>
                <ul class="category-filter">
                    <li<?= $current === '' ? ' class="active"' : '' ?>>
                        <a href="<?= esc_url($base) ?>">All</a>
                    </li>
                    <?php foreach ($terms as $slug => $name): ?>
                        <li<?= $current === $slug ? ' class="active"' : '' ?>>
                            <a href="<?= esc_url(add_query_arg('acf-news-cat', $slug, $base)) ?>"><?= esc_html($name) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($news)): ?>
                <ul class="archive-list">
                    <?php foreach ($news as $post): ?>
                        <li class="archive-list__item">
                            <a class="archive-list__item__menu" href="<?= esc_url($post->url) ?>">
                                <?php if ($post->category): ?>
                                    <span class="cat">(<?= esc_html($post->category) ?>)</span>
                                <?php endif; ?>
                                <?= esc_html($post->title) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?= $news->pagination ?? '' ?>
            <?php else: ?>
                <p>No news found.</p>
            <?php endif; ?>
        </section>
    </div>
    <p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>

<?php get_template_part('template/components/footer'); ?>
