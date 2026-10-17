<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>PKBM PHP-FPM Diagnostics</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO SQLite: " . (extension_loaded('pdo_sqlite') ? 'LOADED' : 'MISSING') . "</p>";
echo "<p>SQLite3: " . (extension_loaded('sqlite3') ? 'LOADED' : 'MISSING') . "</p>";
echo "<p>DB File Exists: " . (file_exists('/var/www/laravel/database/database.sqlite') ? 'YES' : 'NO') . "</p>";
echo "<p>DB File Writable: " . (is_writable('/var/www/laravel/database/database.sqlite') ? 'YES' : 'NO') . "</p>";
echo "<p>Storage Writable: " . (is_writable('/var/www/laravel/storage') ? 'YES' : 'NO') . "</p>";

try {
    $db = new PDO('sqlite:/var/www/laravel/database/database.sqlite');
    $res = $db->query("SELECT count(*) as count FROM users")->fetch(PDO::FETCH_ASSOC);
    echo "<p>Database Connection: OK! Total Users: " . $res['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>DB ERROR: " . $e->getMessage() . "</p>";
}

try {
    require __DIR__ . '/../bootstrap/app.php';
    echo "<p>Bootstrap: OK!</p>";
} catch (Throwable $e) {
    echo "<p style='color:red;'>Bootstrap ERROR: " . $e->getMessage() . "</p>";
}
