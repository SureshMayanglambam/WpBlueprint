<?php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Core\Support\BaseController;
use WpBlueprint\App\Models\Shop;

if (!defined('ABSPATH')) exit;

class ShopController extends BaseController {

    public function __construct() {
        if (is_post_type_archive('shop')) {
            $this->archive();
        } elseif (is_singular('shop')) {
            $this->single();
        }
    }

    /**
     * Archive page
     */
    protected function archive() {
        $shops = Shop::query()
            ->where('status', 'publish')
            ->with(['acf_floor', 'acf_category', 'acf_image']) // optional ACF
            ->filterBy('acf_category')
            ->paginate(9);

        $categories = Shop::categories('acf_category');
        $current    = isset($_GET['acf_category']) ? sanitize_text_field(wp_unslash($_GET['acf_category'])) : '';

        $this->render('archive/shop.php', compact('shops', 'categories', 'current'));
    }

    /**
     * Single shop page
     */
    protected function single($id = null) {
        if (!$id) {
            $post = get_post();
            $id = $post ? $post->ID : 0;
        }

        $shop = Shop::find($id);
        $this->render('single/shop.php', compact('shop'));
    }

}
