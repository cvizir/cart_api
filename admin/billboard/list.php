<?php 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

    startDB();
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=20;
    $bd_name=$_REQUEST['bd_name'];
    $bd_id=$_REQUEST['bd_id'];
	$sh_post = "&bd_name=$bd_name&bd_id=$bd_id";
	$argument= "$sh_post";	//上下頁參數
	$trace=0;
 	$order=" ORDER BY no DESC";
	$where=" WHERE `bd_id`='$bd_id'";
	$tag_no = $_GET["tag"];
 	$productClass = new DBClass('billboard',$page, $pagesize);
	$productList = $productClass->getAdminList($where,$order,$trace);
	$total = $productClass->total;//count($productList) 
	$billboard_ishow=array('0'=>'下架','1'=>'上架','3'=>'依時間上下架');
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="favicon3.ico" rel="shortcut icon">
<link href="../style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">

    sh_post='<?=$sh_post?>';
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
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php");?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <br />
            <table width="709" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="744"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td class="tb_menu_title"><?php echo "$bd_name"; ?>-版面管理</td>
                      <td class="tb_menu_title" align="right">
                        <input name="Submit3" type="button" value="回主目錄" onClick="javascript:window.location.href='../billboard_cat/list.php'" />
                                            <input name="Submit" type="button" value="新增廣告" onClick="onAdd()" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="100%" height="225">
                  <tr>
                    <td valign="bottom">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="center" valign="top"><form action="" method="post" name="form1" id="form1">
                        <table width="733" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="87" height="29" align="center" class="tb_list_title">序號</td>
                            <td width="222" align="center" class="tb_list_title">名稱</td>
                            <?php /* <td width="138" align="left" class="tb_list_title"><div align="center">圖片</div></td> */?>
                            <td width="103" align="center" class="tb_list_title"><p>上下架</p>                            </td>
                            <td width="161" align="center" class="tb_list_title">編輯</td>
                          </tr>
                      <?
                      $no = ($page-1) * $pagesize;
                      while($list_info=@mysql_fetch_array($productList,MYSQL_ASSOC)){
                      $no++;
                      ?>
                          <tr>
                            <td align="center" class="text_1"><?php echo $list_info['no']; ?></td>
                            <td align="center" class="text_1"><?php echo $list_info['name']; ?>
                            <?php if($list_info['web_url']!=''){?>
                            &nbsp;<input name="Submit4" type="button" class="btn_text" value="連結" onClick="window.open('<?php echo $list_info['web_url']; ?>','_blank');" />
							<?php } ?>
                            </td>
                            <td align="center" class="text_1"><?php echo $billboard_ishow[$list_info['ishow']];?></td>
                            <td align="center" class="text_1">
                            <input name="Submit2" type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit('<?php echo $list_info['no']; ?>')" />
							<?php if($list_info['del_sw']=='0'){  ?>
                            <input name="Submit22" type="button" class="btn_text" value="刪除" onClick="onDel('<?php echo $list_info['no']; ?>','<?php echo $list_info['name']; ?>')" />
                            <?php } ?>
                            </td>
                          </tr>
                          <? }?>
                        </table>
                    </form></td>
                  </tr>
                  <tr>
                    <td align="center" class="page"><span class="wd_white_12">
                      <?=$productClass->showPageMenu($argument)?>
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





