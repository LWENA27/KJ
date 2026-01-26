<?php
// Common helper functions

if (!function_exists('format_tsh')) {
    function format_tsh($amount, int $decimals = 2): string {
        // Normalize
        $num = is_numeric($amount) ? (float)$amount : 0.0;
        // Tanzanian Shilling usually displayed without decimals; but allow override
        $dec = $decimals;
        // Use thousands separator
        $formatted = number_format($num, $dec, '.', ',');
        return 'TSh ' . $formatted;
    }
}

if (!function_exists('safe_date')) {
    /**
     * Format a date safely. Returns $fallback when $date is empty or cannot be parsed by strtotime.
     *
     * @param string $format PHP date format
     * @param mixed $date Date string or timestamp
     * @param string $fallback Value to return when date is not available
     * @return string
     */
    function safe_date(string $format, $date, string $fallback = ''): string {
        if ($date === null || $date === '' ) return $fallback;
        // If numeric, assume timestamp
        if (is_numeric($date)) {
            return date($format, (int)$date);
        }
        $ts = @strtotime($date);
        if ($ts === false) return $fallback;
        return date($format, $ts);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token hidden input field
     *
     * @return string HTML hidden input with CSRF token
     */
    function csrf_field(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
    }
}

?>