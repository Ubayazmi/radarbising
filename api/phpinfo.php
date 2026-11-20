<?php
/**
 * Raw PHP info - no PDO usage
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP Version ===\n";
echo PHP_VERSION . "\n\n";

echo "=== Extension Dir ===\n";
echo ini_get('extension_dir') . "\n\n";

echo "=== Loaded Extensions ===\n";
$exts = get_loaded_extensions();
sort($exts);
foreach ($exts as $ext) {
    echo "$ext\n";
}

echo "\n=== PDO Check ===\n";
echo "extension_loaded('pdo'): " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "extension_loaded('pdo_pgsql'): " . (extension_loaded('pdo_pgsql') ? 'YES' : 'NO') . "\n";
echo "extension_loaded('pgsql'): " . (extension_loaded('pgsql') ? 'YES' : 'NO') . "\n";

echo "\n=== PDO Drivers (if PDO class exists) ===\n";
if (class_exists('PDO')) {
    print_r(PDO::getAvailableDrivers());
} else {
    echo "PDO class NOT found\n";
}

echo "\n=== phpinfo() snippet (pdo) ===\n";
ob_start();
phpinfo(INFO_MODULES);
$info = ob_get_clean();
foreach (explode("\n", $info) as $line) {
    if (stripos($line, 'pdo') !== false || stripos($line, 'pgsql') !== false) {
        echo $line . "\n";
    }
}