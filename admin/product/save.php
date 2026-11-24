<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
// 接收基本變數
$cms_mode = isset($_REQUEST['cms_mode']) ? $_REQUEST['cms_mode'] : '';
$no = isset($_REQUEST['no']) ? trim($_REQUEST['no']) : '';
$product_code = isset($_REQUEST['product_code']) ? trim($_REQUEST['product_code']) : '';
$page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
$add_pic = isset($_FILES['add_pic']) ?$_FILES['add_pic'] : null;
$del_pic = isset($_POST['del_pic']) ?$_POST['del_pic'] : [];

// 設定圖片上傳實體路徑
$filePath = dirname(dirname(dirname(__FILE__))) . "/images/upload/product/";
$sh_pdcat_param = isset($_REQUEST['pdcat_code']) && is_array($_REQUEST['pdcat_code']) ? implode(',', $_REQUEST['pdcat_code']) : '';
$sh_post = 'sh_pdcat_code=' . urlencode($sh_pdcat_param);

$uptime = date("Y-m-d H:i:s");
$db_name = 'product'; // 資料表名稱
$web_url = "list.php?{$sh_post}"; // 返回列表頁的網址
$trace = 0; // 是否要追蹤 SQL 執行狀態，0:不追蹤, 1:追蹤

// 組合要新增或更新的欄位資料陣列 (不需呼叫 mysql_real_escape_string)
$sql_data = [
  'uptime'            => $uptime, // 更新時間
  'product_code'      => isset($_REQUEST['product_code']) ?$_REQUEST['product_code'] : '', // 商品編號
  'name'              => isset($_REQUEST['name']) ?$_REQUEST['name'] : '', // 商品名稱
  'pd_stock'          => (isset($_REQUEST['pd_stock']) && is_numeric($_REQUEST['pd_stock'])) ? (int)$_REQUEST['pd_stock'] : 0, // 庫存量
  'pdcat_code'        => isset($_REQUEST['pdcat_code']) ? $_REQUEST['pdcat_code'] : '', // 商品類別
  'o_price'           => (isset($_REQUEST['o_price']) && is_numeric($_REQUEST['o_price'])) ? (int)$_REQUEST['o_price'] : 0, // 原價
  'price'             => (isset($_REQUEST['price']) && is_numeric($_REQUEST['price'])) ? (int)$_REQUEST['price'] : 0, // 售價
  'hot_item'          => isset($_REQUEST['hot_item']) ?$_REQUEST['hot_item'] : 0, // 熱門商品 
  'you_tube_code'     => isset($_REQUEST['you_tube_code']) ? $_REQUEST['you_tube_code'] : '', // YouTube 影片代碼
  'pd_info'           => isset($_REQUEST['pd_info']) ?$_REQUEST['pd_info'] : '', // 商品介紹
  'photo'             => isset($_REQUEST['photo']) ? $_REQUEST['photo'] : '', // 商品圖片
  'ps'                => isset($_REQUEST['ps']) ? $_REQUEST['ps'] : '', // 備註
  'release_date'      => isset($_REQUEST['release_date']) ? trim($_REQUEST['release_date']) : date("Y-m-d"), // 上架日期
  'm_sort'            => (isset($_REQUEST['m_sort']) && is_numeric($_REQUEST['m_sort'])) ? (int)$_REQUEST['m_sort'] : 1,  // 排序
  'ishow'             => (isset($_REQUEST['ishow']) && is_numeric($_REQUEST['ishow'])) ? (int)$_REQUEST['ishow'] : 0  // 是否顯示
];


$insert_id = '';
$msg = '';

// 依照動作模式分支
switch ($cms_mode) {
  case 'add':
    $sql_data['product_code'] = createCode($db_name, 'product_code', 11);
    $sql_data['photo'] = 'product_' .$_REQUEST['product_num'] . '_1.jpg,,,,,,,,,';
    insertArray($db_name,$sql_data, $trace);
    $insert_id = $sql_data['product_code'];
    $msg = "新增成功!";
    break;

  case 're_add':
    $msg = "此商品已經新增過了!!";
    break;

  case 'edit':
    // 依 primary key 更新
    $where_str = " WHERE `no` = " . $pdo->quote($no);
    updataArray($db_name,$where_str, $sql_data,$trace);
    $insert_id =$product_code;
    $msg = "更新成功!!";
    break;

  case 'del':
    // 透過預處理語句刪除資料
    $stmt_del =$pdo->prepare("DELETE FROM `product` WHERE `no` = ?");
    $stmt_del->execute([$no]);
    $msg = "刪除成功!!";

    // 刪除實體檔案
    for ($I = 0; $I < 8; $I++) {
      @unlink($filePath . 'product_' . $product_code . '_' .( $I + 1 ) . '.jpg');
      @unlink($filePath . 'product_' . $product_code . '_' .( $I + 1 ) . '.png');
    }
    break;
}

// 圖片上傳與刪除勾選處置
if (($cms_mode == 'add' || $cms_mode == 'edit') && !empty($insert_id)) {
  $xid = 'product_' . $insert_id . '_';
  $photo_array = isset($_REQUEST['photo']) ? explode(",", $_REQUEST['photo']) : array_fill(0, 8, '');

  for ($I = 0; $I < 8; $I++) {
    $pic_num = $I + 1;
    $ext = (isset($add_pic['type'][$I]) && $add_pic['type'][$I] == 'image/png') ? '.png' : '.jpg';
    $pic_name =$xid . $pic_num .$ext;
    $small = [["$pic_name", "w", "1960", "1024"]];

    // 若使用者勾選刪除此圖
    if (isset($del_pic[$I]) && $del_pic[$I] == "1") {
      $photo_array[$I] = '';
      @unlink($filePath . $xid .$pic_num . '.jpg');
      @unlink($filePath . $xid .$pic_num . '.png');
    } elseif (!empty($add_pic['tmp_name'][$I])) {
      // 處理新上傳圖片
      uploadedPhotoPathR($add_pic["tmp_name"][$I],$filePath, $add_pic["type"][$I], 0, $small);
      $photo_array[$I] =$pic_name;
    }
  }
  
  // 更新資料庫中的 photo 欄位
  $photo_text = implode(",", $photo_array);
  $stmt_photo =$pdo->prepare("UPDATE `{$db_name}` SET `photo` = ? WHERE `product_code` = ?");
  $stmt_photo->execute([$photo_text,$insert_id]);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
  alert("<?= addslashes($msg) ?>");
  window.location.href = "<?= $web_url ?>";
</script> 
</head>
<body></body>
</html>