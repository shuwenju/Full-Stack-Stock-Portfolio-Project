<?php
//Create database "portfolio_manager" in PHPMyAdmin first
define('DB_SERVER', "localhost:3307");
define('DB_USER', "root");
define('DB_PASS', "");
define('DB_NAME', "portfolio_manager");

$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);


if (mysqli_connect_error()) {
    echo "Connection Fail" . mysqli_connect_error();
}
?>