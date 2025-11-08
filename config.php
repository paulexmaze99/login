
<?php
define('DB_SERVER', '127.0.0.1:3306');

define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user_form');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); // Use constants
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// For debugging only; remove in production
//echo 'Connected successfully';
?>