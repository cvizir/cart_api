<?php
// 放在 admin/<資料夾>/list.php（與其他模組同一層），路徑與其他模組一致
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 30;
$trace    = 0;

$CLA       = isset($_REQUEST['CLA']) ? trim($_REQUEST['CLA']) : '';
$GDSNO     = isset($_REQUEST['GDSNO']) ? trim($_REQUEST['GDSNO']) : '';
$bymkerno  = isset($_REQUEST['bymkerno']) ? trim($_REQUEST['bymkerno']) : '';

/*
 * 搜尋條件。原本是把輸入直接串進 SQL（類別、商品 ID、品牌三個欄位都能注入），
 * 另外還有一段「品牌打折」直接 UPDATE goodslst 的程式（對應的表單早就被註解掉了），
 * 但程式本身還在，任何人送出 dmkerno 與 dvalue 就能改全部商品的折扣，已整段移除。
 * 三種條件的優先順序維持原本的：品牌 > 商品 ID > 類別（後面的會蓋掉前面的）。
 */
$where  = "";
$params = array();

if ($CLA !== "") {
  if ($CLA === "1") {
    $where = " WHERE `CLA1NO` <> '0'";
  } elseif ($CLA === "0") {
    $where = " WHERE `CLA1NO` = '0'";
  } elseif (preg_match('/^(\d+)-(\d+)-(\d+)$/', $CLA, $m)) {
    if ($m[3] !== "0") {
      $where = " WHERE `CLA3NO` = ?"; $params = array($m[3]);
    } elseif ($m[2] !== "0") {
      $where = " WHERE `CLA2NO` = ?"; $params = array($m[2]);
    } else {
      $where = " WHERE `CLA1NO` = ?"; $params = array($m[1]);
    }
  }
}
if ($GDSNO !== "") {
  $like   = '%' . $GDSNO . '%';
  $where  = " WHERE `no` = ? OR `GDSNO` = ? OR `GDSNO2` = ? OR `GDSNAME` LIKE ? OR `GDSDESC` LIKE ? OR `GDSDESC_2` LIKE ? OR `GDSDESC_3` LIKE ? OR `GDSDESC_4` LIKE ?";
  $params = array($GDSNO, $GDSNO, $GDSNO, $like, $like, $like, $like, $like);
}
if ($bymkerno !== "") {
  $where  = " WHERE `MKERNO` = ?";
  $params = array($bymkerno);
}

// 上下頁參數
$argument = '&CLA=' . urlencode($CLA) . '&GDSNO=' . urlencode($GDSNO) . '&bymkerno=' . urlencode($bymkerno);
$order    = " ORDER BY `no` DESC";

$goodsClass = new DBClass('goodslst', $page, $pagesize);
$result     = $goodsClass->getAdminList($where, $order, $trace, $params);
$goodsList  = $result['data'];
$total      = $result['total'];

/*
 * 類別、品牌下拉選單。
 * 原本是呼叫舊版 GoodsList 類別的 categoryList()、category2List()、category3List()、brandList()，
 * 那個類別是用 mysql_* 寫的，而且你沒有提供，所以我不知道類別與品牌的資料表名稱與欄位。
 * 這裡先改成從 goodslst 本身列出「實際有用到的」類別與品牌代碼，頁面可以正常運作，但顯示的是代碼、不是名稱。
 * 若要顯示名稱，把下面兩個查詢換成 JOIN 類別 / 品牌資料表即可。
 */
$cat_options = array();   // value => label
$stmt = $pdo->query("SELECT DISTINCT `CLA1NO`, `CLA2NO`, `CLA3NO` FROM `goodslst` WHERE `CLA1NO` <> '0' ORDER BY `CLA1NO`, `CLA2NO`, `CLA3NO`");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
  $a = $c['CLA1NO']; $b = $c['CLA2NO']; $d = $c['CLA3NO'];
  $cat_options[$a . '-0-0'] = $a;
  if ($b !== '0' && $b !== '') { $cat_options[$a . '-' . $b . '-0'] = $a . '-' . $b; }
  if ($d !== '0' && $d !== '') { $cat_options[$a . '-' . $b . '-' . $d] = $a . '-' . $b . '-' . $d; }
}
ksort($cat_options);

$brand_options = array();
$stmt = $pdo->query("SELECT DISTINCT `MKERNO` FROM `goodslst` ORDER BY `MKERNO`");
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $b) {
  $brand_options[] = $b;
}

// 編輯 / 更新庫存 時帶給下一頁的參數
$sh_post = $argument . "&page=" . $page;

