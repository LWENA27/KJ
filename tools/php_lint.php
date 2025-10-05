<?php
// Small helper to run php -l on files and print results in the browser/CLI.
// Usage (CLI): php tools/php_lint.php path/to/file1.php path/to/file2.php
// Usage (browser): /tools/php_lint.php?files=controllers/DoctorController.php,views/doctor/dashboard.php

if (PHP_SAPI === 'cli') {
    array_shift($argv);
    $files = $argv;
} else {
    $files = [];
    if (!empty($_GET['files'])) {
        $files = array_map('trim', explode(',', $_GET['files']));
    }
}

if (empty($files)) {
    echo "No files specified.\n";
    exit(1);
}

foreach ($files as $file) {
    $path = __DIR__ . '/../' . $file;
    echo "Checking: $path\n";
    if (!file_exists($path)) {
        echo "  -> NOT FOUND\n\n";
        continue;
    }

    $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($path);
    // Use proc_open to capture stdout and stderr reliably
    $descriptorspec = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $proc = proc_open($cmd, $descriptorspec, $pipes);
    if (is_resource($proc)) {
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($proc);

        echo "Output:\n" . trim($out) . "\n";
        if (trim($err) !== '') echo "Errors:\n" . trim($err) . "\n";
    } else {
        echo "  -> Failed to run php -l\n";
    }

    echo "---\n";
}

return 0;
