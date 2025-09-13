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

?>