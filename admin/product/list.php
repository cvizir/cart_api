<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
// 接收 GET 參數
$del_pd_file = isset($_GET['del_pd_file']) ? $_GET['del_pd_file'] : '';
$page = !empty($_GET["page"]) ? (int)$_GET["page"] : 1;
$pagesize = 50;
$trace = 0;

$sh_pdcat_code = isset($_REQUEST["sh_pdcat_code"]) ? $_REQUEST["sh_pdcat_code"] : '';
$sh_publishing_code = isset($_REQUEST["sh_publishing_code"]) ? $_REQUEST["sh_publishing_code"] : '';
$sh_status = isset($_REQUEST["sh_status"]) ? $_REQUEST["sh_status"] : '';
$sh_qty = isset($_REQUEST["sh_qty"]) ? $_REQUEST["sh_qty"] : '';
$sh_txt = isset($_REQUEST["sh_txt"]) ? $_REQUEST["sh_txt"] : '';

// 保留換頁查詢條件網址字串
$sh_post = '&sh_pdcat_code=' . urlencode($sh_pdcat_code) . '&sh_txt=' . urlencode($sh_txt) . '&sh_qty=' . urlencode($sh_qty) . '&sh_publishing_code=' . urlencode($sh_publishing_code) . '&sh_status=' . urlencode($sh_status);
$argument = $sh_post;
$order = " ORDER BY no DESC";

// 動態安全組合 WHERE 與綁定參數
$sh_array = [];
$params = [];

if ($sh_pdcat_code != '') {
  $sh_array[] = "`pdcat_code` LIKE ?";
  $params[] = '%' . $sh_pdcat_code . '%';
}
if ($sh_publishing_code != '') {
  $sh_array[] = "`pd_publishing` = ?";
  $params[] = $sh_publishing_code;
}
if ($sh_qty != '') {
  $sh_array[] = "`pd_stock` <= ?";
  $params[] = $sh_qty;
}
if ($sh_status != '') {
  $sh_array[] = "`ishow` = ?";
  $params[] = $sh_status;
}

if (!empty($sh_array)) {
  $where = " WHERE " . implode(" AND ", $sh_array);
} else {
  $where = '';
}

// 模糊關鍵字搜尋
if ($sh_txt != '') {
  $where = " WHERE (`product_num` LIKE ? OR `name` LIKE ? OR `bar_code` LIKE ? OR `pd_info` LIKE ? OR `pd_summary` LIKE ?)";
  $params = ["%{$sh_txt}%", "%{$sh_txt}%", "%{$sh_txt}%", "%{$sh_txt}%", "%{$sh_txt}%"];
}

// 刪除暫存匯入檔案
if ($del_pd_file == 'del_pd_file' && file_exists('../book/book.xlsx')) {
  unlink('../book/book.xlsx');
}

