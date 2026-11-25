<?php
// Prevent duplicate inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Auto-detect Local Environment (XAMPP) vs Online Hosting Environment
    if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0)) {
        // 💻 Local XAMPP Database Credentials
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', 'Glam26@#123');
        define('DB_NAME', 'glamhandy');
    } else {
        // 🌐 Online Hosting Database Credentials (InfinityFree)
        define('DB_HOST', 'sql113.infinityfree.com');
        define('DB_USER', 'if0_42922286');
        define('DB_PASS', 'KhsrSRDLke6');
        define('DB_NAME', 'if0_42922286_glamhandy');
    }

    // Helper function to establish connection
    function getDBConnection() {
        $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$connection) {
            die("Database Connection Error: " . mysqli_connect_error());
        }
        return $connection;
    }

    // Default global connection object
    $con = getDBConnection();
}
?>
