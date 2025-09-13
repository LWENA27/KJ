<?php
// Error logging configuration
class Logger {
    private static $logFile = __DIR__ . '/../logs/application.log';
    
    public static function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$level}: {$message}";
        
        if (!empty($context)) {
            $logEntry .= ' | Context: ' . json_encode($context);
        }
        
        $logEntry .= PHP_EOL;
        
        // Ensure log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }
    
    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }
    
    public static function debug($message, $context = []) {
        self::log('DEBUG', $message, $context);
    }
    
    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }
}

// Set custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $context = [
        'errno' => $errno,
        'file' => $errfile,
        'line' => $errline
    ];
    Logger::error($errstr, $context);
    return false; // Let PHP handle the error too
}

// Set custom exception handler
function customExceptionHandler($exception) {
    $context = [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
    Logger::error($exception->getMessage(), $context);
}

// Register handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Also log to PHP error log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
?>
