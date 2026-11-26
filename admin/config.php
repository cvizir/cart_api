<?php
/**
 * 網站根目錄的設定載入器（放在網站根目錄，與 include/、class/ 同一層）
 *
 * 一律用 dirname(__FILE__) 組出絕對路徑；原本寫成 "include/config.inc.php" 這種相對路徑，
 * 是否找得到檔案要看「從哪個資料夾執行」，從 admin/ 底下呼叫時要靠 include_path 剛好有 "." 才會成功。
 */
$base = dirname(__FILE__);

// 載入設定（資料庫連線 PDO）與共用函式、分頁類別
require_once($base . "/include/config.inc.php");
require_once($base . "/include/function.php");
require_once($base . "/include/DBClass.php");

/*
 * 舊版類別（class/DB.php、DomainObject.php、BillboardCat.php…）。
 * 它們是用已經在 PHP 7 移除的 mysql_* 函式寫的，載入後一呼叫就會出現致命錯誤，
 * 而且可能和上面 PDO 版的 startDB() 等函式重複定義，所以預設不再載入。
 * 後台所有模組都已改成直接使用 $pdo，不需要它們。
 * 若前台頁面還需要，請先把類別改寫成 PDO，再在載入 config.php 之前加上：
 *     define('LOAD_LEGACY_CLASSES', true);
 */
if (defined('LOAD_LEGACY_CLASSES') && LOAD_LEGACY_CLASSES) {
  require_once($base . "/class/DB.php");
  require_once($base . "/class/DomainObject.php");
  require_once($base . "/class/DomainObjectList.php");

  //require_once($base . "/class/Category.php");
  //require_once($base . "/class/CategoryTwo.php");
  //require_once($base . "/class/CategoryThree.php");
  require_once($base . "/class/BillboardCat.php");
  require_once($base . "/class/Billboard.php");
  require_once($base . "/class/QACat.php");
}
