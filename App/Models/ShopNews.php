<?php
namespace WpBlueprint\App\Models;

use WpBlueprint\App\Core\Support\BaseModel;

if (!defined('ABSPATH')) exit;

class ShopNews extends BaseModel {
    protected static $post_type = 'shopnews';

    /**
     * Get related shop data including ACF fields
     * Returns null if no shop assigned
     */
    public function relatedShop(): ?array {
        $shop_post = $this->acf['shop'] ?? null;

        if (!$shop_post || !($shop_post instanceof \WP_Post)) {
            return null;
        }

        $shop_id = $shop_post->ID;

        return [
            'id'       => $shop_id,
            'title'    => get_the_title($shop_id),
            'url'      => get_permalink($shop_id),
            'category' => get_field('acf_category', $shop_id),
            'floor'    => get_field('acf_floor', $shop_id),
            'image'    => get_field('acf_image', $shop_id),
        ];
    }
}
