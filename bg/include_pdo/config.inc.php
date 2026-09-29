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
  define("DB_CHARSET", "utf8mb4");
} else {
  define("DB_HOST", "localhost");
  define("DB_NAME", "lerevepa_demo");
  define("DB_USER", "lerevepa_cvizir");
  define("DB_PASSWORD", "1q2w3e4r");
  define("WEB_ROOT", "http://www.lereveparis.com/lereve/"); //http://neocity.com.tw/
  define("WEB_ADMIN", WEB_ROOT . 'admin/'); //http://neocity.com.tw/
  define("DB_CHARSET", "utf8mb4");
}

function startDB()
{
  // 資料庫設定
  $host = '127.0.0.1';
  $db = 'hongdao';
  $user = 'root';
  $pass = 'root';
  $charset = 'utf8';

  // Data Source Name (DSN)
  $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;

  // PDO 參數設定
  $options = [
    // 發生錯誤時拋出例外 (Exception)，方便我們用 try-catch 捕捉
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // 預設將取回的資料轉為關聯式陣列 (Associative Array)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // 關閉模擬預處理，強制使用真實的預處理，安全性更高
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    // 建立 PDO 實體
    // 回傳建立好的 PDO 連線實體
    return new PDO($dsn, $user, $pass, $options);
    // $pdo = new PDO($dsn, $user, $pass, $options);
    echo "PDO 資料庫連線成功！";

  } catch (\PDOException $e) {
    // 實際產品上線時，請勿直接 echo 出錯誤訊息，應寫入 log
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
  }

}

// 呼叫連線函式，取得 $pdo 物件
$pdo = startDB();

// 網站實體路徑	
define("SITE_ROOT", dirname(dirname(__FILE__)));
define("ADMIN_TITLE", "CART 後台管理");
define("SITE_TITLE", "CART");
//啟動資料庫
