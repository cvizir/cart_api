<? 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=20;	
	$sh_post="";
	$teace=0;	
	$sh_name=$_REQUEST['sh_name'];
	
	if($sh_name!=""){	$where=$where." WHERE 1=1";	}
	if($sh_name!=""){	$where=$where."  AND `name` LIKE '%$sh_name%'"; }
	if($where!=""){ $where=str_replace('WHERE 1=1  AND', 'WHERE ', $where); }
	
	$order=" ORDER BY no DESC";
	
	
 	$memberClass = new DBClass('tmp_model',$page, $pagesize);
	$memberList = $memberClass->getAdminList($where,$order,$teace);
	$total = $memberClass->total;//count($memberList) 

	$sh_post="&page=$page&pagesize=$pagesize&total=$total".$sh_post;
    //上下頁參數
	$argument= "$sh_post";	
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE?></title>
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
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                      <td class="tb_menu_title">TMP</td>
                      <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="新增會員"  onClick="onAdd()" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td align="center" valign="top">
                <table width="90%">
                  <tr>
                    <td valign="bottom" style="padding:10px 0 10px 0;">
                    <form name="form1" method="post" action="list.php" style="margin:0px;">
                      <input name="sh_name" type="text" id="sh_name" size="15">
                      <input name="button" type="submit" id="button" value="送出">
                      <input name="Submit4" type="button" value="清除" onClick="javascript:window.location.href='list.php'" />
                    </form>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                        <table width="749" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="47" class="tb_list_title">序號</td>
                            <td width="117" class="tb_list_title">姓名</td>
                            <td width="248" class="tb_list_title">E-mail</td>
                            <td width="147" class="tb_list_title">電話</td>
                            <td width="158" class="tb_list_title">編輯</td>
                          </tr>
                          <?
                          $no = ($page-1) * $pagesize;
                          while($mem_info=@mysql_fetch_array($memberList,MYSQL_ASSOC)){
                          $no++;
                          ?>
                          <tr>
                            <td class="tb_list_info"><?php echo $no; ?></td>
                            <td class="tb_list_info"><?php echo $mem_info['name']; ?></td>
                            <td class="tb_list_info"><?php echo $mem_info['email']; ?></td>
                            <td align="left" class="tb_list_info" style="padding-left:10px; text-align:left;"><?php if($mem_info['tel']<>""){ echo '市話: '.$mem_info['tel']; } ?>							<?php if($mem_info['tel']<>""){ echo '<br>手機: '.$mem_info['mphone']; } ?></td>
                            <td class="tb_list_info">
                            <input name="Submit2" type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit('<?=$mem_info['no'];?>')" />
                            <input name="Submit2" type="button" class="btn_text" value="刪除" onClick="onDel('<?=$mem_info['no'];?>','<?=$mem_info['name'];?>')" />
                            </td>
                          </tr>
                          <? }?>
                        </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" class="page" style="padding:10px;"><?php echo $memberClass->showPageMenu($argument)?></td>
                  </tr>
                </table></td>
              </tr>
            </table>
         </td>
  </tr>
</table>
</body>
</html>





