<?
require_once("../../include_pdo/config.inc.php");
require_once("../../include_pdo/function.php");
require_once("../../include_pdo/DBClass.php");

$page = ($_GET["page"] != "") ? $_GET["page"] : 1;
$pagesize = 5;

$sh_post = "";
//上下頁參數
$argument = "$sh_post";
$trace = 0;
$params = [];
$where = "";
$order = " ORDER BY no DESC";

$qaClass = new DBClass($pdo, 'qa', $page, $pagesize);
$qaList = $qaClass->getAdminList($where, $order, $trace, $params);
$total = $qaClass->total;
$sh_post = "&page=$page&pagesize=$pagesize&total=$total" . $sh_post;
// print_r($qaList);
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo ADMIN_TITLE; ?></title>
  <link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="101%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2"><?php require_once("../header.php"); ?></td>
    </tr>
    <tr>
      <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
      <td width="90%" valign="top">
        <br />
        <table width="724" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
          <tr>
            <td width="716">
              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                <tr>
                  <td class="tb_menu_title">Q&amp;A管理</td>
                  <td class="tb_menu_title" align="right"><input name="Submit" type="button" value="新增問題"
                      onClick="onAdd()" /></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height="300" align="center" valign="top">
              <table width="93%">
                <tr>
                  <td valign="bottom"></td>
                </tr>
                <tr>
                  <td align="center" valign="top">
                    <form action="" method="post" name="form1" id="form1">
                      <table width="692" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                        <tr>
                          <td width="94" height="28" align="center" class="tb_list_title">序號</td>
                          <td width="307" align="center" class="tb_list_title">問題</td>
                          <td width="112" align="center" class="tb_list_title">上下架</td>
                          <td width="153" align="center" class="tb_list_title">編輯</td>
                        </tr>
                        <?
                        $no = ($page - 1) * $pagesize;
                        foreach ($qaList as $list_info) {
                          $no++;
                          ?>
                          <tr>
                            <td align="center" class="tb_list_info"><?php echo $no; ?></td>
                            <td align="center" class="tb_list_info"><?php echo $list_info['question']; ?>&nbsp;</td>
                            <td align="center" class="tb_list_info"><?php echo getIshow($list_info['ishow']); ?></td>
                            <td align="center" class="tb_list_info"><input name="Submit2" type="button" class="btn_text"
                                value="檢視 / 編輯" onClick="onEdit('<?= $list_info['qa_code']; ?>')" />
                              <input name="Submit2" type="button" class="btn_text" value="刪除"
                                onClick="onDel('<?= $list_info['qa_code']; ?>','<?= $list_info['question']; ?>')" />
                            </td>
                          </tr>
                        <? } ?>
                      </table>
                    </form>
                  </td>
                </tr>
                <tr>
                  <td height="20" align="center" class="page"><span class="wd_white_12">
                      <?php echo $qaClass->showPageMenu($argument) ?> </span></td>
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

<script type="text/JavaScript">
  sh_post='<?= $sh_post ?>';
  function onDel(no, name){
    if(window.confirm("確定要刪除 [ "+name+" ] 這筆資料嗎?")){
    window.location.href="save.php?cms_mode=del&total=<?php echo "$total" ?>&qa_code="+no+sh_post;
      return true;
    }
    return false;
  };
  function onEdit(no){	 	
    alert('onEdit='+no);
    window.location.href="edit.php?cms_mode=edit&qa_code="+no+sh_post;
  };
  function onAdd(){
    alert('onAdd');
    window.location.href="edit.php?cms_mode=add"+sh_post;
  };

</script>