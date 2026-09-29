<?php
/**
 * 取得排序最大值
 */
function sortMax($cms_mode, $db_name, $sortmax_where, $field_name = 'no', $trace = 0) {
  global $pdo;
  $sql = "SELECT COUNT(1) AS total_num FROM `{$db_name}` {$sortmax_where}";
  $stmt = $pdo->query($sql);
  $row = $stmt->fetch(PDO::FETCH_NUM);
  
  if ($trace == '1') {
    echo $cms_mode . '<br>';
    echo $sql . '<br>';
  }
  
  if (empty($row[0])) {
    return 1;
  } else {
    return ($cms_mode == 'edit') ? (int)$row[0] : ((int)$row[0] + 1);
  }
}

/**
 * 新增/修改/刪除時的 sort 調整
 */
function changSortSet($cms_mode, $db_name, $sort_sql_where, $field_name = 'm_sort', $sort_new_value, $sort_old_value, $trace = 0) {
  global $pdo;
  $sql_sort = "";
  if ($sort_new_value != $sort_old_value) {
    if ($sort_old_value == '' || $sort_old_value == '0') {
      $sql_sort = "UPDATE `{$db_name}` SET `{$field_name}` = `{$field_name}` + 1 WHERE `{$field_name}` >= ? {$sort_sql_where}";
      $stmt = $pdo->prepare($sql_sort);
      $stmt->execute([$sort_new_value]);
    } else {
      if ($sort_new_value < $sort_old_value) {
        $sql_sort = "UPDATE `{$db_name}` SET `{$field_name}` = `{$field_name}` + 1 WHERE `{$field_name}` >= ? AND `{$field_name}` < ? {$sort_sql_where}";
        $stmt = $pdo->prepare($sql_sort);
        $stmt->execute([$sort_new_value, $sort_old_value]);
      } else {
        $sql_sort = "UPDATE `{$db_name}` SET `{$field_name}` = `{$field_name}` - 1 WHERE `{$field_name}` <= ? AND `{$field_name}` > ? {$sort_sql_where}";
        $stmt = $pdo->prepare($sql_sort);
        $stmt->execute([$sort_new_value, $sort_old_value]);
      }
    }
  }
  if ($cms_mode == 'del' && $sort_new_value != '' && $sort_new_value != '0') {
    $sql_sort = "UPDATE `{$db_name}` SET `{$field_name}` = `{$field_name}` - 1 WHERE `{$field_name}` > ? {$sort_sql_where}";
    $stmt = $pdo->prepare($sql_sort);
    $stmt->execute([$sort_new_value]);
  }
  if ($trace == 1) { echo $sql_sort . '<br>'; }
}

/**
 * 檢查字串是否在以逗號分隔的字串串列中
 */
function strExist($o_str, $check_str) {
  return (strpos(',' . $o_str . ',', ',' . $check_str . ',') !== false);
}

function strExist2($o_str, $check_str) {
  return (strpos($o_str, $check_str) !== false);
}

/**
 * 以關聯陣列方式更新資料表 (自動防 SQL 注入)
 */
function updataArray($db_name, $where_str, $sql_data, $trace = 0) {
  global $pdo;
  if (empty($where_str) || empty($sql_data)) {
    return false;
  }
  
  $fields = [];
  $values = [];
  // 透過 foreach 取得鍵值，替代 PHP 5.4+ 不建議使用的 each()
  foreach ($sql_data as $key => $value) {
    $fields[] = "`{$key}` = :{$key}";
    $values[":{$key}"] = $value;
  }
  
  $sql = "UPDATE `{$db_name}` SET " . implode(", ", $fields) . " " . $where_str;
  if ($trace == 1) { echo '<br>updataArraySql=' . $sql . "<br>"; }
  print_r($values); // Debug: Print the values array to check the parameters
  $stmt = $pdo->prepare($sql);
  return $stmt->execute($values);
}

/**
 * 以關聯陣列方式新增資料 (自動防 SQL 注入)
 */
function insertArray($db_name, $sql_data, $trace = 0) {
  global $pdo;
  if (empty($sql_data)) return false;

  $fields = [];
  $placeholders = [];
  $values = [];
  foreach ($sql_data as $key => $value) {
    $fields[] = "`{$key}`";
    $placeholders[] = ":{$key}";
    $values[":{$key}"] = $value;
  }
  
  $sql = "INSERT INTO `{$db_name}` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
  if ($trace == 1) { echo '<br>insertArraySql=' . $sql . "<br>"; }
  
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);
  return $pdo->lastInsertId(); // 取得最後新增的自增 ID
}

