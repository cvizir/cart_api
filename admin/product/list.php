<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

$del_pd_file = isset($_GET['del_pd_file']) ? $_GET['del_pd_file'] : '';
$page = ($_GET["page"] != "") ? $_GET["page"] : 1;
$pagesize = 50;
$trace = 1;
$sh_pdcat_code = $_REQUEST["sh_pdcat_code"];
$sh_publishing_code = $_REQUEST["sh_publishing_code"];
$sh_status = $_REQUEST["sh_status"];
$sh_qty = $_REQUEST["sh_qty"];
$sh_txt = $_REQUEST["sh_txt"];
$sh_post = '&sh_pdcat_code=' . $sh_pdcat_code . '&sh_txt=' . $sh_txt . '&sh_qty=' . $sh_qty . '&sh_publishing_code=' . $sh_publishing_code . '&sh_status=' . $sh_status;
//print_r($_REQUEST);
//上下頁參數
$argument = "$sh_post";

$order = " ORDER BY no DESC";

if ($sh_publishing_code != '' || $sh_pdcat_code != '' || $sh_qty != '' || $sh_status != '') {

  if ($sh_pdcat_code != '') {
    $sh_array[] = "`pdcat_code` LIKE '%$sh_pdcat_code%'";
  }

  if ($sh_publishing_code != '') {
    $sh_array[] = "`pd_publishing`='$sh_publishing_code'";
  }

  if ($sh_qty != '') {
    $sh_array[] = "`pd_stock`<='$sh_qty'";
  }

  if ($sh_status != '') {
    $sh_array[] = "`ishow`='$sh_status'";
  }

  $where = " WHERE " . @join(" AND ", $sh_array);
} else {
  $where = '';
}

if ($sh_txt != '') {
  $where = " WHERE `product_num` LIKE '%$sh_txt%' OR `name` LIKE '%$sh_txt%' OR `bar_code` LIKE '%$sh_txt%' OR `pd_info` LIKE '%$sh_txt%' OR `pd_summary` LIKE '%$sh_txt%'";
}
if ($del_pd_file == 'del_pd_file') {
  if (file_exists('../book/book.xlsx')) {
    unlink('../book/book.xlsx');
  }
}
$tag_no = $_GET["tag"];
$productClass = new DBClass('product', $page, $pagesize);
$productList = $productClass->getAdminList($where, $order, $trace);
$total = $productClass->total; //count($productList) 
//echo'$total='."$total";
//print_r($_SESSION["admin_pv"]);
// $chk_index = strExist($_SESSION["admin_pv"], 'product_et');

?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo ADMIN_TITLE; ?></title>
  <script language="JavaScript" type="text/JavaScript">

    sh_post='<?= $sh_post ?>';
	function onDel(no,name){
		if(window.confirm("確定要刪除 [ "+name+" ] 這筆資料嗎?")){
	 	window.location.href="save.php?cms_mode=del&total=<?php echo "$total" ?>&no="+no+sh_post;
			return true;
		}
		return false;
	}
    function onEdit(no){	 	
	 	window.location.href="edit.php?cms_mode=edit&no="+no+sh_post;
	}
    function onAdd(){	 	
	 	window.location.href="edit.php?cms_mode=add"+sh_post;
	}
	
