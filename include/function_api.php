<?php
/**
 * 檢查會員資格 API (改用 PDO 預處理驗證帳密)
 * 
 * @param string $email 會員信箱
 * @param string $mem_id 會員編號
 * @param string $psw 密碼
 * @param string $mphone 手機號碼
 * @return bool 是否驗證成功
 */
function api_mem_check($email, $mem_id, $psw, $mphone) {
  global $pdo;
  $sql = "SELECT COUNT(*) FROM `member` WHERE `email` = ? AND `mem_id` = ? AND `psw` = ? AND `mphone` = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$email, $mem_id, $psw, $mphone]);
  return ($stmt->fetchColumn() == 1);
}