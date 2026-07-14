<?php 
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$no=$_REQUEST['no'];
	$order_code=$_REQUEST['order_code'];	
	$order_state=$_REQUEST['order_state'];

	
	if($no!='' && $order_code!='' && $order_state!=''){
		$sql_update="UPDATE `orderlist` SET `order_state` = '$order_state' WHERE `no` ='$no' AND `order_code` ='$order_code'";
		mysql_query($sql_update);
	}
	
	echo'sql_update='."$sql_update";
?>
