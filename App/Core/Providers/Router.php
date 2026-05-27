<?php
namespace WpBlueprint\App\Core\Providers;

if (!defined('ABSPATH')) exit;

class Router {

    protected static array $routes = [];

    public static function frontPage(string $controller): void {
        static::$routes[] = ['match' => 'front_page', 'controller' => $controller];
    }

    public static function postType(string $postType, string $controller): void {
        static::$routes[] = ['match' => 'post_type', 'post_type' => $postType, 'controller' => $controller];
    }

    public static function page(string $slug, string $controller): void {
        static::$routes[] = ['match' => 'page', 'slug' => $slug, 'controller' => $controller];
    }

    public static function pageFallback(): void {
        static::$routes[] = ['match' => 'page_fallback'];
    }

    public static function dispatch(): void {
        foreach (static::$routes as $route) {
            if (static::matches($route)) {
                static::handle($route);
                exit;
            }
        }
        exit;
    }

    protected static function matches(array $route): bool {
        return match ($route['match']) {
            'front_page'    => \is_front_page(),
            'post_type'     => \is_singular($route['post_type']) || \is_post_type_archive($route['post_type']),
            'page'          => \is_page($route['slug']),
            'page_fallback' => \is_page(),
            default         => false,
        };
    }

    protected static function handle(array $route): void {
        if ($route['match'] === 'page_fallback') {
            static::renderPageTemplate();
            return;
        }
        new $route['controller']();
    }

    protected static function renderPageTemplate(): void {
        $page = \get_post();
        if (!$page) return;

        $slug = $page->post_name;

        if ($page->post_parent > 0) {
            $parent      = \get_post($page->post_parent);
            $parent_slug = $parent ? $parent->post_name : '';
            $combined    = $parent_slug ? "{$parent_slug}-{$slug}" : $slug;
        } else {
            $combined = $slug;
        }

        $template = \locate_template("template/page/{$combined}.php")
                 ?: \locate_template("template/page/{$slug}.php");

        if ($template) {
            include $template;
        }
    }
}
