<?php
// 設定預設編碼與除錯訊息
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 依據主機網址動態切換本機測試環境或線上正式環境
if (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168')) {
  define("DB_HOST", "localhost");
  define("DB_NAME", "demo_template");
  define("DB_USER", "root");
  define("DB_PASSWORD", "root");
  define("WEB_ROOT", "http://localhost/php5/cart_api/");
  define("WEB_ADMIN", WEB_ROOT . 'admin/');
} else {
  define("DB_HOST", "localhost");
  define("DB_NAME", "lerevepa_demo");
  define("DB_USER", "lerevepa_cvizir");
  define("DB_PASSWORD", "1q2w3e4r");
  define("WEB_ROOT", "http://www.lereveparis.com/lereve/");
  define("WEB_ADMIN", WEB_ROOT . 'admin/');
}

// 網站實體路徑與後台設定
define("SITE_ROOT", dirname(dirname(__FILE__)));
define("ADMIN_TITLE", "CART 後台管理");
define("SITE_TITLE", "CART");

// 全域 PDO 連線實例
$pdo = null;

/**
 * 初始化並取得 PDO 連線物件
 * 
 * @return PDO
 */
function startDB()
{
  global $pdo;
  
  // 若連線已建立過，直接回傳以節省建立連線資源
  if ($pdo !== null) {
    return $pdo;
  }

  // 設定 DSN (Data Source Name)，指定編碼為 utf8mb4 避免中文字符遺失或亂碼
  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
  
  // PDO 核心設定 (PHP 5.4 支援短陣列語法 [])
  $options = [
    // 發生錯誤時拋出 PDOException，利於捕捉並阻止後續危險操作
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // 預設抓取模式為關聯陣列 (替代原本 mysql_fetch_assoc)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // 關閉模擬預處理，強制使用 MySQL 真正的預處理引擎，防止 SQL 注入
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    return $pdo;
  } catch (PDOException $e) {
    // 捕捉連線失敗例外，防止洩漏資料庫帳密
    die("資料庫連線失敗: " . $e->getMessage());
  }
}

// 載入時立即建立連線
startDB();

if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
