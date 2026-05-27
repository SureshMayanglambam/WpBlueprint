<?php get_template_part('template/components/header'); ?>



<div class="topPage">

    <?php if(!empty($kvimg)): ?>
        <div class="top_kv">
            <ul class="top_kv__slider">
                <?php foreach($kvimg as $post): ?>
                    <li class="top_kv__slider__slide">
                        <?php if($post->acf['acf_target'] === 1): ?>
                            <a href="<?= esc_url($post->acf['acf_url']) ?>">
                                <div class="img" style="background-image: url(`<?= esc_url($post->acf['acf_image']) ?>`);"></div>
                            </a>
                        <?php else : ?>
                            <a href="<?= esc_url($post->acf['acf_url']) ?>" target="_blank">
                                <div class="img" style="background-image: url('<?= esc_url($post->acf['acf_image']) ?>');"></div>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="notice_text">
            <h2 class="text">Do not edit any file inside Providers(if you are not familiar) because that is the core file of this theme all the feature will set inside the Providers.</h2>
            <p class="text-sub">Please read the documentation first before using it<br>All the documentation are post on News please check it first.</p>
        </div>
        <!-- News Section -->
        <section class="front-page-news">
            <h2 class="title">Latest News</h2>
            <?php if(!empty($news)): ?>
                <?php foreach($news as $post): ?>
                    <article class="front-post news">
                        <h3 class="item_ttl">
                            <a href="<?= esc_url($post->url) ?>">
                                <?= esc_html($post->title) ?>
                            </a>
                        </h3>
                        <small><?= esc_html($post->date) ?></small>
                    </article>
                <?php endforeach; ?>
                <div class="btn">
                    <a href="<?= esc_url(home_url('/news')); ?>">VIEW MORE</a>
                </div>
            <?php else: ?>
                <p>No news found.</p>
            <?php endif; ?>
        </section>

        <div class="video">
            <video id="myVideo" controls poster="thumbnail.jpg" style="width:100%"></video>
            <p id="debug" style="color:red;font-size:12px"></p>
        </div>
    
        <!-- Shops Section -->
        <section class="archive-shop">
            <h2 class="title">Latest Shop</h2>
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
                <div class="btn">
                    <a href="<?= esc_url(home_url('/news')); ?>">VIEW MORE</a>
                </div>
            <?php else: ?>
                <p>No shops found.</p>
            <?php endif; ?>
        </section>
    
    </div><!--/container-->
</div><!--/topPage-->

<?php get_template_part('template/components/footer'); ?>
