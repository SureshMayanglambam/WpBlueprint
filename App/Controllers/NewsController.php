<?php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Core\Support\BaseController;
use WpBlueprint\App\Models\News;

if (!defined('ABSPATH')) exit;

class NewsController extends BaseController {

    public function __construct() {
        if (is_singular('news')) {
            $this->show();
        } elseif (is_post_type_archive('news')) {
            $this->index();
        }
    }

    /**
     * Archive page
     */
    protected function index() {
        $news = News::query()
            ->where('status', 'publish')
            ->with(['acf_category', 'acf_image']) // optional ACF
            ->filterByTax('acf-news-cat')
            ->paginate(6);

        // Attach the post's category name so the template can render it cleanly.
        foreach ($news as $post) {
            $post_terms     = get_the_terms($post->id, 'acf-news-cat');
            $post->category = (!empty($post_terms) && !is_wp_error($post_terms)) ? $post_terms[0]->name : '';
        }

        $terms   = News::terms('acf-news-cat');
        $current = isset($_GET['acf-news-cat']) ? sanitize_title(wp_unslash($_GET['acf-news-cat'])) : '';

        $this->render('archive/news.php', compact('news', 'terms', 'current'));
    }

    /**
     * Single shop page
     */
    protected function show($id = null) {
        if (!$id) {
            $post = get_post();
            $id = $post ? $post->ID : 0;
        }

        $news = News::find($id);

        if ($news) {
            $post_terms     = get_the_terms($news->id, 'acf-news-cat');
            $news->category = (!empty($post_terms) && !is_wp_error($post_terms)) ? $post_terms[0]->name : '';
        }

        $this->render('single/news.php', compact('news'));
    }
}