/**
 * 依據 ID 陣列批次刪除 (以 PDO 佔位符防止注入)
 */
function deleteArray($db_name, $del_no, $id_no = 'no', $trace = 0) {
  global $pdo;
  // 移除空值陣列元素
  $del_no = array_values(array_filter($del_no, 'strlen'));
  if (empty($del_no)) return;

  // 動態建立對應數量的 ? 佔位符
  $placeholders = implode(',', array_fill(0, count($del_no), '?'));
  $sql = "DELETE FROM `{$db_name}` WHERE `{$id_no}` IN ({$placeholders})";
  
  if ($trace == "1") { echo '<br>deleteArraySql=' . $sql . "<br>"; }
  $stmt = $pdo->prepare($sql);
  $stmt->execute($del_no);
}

/**
 * 依據條件字串刪除資料
 */
function deleteDb($db_name, $where_str, $sql_data = [], $trace = 0) {
  global $pdo;
  $sql = "DELETE FROM `{$db_name}` {$where_str}";
  if ($trace == "1") { echo '<br>deleteDb=' . $sql . "<br>"; }
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
}

/**
 * 輸出 JavaScript Alert 提示訊息並轉址
 */
function alertHref($msg, $href) {
  $return_code = '<script type="text/javascript">';
  if ($msg != "") {
    $return_code .= 'alert("' . addslashes($msg) . '");';
  }
  if ($href != "") {
    $return_code .= 'window.location.href="' . $href . '";';
  } else {
    $return_code .= 'history.go(-1);';
  }
  $return_code .= '</script>';
  echo $return_code;
}

/**
 * 產生英數亂數字串
 */
function randomStr($random) {
  $randoma = "";
  for ($i = 1; $i <= $random; $i++) {
    $c = rand(1, 3);
    if ($c == 1) { $b = chr(rand(97, 122)); }
    if ($c == 2) { $b = chr(rand(65, 90)); }
    if ($c == 3) { $b = rand(0, 9); }
    $randoma .= $b;
  }
  return $randoma;
}

/**
 * 檢查並產生唯一欄位代碼
 */
function createCode($tb_name, $tb_filed, $code_num) {
  global $pdo;
  $x = 1;
  while ($x > 0) {
    $code_str = randomStr($code_num);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$tb_name}` WHERE `{$tb_filed}` = ?");
    $stmt->execute([$code_str]);
    $x = $stmt->fetchColumn();
  }
  return $code_str;
}

function randomStr2($random) {
  $randoma = "";
  for ($i = 1; $i <= $random; $i++) {
    $c = rand(1, 2);
    if ($c == 2) { $b = chr(rand(65, 90)); }
    if ($c == 1) { $b = rand(0, 9); }
    $randoma .= $b;
  }
  return $randoma;
}

function createOrderCode($tb_name, $tb_filed, $code_num) {
  global $pdo;
  $x = 1;
  while ($x > 0) {
    $code_str = randomStr2($code_num);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$tb_name}` WHERE `{$tb_filed}` = ?");
    $stmt->execute([$code_str]);
    $x = $stmt->fetchColumn();
  }
  return $code_str;
}

function getIshow($value) {
  return ($value == "1") ? "上架" : "下架";
}

// 移除已廢棄的 ereg_replace，改用安全的 addslashes 與 str_replace
function fixSql($string) {
  return addslashes($string);
}

function fixEnter($string, $chr = "") {
  if ($chr == "") $chr = chr(2);
  return str_replace("\r\n", $chr, $string);
}

function fixAnd($string) {
  return str_replace("&", chr(2), $string);
}

function getDateSelectOption($int_start, $int_end, $int_value, $numfill = 0) {
  $str = "";
  for ($i = $int_start; $i <= $int_end; $i++) {
    $val = ($numfill > 0) ? sprintf("%0" . $numfill . "d", $i) : $i;
    $selected = ($int_value == $val) ? ' selected="selected"' : '';
    $str .= "<option value='{$val}'{$selected}>{$val}</option>\n";
  }
  echo $str;
}

/**
 * 讀取資料表產生 HTML 下拉選單
 */
