<?php
namespace WpBlueprint\App\Core\Providers;

if (!defined('ABSPATH')) exit;

class MenuProvider
{
    public static function register(): void
    {
        add_action('after_setup_theme', [__CLASS__, 'registerMenus']);
    }

    public static function registerMenus(): void
    {
        register_nav_menus([
            'header_menu' => __('Header Menu', 'WpBlueprint'),
            'footer_menu' => __('Footer Menu', 'WpBlueprint'),
        ]);
    }

    public static function render(string $location = 'header_menu'): void
    {
        if (has_nav_menu($location)) {
            wp_nav_menu([
                'theme_location' => $location,
                'container'      => false,
                'items_wrap'     => '<ul class="nav__list">%3$s</ul>',
                'link_before'    => '',
                'link_after'     => '',
            ]);
        }
    }
}
