<?php
namespace WpBlueprint\App\Core\Helpers;

if (!defined('ABSPATH')) exit;

class Debug {
    /**
     * Log controller, template, and data to a fixed overlay on the front-end
     *
     * @param string $controller
     * @param string $template
     * @param array $data
     */
    public static function log($controller, $template, $data = []) {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('THEME_DEBUG') && THEME_DEBUG) {
            $output = "<div style='position:fixed;bottom:0;left:0;width:100%;background:#222;color:#fff;padding:10px;font-family:monospace;font-size:12px;z-index:9999;overflow:auto;max-height:200px;'>";
            $output .= "<strong>Controller:</strong> " . esc_html($controller) . "<br>";
            $output .= "<strong>Template:</strong> " . esc_html($template) . "<br>";
            $output .= "<strong>Data:</strong> <pre style='color:#0f0;'>";
            $output .= esc_html(print_r($data, true));
            $output .= "</pre></div>";
            echo $output;
        }
    }
}
