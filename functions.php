<?php
namespace WpBlueprint;

if (!defined('ABSPATH')) exit;




/**
 * Autoloader for App classes (Providers, Controllers, Models, Router, ...)
 */
spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__ . '\\';
    $base_dir = __DIR__ . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $relative_class = str_replace('\\', '/', $relative_class) . '.php';

    $file = $base_dir . $relative_class;

    if (file_exists($file)) {
        require_once $file;
    }
});




/**
 * Auto-register all Providers
 */
$providers_dir = __DIR__ . '/App/Core/Providers/';
if (is_dir($providers_dir)) {
    foreach (glob($providers_dir . '*.php') as $provider_file) {
        require_once $provider_file;

        $class_name = 'WpBlueprint\\App\\Core\\Providers\\' . basename($provider_file, '.php');

        if (class_exists($class_name) && method_exists($class_name, 'register')) {
            $class_name::register();
        }
    }
}




/**
 * Global helpers (view, breadcrumb, ...)
 */
include 'functions/helpers.php';

/**
 * Asset & theme setup
 */
include 'functions/assets.php';
