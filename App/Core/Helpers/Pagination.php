<?php
namespace WpBlueprint\App\Core\Helpers;

if (!defined('ABSPATH')) exit;

class Pagination {

    /**
     * Render pagination links
     *
     * @param int $paged Current page number
     * @param int $max_pages Total number of pages
     * @param string $relative_path Relative path after home_url (like 'shop/' or 'news/')
     * @return string HTML pagination
     */

    public static function render($paged, $max_pages) {
        if ($max_pages <= 1) return '';

        $current_url = remove_query_arg('page');

        $html = '<nav class="pagination"><ul>';

        $start = max(1, $paged - 2);
        $end   = min($max_pages, $paged + 2);

        if ($start > 1) {
            $html .= '<li><a href="' . esc_url(add_query_arg('page', 1, $current_url)) . '">1</a></li>';
            if ($start > 2) $html .= '<li>...</li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active   = $i === $paged ? ' class="active"' : '';
            $page_url = add_query_arg('page', $i, $current_url);
            $html    .= "<li{$active}><a href=\"" . esc_url($page_url) . "\">{$i}</a></li>";
        }

        if ($end < $max_pages) {
            if ($end < $max_pages - 1) $html .= '<li>...</li>';
            $html .= '<li><a href="' . esc_url(add_query_arg('page', $max_pages, $current_url)) . '">' . $max_pages . '</a></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }
}