// 實例化分頁查詢物件
$productClass = new DBClass('product', $page, $pagesize);
$listData = $productClass->getAdminList($where, $order, $trace, $params);
$productList = $listData['data'];
$total = $listData['total'];
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo ADMIN_TITLE; ?></title>
  <script type="text/javascript">
    var sh_post = '<?= $sh_post ?>';

    function onDel(no, name) {
      if (confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
        window.location.href = "save.php?cms_mode=del&total=<?= $total ?>&no=" + no + sh_post;
        return true;
      }
      return false;
    }

    function onEdit(no) {
      window.location.href = "edit.php?cms_mode=edit&no=" + no + sh_post;
    }

    function onAdd() {
      window.location.href = "edit.php?cms_mode=add" + sh_post;
    }
  </script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
      <td valign="top">
        <div align="center" style="margin-left:10px;"><br /><br />
          <table width="95%" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="1170">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td class="tb_menu_title">產品管理</td>
                    <td align="right" class="tb_menu_title">
                      <input name="Submit" type="button" value="新增" onClick="onAdd()" />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <table width="90%">
                  <tr>
                    <td valign="bottom">
                      <div align="right">
                        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <form action="" method="GET">
                              <td width="90%" height="30" valign="middle">出 版 社：
                                類別：
                                <select name="sh_pdcat_code" id="sh_pdcat_code">
                                  <option value="" <?php chkSelected("", $sh_status) ?>>全部商品</option>
                                  <?php
                                  // 使用 PDO 讀取商品分類下拉選單
                                  $sql_product_cat = "SELECT a.name AS m_name, b.* FROM pdcat_m a RIGHT OUTER JOIN pdcat b ON a.pdcat_m_code = b.pdcat_m_code ORDER by a.pdcat_m_code";
                                  $stmt_cat = $pdo->query($sql_product_cat);
                                  while ($row_product_cat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                  ?>
                                    <option value="<?= htmlspecialchars($row_product_cat['pdcat_code']); ?>" <?php chkSelected($row_product_cat['pdcat_code'], $sh_pdcat_code) ?>>
                                      <?= htmlspecialchars($row_product_cat['m_name'] . '-' . $row_product_cat['name']); ?>
                                    </option>
                                  <?php } ?>
                                </select>&nbsp;&nbsp;
                                庫存小於查詢：
                                <input name="sh_qty" type="text" value="<?= htmlspecialchars($sh_qty) ?>" size="6" maxlength="4">&nbsp;&nbsp;
                                狀態：
                                <select name="sh_status" id="sh_status">
                                  <option value="" <?php chkSelected("", $sh_status) ?>>請選擇</option>
                                  <option value="1" <?php chkSelected(1, $sh_status) ?>>上架</option>
                                  <option value="0" <?php chkSelected(0, $sh_status) ?>>下架</option>
                                  <option value="9" <?php chkSelected(9, $sh_status) ?>>待審查</option>
                                </select>&nbsp;&nbsp;
                                <input type="submit" name="button" id="button" value="送出">
                              </td>
                            </form>
                          </tr>
                          <tr>
                            <form action="" method="GET">
                              <td height="30" colspan="1" valign="middle">模糊查詢：
                                <input name="sh_txt" type="text" value="<?= htmlspecialchars($sh_txt) ?>" size="46">&nbsp;&nbsp;
                                <input type="submit" name="button" id="button" value="送出">
                              </td>
                            </form>
                          </tr>
                          <tr>
                            <td height="30" colspan="1" valign="middle">
                              <input type="button" name="button2" id="button2" onClick="location.href='list.php'" value="搜尋重置">
                              <input type="button" name="button3" id="button3" onClick="location.href='list.php?del_pd_file=del_pd_file'" value="刪除匯入檔">
                            </td>
                          </tr>
                        </table>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <form action="" method="post" name="form1" id="form1">
                        <table width="1100" border="1" cellpadding="2" cellspacing="0">
                          <tr>
                            <td width="93" height="28" class="tb_list_title">No</td>
                            <td width="349" align="left" class="tb_list_title">商品名稱</td>
                            <td width="114" align="left" class="tb_list_title">商品類別</td>
                            <td width="48" align="left" class="tb_list_title">價格</td>
                            <td width="48" class="tb_list_title">庫存</td>
                            <td width="48" class="tb_list_title">狀態</td>
                            <td width="91" class="tb_list_title">編輯</td>
                          </tr>
                          <?php
                          foreach ($productList as $list_info) {
                          ?>
                            <tr>
                              <td align="left" class="tb_list_info"><?= htmlspecialchars($list_info['no']); ?></td>
                              <td align="left" class="tb_list_info"><?= htmlspecialchars($list_info['name']); ?></td>
                              <td align="left" class="tb_list_info"><?= htmlspecialchars($list_info['pdcat_code']); ?></td>
                              <td align="center" class="tb_list_info"><?= htmlspecialchars($list_info['price']); ?></td>
                              <td align="center" class="tb_list_info"><?= htmlspecialchars($list_info['pd_stock']); ?></td>
                              <td class="tb_list_info"><?= getIshow($list_info['ishow']); ?></td>
                              <td class="tb_list_info">
                                <input name="Submit2" type="button" class="btn_text" value="編輯" onClick="onEdit('<?= $list_info['no']; ?>')" />
                                <input name="Submit2" type="button" class="btn_text" value="刪除" onClick="onDel('<?= $list_info['no']; ?>','<?= htmlspecialchars(addslashes($list_info['name'])); ?>')" />
                              </td>
                            </tr>
                          <?php } ?>
                        </table>
                      </form>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" class="page"><span class="wd_white_12"><?= $productClass->showPageMenu($argument) ?></span></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br />
        </div>
      </td>
    </tr>
  </table>
</body>

</html>