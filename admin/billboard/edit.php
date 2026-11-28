<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$bd_id   = isset($_REQUEST['bd_id']) ? trim($_REQUEST['bd_id']) : '';
$bd_name = isset($_REQUEST['bd_name']) ? trim($_REQUEST['bd_name']) : '';
$page    = (isset($_REQUEST['page']) && $_REQUEST['page'] !== '') ? max(1, (int)$_REQUEST['page']) : 1;
$no      = isset($_REQUEST['no']) ? trim($_REQUEST['no']) : '';
$cms_mode = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($no === '' ? 'add' : 'edit');
$sh_post = $_SERVER['QUERY_STRING'];
$trace   = 0;

// 新增模式預設值
$billboard = array(
  'no' => '', 'name' => '', 'bd_id' => '', 'm_type' => 'p', 'del_sw' => '0',
  'web_code' => '', 'start_time' => '', 'end_time' => '', 'web_url' => '',
  'info' => '', 'm_sort' => '', 'ishow' => '1', 'pic_size' => '', 'photo' => ''
);

if ($no !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `billboard` WHERE `no` = ?");
  $stmt->execute(array($no));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $billboard = array_merge($billboard, $row);
  }
}

// 目前所屬類別：以資料本身為優先，沒有則用網址帶入的 bd_id
if ($billboard['bd_id'] !== '' && $billboard['bd_id'] !== null) {
  $bd_id = $billboard['bd_id'];
}

// 排序最大值（原本誤把 $no 當成 cms_mode 傳入，已改正）
$sql_where = " WHERE `bd_id` = " . $pdo->quote($bd_id);
$sort_max  = sortMax($cms_mode, 'billboard', $sql_where, 'no', $trace);
$sort_selected = ($billboard['m_sort'] === '' || $billboard['m_sort'] === null) ? $sort_max : $billboard['m_sort'];
$sort_end      = max((int)$sort_max, (int)$sort_selected);

if ($trace == 1) {
  echo '$sort_max=' . h($sort_max) . '<br>';
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function validator(f) {
    if (f.name.value.replace(/\s/g, '') === '') {
      alert('請輸入名稱');
      f.name.focus();
      return false;
    }
    return true;
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
          <div><?php echo h($bd_name); ?>-版面管理</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table" style="width:96%;">
<div class="edit_row">
  <div class="edit_title tb_edit_title">名稱</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($billboard['name']); ?>" size="64"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">廣告類別</div>
  <div class="edit_info tb_edit_info"><select name="cat_id">
      <?php
      $stmt_cat = $pdo->query("SELECT * FROM `billboard_cat`");
      while ($row_category = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <option value="<?php echo h($row_category['no']); ?>" <?php chkSelected($row_category['no'], $bd_id); ?>><?php echo h($row_category['name']); ?></option>
      <?php } ?>
    </select></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">顯示類型</div>
  <div class="edit_info tb_edit_info"><label><input type="radio" name="m_type" value="p" <?php chkChecked('p', $billboard['m_type']); ?>>圖片</label>
    <label><input type="radio" name="m_type" value="pu" style="margin-left:10px;" <?php chkChecked('pu', $billboard['m_type']); ?>>圖片+連結</label>
    <label><input type="radio" name="m_type" value="pi" style="margin-left:10px;" <?php chkChecked('pi', $billboard['m_type']); ?>>圖片+說明</label>
    <label><input type="radio" name="m_type" value="piu" style="margin-left:10px;" <?php chkChecked('piu', $billboard['m_type']); ?>>圖片+說明連結</label>
    <label><input type="radio" name="m_type" value="c" style="margin-left:10px;" <?php chkChecked('c', $billboard['m_type']); ?>>程式碼</label>
    <label><input type="radio" name="m_type" value="ciu" style="margin-left:10px;" <?php chkChecked('ciu', $billboard['m_type']); ?>>程式碼+說明連結</label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">可否刪除</div>
  <div class="edit_info tb_edit_info"><label><input type="radio" name="del_sw" value="0" <?php chkChecked('0', $billboard['del_sw']); ?>>可刪除</label>
    <label><input type="radio" name="del_sw" value="1" style="margin-left:10px;" <?php chkChecked('1', $billboard['del_sw']); ?>>不可刪除</label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">程式碼</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" name="web_code" cols="60" rows="6"><?php echo h($billboard['web_code']); ?></textarea>
    <br>
    範例:
    <br>
    &lt;iframe width=&quot;100%&quot; height=&quot;100%&quot; src=&quot;//www.youtube.com/embed/<span style="color:#F00;">n3ZvI70GCb0(影片代號)</span>?rel=0&amp;wmode=opaque&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;/iframe&gt;</div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上架日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?php echo h($billboard['start_time']); ?>" name="start_time" id="start_time">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">下架日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?php echo h($billboard['end_time']); ?>" name="end_time" id="end_time">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">連結</div>
  <div class="edit_info tb_edit_info"><textarea name="web_url" cols="60" rows="6"><?php echo h($billboard['web_url']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">廣告說明</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" name="info" cols="60" rows="6"><?php echo h($billboard['info']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">排序</div>
  <div class="edit_info tb_edit_info"><select name="m_sort" id="m_sort">
      <?php getDateSelectOption(1, $sort_end, $sort_selected); ?>
    </select></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><label><input type="radio" name="ishow" value="1" <?php chkChecked('1', $billboard['ishow']); ?>>上架</label>
    <label><input type="radio" name="ishow" value="0" <?php chkChecked('0', $billboard['ishow']); ?>>下架</label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">列表圖示</div>
  <div class="edit_info tb_edit_info"><div class="edit_sub"><input type="file" name="add_pic[]" id="fileField"></div>
    <div class="edit_sub">
      <input type="text" name="pic_size[]" id="pic_size" value="<?php echo h($billboard['pic_size']); ?>">
      <br>(如果填0x0將以原尺寸上傳，200x150及寬200高150。若要限制寬度高度填0即可(例:140x0))
    </div>
    <?php
    if ($billboard['no'] !== '') {
      $filename = '../../images/upload/billboard/billboard_' . sprintf("%06d", $billboard['no']) . '_1.jpg';
      if (file_exists($filename)) {
    ?>
    <div class="edit_sub">
      <a href="<?php echo h($billboard['web_url']); ?>" target="_blank">
        <img src="<?php echo h($filename); ?>" width="220" border="0">
      </a>
    </div>
    <?php
      }
    }
    ?></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($bd_name); ?>" name="bd_name" id="bd_name" />
      <input type="hidden" value="<?php echo h($bd_id); ?>" name="bd_id" id="bd_id" />
      <input type="hidden" value="<?php echo h($billboard['photo']); ?>" name="photo" id="photo" />
      <input type="hidden" value="<?php echo h($billboard['no']); ?>" name="no" id="no" />
      <input type="hidden" value="<?php echo h($billboard['m_sort']); ?>" name="sort_old_value" id="sort_old_value" />
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