function getDateSelectOption3($table, $view_value, $view_name, $int_value, $where = "") {
  global $pdo;
  $sql = "SELECT * FROM `{$table}` {$where}";
  $stmt = $pdo->query($sql);
  $str = "";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = ($row[$view_value] == $int_value) ? ' selected="selected"' : '';
    $str .= "<option value='" . htmlspecialchars($row[$view_value]) . "'{$selected}>" . htmlspecialchars($row[$view_name]) . "</option>\n";
  }
  echo $str;
}

function chkSelected($value1, $value2) {
  if ((string)$value1 === (string)$value2) {
    echo 'selected="selected"';
  }
}

function chkChecked($value1, $value2) {
  if ((string)$value1 === (string)$value2) {
    echo 'checked="checked"';
  }
}

// 移除已廢棄的 split()，改為標準 explode()
function chkCheckbox($value, $list) {
  if (empty($list)) return;
  $arr = explode(",", $list);
  if (in_array((string)$value, $arr)) {
    echo 'checked="checked"';
  }
}

function chkIshow($value1) {
  if ($value1 == "1") {
    echo '<span class="iconok">上架</span>';
  } else {
    echo '<span class="iconno">下架</span>';
  }
}

/**
 * 圖片上傳與自動等比例縮圖
 */
function uploadedPhoto($uploadFile, $id, $small = null) {
  $filePath = SITE_ROOT . "/files/";
  if (!is_dir($filePath)) mkdir($filePath, 0777, true);
  uploadedImage($filePath, $uploadFile, $id, $small);
}

function uploadedImage($filePath, $uploadFile, $newName, $small) {
  $fileName = $filePath . (isset($newName) ? $newName : $uploadFile['name']);
  move_uploaded_file($uploadFile['tmp_name'], $fileName);
  chmod($fileName, 0777);
  
  if ($small !== null && file_exists($fileName)) {
    $srcImg = @imagecreatefromjpeg($fileName);
    if (!$srcImg) return;
    
    $src_X = imagesx($srcImg);
    $src_Y = imagesy($srcImg);
    $new_W = $small[0];
    $new_H = $small[1];
    
    if ($src_X > $src_Y) {
      $new_X = $new_W;
      $new_Y = round($src_Y * $new_W / $src_X);
    } else {
      $new_X = round($src_X * $new_H / $src_Y);
      $new_Y = $new_H;
    }
    
    $newImg = imagecreatetruecolor($new_X, $new_Y);
    imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
    imagejpeg($newImg, $fileName, 80);
    imagedestroy($newImg);
    imagedestroy($srcImg);
  }
}

/**
 * 支援旋轉與多格式圖片壓縮上傳
 */
function uploadedPhotoPathR($uploadFile, $filePath, $fileType, $rotate, $small = null) {
  if (!is_dir($filePath)) mkdir($filePath, 0777, true);
  $fileName = $filePath . 'opx.jpg';
  move_uploaded_file($uploadFile, $fileName);
  chmod($fileName, 0666);
  
  if (empty($small)) return;
  $pic_num = count($small) - 1;
  
  for ($I = 0; $I <= $pic_num; $I++) {
    if ($small[$I][1] == "a") {
      copy($fileName, $filePath . $small[$I][0]);
      chmod($filePath . $small[$I][0], 0666);
    } else {
      $rotate_Img = ($fileType == 'image/png') ? @imagecreatefrompng($fileName) : @imagecreatefromjpeg($fileName);
      if (!$rotate_Img) continue;
      
      $rotate = ($rotate == "") ? 360 : (int)$rotate;
      $srcImg = @imagerotate($rotate_Img, $rotate, 0);
      imagedestroy($rotate_Img);
      
      $src_X = imagesx($srcImg);
      $src_Y = imagesy($srcImg);
      $new_W = $small[$I][2];
      $new_H = $small[$I][3];
      
      if ($small[$I][1] == "w") {
        $new_X = ($src_X > $new_W) ? $new_W : $src_X;
        $new_Y = ($src_X > $new_W) ? round($src_Y * $new_W / $src_X) : $src_Y;
        $newImg = imagecreatetruecolor($new_X, $new_Y);
        imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
      } else {
        $newImg = imagecreatetruecolor($new_W, $new_H);
        $white = imagecolorallocate($newImg, 255, 255, 255);
        imagefill($newImg, 0, 0, $white);
        imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_W, $new_H, $src_X, $src_Y);
      }
      
      if ($fileType == 'image/png') {
        imagepng($newImg, $filePath . $small[$I][0], 9);
      } else {
        imagejpeg($newImg, $filePath . $small[$I][0], 100);
      }
      imagedestroy($newImg);
      imagedestroy($srcImg);
    }
  }
}