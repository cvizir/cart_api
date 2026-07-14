<?php
@session_start();

if ($_SESSION['admin_id'] == "") {
	echo '<script language="JavaScript" type="text/JavaScript">alert("請先登入");	window.location.href="' . WEB_ADMIN . '";
</script>';
}
?>