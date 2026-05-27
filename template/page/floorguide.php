<?php get_template_part('template/components/header'); ?>
<?php
use function WpBlueprint\breadcrumb;
?>
<div class="container">
    <section class="front-page-shops">
        <h2>Latest Shops</h2>
        <?php if(!empty($shops)): ?>
                <?php foreach($shops as $post): ?>
                    <article class="front-post shop">
                        <h3 class="item_ttl">
                            <a href="<?= esc_url($post->url) ?>">
                                <?= esc_html($post->title) ?>
                            </a>
                        </h3>
                        <small><?= esc_html($post->date) ?></small>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No shops found.</p>
            <?php endif; ?>
    </section>
</div>
<p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>
<?php get_template_part('template/components/footer'); ?>
