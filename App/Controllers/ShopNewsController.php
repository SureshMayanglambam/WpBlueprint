<?php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Core\Support\BaseController;
use WpBlueprint\App\Models\ShopNews;

if (!defined('ABSPATH')) exit;

class ShopNewsController extends BaseController {

    public function __construct() {
        if (is_post_type_archive('shopnews')) {
            $this->archive();
        } elseif (is_singular('shopnews')) {
            $this->single();
        }
    }

    /**
     * Archive page
     */
    protected function archive() {
        // Fetch paginated shopnews posts
        $shopnews = ShopNews::query()
            ->paginate(9); // You can adjust per page

        // Preload related shop ACF data
        foreach ($shopnews as $news) {
            $news->acf['related_shop_data'] = $news->relatedShop();
        }

        $this->render('archive/shopnews.php', compact('shopnews'));
    }

    /**
     * Single page
     */
    protected function single($id = null) {
        if (!$id) {
            $post = get_post();
            $id = $post ? $post->ID : 0;
        }

        $news = ShopNews::find($id);

        if ($news) {
            // Preload related shop ACF data
            $news->acf['related_shop_data'] = $news->relatedShop();
        }

        $this->render('single/shopnews.php', compact('news'));
    }
}
