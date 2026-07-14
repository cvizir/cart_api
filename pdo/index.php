<?php
// 強制開啟錯誤顯示 (開發測試用)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('default_charset', 'UTF-8');
// 資料庫設定
$host = '127.0.0.1';
$db   = 'hongdao';
$user = 'root';
$pass = 'root';
$charset = 'utf8';

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO 參數設定
$options = [
  // 發生錯誤時拋出例外 (Exception)，方便我們用 try-catch 捕捉
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  // 預設將取回的資料轉為關聯式陣列 (Associative Array)
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  // 關閉模擬預處理，強制使用真實的預處理，安全性更高
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  // 建立 PDO 實體
  $pdo = new PDO($dsn, $user, $pass, $options);
  // echo "資料庫連線成功！";

} catch (\PDOException $e) {
  // 實際產品上線時，請勿直接 echo 出錯誤訊息，應寫入 log
  throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


// R - Read(讀取資料)
// --- 讀取單筆資料 ---
$search_email = "cvizir@yahoo.com.tw"; // 假設這是你要搜尋的使用者信箱
// $search_email = "admin' --"; // 惡意輸入，試圖進行 SQL 注入攻擊，如果沒有使用預處理，這會導致 SQL 語句被破壞

$sql = "SELECT * FROM member WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $search_email]);

// fetch() 用來抓取單一筆結果
$user = $stmt->fetch();
if ($user) {
  echo "找到使用者: " . $user['name'] . "<br>";
} else {
  echo "找不到該使用者。<br>";
}


// --- 讀取多筆資料 ---
$sql = "SELECT * FROM member LIMIT 5"; // SQL 查詢語句，這裡是選取 member 表中的前 5 筆資料
$stmt = $pdo->prepare($sql);
$stmt->execute();

// fetchAll() 用來抓取所有符合條件的結果，回傳一個二維陣列
$users = $stmt->fetchAll();
foreach ($users as $row) {
  echo "列表 - 使用者: " . $row['name'] . ", 信箱: " . $row['email'] . "<br>";
}


// U - Update (更新資料)
// 假設這是前端傳來的資料 (可能是 $_POST)，使用者這次只填了 2 個想更新的欄位

$search_email = "cvizir@yahoo.com.tw";
// $data_to_update = [
//   'regtime' => '2012-11-11 04:12:46',
//   'nickname' => 'nickname_20260620_01_05',
//   'name' => 'name_20260620_01_05',
//   'email' => $search_email,
//   'psw' => 'psw_20260620_01_05',
//   'region' => '2012-11-11 04:12:46',
//   'sex' => '1',
//   'bday' => '1979-03-24',
//   'tel' => '0223064005',
//   'mphone' => '0952620116',
//   'city' => '臺北市',
//   'area' => '萬華區',
//   'addr' => '台北市大埔街7巷2號之1',
//   'zipcode' => '108',
//   'career' => 'career_20260620_01_05',
//   'income' => 'income_20260620_01_05',
//   'vip_mode' => '1',
//   'last_login' => '2012-11-11 04:12:46',
//   'esend' => '1',
//   'authcode' => '123456789'
// ];

$data_to_update = [
  'regtime' => '2012-11-11 04:12:46',
  'nickname' => 'sean',
  'name' => 'wang2',
  'email' => $search_email
];



// 如果沒有要更新的資料，就直接中斷
if (empty($data_to_update)) {
  die("沒有需要更新的欄位");
}

$set_parts = [];
$params = [];

// 1. 動態組裝 SET 語法與 PDO 參數
foreach ($data_to_update as $column => $value) {
  // 拼湊字串： "email = :email"
  $set_parts[] = "{$column} = :{$column}";
  // 準備 execute 要用的陣列： [':email' => 'dynamic@example.com']
  $params[":{$column}"] = $value;
}

// 將陣列結合成字串： "email = :email, phone = :phone"
$set_string = implode(", ", $set_parts);

// 2. 組合最終的 SQL 語句
$sql = "UPDATE member SET {$set_string} WHERE email = :search_email";
$stmt = $pdo->prepare($sql);

// 3. 把 id 也塞進參數陣列中，然後執行
$params[':search_email'] = $search_email;
$stmt->execute($params);

echo "動態更新成功！";


// C - Create(新增資料)

// 假設這是前端傳來的資料，這次只打算新增 2 個欄位
$data_to_update = [
  'regtime' => '2026-06-20',
  'nickname' => 'nickname_20260620_01_08',
  'name' => 'name_20260620_01_08',
  'email' => 'email_20260620_01_08',
  'psw' => 'psw_20260620_01_08',
  'region' => 'region_20260620_01_08',
  'sex' => '1',
  'bday' => '1979-06-20',
  'tel' => 'tel_20260620_01_08',
  'mphone' => 'mphone_20260620_01_08',
  'city' => 'city_20260620_01_08',
  'area' => 'area_20260620_01_08',
  'addr' => 'addr_20260620_01_08',
  'zipcode' => '108',
  'career' => 'career_20260620_01_08',
  'income' => 'income_20260620_01_08',
  'vip_mode' => '1',
  'last_login' => '1976-03-24 00:00:00',
  'esend' => '1',
  'authcode' => 'authcode_20260620_01_08'
];

if (empty($data_to_update)) {
  die("沒有需要新增的資料");
}

// 1. 取出所有欄位名稱，組合成字串： "name, email"
$columns = implode(', ', array_keys($data_to_update));

// 2. 製作佔位符陣列，組合成字串： ":name, :email"
$placeholders = [];
foreach ($data_to_update as $column => $value) {
  $placeholders[] = ':' . $column;
}
$placeholders_string = implode(', ', $placeholders);

// 3. 組合最終的 SQL 語句
$sql = "INSERT INTO member ({$columns}) VALUES ({$placeholders_string})";
$stmt = $pdo->prepare($sql);

// 4. 準備 execute 要用的參數陣列
$params = [];
foreach ($data_to_update as $column => $value) {
  $params[":{$column}"] = $value;
}

// 執行寫入
$stmt->execute($params);

echo "動態新增成功！新資料的 ID 是: " . $pdo->lastInsertId();

// D - Delete(刪除資料)

// $search_email = ""; // 假設這是你要刪除的使用者信箱
// $sql = "DELETE FROM users WHERE email = :search_email";
// $stmt = $pdo->prepare($sql);
// $stmt->execute([':search_email' => $search_email]);

// echo "成功刪除了 " . $stmt->rowCount() . " 筆資料。<br>";


?>