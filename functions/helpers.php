<?php
namespace WpBlueprint;

if (!defined('ABSPATH')) exit;

/**
 * Global breadcrumb helper
 *
 * Templates import via: use function WpBlueprint\breadcrumb;
 */
if (!function_exists(__NAMESPACE__ . '\breadcrumb')) {
    function breadcrumb(?array $override = null): string {
        return \WpBlueprint\App\Core\Helpers\Breadcrumb::generate($override);
    }
}
