<?php
namespace WpBlueprint\App\Core\Support;

use WpBlueprint\App\Core\Helpers\Debug;
use WpBlueprint\App\Core\Helpers\Pagination;

if (!defined('ABSPATH')) exit;

abstract class BaseController {

    protected $data = [];

    protected function render(string $template, array $data = []) {
        $this->data = $data;
        $template_path = get_template_directory() . '/template/' . $template;

        if (file_exists($template_path)) {
            extract($this->data);
            Debug::log(get_class($this), $template, $this->data);
            include $template_path;
        } else {
            echo "Template not found: " . esc_html($template_path);
        }
    }

    // Paginated posts (archive)
    protected function getPaginatedPosts($post_type, $per_page = 10, $acf_fields = []) {
        $paged = max(1, get_query_var('paged', 1));
        if (isset($_GET['page']) && is_numeric($_GET['page'])) $paged = intval($_GET['page']);

        $query = new \WP_Query([
            'post_type'      => $post_type,
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'post_status'    => 'publish',
        ]);

        $posts = array_map(function($p) use ($acf_fields) {
            $postObj = (object) [
                'id'      => $p->ID,
                'title'   => get_the_title($p),
                'url'     => get_permalink($p),
                'date'    => get_the_date('', $p),
                'excerpt' => get_the_excerpt($p),
                'content' => apply_filters('the_content', $p->post_content),
            ];
            foreach ($acf_fields as $field) {
                $postObj->$field = get_field($field, $p->ID);
            }
            return $postObj;
        }, $query->posts);

        $pagination = $this->makePagination($paged, $query->max_num_pages, '/' . $post_type . '/');

        return (object) compact('posts', 'pagination'); // return as object
    }

    // Non-paginated posts (single or limited)
    protected function getPosts($post_type, $number = 5, $acf_fields = []) {
        $args = [
            'post_type'   => $post_type,
            'numberposts' => $number,
            'post_status' => 'publish',
        ];

        $posts = get_posts($args);

        $postObjs = array_map(function($p) use ($acf_fields) {
            $postObj = (object) [
                'id'      => $p->ID,
                'title'   => get_the_title($p),
                'url'     => get_permalink($p),
                'date'    => get_the_date('', $p),
                'excerpt' => get_the_excerpt($p),
                'content' => apply_filters('the_content', $p->post_content),
            ];
            foreach ($acf_fields as $field) {
                $postObj->$field = get_field($field, $p->ID);
            }
            return $postObj;
        }, $posts);

        return $postObjs;
    }

    protected function makePagination($paged, $max_pages, $base_url) {
        return Pagination::render($paged, $max_pages, $base_url);
    }
}
