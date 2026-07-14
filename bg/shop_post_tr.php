<?php 
    session_start();    
    require_once("config.php");
	
	if($_SESSION['member']['no']!=''){
		if($_REQUEST['invoice_name']!=''){ $_SESSION['post_info']['invoice_name']=$_REQUEST['invoice_name']; }
		if($_REQUEST['invoice_tel']!=''){ $_SESSION['post_info']['invoice_tel']=$_REQUEST['invoice_tel']; }
		if($_REQUEST['invoice_zipcode']!=''){ $_SESSION['post_info']['invoice_zipcode']=$_REQUEST['invoice_zipcode']; }
		if($_REQUEST['invoice_country']!=''){ $_SESSION['post_info']['invoice_country']=$_REQUEST['invoice_country']; }
		if($_REQUEST['invoice_city']!=''){ $_SESSION['post_info']['invoice_name']=$_REQUEST['invoice_city']; }
		if($_REQUEST['invoice_area']!=''){ $_SESSION['post_info']['invoice_name']=$_REQUEST['invoice_area']; }	
		if($_REQUEST['receives_city']!=''){ $_SESSION['post_info']['receives_city']=$_REQUEST['receives_city']; }
		
		if($_REQUEST['receives_name']!=''){ $_SESSION['post_info']['receives_name']=$_REQUEST['receives_name']; }
		if($_REQUEST['email']!=''){ $_SESSION['post_info']['email']=$_REQUEST['email']; }
		if($_REQUEST['receives_tel']!=''){ $_SESSION['post_info']['receives_tel']=$_REQUEST['receives_tel']; }
		if($_REQUEST['receives_mphone']!=''){ $_SESSION['post_info']['receives_mphone']=$_REQUEST['receives_mphone']; }
		if($_REQUEST['receives_zipcode']!=''){ $_SESSION['post_info']['receives_zipcode']=$_REQUEST['receives_zipcode']; }		
		if($_REQUEST['receives_area']!=''){ $_SESSION['post_info']['receives_area']=$_REQUEST['receives_area']; }
		if($_REQUEST['receives_city']!=''){ $_SESSION['post_info']['receives_city']=$_REQUEST['receives_city']; }
		if($_REQUEST['receives_country']!=''){ $_SESSION['post_info']['receives_country']=$_REQUEST['receives_country']; }
		if($_REQUEST['receives_addr']!=''){ $_SESSION['post_info']['receives_addr']=$_REQUEST['receives_addr']; }
		if($_REQUEST['ps']!=''){ $_SESSION['post_info']['ps']=$_REQUEST['ps']; }
		if($_REQUEST['transportation']!=''){ $_SESSION['post_info']['transportation']=$_REQUEST['transportation']; }
	}else{
		$msg='請先登入會員!!';
		$web_url='mem_login.php';
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LeRêve Paris Inc. Shop</title>
<script language="JavaScript" type="text/JavaScript">
 	<?php if($msg!=''){ ?>alert("<?=$msg?>");<?php }?>
	//window.location.href="<?php echo $web_url; ?>";
</script>    
</body>
</html>