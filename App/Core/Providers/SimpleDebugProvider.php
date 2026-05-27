<?php
namespace WpBlueprint\App\Core\Providers;

class SimpleDebugProvider
{
    public static function register()
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) return;

        static $rendered = false;

        $renderError = function(array $data) use (&$rendered) {
            if ($rendered) return;
            // Ignore errors without a message
            if (empty($data['message'])) return;

            $rendered = true;
            // Clean any previous empty output
            if (ob_get_length()) {
                ob_clean();
            }

            include __DIR__ . '/error.php';
            exit;
        };

        // Start output buffering to catch any blank output
        ob_start();

        // Ignore non-fatal warnings/notices
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            // Let PHP handle non-fatal errors
            return false;
        });

        // Handle uncaught exceptions
        set_exception_handler(function ($e) use ($renderError) {
            $renderError([
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        // Handle fatal errors
        register_shutdown_function(function() use ($renderError) {
            $err = error_get_last();
            if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
                $renderError([
                    'type' => 'Fatal Error',
                    'message' => $err['message'],
                    'file' => $err['file'],
                    'line' => $err['line'],
                    'trace' => 'N/A'
                ]);
            }
        });
    }
}
