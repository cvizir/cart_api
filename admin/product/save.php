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

// 商品類別字串串接
$pdcat_code = isset($_REQUEST['pdcat_code']) && is_array($_REQUEST['pdcat_code']) 
  ? ',' . implode(",", $_REQUEST['pdcat_code']) . ',' 
  : ',,';

$uptime = date("Y-m-d H:i:s");
$release_date = isset($_REQUEST['release_date']) ? trim($_REQUEST['release_date']) : date("Y-m-d");

$o_price = 9999;

// 處理推薦商品編號換行切換為逗號
$text = isset($_REQUEST['push_product_code']) ?$_REQUEST['push_product_code'] : '';
$textArr = preg_split('/[\r\n]+/',$text);
$newArr = array_filter(array_map('trim',$textArr));
$push_product_code = implode(",", $newArr);

$db_name = 'product';
$web_url = "list.php?{$sh_post}";
$trace = 1;

// 組合要新增或更新的欄位資料陣列 (不需呼叫 mysql_real_escape_string)
$sql_data = [
  'uptime'            => $uptime,
  'product_code'      => isset($_REQUEST['product_code']) ?$_REQUEST['product_code'] : '',
  'name'              => isset($_REQUEST['name']) ?$_REQUEST['name'] : '',
  'pd_stock'          => (isset($_REQUEST['pd_stock']) && is_numeric($_REQUEST['pd_stock'])) ? (int)$_REQUEST['pd_stock'] : 0,
  'pdcat_code'        => $pdcat_code,
  'o_price'           => (isset($_REQUEST['o_price']) && is_numeric($_REQUEST['o_price'])) ? (int)$_REQUEST['o_price'] : 0,
  'price'             => (isset($_REQUEST['price']) && is_numeric($_REQUEST['price'])) ? (int)$_REQUEST['price'] : 0,
  'hot_item'          => isset($_REQUEST['hot_item']) ?$_REQUEST['hot_item'] : 0,
  'you_tube_code'     => isset($_REQUEST['you_tube_code']) ? $_REQUEST['you_tube_code'] : '',
  'pd_info'           => isset($_REQUEST['pd_info']) ?$_REQUEST['pd_info'] : '',
  'photo'             => isset($_REQUEST['photo']) ? $_REQUEST['photo'] : '',
  'ps'                => isset($_REQUEST['ps']) ? $_REQUEST['ps'] : '',
  'release_date'      => $release_date,
  'm_sort'            => (isset($_REQUEST['m_sort']) && is_numeric($_REQUEST['m_sort'])) ? (int)$_REQUEST['m_sort'] : 1,
  'ishow'             => (isset($_REQUEST['ishow']) && is_numeric($_REQUEST['ishow'])) ? (int)$_REQUEST['ishow'] : 0
];


$insert_id = '';
$msg = '';

// 依照動作模式分支
switch ($cms_mode) {
  case 'add':
    $sql_data['product_code'] = createCode($db_name, 'product_code', 9);
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
    for ($I = 1; $I <= 8; $I++) {
      @unlink($filePath . 'product_' . $product_code . '_' .$I . '.jpg');
      @unlink($filePath . 'product_' . $product_code . '_' .$I . '.png');
    }
    break;
}

// 圖片上傳與刪除勾選處置
if (($cms_mode == 'add' || $cms_mode == 'edit') && !empty($insert_id)) {$xid = 'product_' . $insert_id . '_';$photo_array = isset($_REQUEST['photo']) ? explode(",", $_REQUEST['photo']) : array_fill(0, 10, '');

  for ($I = 0; $I <= 9; $I++) {
    $pic_num =$I + 1;
    $ext = (isset($add_pic['type'][$I]) && $add_pic['type'][$I] == 'image/png') ? '.png' : '.jpg';
    $pic_name =$xid . $pic_num .$ext;
    $small = [["$pic_name", "a", "640", "640"]];

    // 若使用者勾選刪除此圖
    if (isset($del_pic[$I]) && $del_pic[$I] == "1") {
      $photo_array[$I] = '';
      @unlink($filePath . $xid .$pic_num . '.jpg');
      @unlink($filePath . $xid .$pic_num . '.png');
    } elseif (!empty($add_pic['tmp_name'][$I])) {
      // 處理新上傳圖片
      uploadedPhotoPathR($add_pic["tmp_name"][$I],$filePath, $add_pic["type"][$I], 0, $small);$photo_array[$I] =$pic_name;
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
<!-- 
<script type="text/javascript">
  alert("<?= addslashes($msg) ?>");
  window.location.href = "<?= $web_url ?>";
</script> 
-->
</head>
<body></body>
</html>