// 商品說明只取前 80 個字（去掉 HTML 標籤）
function shortDesc($s) {
  $s = strip_tags((string)$s);
  return function_exists('mb_substr') ? mb_substr($s, 0, 80, 'UTF-8') : substr($s, 0, 240);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function setStock(name, no) {
    if (window.confirm("確定要更新 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "get_stock.php?page=<?php echo (int)$page; ?>&pagesize=<?php echo (int)$pagesize; ?>&total=<?php echo (int)$total; ?>&GDSNO=" + encodeURIComponent(no);
      return true;
    }
    return false;
  }
  function onEdit(no) {
    window.location.href = "new.php?" + sh_post.replace(/^&/, '') + "&GDSNO=" + encodeURIComponent(no);
  }
  function setShow(no, ishow) {
    window.location.href = "show_tr.php?no=" + encodeURIComponent(no) + "&ishow=" + encodeURIComponent(ishow) + sh_post;
  }
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
      <br />
      <table width="1003" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="1070"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="46%" bgcolor="#666666" class="title_2">商品資料</td>
              <td width="54%" align="right" bgcolor="#666666" class="title_2"><input name="Submit" type="button" value="編輯商品" onClick="window.location.href='list.php'" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="97%">
            <tr>
              <td valign="bottom"><div align="right">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="412" height="20" valign="middle">
                      <form name="form2" method="get" action="list.php">選擇顯示類別:
                        <select name="CLA" id="CLA">
                          <option value="1" <?php chkSelected('1', $CLA); ?>>已設定類別</option>
                          <option value="0" <?php chkSelected('0', $CLA); ?>>未設定類別</option>
                          <?php foreach ($cat_options as $val => $label) { ?>
                          <option value="<?php echo h($val); ?>" <?php chkSelected($val, $CLA); ?>><?php echo h($label); ?></option>
                          <?php } ?>
                        </select>
                        <input type="submit" value="類別搜尋">
                      </form>
                    </td>
                    <td width="358" valign="middle">
                      <form name="form4" method="get" action="list.php">
                        <div align="right">搜尋商品ID:
                          <input type="text" name="GDSNO" id="GDSNO" value="<?php echo h($GDSNO); ?>">
                          <input type="submit" value="ID搜尋">
                        </div>
                      </form>
                    </td>
                  </tr>
                  <tr>
                    <td height="20" align="left" valign="middle">
                      <form name="form3" method="get" action="list.php">
                        選擇品牌:
                        <select name="bymkerno" id="bymkerno">
                          <?php foreach ($brand_options as $b) { ?>
                          <option value="<?php echo h($b); ?>" <?php chkSelected($b, $bymkerno); ?>><?php echo h($b); ?></option>
                          <?php } ?>
                        </select>
                        <input type="submit" value="品牌搜尋">
                      </form>
                    </td>
                    <td align="right" valign="middle">&nbsp;</td>
                  </tr>
                </table>
              </div></td>
            </tr>
            <tr>
              <td align="center" valign="top">
                <table width="974" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="92" height="28" align="center" class="title_3">商品NO</td>
                    <td width="145" align="center" class="title_3">商品貨號</td>
                    <td width="159" align="center" class="title_3">商品名稱</td>
                    <td width="284" align="center" class="title_3">商品說明</td>
                    <td width="99" align="center" class="title_3">上下架</td>
                    <td width="157" align="center" class="title_3">編輯</td>
                  </tr>
                  <?php foreach ($goodsList as $goods) {
                    $no_js   = h(json_encode((string)$goods['no']));
                    $gds_js  = h(json_encode((string)$goods['GDSNO']));
                    $name_js = h(json_encode((string)$goods['GDSNAME']));
                  ?>
                  <tr>
                    <td align="center" class="text_1"><?php echo h($goods['no']); ?></td>
                    <td align="center" class="text_1"><?php
                      if ($goods['GDSNO'] != "" && $goods['GDSNO2'] != "") {
                        echo h($goods['GDSNO']) . '<br>' . h($goods['GDSNO2']);
                      } else {
                        echo h($goods['GDSNO'] . $goods['GDSNO2']);
                      }
                    ?></td>
                    <td align="center" class="text_1"><?php echo h($goods['GDSNAME']); ?></td>
                    <td align="center" class="text_1"><?php echo h(shortDesc($goods['GDSDESC'])); ?>&nbsp;</td>
                    <td align="center" class="text_1">
                      <?php if ($goods['ISHOW'] == "0") { ?>
                      <input type="button" onClick="setShow(<?php echo $no_js; ?>, '1')" value="下架">
                      <?php } else { ?>
                      <input type="button" onClick="setShow(<?php echo $no_js; ?>, '0')" value="上架">
                      <?php } ?>
                    </td>
                    <td align="center" class="text_1">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $gds_js; ?>)" />
                      <input type="button" class="btn_text" value="更新庫存" onClick="setStock(<?php echo $name_js; ?>, <?php echo $gds_js; ?>)" />
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($goodsList)) { ?>
                  <tr><td colspan="6" align="center" class="text_1">沒有符合的商品</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $goodsClass->showPageMenu($argument); ?>
              </span></td>
            </tr>
          </table></td>
        </tr>
      </table>
      <br />
    </div></td>
  </tr>
</table>
</body>
</html>
