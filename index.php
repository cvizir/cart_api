<?php
require 'AltoRouter.php';
$router = new AltoRouter();
// $router->map( 'GET', 'menu', function() {
//     require __DIR__ . '/menu.php';
// });
$router->map('GET', '/', function() {
    echo '<h1>歡迎來到首頁</h1>';
});
?>