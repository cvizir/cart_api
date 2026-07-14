<?php
ob_start();
session_start();
header("Content-Type:text/html;charset=utf-8");
require_once("../config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="三峽弘道養雞場" />
<meta name="keywords" content="三峽弘道養雞場,黑羽,放山閹雞養殖場,金鳳滴雞精,手工雞精,黑毛土雞">
<meta name="description" content="三峽弘道養雞場,黑羽,放山閹雞養殖場,金鳳滴雞精,手工雞精,黑毛土雞,海中天餐飲時尚館、馥園餐廳、貓空清心茗茶餐廳、貓空大茶壺1F茶餐廳、三峽海霸船餐廳、三峽龍水魚餐廳……皆使用品質優良的放山閹雞，土雞，作為招牌菜色！防治所定期檢驗，品質安全無虞，絕無金屬農藥殘留，無抗生素，無生長激素。
日常全以玉米、大豆粉為飼料，絕對不添加生長激素及抗生素">
<title>管理者帳號設定</title>
<script type="text/javascript">
function chk(){
	if((document.admin.admin_mail.value!="")&&(document.admin.admin_mail.value.indexOf("@")!=-1)){
		document.admin.submit();
	}else{
		alert("請填入正確格式之EMAIL");
	}
}
</script>
</head>

<body>
<?php 
if(($_POST["admin_id"]!="")&&($_POST["admin_psw"]!="")&&($_POST["admin_mail"]!="")){
	$sql_str="select * from admin where admin_id='".$_POST["admin_id"]."'";
	$res_str=mysql_query($sql_str);
	@$num_str=mysql_num_rows($res_str);
	if($num_str<1){
		$sql_doo="insert into admin values('".$admin_no."','".$_POST["admin_id"]."','".$_POST["admin_psw"]."','".$_POST["admin_mail"]."')";
		$res_doo=mysql_query($sql_doo);
		if($res_doo){
			echo "<script language=javascript>
				alert('您已成功加入會員,請登入您的帳號!!');
				window.location='index.php';
				</script>";
		}
	}else{
		echo "<script language=javascript>
				alert('帳號重複!!');
				window.location='admin.php';
				</script>";
		die();
	}
}
?>
<form id="admin" name="admin" method="post" action="">
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">管理者帳號設定</td>
    </tr>
    <tr>
      <td align="right">ID：</td>
      <td><input name="admin_id" type="text" class="form_text" id="admin_id" /></td>
    </tr>
    <tr>
      <td align="right">PASSWORD：</td>
      <td><input name="admin_psw" type="password" class="form_text" id="admin_psw" /></td>
    </tr>
    <tr>
      <td align="right">E-MAIL：</td>
      <td><input type="text" name="admin_mail" class="form_text" id="admin_mail" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input name="Submit" type="submit" class="form_button" value="LOGIN" onclick="chk();" /></td>
    </tr>
  </table>
</form>
</body>
</html>
