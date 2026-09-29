<?php
// 1. CORS 設定 (根據開發環境調整，* 代表允許所有來源)
// 1. 允許所有網域存取
header("Access-Control-Allow-Origin: *");

// 2. 允許的請求方法（必須包含 OPTIONS）
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// 3. 允許的 Header 類型（Axios 發送 JSON 必備 Content-Type）
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// 2. 處理 Preflight (預檢請求)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}

// 3. 設定回傳 JSON
header('Content-Type: application/json; charset=utf-8');

// --- 重點開始 ---
// 因為 Axios 傳送的是 JSON，$_POST 是抓不到內容的。
// 我們必須從 php://input 取得原始字串
$json_raw = file_get_contents('php://input');

// 將 JSON 字串轉成 PHP 關聯陣列
$data = json_decode($json_raw, true);
// --- 重點結束 ---


// 檢查資料是否成功接收
if (count($data) > 0) {

  // ==========================================
  // 2. 接收使用者輸入的帳號密碼 (通常來自表單 POST)
  // ==========================================
  if ($data['account'] != '' && $data['password'] != null) {
    $account = $data['account'];
  } else {
    $account = '未知用戶';
  }
  if ($data['password'] != '' && $data['password'] != null) {
    $password = $data['password'];
  } else {
    $password = '無密碼';
  }

  // 簡單檢查是否有輸入
  if (empty($account) || empty($password)) {
    die("請輸入帳號與密碼。");
  }

  // ==========================================
  // 3. 執行 SQL 查詢 (使用預處理語句)
  // ==========================================
  // 使用 LIMIT 1 提高效能，因為帳號通常是唯一的，找到一筆就可以停了
  $sql = "SELECT id, account FROM member WHERE account = :account AND password = :password LIMIT 1";

  $stmt = $pdo->prepare($sql);

  // 綁定參數並執行 (PDO 會自動幫你過濾特殊字元，防止 SQL 注入)
  $stmt->execute([
    ':account'  => $account,
    ':password' => $password
  ]);

  // 嘗試抓取這筆資料
  $member = $stmt->fetch();

  // ==========================================
  // 4. 判斷登入結果
  // ==========================================
  // 如果 $member 有資料，代表帳號密碼完全符合
  if ($member) {
    // 登入成功！將會員資訊存入 Session
    $_SESSION['is_logged_in'] = true;
    $_SESSION['member_id']    = $member['id'];
    $_SESSION['account']      = $member['account'];
    $_SESSION['member_info']  = $member;
    echo "登入成功！歡迎回來，" . htmlspecialchars($member['account']);

    echo json_encode([
      'success' => true,
      'rtnCode' => 0,
      'rtnMsg' => "你好 {$account}，登入成功！",
      'data' => $member
    ]);

    // 通常登入成功後會跳轉到首頁或後台
    // header("Location: dashboard.php");
    // exit;

  } else {
    // 登入失敗 (找不到符合的資料)
    // 💡 小提示：基於資安考量，請統一顯示「帳號或密碼錯誤」，不要明確告訴使用者是哪一個錯
    echo json_encode([
      'success' => false,
      'rtnCode' => 0,
      'rtnMsg' => "帳號或密碼錯誤",
      'data' => ''
    ]);
  }

} else {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'rtnCode' => 0,
    'rtnMsg' => "無效的資料格式",
    'data' => ''
  ]);
}
