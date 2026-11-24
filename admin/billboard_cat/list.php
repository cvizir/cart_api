<?php 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=20;	
	$sh_post = $_SERVER['QUERY_STRING'];
	//上下頁參數
	$argument= "&$sh_post";	
	$trace=0;
 	$order=" ORDER BY no DESC";
	
	$tag_no = $_GET["tag"];
 	$productClass = new DBClass('billboard_cat',$page, $pagesize);
	$productList = $productClass->getAdminList($where,$order,$trace);
	$total = $productClass->total;//count($productList) 
	$billboard_ishow=array('0'=>'下架','1'=>'上架','3'=>'依時間上下架');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
    sh_post='<?=$sh_post?>';
	function onDel(no, name){
		if(window.confirm("確定要刪除 [ "+name+" ] 這筆資料嗎?")){
			window.location.href="del.php?page=<?=$page?>&pagesize=<?=$pagesize?>&total=<?=$billboardClass->total?>&no="+no;
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
   function subCategory(no,name){	 	
	 	window.location.href="../billboard/list.php?page=<?=$page?>&cat_id="+no +"&cat_name="+name;
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
                      <td class="tb_menu_title">版面管理</td>
                      <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="新增廣告"  onClick="onAdd()"  /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td align="center" valign="top"><table width="88%" >
                  <tr>
                    <td valign="bottom">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="center" valign="top"><form action="" method="post" name="form1" id="form1">
                        <table width="734" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="72" height="29" align="center" class="tb_list_title">序號</td>
                            <td width="272" align="left" class="tb_list_title"><div align="center">廣告名稱</div></td>
                            <td width="88" align="center" class="tb_list_title">編輯子類別</td>
                            <td width="98" align="center" class="tb_list_title">編輯</td>
                          </tr>
                          <?
                          $no = ($page-1) * $pagesize;
                          while($list_info=mysql_fetch_array($productList,MYSQL_ASSOC)){
                          $no++;
                          ?>
                          <tr>
                            <td align="center" class="text_1"><?=$no?></td>
                            <td align="left" class="text_1">&nbsp;<?php echo $list_info['name']; ?>
                            <?php if($list_info['web_url']!=''){  ?>
                            &nbsp;
                            <input name="Submit3" type="button" class="btn_text" value="連結" onClick="javascript:window.open('<?php echo $list_info['web_url']; ?>', '_blank') " />
                            <?php } ?>
                            </td>
                            <td align="center" class="text_1"><input name="Submit4" type="button" class="btn_text" value="編輯子類別" onClick="javascript:window.location.href='../billboard/list.php?bd_id=<?php echo $list_info['no']; ?>&bd_name=<?php echo $list_info['name']; ?>'" /></td>
                            <td align="center" class="text_1"><input name="Submit2" type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit('<?php echo $list_info['no']; ?>')" /></td>
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





