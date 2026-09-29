<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
// 接收參數
$pdcat_code = isset($_REQUEST["pdcat_code"]) ? $_REQUEST["pdcat_code"] : '';
$product_code = isset($_REQUEST["product_code"]) ? $_REQUEST["product_code"] : '';
$cms_mode = isset($_REQUEST["cms_mode"]) ? $_REQUEST["cms_mode"] : 'add';
$sh_post = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
$no = isset($_REQUEST['no']) ? $_REQUEST['no'] : '';
$db_name = 'product';
$trace = 0;

// 透過 PDO quote 處理條件，防止注入
$sortmax_where = ($pdcat_code == '') ? '' : " WHERE `pdcat_code` = " . $pdo->quote($pdcat_code);
$sort_max = sortMax($cms_mode, $db_name, $sortmax_where, 'no', $trace);

$product = [];
// 編輯模式時透過預處理語句取得商品資料
if ($no != "") {
  $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `no` = ?");
  $stmt->execute([$no]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($cms_mode == 'add') {
  $product['pdcat_code'] = $pdcat_code;
}
if (empty($product['pd_mode'])) {
  $product['pd_mode'] = 1;
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?= ADMIN_TITLE ?></title>
  <meta http-equiv="cache-control" content="no-cache">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="0">
  <script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
  <script type="text/javascript" src="../../script/jquery.min.js"></script>
  <script type="text/javascript">
    <?php
    $check_txt = 'product_read';
    $all_str = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
    if (function_exists('checkAdmim') && !checkAdmim($all_str, $check_txt, 0)) {
    ?>
      alert("<?= isset($pv_err_msg) ? $pv_err_msg : '無權限' ?>");
      window.location.href = "../main.php";
    <?php } ?>
  </script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="101%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2"><?php require_once("../header.php"); ?></td>
    </tr>
    <tr>
      <td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php"); ?></td>
      <td width="90%" valign="top">
        <div align="center"><br /><br />
          <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="100%">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td width="49%" class="tb_menu_title">產品管理</td>
                    <td width="51%" align="right" class="tb_menu_title">
                      <input name="Submit" type="button" value="回上一頁" onClick="history.go(-1);" />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <form action="save.php?<?= htmlspecialchars($sh_post) ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm">
                  <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <tr>
                      <td class="tb_edit_title">上次修改時間</td>
                      <td class="tb_edit_info"><?= isset($product['uptime']) ? $product['uptime'] : '' ?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="15%" class="tb_edit_title">名稱</td>
                      <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?= isset($product['name']) ? htmlspecialchars($product['name']) : '' ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">上架日期</td>
                      <td class="tb_edit_info">
                        <input type="text" value="<?= isset($product['release_date']) ? $product['release_date'] : '' ?>" name="release_date" id="release_date">
                        <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'release_date'})">
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">商品類別</td>
                      <td class="tb_edit_info">
                        <div style="width:100%; height:200px; overflow:scroll;overflow-x: hidden;">
                          <?php
                          $stmt_pdcat = $pdo->query("SELECT * FROM `pdcat` WHERE `pdcat_code` = '{$product['pdcat_code']}'");
                          $row_pdcat = $stmt_pdcat->fetch(PDO::FETCH_ASSOC);
                          echo "<input type='hidden' name='pdcat_code' value='" . htmlspecialchars($row_pdcat['name']) . "'>";
                          ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">庫存數</td>
                      <td class="tb_edit_info"><input name="pd_stock" type="text" value="<?= isset($product['pd_stock']) ? htmlspecialchars($product['pd_stock']) : '' ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">原價</td>
                      <td class="tb_edit_info"><input type="text" name="o_price" value="<?= isset($product['o_price']) ? htmlspecialchars($product['o_price']) : '' ?>"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">售價</td>
                      <td class="tb_edit_info"><input type="text" name="price" value="<?= isset($product['price']) ? htmlspecialchars($product['price']) : '' ?>"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">上下架</td>
                      <td class="tb_edit_info">
                        <?php $ishow = isset($product['ishow']) ? $product['ishow'] : '0'; ?>
                        上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $ishow); ?>>&nbsp;&nbsp;&nbsp;
                        下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $ishow); ?>>&nbsp;&nbsp;&nbsp;
                        待審查<input type="radio" value="9" name="ishow" <?php chkChecked('9', $ishow); ?>>&nbsp;&nbsp;&nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">列表排序</td>
                      <td class="tb_edit_info">
                        <select name="m_sort" id="m_sort">
                          <?php getDateSelectOption('1', $sort_max, isset($product['m_sort']) ? $product['m_sort'] : 1); ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">商品特色</td>
                      <td class="tb_edit_info"><textarea class="ckeditor" name="pd_info" cols="60" rows="6"><?= isset($product['pd_info']) ? htmlspecialchars($product['pd_info']) : '' ?></textarea></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">&nbsp;</td>
                      <td class="tb_edit_info">
                        <script type="text/javascript">
                          function clearFile(id_name) {
                            var obj = document.getElementById(id_name);
                            obj.value = '' //FF下
                            obj.select(); //IE下
                            document.execCommand('Delete');
                          }
                        </script>
                        <div id="div_photo_upload">
                          <?php
                          $photo_array = isset($product['photo']) ? explode(",", $product['photo']) :  ['', '', '', '', '', ''];
                          $photo_ps = ['', '', '', '', '', ''];
                          for ($I = 0; $I < 6; $I++) {
                            $pix = ($I - 1);
                            $filename = '../../images/upload/product/' . $photo_array[$pix];
                            if (!file_exists($filename)  || $photo_array[$pix] == '') {
                              $filename = 'images/no_pic.gif';
                            }
                          ?>
                            <div style="width:45%; height:450px;; float:left; margin:10px;">
                              <ul>
                                <li>
                                  <input type="checkbox" name="del_pic[<?php echo "$pix"; ?>]" value="1">
                                  刪除此產品圖片
                                  <?php if ($photo_ps[$I] <> "") {
                                    echo '(' . $photo_ps[$I] . ')';
                                  } ?>
                                </li>
                                <li>
                                  <input id="input_file_<?php echo "$pix"; ?>" name="add_pic[<?php echo "$pix"; ?>]" type="file" size="19">
                                  <input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$pix"; ?>');" />
                                </li>
                                <li style="width:100%; height:auto;"><img src="<?php echo "$filename";  ?>" alt="" width="100%"></li>
                              </ul>
                            </div>
                          <?php } ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?= isset($product['no']) ? $product['no'] : '' ?>" name="no" />
                        <input type="hidden" value="<?= isset($product['m_sort']) ? $product['m_sort'] : '' ?>" name="sort_old_value" />
                        <input type="hidden" value="<?= isset($product['product_code']) ? $product['product_code'] : '' ?>" name="product_code" />
                        <input type="hidden" value="<?= isset($product['photo']) ? $product['photo'] : '' ?>" name="photo" />
                        <input type="hidden" value="<?= isset($page) ? $page : 1 ?>" name="page" />
                        <input type="submit" name="button" value="<?= ($cms_mode == 'add') ? '新增資料' : '修改資料' ?>">
                      </td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
</body>

</html>