<?php
ini_set("error_reporting", "E_ALL & ~E_NOTICE");
date_default_timezone_set("Asia/Taipei");

if ($expire == 0) {
	$expire = ini_get('session.gc_maxlifetime');
} else {
	ini_set('session.gc_maxlifetime', 28800);
}
if (empty($_COOKIE['PHPSESSID'])) {
	@session_set_cookie_params($expire);
	@session_start();
} else {
	@session_start();
	@setcookie('PHPSESSID', session_id(), time() + 28800);
}
//@session_start();

// 1. 資料庫連線設定
if (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168')) {
	$host     = '127.0.0.1';
	$db       = 'demo_template';  // 請替換為您的資料庫名稱
	$user     = 'root';          // 請替換為您的資料庫帳號
	$password = 'root'; // 請替換為您的資料庫密碼
	$charset  = 'utf8mb4';
} else {
	$host     = '127.0.0.1';
	$db       = 'demo_template';  // 請替換為您的資料庫名稱
	$user     = 'root';          // 請替換為您的資料庫帳號
	$password = 'root'; // 請替換為您的資料庫密碼
	$charset  = 'utf8mb4';
}




$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 錯誤拋出例外
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 預設取得關聯陣列
	PDO::ATTR_EMULATE_PREPARES   => false,                  // 停用模擬預處理，提高安全性
];

try {
	$pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
	// 若連線失敗，回傳錯誤的 JSON 格式
	echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
	exit;
}


$now_time = date("Y-m-d H:i:s"); //顯示當前時

?>