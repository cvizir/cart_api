<?php
	
	
	//會員資格確認
	function api_mem_check($mem_id,$psw,$mphone){
		$sql_list="SELECT * FROM `member` WHERE `email`='$email' AND `mem_id`='$mem_id' AND `psw`='$psw' AND `mphone`='$mphone'";
		$rs=mysql_query($sql_list);
		$num=@mysql_num_rows($rs);	
		if($num==1){
			return true;
		}else{
			return false;			
		}
	}
	
	


?>
