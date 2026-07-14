<?php
ini_set('default_charset','utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168')) {
  define("DB_HOST", "localhost");
  define("DB_NAME", "demo_template");
  define("DB_USER", "root");
  define("DB_PASSWORD", "root");
  define("WEB_ROOT", "http://localhost/php5/cart_api/"); // 網站路徑	
  define("WEB_ADMIN", WEB_ROOT . 'admin/'); //http://neocity.com.tw/
} else {
  define("DB_HOST", "localhost");
  define("DB_NAME", "lerevepa_demo");
  define("DB_USER", "lerevepa_cvizir");
  define("DB_PASSWORD", "1q2w3e4r");
  //define("DB_PASSWORD","peter919");
  define("WEB_ROOT", "http://www.lereveparis.com/lereve/"); //http://neocity.com.tw/
  define("WEB_ADMIN", WEB_ROOT . 'admin/'); //http://neocity.com.tw/
}

function startDB()
{
  // mysql_pconnect(DB_HOST, DB_USER, DB_PASSWORD);
  // mysql_select_db(DB_NAME);
  // mysql_query("set names utf8");

  $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // 檢查連線
  if (!$conn) {
    die("連線失敗: " . mysqli_connect_error());
  }

  // 設定字元集 (解決之前看到的 utf8 亂碼或錯誤問題)
  mysqli_set_charset($conn, "utf8mb4");

  echo "資料庫連線成功！";

}

// 網站實體路徑	
define("SITE_ROOT", dirname(dirname(__FILE__)));
define("ADMIN_TITLE", "CART 後台管理");
define("SITE_TITLE", "CART");
//啟動資料庫
