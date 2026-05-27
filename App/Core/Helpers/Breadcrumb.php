<?php
namespace WpBlueprint\App\Core\Helpers;

if (!defined('ABSPATH')) exit;

class Breadcrumb {

    /**
     * Generate breadcrumb
     * 
     * @param array|null $override Optional array of ['label' => '...', 'link' => '...'] per item
     * @return string
     */
    public static function generate(array $override = null): string {
        $items = [];

        // Home always links to home_url()
        $home_label = 'トップ';
        $home_link  = esc_url(home_url('/'));
        if ($override && isset($override[0]['label'])) {
            $home_label = $override[0]['label'];
        }
        $items[] = '<a href="' . $home_link . '">' . esc_html($home_label) . '</a>';

        if (is_front_page()) {
            return implode(' ', $items);
        }

        if ($override) {
            // Skip first item (home) since we already handled it
            foreach (array_slice($override, 1) as $o) {
                if (isset($o['link'])) {
                    $items[] = '<a href="' . esc_url($o['link']) . '">' . esc_html($o['label']) . '</a>';
                } else {
                    $items[] = esc_html($o['label']);
                }
            }
            return implode(' ', $items);
        }

        // Pages: include parent hierarchy
        if (is_page()) {
            global $post;
            $parent_ids = get_post_ancestors($post);
            if ($parent_ids) {
                $parent_ids = array_reverse($parent_ids); // Start from top parent
                foreach ($parent_ids as $parent_id) {
                    $items[] = '<a href="' . get_permalink($parent_id) . '">' . get_the_title($parent_id) . '</a>';
                }
            }
            $items[] = get_the_title($post->ID);

        // Singular posts
        } elseif (is_singular()) {
            global $post;
            $post_type = get_post_type($post);

            // CPT archive
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj && $post_type_obj->has_archive) {
                $archive_link = get_post_type_archive_link($post_type_obj->name);
                $archive_title = $post_type_obj->labels->singular_name;
                $items[] = '<a href="' . esc_url($archive_link) . '">' . esc_html($archive_title) . '</a>';
            }

            // Categories (only for 'post')
            if ($post_type === 'post') {
                $categories = get_the_category($post->ID);
                if ($categories) {
                    $category = $categories[0]; // first category
                    $items = array_merge($items, self::get_category_parents($category));
                }
            }

            $items[] = get_the_title($post->ID);

        // Post type archive
        } elseif (is_post_type_archive()) {
            $archive_title = post_type_archive_title('', false);
            $items[] = esc_html($archive_title);

        // Category / Tag / Custom Taxonomy archive
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $items = array_merge($items, self::get_term_parents($term));
        }

        return implode(' ', $items);
    }

    /**
     * Get category hierarchy as breadcrumb items
     */
    protected static function get_category_parents($category) {
        $parents = [];
        if ($category->parent) {
            $parent_cats = get_ancestors($category->term_id, 'category');
            $parent_cats = array_reverse($parent_cats);
            foreach ($parent_cats as $parent_id) {
                $parent = get_category($parent_id);
                $parents[] = '<a href="' . get_category_link($parent->term_id) . '">' . $parent->name . '</a>';
            }
        }
        $parents[] = '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
        return $parents;
    }

    /**
     * Get taxonomy term hierarchy as breadcrumb items
     */
    protected static function get_term_parents($term) {
        $parents = [];
        if ($term->parent) {
            $parent_terms = get_ancestors($term->term_id, $term->taxonomy);
            $parent_terms = array_reverse($parent_terms);
            foreach ($parent_terms as $parent_id) {
                $parent = get_term($parent_id, $term->taxonomy);
                $parents[] = '<a href="' . get_term_link($parent) . '">' . $parent->name . '</a>';
            }
        }
        $parents[] = $term->name;
        return $parents;
    }
}
