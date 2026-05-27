<?php
use function WpBlueprint\breadcrumb;
?>
<?php get_template_part('template/components/header'); ?>

    <div class="container">
        <article class="single-news">
            <h1 class="single_ttl">
                <?php if ($news->category): ?>
                    <span class="cat">(<?= esc_html($news->category) ?>)</span>
                <?php endif; ?>
                <?= esc_html($news->title) ?>
            </h1>
            <small><?= esc_html($news->date) ?></small>
            <div class="content">
                <?= $news->content ?>
            </div>
        </article>
    </div>
    <p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>

<?php get_template_part('template/components/footer'); ?>
