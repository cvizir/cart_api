<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$bd_id   = isset($_REQUEST['bd_id']) ? trim($_REQUEST['bd_id']) : '';
$bd_name = isset($_REQUEST['bd_name']) ? trim($_REQUEST['bd_name']) : '';
$msg     = '';

// 儲存拖曳後的順序
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['no_id']) && is_array($_POST['no_id']) && count($_POST['no_id']) >= 2) {
  try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE `billboard` SET `m_csort` = ? WHERE `no` = ? AND `bd_id` = ?");
    $i = 0;
    foreach ($_POST['no_id'] as $id) {
      $i++;   // 原本迴圈多跑一圈（<= count），會多更新一筆不存在的資料
      $stmt->execute(array($i, (string)$id, $bd_id));
    }
    $pdo->commit();
    $msg = "排序已更新";
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log("billboard sort error: " . $e->getMessage());
    $msg = "排序更新失敗，請稍後再試";
  }
}

// 讀取目前順序（以 bd_id 為準，與 list.php / edit.php 一致）
$stmt = $pdo->prepare("SELECT * FROM `billboard` WHERE `bd_id` = ? ORDER BY `m_csort` ASC, `no` DESC");
$stmt->execute(array($bd_id));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$back_url = "list.php?bd_id=" . urlencode($bd_id) . "&bd_name=" . urlencode($bd_name);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
  ul, li { margin: 0; padding: 0; list-style: none; }
  .box { width: 600px; height: auto; margin: 25px 0 0 0; border: 1px solid #fff; }
  .main { cursor: pointer; position: static; width: 600px; min-height: 20px; margin-bottom: 5px; padding: 5px; border: 1px dashed #00f; background: #fff; }
  .maindash { position: absolute; width: 600px; min-height: 20px; margin-bottom: 5px; padding: 5px; border: 1px dashed blue; background: #ececec; opacity: 0.7; }
  .dash { position: static; width: 600px; height: 20px; margin-bottom: 5px; border: 1px dashed #f00; }
</style>
<script type="text/javascript" src="../script/jquery-1.7.1.js"></script>
<script type="text/javascript">
$(document).ready(function () {
  var range = { x: 0, y: 0 };                         // 滑鼠相對元素的偏移量
  var lastPos = { x: 0, y: 0, x1: 0, y1: 0 };         // 拖曳元素座標
  var tarPos = { x: 0, y: 0, x1: 0, y1: 0 };          // 目標元素座標
  var theDiv = null, move = false;                    // 拖曳元素 / 拖曳狀態
  var theDivHeight = 0, theDivHalf = 0, tarFirstY = 0;
  var tarDiv = null, tarFirst, tempDiv;

  $(".main").each(function () {
    $(this).mousedown(function (event) {
      event.preventDefault();                         // 避免瀏覽器內建的圖片拖曳
      theDiv = $(this);
      range.x = event.pageX - theDiv.offset().left;
      range.y = event.pageY - theDiv.offset().top;
      theDivHeight = theDiv.outerHeight();
      theDivHalf = theDivHeight / 2;
      move = true;
      theDiv.attr("class", "maindash");
      // 在原位置插入虛線佔位框（高度與拖曳元素相同）
      $("<div class='dash'></div>").css('height', theDivHeight).insertBefore(theDiv);
    });
  });

  $(document).mousemove(function (event) {
    if (!move) return false;
    lastPos.x = event.pageX - range.x;
    lastPos.y = event.pageY - range.y;
    lastPos.y1 = lastPos.y + theDivHeight;
    theDiv.css({ left: lastPos.x + 'px', top: lastPos.y + 'px' });

    var $main = $('.main');
    tempDiv = $(".dash");
    $main.each(function () {
      tarDiv = $(this);
      tarPos.x = tarDiv.offset().left;
      tarPos.y = tarDiv.offset().top;
      tarPos.y1 = tarPos.y + tarDiv.height() / 2;
      tarFirst = $main.eq(0);
      tarFirstY = tarFirst.offset().top + theDivHalf;
      if (lastPos.y <= tarFirstY) {
        tempDiv.insertBefore(tarFirst);
      }
      if (lastPos.y >= tarPos.y - theDivHalf && lastPos.y1 >= tarPos.y1) {
        tempDiv.insertAfter(tarDiv);
      }
    });
  }).mouseup(function () {
    if (!move) return;
    theDiv.insertBefore(tempDiv);
    theDiv.attr("class", "main");
    theDiv.css({ left: '', top: '' });
    tempDiv.remove();
    move = false;
  });
});
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <table width="850" border="0" cellspacing="0" cellpadding="0">
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
          <td height="17">&nbsp;</td>
          <td height="30" valign="top">
            <input type="button" value="回列表" onClick="window.location.href=<?php echo h(json_encode($back_url)); ?>" />
            <?php if ($msg !== '') { ?><span style="color:#d00; margin-left:10px;"><?php echo h($msg); ?></span><?php } ?>
          </td>
        </tr>
        <tr>
          <td width="15" height="17">&nbsp;</td>
          <td width="835">
            <form name="form1" method="post" action="sort.php">
              <input type="hidden" name="bd_id" value="<?php echo h($bd_id); ?>">
              <input type="hidden" name="bd_name" value="<?php echo h($bd_name); ?>">
              <div class="box" id="box">
                <?php
                foreach ($rows as $row_cat) {
                  $base = '../../images/upload/billboard/billboard_' . sprintf("%06d", $row_cat['no']) . '_1';
                  $img  = '';
                  if (file_exists($base . '.jpg')) {
                    $img = $base . '.jpg';
                  } elseif (file_exists($base . '.png')) {
                    $img = $base . '.png';
                  }
                ?>
                <div class="main">
                  <?php if ($row_cat['web_code'] !== '' && $row_cat['web_code'] !== null) { ?>
                    <?php echo $row_cat['web_code']; /* 廣告程式碼本來就是 HTML，由後台管理員輸入，刻意不跳脫 */ ?>
                  <?php } elseif ($img !== '') { ?>
                    <img src="<?php echo h($img); ?>" width="110">
                  <?php } else { ?>
                    <?php echo h($row_cat['name']); ?>
                  <?php } ?>
                  <input name="no_id[]" type="hidden" value="<?php echo h($row_cat['no']); ?>">
                </div>
                <?php } ?>
                <?php if (empty($rows)) { ?>
                <div>目前沒有資料</div>
                <?php } ?>
              </div>
              <br>
              <input type="submit" value="送出" />
            </form>
          </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
      </table>
      <br />
      <br />
    </div></td>
  </tr>
</table>
</body>
</html>