</script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr>
      <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
      <td valign="top">
        <div align="center" style="margin-left:10px;"><br />
          <br />
          <table width="95%" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="1170">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td class="tb_menu_title">產品管理</td>
                    <td align="right" class="tb_menu_title"><span class="tb_list_title_bt">
                        <input name="Submit" type="button" value="新增" onClick="onAdd()" />
                      </span></td>
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
                                <select name="sh_publishing_code" id="sh_publishing_code">
                                  <option value="">全部</option>
                                  <?php
                                  $sql_publishing = "SELECT * FROM `publishing`";
                                  $rs_publishing = mysql_query($sql_publishing);
                                  $num_publishing = mysql_num_rows($rs_publishing);
                                  while ($row_publishing = mysql_fetch_array($rs_publishing, MYSQL_ASSOC)) {
                                  ?>
                                    <option value="<?php echo $row_publishing['publishing_code'];  ?>" <?php chkSelected($row_publishing['publishing_code'], $sh_publishing_code) ?>> <?php echo $row_publishing['name'];  ?></option>
                                  <?php } ?>
                                </select>&nbsp;&nbsp;
                                類別：
                                <select name="sh_pdcat_code" id="sh_pdcat_code">
                                  <option value="" <?php chkSelected("", $sh_status) ?>>全部商品</option>
                                  <?php
                                  $sql_product_cat = "SELECT a.name AS m_name,b.* FROM  pdcat_m a RIGHT OUTER JOIN   pdcat b ON a.pdcat_m_code = b.pdcat_m_code ORDER by a.pdcat_m_code ";
                                  $rs_product_cat = mysql_query($sql_product_cat);
                                  while ($row_product_cat = mysql_fetch_array($rs_product_cat, MYSQL_ASSOC)) {


                                  ?>
                                    <option value="<?php echo $row_product_cat['pdcat_code']; ?>" <?php chkSelected($row_product_cat['pdcat_code'], $sh_pdcat_code) ?>>
                                      <?php echo $row_product_cat['m_name'] . '-' . $row_product_cat['name']; ?>
                                    </option>
                                  <?php } ?>
                                </select>&nbsp;&nbsp;
                                庫存小於查詢：
                                <input name="sh_qty" type="text" value="<?php echo $sh_qty ?>" size="6" maxlength="4">&nbsp;&nbsp;
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
                                <input name="sh_txt" type="text" size="46">&nbsp;&nbsp;
                                <input type="submit" name="button" id="button" value="送出">
                              </td>
                            </form>
                          </tr>
                          <tr>
                            <td height="30" colspan="1" valign="middle">
                              <input type="button" name="button2" id="button2" onClick="javascript:window.location.href='list.php'" value="搜尋重置">
                              <input type="button" name="button3" id="button3" onClick="javascript:window.location.href='list.php?del_pd_file=del_pd_file'" value="刪除匯入檔">
                            </td>
                          </tr>
                          <tr>
                            <td height="30" colspan="1" valign="middle"></td>
                          </tr>
                        </table>

                      </div>
                    </td>

                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <form action="" method="post" name="form1" id="form1">
                        <table width="1100" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="93" height="28" class="tb_list_title">書號</td>
                            <td width="93" align="left" class="tb_list_title">商品條碼</td>
                            <td width="106" align="left" class="tb_list_title">出版社</td>
                            <td width="349" align="left" class="tb_list_title">商品名稱</td>
                            <td width="114" align="left" class="tb_list_title">商品類別</td>
                            <td width="48" align="left" class="tb_list_title">價格</td>
                            <td width="48" class="tb_list_title">庫存</td>
                            <td width="48" class="tb_list_title">重量</td>
                            <td width="48" class="tb_list_title">狀態</td>
                            <td width="91" class="tb_list_title">編輯</td>
                          </tr>
                          <?
                          $no = ($page - 1) * $pagesize;

                          while ($list_info = @mysql_fetch_array($productList, MYSQL_ASSOC)) {
                            unset($pdcat_code_array);
                            $str_code = str_replace(",", "','", $list_info['pdcat_code']);



                            $sql_publishing = "SELECT * FROM `publishing` WHERE `publishing_code`='" . $list_info['pd_publishing'] . "'";
                            $rs_publishing = mysql_query($sql_publishing);
                            $num_publishing = mysql_num_rows($rs_publishing);
                            $row_publishing = mysql_fetch_array($rs_publishing, MYSQL_ASSOC);
                            //echo"$sql_publishing";	

                            $sql_discount = "SELECT * FROM `discount` WHERE `discount_code`='" . $list_info['discount_code'] . "'";
                            $rs_discount = mysql_query($sql_discount);
                            $num_discount = mysql_num_rows($rs_discount);
                            $row_discount = mysql_fetch_array($rs_discount, MYSQL_ASSOC);
                            //echo"$sql_discount";					  

                            $sql_pdcat_m = "SELECT * FROM `pdcat_m` WHERE `pdcat_m_code` IN ('" . $str_code . "')";
                            $rs_pdcat_m = mysql_query($sql_pdcat_m);
                            while ($row_pdcat_m = mysql_fetch_array($rs_pdcat_m, MYSQL_ASSOC)) {
                              $pdcat_code_array[] = $row_pdcat_m['name'];
                            }

                            $sql_pdcat = "SELECT * FROM `pdcat` WHERE `pdcat_code` IN ('" . $str_code . "')";
                            $rs_pdcat = mysql_query($sql_pdcat);
                            while ($row_pdcat = mysql_fetch_array($rs_pdcat, MYSQL_ASSOC)) {
                              $pdcat_code_array[] = $row_pdcat['name'];
                            }

                            $pdcat_code = @join(",", $pdcat_code_array);
                            $no++;
                          ?>
                            <tr>
                              <td align="left" class="tb_list_info" style="text-align:left;"><?php echo $list_info['product_num']; ?></td>
                              <td align="left" class="tb_list_info" style="text-align:left;"><?php echo $list_info['bar_code']; ?></td>
                              <td align="left" class="tb_list_info" style="text-align: left; "><?php echo $row_publishing['name'];; ?>-<?php echo $row_discount['name'];; ?></td>
                              <td class="tb_list_info" style="text-align:left;">

                                <a href="http://shopping.windmill.com.tw/product.php?m_action=admin&product_num=<?php echo $list_info['product_num']; ?>" target="_blank">
                                  (<?php echo $list_info['no']; ?>)<?php echo $list_info['name']; ?>
                              </td>
                              <td align="left" class="tb_list_info" style="text-align:left;"><?php echo $pdcat_code; ?></td>
                              <td align="center" class="tb_list_info"><?php echo $list_info['price']; ?></td>
                              <td align="center" class="tb_list_info"><?php echo $list_info['pd_stock']; ?></td>
                              <td align="center" class="tb_list_info"><?php echo $list_info['pd_weight']; ?></td>
                              <td class="tb_list_info"><?php echo getIshow($list_info['ishow']); ?></td>
                              <td class="tb_list_info">
                                <input name="Submit2" type="button" class="btn_text" value="編輯" onClick="onEdit('<?= $list_info['no']; ?>')" />
                                <input name="Submit2" type="button" class="btn_text" value="刪除" onClick="onDel('<?= $list_info['no']; ?>','<?= $list_info['name']; ?>')" />
                              </td>
                            </tr>
                          <? } ?>
                        </table>
                      </form>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" class="page"><span class="wd_white_12">
                        <?php echo $productClass->showPageMenu($argument) ?>
                      </span> </td>
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