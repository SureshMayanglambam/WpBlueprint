<?php
namespace WpBlueprint\App\Core\Providers;

if (!defined('ABSPATH')) exit;

class RouteServiceProvider {

    public static function register(): void {
        require_once \get_template_directory() . '/routes/web.php';

        \add_action('template_redirect', [Router::class, 'dispatch']);
    }
}
