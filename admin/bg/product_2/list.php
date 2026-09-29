<? 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=20;	
	
	$sh_post="";
	//上下頁參數
	$argument= "$sh_post";	
	$trace=0;
 	$order=" ORDER BY no DESC";
	
	$tag_no = $_GET["tag"];
 	$productClass = new DBClass('product',$page, $pagesize);
	$productList = $productClass->getAdminList($where,$order,$trace);
	$total = $productClass->total;//count($productList) 

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE?></title>
<script language="JavaScript" type="text/JavaScript">

    sh_post='<?=$sh_post?>';
	function onDel(no,name){
		if(window.confirm("確定要刪除 [ "+name+" ] 這筆資料嗎?")){
	 	window.location.href="save.php?cms_mode=del&total=<?php echo "$total" ?>&product_code="+no+sh_post;
			return true;
		}
		return false;
	}
    function onEdit(no){	 	
	 	window.location.href="edit.php?cms_mode=edit&product_code="+no+sh_post;
	}
    function onAdd(){	 	
	 	window.location.href="edit.php?cms_mode=add"+sh_post;
	}

</script>
<link href="../style.css" rel="stylesheet" type="text/css" />
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
            <table width="806" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="798"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td class="tb_menu_title">商品管理</td>
                      <td align="right" class="tb_menu_title"><span class="tb_list_title_bt">
                        <input name="Submit" type="button" value="新增" onClick="onAdd()" />
                      </span></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="89%">
                  <tr>
                    <td valign="bottom"><div align="right">
                        <table width="790" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="412" height="20" valign="middle">&nbsp;</td>
                            <td width="358" valign="middle">&nbsp;</td>
                          </tr>
                        </table>
                    </div></td>
                  </tr>
                  <tr>
                    <td align="center" valign="top"><form action="" method="post" name="form1" id="form1">
                        <table width="792" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="93" height="28" class="tb_list_title">序號</td>
                            <td width="332" align="left" class="tb_list_title">商品名稱</td>
                            <td width="100" class="tb_list_title">上下架</td>
                            <td width="135" class="tb_list_title">編輯</td>
                          </tr>
                          <?
                          $no = ($page-1) * $pagesize;
                          while($list_info=mysql_fetch_array($productList,MYSQL_ASSOC)){
                          $no++;
                          ?>
                          <tr>
                            <td class="tb_list_info"><?php echo $no; ?></td>
                            <td class="tb_list_info" style="text-align:left;"><?php echo $list_info['name']; ?></td>
                            <td class="tb_list_info"><?php echo getIshow($list_info['ishow']); ?></td>
                            <td class="tb_list_info">
                            <input name="Submit2" type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit('<?=$list_info['product_code'];?>')" />
                            <input name="Submit2" type="button" class="btn_text" value="刪除" onClick="onDel('<?=$list_info['product_code'];?>','<?=$list_info['name'];?>')" />
                            </td>
                          </tr>
                          <? }?>
                        </table>
                    </form></td>
                  </tr>
                  <tr>
                    <td align="center" class="page"><span class="wd_white_12">
                      <?php echo $productClass->showPageMenu($argument)?>
                    </span> </td>
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





