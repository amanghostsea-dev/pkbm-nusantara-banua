<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>PKBM PHP-FPM Diagnostics</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Vendor Autoload Exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "</p>";

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    echo "<p>Autoload: LOADED!</p>";
}

try {
    $db = new PDO('sqlite:/var/www/laravel/database/database.sqlite');
    $res = $db->query("SELECT count(*) as count FROM users")->fetch(PDO::FETCH_ASSOC);
    echo "<p>Database Connection: OK! Total Users: " . $res['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>DB ERROR: " . $e->getMessage() . "</p>";
}

try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "<p>Bootstrap Application: OK!</p>";
    $request = Illuminate\Http\Request::capture();
    echo "<p>Request Capture: OK!</p>";
} catch (Throwable $e) {
    echo "<p style='color:red;'>Bootstrap ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "</p>";
}
