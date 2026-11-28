<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$spec_code = isset($_REQUEST['spec_code']) ? trim($_REQUEST['spec_code']) : '';
$cms_mode  = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($spec_code === '' ? 'add' : 'edit');
$page      = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post   = $_SERVER['QUERY_STRING'];
$trace     = 0;

// 新增模式預設值
$spec_info = array(
  'no' => '', 'spec_code' => '', 'text' => '', 'color' => '', 'spec_type' => 'color',
  'show_type' => 'text', 'ishow' => '1', 'm_sort' => '', 'img_url' => ''
);

if ($spec_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `spec_info` WHERE `spec_code` = ?");
  $stmt->execute(array($spec_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $spec_info = array_merge($spec_info, $row);
  }
}

// 排序最大值：新增時 = 筆數 + 1，編輯時 = 筆數（原本的 $sql_where 沒定義）
$sort_max      = sortMax($cms_mode, 'spec_info', '', 'no', $trace);
$sort_selected = ($spec_info['m_sort'] === '' || $spec_info['m_sort'] === null) ? $sort_max : $spec_info['m_sort'];
$sort_end      = max((int)$sort_max, (int)$sort_selected);

// 圖片預覽：只接受單純的檔名，避免讀到其他目錄
$filename = 'images/no_pic.gif';
$img_url  = basename((string)$spec_info['img_url']);
if ($img_url !== '' && file_exists('../../images/upload/spec_info/' . $img_url)) {
  $filename = '../../images/upload/spec_info/' . $img_url;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function validator(f) {
    if (f.text.value.replace(/\s/g, '') === '') {
      alert('請輸入規格名稱');
      f.text.focus();
      return false;
    }
    return true;
  }
  // 清除已選的檔案（原本用 execCommand('Delete')，新版瀏覽器已不支援）
  function clearFile(id_name) {
    var obj = document.getElementById(id_name);
    obj.value = '';
  }
</script>
</head>
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_md">
        <div class="panel_head tb_menu_title">
          <div>規格管理</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table">
<div class="edit_row">
  <div class="edit_title tb_edit_title">規格名稱</div>
  <div class="edit_info tb_edit_info"><input name="text" type="text" id="text" value="<?php echo h($spec_info['text']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">規格色票</div>
  <div class="edit_info tb_edit_info"><input name="color" type="text" id="color" value="<?php echo h($spec_info['color']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">規格類型</div>
  <div class="edit_info tb_edit_info"><label>顏色<input type="radio" value="color" name="spec_type" <?php chkChecked('color', $spec_info['spec_type']); ?>></label>
    <label>容量<input type="radio" value="capacity" name="spec_type" <?php chkChecked('capacity', $spec_info['spec_type']); ?>></label>
    <label>解析度<input type="radio" value="resolution" name="spec_type" <?php chkChecked('resolution', $spec_info['spec_type']); ?>></label>
    <label>尺寸<input type="radio" value="size" name="spec_type" <?php chkChecked('size', $spec_info['spec_type']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">顯示類型</div>
  <div class="edit_info tb_edit_info"><label>文字<input type="radio" value="text" name="show_type" <?php chkChecked('text', $spec_info['show_type']); ?>></label>
    <label>色票<input type="radio" value="code" name="show_type" <?php chkChecked('code', $spec_info['show_type']); ?>></label>
    <label>圖片<input type="radio" value="image" name="show_type" <?php chkChecked('image', $spec_info['show_type']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><label>上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $spec_info['ishow']); ?>></label>
    <label>下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $spec_info['ishow']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">排序</div>
  <div class="edit_info tb_edit_info"><select name="m_sort" id="m_sort">
      <?php getDateSelectOption(1, $sort_end, $sort_selected); ?>
    </select></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">規格圖片</div>
  <div class="edit_info tb_edit_info"><div class="photo_item">
      <ul>
        <li><label><input type="checkbox" name="del_pic[0]" value="1">刪除此圖片</label></li>
        <li><img src="<?php echo h($filename); ?>" alt="" width="240"></li>
        <li>
          <input id="input_file_0" name="add_pic[0]" type="file" size="19" accept="image/jpeg,image/png">
          <input type="button" value="清除" onClick="clearFile('input_file_0');" />
          <br>(僅限 JPG / PNG)
        </li>
      </ul>
    </div></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($spec_info['no']); ?>" name="no" id="no" />
      <input type="hidden" value="<?php echo h($spec_info['spec_code']); ?>" name="spec_code" id="spec_code" />
      <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
      <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
    </div>
  </div>
</form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
