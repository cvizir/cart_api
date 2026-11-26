<?php
/**
 * 後台登入相關的共用函式（login.php、admin.php、session.php、index.php、logout.php 都會用到）
 * 放在 admin/ 資料夾，與 session.php 同一層。
 */

/** 啟動 session：Cookie 加上 HttpOnly（JavaScript 讀不到），HTTPS 網站時加上 Secure */
function adminSessionStart() {
  if (session_status() === PHP_SESSION_NONE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(0, '/', '', $secure, true);
    session_start();
  }
}

/** 取得（或建立）本次 session 的 CSRF 權杖 */
function adminCsrfToken() {
  adminSessionStart();
  if (empty($_SESSION['csrf_token'])) {
    if (function_exists('random_bytes')) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
      $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    } else {
      $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
    }
  }
  return $_SESSION['csrf_token'];
}

/** 檢查表單送來的 CSRF 權杖 */
function adminCsrfCheck($token) {
  adminSessionStart();
  return !empty($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * admin.admin_psw 欄位是否放得下雜湊後的密碼（bcrypt 需要 60 個字元以上）。
 * 欄位太短時如果硬寫入，雜湊會被截斷，之後就再也登入不了，所以必須先檢查。
 */
function adminPswColumnOk($pdo) {
  static $result = null;
  if ($result !== null) { return $result; }
  try {
    $stmt = $pdo->prepare("SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin' AND COLUMN_NAME = 'admin_psw'");
    $stmt->execute();
    $len = $stmt->fetchColumn();
    $result = ($len !== false && $len !== null && (int)$len >= 60);
  } catch (PDOException $e) {
    $result = false;
  }
  return $result;
}

/** 驗證密碼：同時支援已雜湊的密碼，以及舊資料裡的明文密碼 */
function adminVerifyPassword($input, $stored) {
  $stored = (string)$stored;
  if (strlen($stored) >= 60 && substr($stored, 0, 4) === '$2y$' && function_exists('password_verify')) {
    return password_verify($input, $stored);
  }
  return hash_equals($stored, (string)$input);
}
