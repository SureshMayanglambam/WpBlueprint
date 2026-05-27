<?php
use WpBlueprint\App\Core\Providers\MenuProvider;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?= bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <script src="<?= get_template_directory_uri(); ?>/assets/lib/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;family=ZCOOL+XiaoWei&amp;family=Zen+Kaku+Gothic+New:wght@400;500;700&amp;display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
    <?php
        if(is_front_page()){
            echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/lib/slick/slick.css">';
            echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/lib/slick/slick-theme.css">';
        }
    ?>
    <link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/assets/css/style.css">  
</head>
<body>
<header class="header">
    <div class="header__inner">
        <h1 class="logo"><a href="<?= esc_url(home_url('/')); ?>"><?= esc_html(get_bloginfo('name')); ?></a></h1>
        <nav class="nav">
            <ul class="nav__list">
                <li class="nav__item menu-item">
                    <a href="<?= esc_url(home_url('/')); ?>">Home</a>
                </li>
                <li class="nav_item menu-item">
                    <a href="<?= esc_url(home_url('/news')); ?>">News</a>
                </li>
                <li class="nav_item menu-item">
                    <a href="<?= esc_url(home_url('/shop')); ?>">Shop</a>
                </li>
                <li class="nav_item menu-item">
                    <a href="<?= esc_url(home_url('/about')); ?>">About</a>
                </li>
                <li class="nav_item menu-item">
                    <a href="<?= esc_url(home_url('/floorguide')); ?>">Floorguide</a>
                </li>
                <li class="nav_item menu-item">
                    <a href="<?= esc_url(home_url('/shopnews')); ?>">Shopnews</a>
                </li>
            </ul><!--/nav__list-->
        </nav><!--nav-->
    </div><!--/header__inner-->
</header><!--/header-->

<p>Menu from admin panel↓↓↓↓</p>

<header class="header default">
    <div class="header__inner">
        <h1 class="logo"><a href="<?= esc_url(home_url('/')); ?>"><?= esc_html(get_bloginfo('name')); ?></a></h1>
        <nav class="nav">
            <?php MenuProvider::render('header_menu'); ?>
        </nav><!--/nav-->
    </div><!--header__inner-->
</header><!--/header-->
<main>