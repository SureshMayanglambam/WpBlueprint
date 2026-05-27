<?php
namespace WpBlueprint;

use WpBlueprint\App\Core\Providers\Router;
use WpBlueprint\App\Controllers\FrontPageController;
use WpBlueprint\App\Controllers\NewsController;
use WpBlueprint\App\Controllers\ShopController;
use WpBlueprint\App\Controllers\ShopNewsController;
use WpBlueprint\App\Controllers\FloorguideController;

if (!defined('ABSPATH')) exit;

Router::frontPage(FrontPageController::class);

Router::postType('news',     NewsController::class);
Router::postType('shop',     ShopController::class);
Router::postType('shopnews', ShopNewsController::class);

Router::page('floorguide', FloorguideController::class);

// Fallback: resolve any other page by slug (or {parent-slug}-{slug}) under template/page/
Router::pageFallback();
