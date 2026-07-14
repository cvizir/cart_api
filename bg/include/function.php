<?php
	function start_session($expire = 0){
		if ($expire == 0) {
			$expire = ini_get('session.gc_maxlifetime');
		} else {
			ini_set('session.gc_maxlifetime', $expire);
		}
	
		if (empty($_COOKIE['PHPSESSID'])) {
			session_set_cookie_params($expire);
			session_start();
		} else {
			session_start();
			setcookie('PHPSESSID', session_id(), time() + $expire);
		}
    }
	function cart_re_total(){
		$_SESSION['amount']=$_SESSION['total_price']-$_SESSION['promo']['promo_total']+$_SESSION['post_info']['transportation']-$_SESSION['gift_card']['gift_price']+$_SESSION['pay_mode_price'];			
		if($_SESSION['amount']<0){ $_SESSION['amount']=0; }
	}
	function cart_transportation_total(){		
		if(@in_array('health', $_SESSION['order_post_type']) || @in_array('time_limit', $_SESSION['order_post_type']) || @in_array('normal', $_SESSION['order_post_type'])){
			$_SESSION['post_info']['transportation']=0; 
		}
		if(@in_array('frozen', $_SESSION['order_post_type']) || @in_array('cold', $_SESSION['order_post_type']) || @in_array('live', $_SESSION['order_post_type']) || in_array('baking', $_SESSION['order_post_type'])){
			if(($_SESSION['total_price']-$_SESSION['promo']['promo_total'])>=2500){
				$_SESSION['post_info']['transportation']=0; 
			}else{
				if(@in_array('live', $_SESSION['order_post_type'])){
				$_SESSION['post_info']['transportation']=200;
				}else{
				$_SESSION['post_info']['transportation']=150;
				}
			}
		}
		
		if(@in_array('selftake', $_SESSION['order_post_type'])){
			$_SESSION['post_info']['transportation']=0; 
		} 

		if($_SESSION['post_info']['get_type']=='自取'){
			$_SESSION['post_info']['transportation']=0; 
		}
	}
	
	
	function inject_check($sql_str)	{
		return eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str); // 進行過濾
	}
	function checkAdmim($all_str,$check_txt,$trace) {
		if(CHECK_ADMIN=='true'){
			if($trace=='1'){ echo 'CHECK_ADMIN='.CHECK_ADMIN.'<br />'.'$all_str='.$all_str.'<br />'.'$check_txt='.$check_txt.'<br />'; }
			//如果有權限資料

			if($all_str!=''){
				//權限是否符合
				if(strExist($all_str,$check_txt)){
					return true;
					
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return true;
		}	
				
	}
	function checkAdmimPv($admin_str,$check_txt,$check_type,$trace) {
		$index_err=0;
		if($trace=='1'){ echo 'admin_str='.$admin_str.'<br />'.'$check_txt='.$check_txt.'<br />'.'$check_type='.$check_type.'<br />'; }
		
		if($check_txt!='' && $check_type!=''){
			
			if(CHECK_ADMIN==true &&  $_SESSION["admin_index"]!='root'){

				//權限是否符合
				if(strExist($admin_str,$check_txt.'_'.$check_type)){
					return true;
				}else{
					$index_err=1;
				}
				
			}	
		}else{
			$index_err=1;
		}	
		
		if($index_err=='1'){
			
			if($check_type=='save'){
				global $cms_mode,$msg,$web_url;
				$cms_mode='pv_error';	
				$msg='您的權限不足!!';	
				$web_url='../main.php';	
			}
			echo'<script language="JavaScript" type="text/JavaScript">
				alert("您的權限不足!!");
				window.location.href="../main.php";
			</script> ';  
		}	
	}
	
	function getStock($product_num,$trace) {
		$sql_product="SELECT * FROM product WHERE `product_num`='$product_num'";
		$rs_product=mysql_query($sql_product);
		$row_pd=@mysql_fetch_array($rs_product,MYSQL_ASSOC);
		return $row_pd['pd_stock'];
			
	}
	
	
	function LoginMember($account,$psw,$trace) {
		if($account!='' && $psw!='' ){
		$sql_member="SELECT * FROM `member` WHERE `account`='$account' AND `psw`='$psw'";
		//echo"$sql_member";
		$rs_member=mysql_query($sql_member);
		$num_member=mysql_num_rows($rs_member);
		if($num_member=='1'){
			$row_member=mysql_fetch_array($rs_member,MYSQL_ASSOC);
			if($row_member['email_check']=='1'){
			//print_r($row_member);
			$_SESSION['member']['no'] = $row_member['no'];
			$_SESSION['member']['member_code'] = $row_member['member_code'];
			$_SESSION['member']['psw'] = $row_member['psw'];	
			$_SESSION['member']['name'] = $row_member['name'];
			$_SESSION['member']['email'] = $row_member['email'];
			$_SESSION['member']['account'] = $row_member['account'];
			$_SESSION['member']['mem_lv'] = $row_member['mem_lv'];
			$_SESSION['member']['mem_lv_start'] = $row_member['mem_lv_start'];
			$_SESSION['member']['mem_lv_end'] = $row_member['mem_lv_end'];
			$_SESSION['member']['regtime'] = $row_member['regtime'];
			$_SESSION['member']['sex'] = $row_member['sex'];
			$_SESSION['member']['phone'] = $row_member['phone'];
			$_SESSION['member']['country'] = $row_member['country'];
			$_SESSION['member']['city'] = $row_member['city'];
			$_SESSION['member']['area'] = $row_member['area'];
			$_SESSION['member']['addr'] = $row_member['addr'];
			$_SESSION['member']['zipcode'] = $row_member['zipcode'];	
			$_SESSION['member']['birthday'] = $row_member['birthday'];
			$_SESSION['member']['email_check'] = $row_member['email_check'];	
			$_SESSION['member']['esend'] = $row_member['esend'];	
			$_SESSION['member']['wish_list'] = explode(",",$row_member['wish_list']);	
		    $_SESSION['member']['last_login'] =$row_member['last_login']; 
		    $_SESSION['member']['buy_total'] =$row_member['buy_total'];
			$_SESSION['member']['mem_mode'] =$row_member['mem_mode'];
			
			
		    $_SESSION['post_info']['invoice_name'] =$row_member['name']; 
		    $_SESSION['post_info']['invoice_email'] =$row_member['email']; 
		    $_SESSION['post_info']['invoice_tel'] =$row_member['phone']; 
		    $_SESSION['post_info']['invoice_city'] =$row_member['city']; 
		    $_SESSION['post_info']['invoice_area'] =$row_member['area']; 
		    $_SESSION['post_info']['invoice_addr'] =$row_member['addr']; 
		    $_SESSION['post_info']['invoice_zipcode'] =$row_member['zipcode'];
			$_SESSION['post_info']['invoice_country'] =$row_member['country'];
			}
		}
		}
	}
	
	
	
	
	function getPdPprice($product_num,$trace) {	
	    $now_time=date("Y-m-d H:i:s");
	    //echo"$trace";
	
		$sql_product="SELECT a.*,b.name AS pdcat_name ,c.pdcat_t_code AS pdcat_t_code ,c.name AS pdcat_t_name  FROM product a left join pdcat b on a.pdcat_code = b.pdcat_code left join pdcat_m c on b.pdcat_m_code = c.pdcat_m_code WHERE a.product_num='$product_num' AND a.ishow='1'
	";
		$rs_product=@mysql_query($sql_product);
		$num_product=@mysql_num_rows($rs_product);
		$row_pd=@mysql_fetch_array($rs_product,MYSQL_ASSOC);
		//echo'$sql_product='."$sql_product";
	
	
		$product_data=$row_pd;
	
		//判斷所屬的類型
		
		$sql_act_sell="SELECT * FROM `act_sell` WHERE ( `product_code` LIKE '%$product_num%' OR `product_price` LIKE '%$product_num%' ) AND `start_time`<='$now_time' AND `end_time`>='$now_time' AND `ishow`='1' LIMIT 1";

		$rs_act_sell=mysql_query($sql_act_sell);
		$num_act_sell=mysql_num_rows($rs_act_sell);
		$row_act_sell=mysql_fetch_array($rs_act_sell,MYSQL_ASSOC);
		$product_data['act_sell_code']=$row_act_sell['act_sell_code'];
		$product_data['act_sell_name']=$row_act_sell['name'];
		$product_data['num_discount']=$row_act_sell['num_discount'];
		if($num_act_sell==0){
			$m_action='add';	
			$product_data['m_action']='add';	
		}else{
			if($row_act_sell['m_type']=='1'){ 
				$m_action='sp_sell'; 
				$product_data['m_action']='sp_sell';	
			}
			if($row_act_sell['m_type']=='0'){ 
				$m_action='num_sell';
				$product_data['m_action']='num_sell';	 
			}	
		}
		
		if($_SESSION['member']['mem_lv']=='lv_4' || $_SESSION['member']['mem_lv']=='lv_5' || $_SESSION['member']['mem_lv']=='lv_6'){
			$m_action='add';	
			$product_data['m_action']='add';
			$product_data['act_sell_code']='';
			$product_data['act_sell_name']='';
			$product_data['num_discount']='';
		}
		
		

		//取得會員折扣價
		$sql_discount="SELECT * FROM `discount` WHERE `discount_code`='".$row_pd['discount_code']."'";
		$rs_discount=mysql_query($sql_discount);
		$num_discount=mysql_num_rows($rs_discount);
		$row_discount=mysql_fetch_array($rs_discount,MYSQL_ASSOC);
		$discount_r=$row_discount[$_SESSION['member']['mem_lv']];
		if($discount_r==''){ $discount_r=0; }
		$mem_price=floor($row_pd['price']*($discount_r)/100);

		
		//商品特賣
		if($m_action=='sp_sell'){
			
			$act_array=explode(",",$row_act_sell['product_price']);
			for( $I=0; $I<count($act_array); $I+=2){
				$price[$act_array[$I]]=$act_array[($I+1)];
				$product_code_array[]=$act_array[$I];
				$product_code_txt="('".join("','",$product_code_array)."')";
			}
			$sp_price=$price[$product_num];
			if($mem_price>=$sp_price){ $sell_price=$sp_price; }
			if($mem_price<=$sp_price){ $sell_price=$mem_price; }
		}
		if($m_action=='add'){
			$sell_price=$mem_price;
		}
		if($m_action=='num_sell'){
			$sell_price=$mem_price;
		}	
		if($_SESSION['member']['member_code']!=''){
	
			//加入購物車
			$buy_index='no';
			if($_SESSION['order_list'][$row_pd['product_code']]['pd_buy_num']!=''){
			$buy_index='yes';
			}
			if($_SESSION['sp_sell'][$row_pd['product_code']]['pd_buy_num']!=''){
			$buy_index='yes';
			}
			if($_SESSION['num_sell'][$row_act_sell['act_sell_code']][$row_pd['product_code']]['pd_buy_num']!=''){
			$buy_index='yes';
			}
			//echo'num_sell='.$_SESSION['num_sell'][$row_act_sell['act_sell_code']][$row_pd['product_code']]['pd_buy_num'];
			if($buy_index=='yes'){
			$product_data['add_cart_url']="window.location.href='cart_addshopcar.php'";
			}else{
			$product_data['add_cart_url']="window.location.href='".'cart_tr.php?m_action=add_cart&product_num='."$product_num"."'";
			}
			
			//願望清單
	
			$product_data['wish_url']="window.location.href='".'wish_tr.php?m_action=add&product_num='."$product_num"."'";
		
		}else{
			$product_data['add_cart_url']="javascript:alert('請登入會員!!');return false;";
			$product_data['wish_url']="javascript:alert('請登入會員!!');return false;";
		}


		
		$product_data['mem_lv']=$_SESSION['member']['mem_lv'];
		$product_data['price']=$row_pd['price'];
		$product_data['mem_price']=$mem_price;
		$product_data['sp_price']=$sp_price;
		$product_data['sell_price']=$sell_price;

		
		if($trace=='p'){
			$product_data['pd_info']='';
			$product_data['pd_summary']='';
			//echo"$sql_act_sell".'<br /><br />';
			//print_r($product_data);
		}
		if($trace=='all'){
			/* echo'<br /><br />';
			//echo'$sql_act_sell='.$sql_act_sell.'<br /><br />';
			//echo'$num_act_sell='.$num_act_sell.'<br /><br />';
			echo'$m_action='.$m_action.'<br />';
			echo'會員等級='.$_SESSION['member']['mem_lv'].'<br />';
			echo'原價='.$row_pd['price'].'<br />';
			echo'會員價='.$mem_price.'<br />';
			echo'特賣價='.$sp_price.'<br />';
			echo'販售價='.$sell_price.'<br />'; */
			return $product_data;
		}
		
		
		//原價
		if($trace=='price'){
			return $product_data['price']; 
		}	
		//賣價
		if($trace=='sell_price'){
			return $product_data['sell_price']; 
		}
		//會員價
		if($trace=='mem_price'){
			return $product_data['mem_price']; 
		}
		
		
		
	}
	
	
	function formatMoney($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
	  if (is_numeric($number)) { // a number
		if (!$number) { // zero
		  $money = ($cents == 2 ? '0.00' : '0'); // output zero
		} else { // value
		  if (floor($number) == $number) { // whole number
			$money = number_format($number, ($cents == 2 ? 2 : 0)); // format
		  } else { // cents
			$money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
		  } // integer or decimal
		} // value
		return '$'.$money;
	  } // numeric
	} // formatMoney

	//取得最大sort的值
	function sortMax($cms_mode,$db_name,$sortmax_where,$field_name='no',$trace){
		$sql="SELECT COUNT(1) AS total_num FROM `$db_name` $sortmax_where";
		$rs=@mysql_query($sql);
		$row=@mysql_fetch_array($rs,MYSQL_NUM);
		if($trace=='1'){
			echo"$cms_mode".'<br>';
			echo"$sql".'<br>';
		}
		if($row['0']==0 || $row['0']=='NULL'){
			return 1;	
		}else{
			if($cms_mode=='edit'){
			return $row['0'];	
			}else{
			return ($row['0']+1);
			}
			
		}

     }
	
	//取得最大sort的值
	function sortMaxNum($cms_mode,$db_name,$sortmax_where,$field_name='no',$trace){
		$sql="SELECT COUNT(1) AS total_num FROM `$db_name` $sortmax_where";
		$rs=mysql_query($sql);
		$row=mysql_fetch_array($rs,MYSQL_NUM);
		if($trace=='1'){
			echo"$cms_mode".'<br>';
			echo"$sql".'<br>';
		}
		if($row['0']==0 || $row['0']=='NULL'){
			return 1;	
		}else{
			if($cms_mode=='edit'){
			return $row['0'];	
			}else{
			return ($row['0']+1);
			}
			
		}

     }
	 
	 
	//新增資料時的sort調整
	
	function changSortSet($cms_mode,$db_name,$sort_sql_where,$field_name='m_sort',$sort_new_value,$sort_old_value,$trace){
		if($sort_new_value!=$sort_old_value){
			//新增的時候
			if($sort_old_value=='' || $sort_old_value=='0'){
				$sql_sort="UPDATE `$db_name` SET `$field_name`=$field_name+1 WHERE `$field_name` >='$sort_new_value' $sort_sql_where";
				mysql_query($sql_sort);
			}else{
				
				if($sort_new_value<$sort_old_value){
					$sql_sort="UPDATE `$db_name` SET `$field_name`=$field_name+1 WHERE `$field_name` >='$sort_new_value' AND `$field_name` <'$sort_old_value'  $sort_sql_where";
					mysql_query($sql_sort);		
				}else{
					$sql_sort="UPDATE `$db_name` SET `$field_name`=$field_name-1 WHERE `$field_name` <='$sort_new_value' AND `$field_name` >'$sort_old_value'  $sort_sql_where";
					mysql_query($sql_sort);	
				}
			}
		}
		if($cms_mode=='del' && $sort_new_value!='' && $sort_new_value!='0'){
			$sql_sort="UPDATE `$db_name` SET `$field_name`=$field_name-1 WHERE `$field_name` >'$sort_new_value' $sort_sql_where";
			mysql_query($sql_sort);
		}
		if($trace==1){echo"$sql_sort".'<br>';}
	}
		
	
	
	function strExist($o_str,$check_str){
		if (false !== ($rst = strpos(','.$o_str.',',','.$check_str.','))) {
			return true;
		} else {
			return false;
		}
	}
	function strExistPv($o_str,$check_str){
		if (false !== ($rst = strpos(','.$o_str.',',','.$check_str.','))) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	function strExist2($o_str,$check_str){
		if (false !== ($rst = strpos($o_str,$check_str))) {
			return true;
		} else {
			return false;
		}
	}

	
    //修改資料時sort調整
    function updataSortSet($db_name,$field_value_ed,$index_name,$index_value,$field_name='m_sort'){
        
		if($field_value<$field_value_ed){
			$sql_sort_add="UPDATE `$db_name` SET $field_name=$field_name+1 WHERE `$index_name`<>'$index_value' AND `$field_name` >='$field_value' AND `$field_namet` <'$field_value_ed'";
			mysql_query($sql_sort_add);
		}else{
			$sql_sort_add="UPDATE `$db_name` SET $field_name=$field_name-1 WHERE `$index_name`<>'$index_value' AND `$field_name` >'"."$field_value_ed"."' AND `m_sort` <='".$this->m_sort."'";
			mysql_query($sql_sort_add);     
		}
	}
	
	
	//將no轉成目前資料的頁碼
    function noPageTr($sql_str,$index_value){
		$rs=mysql_query($sql_str);
		$page_num="1";
        while($row=mysql_fetch_array($rs,MYSQL_NUM)){  
			if($row['0']==$index_value){
				break;
			}else{
				$page_num++;
			}
		} 
		return $page_num;
	}
	
    //以ARRAY寫入
	function updataArray($db_name,$where_str,$sql_data,$trace=0){
		if(empty($where_str)){
			return false;	
		}
		while(list($key, $value) = each($sql_data)) {
			$txt.= ",`".$key."`='".$value."'";
		}	
		$sql="UPDATE `".$db_name."` SET ";
		$sql.=substr($txt, 1);		
		$sql.= " $where_str";
		if($trace==1){ echo  '<br>updataArraySql='."$sql<br>"; }
		mysql_query($sql);			
	}
	
	
	//以ARRAY新增
	function insertArray($db_name,$sql_data,$trace=0){		
		while(list($key, $value) = each($sql_data)) {
			$txt.= ",`".$key."`='".mysql_real_escape_string($value)."'";
		}	
		$sql="INSERT INTO `".$db_name."` SET ";
		$sql.=substr($txt, 1);						
		if($trace==1){ echo '<br>insertArraySql='."$sql<br>"; }
		mysql_query($sql);
		return mysql_insert_id();
	}


	function deleteArray($db_name,$del_no,$id_no='no',$trace){
		$array_num=count(@array_diff($del_no,array('')));
		asort($del_no);
		@array_diff($del_no,array(''));
		$array_list=join(",",$del_no);			
		if($array_num=="1"){
			$where= " WHERE `$id_no`='".$del_no[0]."'";
			}else{
	
		   $where=" WHERE `$id_no` IN (".$array_list.")";
		}
			$sql="DELETE FROM `$db_name.` $where";
		if($trace=="1"){ echo '<br>deleteArraySql='."$sql<br>"; }
		mysql_query($sql);
	}

	function deleteDb($db_name,$where_str,$sql_data,$trace=0){
		$sql="DELETE FROM `$db_name` $where_str";
		mysql_query($sql);
		if($trace=="1"){ echo '<br>deleteDb='."$sql<br>"; }
		mysql_query($sql);
	}


	function alertHref($msg,$href){
		$return_code='<script language="JavaScript" type="text/JavaScript">';
			if($msg<>""){
			$return_code=$return_code.'alert("'."$msg".'");';
			}
			if($href<>""){
			$return_code=$return_code.'window.location.href="'."$href".'";';
			}else{
			$return_code=$return_code.'history.go(-1)';
			}
			$return_code=$return_code.'</script>';
		echo $return_code;
	}

	function get_chinese_time($datetime){
		$time_tr=strtotime($datetime);
		$weekday  = date('w', $time_tr);
		$weeklist = array('日', '一', '二', '三', '四', '五', '六');

		if(date("A",$time_tr)=='AM'){ $time_h='上午';}else{$time_h='下午';}

		echo date("Y年n月j日",$time_tr).' 星期'.$weeklist[$weekday]." $time_h".date("g時i分s秒",$time_tr);
	}

    //產生亂數
	function randomStr($random){
		for ($i=1;$i<=$random;$i=$i+1){	
			$c=rand(1,3);
			if($c==1){$a=rand(97,122);$b=chr($a);}//小寫
			if($c==2){$a=rand(65,90);$b=chr($a);}//大寫
			if($c==3){$b=rand(0,9);}
			$randoma=$randoma.$b;
		}
		return $randoma;
	}

    //取得唯一的代碼值
	function createCode($tb_name,$tb_filed,$code_num){
		$x = 1;
    	while ($x > 0) {
		$code_str=randomStr($code_num);
        $sql="SELECT $tb_filed FROM `$tb_name` WHERE `$tb_filed`='$code_str'";
    	$rs=mysql_query($sql);
    	$x=@mysql_num_rows($rs);
    	}
		return $code_str;
	}


	function randomStr2($random){
		for ($i=1;$i<=$random;$i=$i+1){	
			$c=rand(1,2);
			if($c==2){$a=rand(65,90);$b=chr($a);}//大寫
			if($c==1){$b=rand(0,9);}//數字
			$randoma=$randoma.$b;
		}
		return $randoma;
	}

    //取得唯一的代碼值
	function createOrderCode($tb_name,$tb_filed,$code_num){
		$x = 1;
    	while ($x > 0) {
		$code_str=randomStr2($code_num);
        $sql="SELECT $tb_filed FROM `$tb_name` WHERE `$tb_filed`='$code_str'";
    	$rs=mysql_query($sql);
    	$x=@mysql_num_rows($rs);
    	}
		return $code_str;
	}
	
	function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
		static $recursive_counter = 0;
		if (++$recursive_counter > 1000) {
			die('possible deep recursion attack');
		}
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else {
				$array[$key] = $function($value);
			}
	
			if ($apply_to_keys_also && is_string($key)) {
				$new_key = $function($key);
				if ($new_key != $key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
		$recursive_counter--;
	}

	function JSON($array) {
		 arrayRecursive($array, 'urlencode', true);
		 $json = json_encode($array);
		 return urldecode($json);
	}

	function getIshow($value){
		if($value=="1"){
			echo"上架";
		}
		if($value=="0"){
			echo"下架";
		}
		if($value=="9"){
			echo"待審查";
		}
	}
	
	
	//修正SQL符號： ' 
	function fixSql($string){
		//$string = ereg_replace ("'", "&acute;", $string); 
		$string = ereg_replace ("'", "\'", $string); 
		return $string;
	}
	//chr(13).chr(10)取代換行
	function fixEnter($string, $chr=""){
		if($chr=="") $chr=chr(2);		return ereg_replace (chr(13).chr(10), $chr, $string); 
	}
	//chr(2)??
	function fixAnd($string){
		return ereg_replace ("&", chr(2), $string); 
	}
				
	//產生數字下拉選單內容
	function getDateSelectOption($int_start, $int_end, $int_value){
		$str = "";
		for($i=$int_start; $i<=$int_end; $i++){
			$i=sprintf("%0"."$numfill"."d", $i);
			if($int_value==$i){
			$str .= "<option value='".$i."' selected=\"selected\">".$i."</option>\n";		
			}else{
			$str .= "<option value='".$i."'>".$i."</option>\n";		
			}
		}
		echo $str;
	}
	
	//產生數字下拉選單內容
	function getDateSelectOption2($int_start, $int_end, $int_value,$numfill){
		$str = "";
		for($i=$int_start; $i<=$int_end; $i++){
			$i=sprintf("%0"."$numfill"."d", $i);
			if($int_value==$i){
			$str .= "<option value='".$i."' Selected>".$i."</option>\n";		
			}else{
			$str .= "<option value='".$i."'>".$i."</option>\n";		
			}

		}
		echo $str;
	}

	
	//產生數字下拉選單內容

	//$int_value?的功用
	function getDateSelectOptionTime($int_start, $int_end, $int_value){
		$str = "";
		for($i=$int_start; $i<=$int_end; $i++){
			$i=sprintf("%02d", $i);
			$str .= "<option value='".$i."'".chkSelected2($i, $int_value).">".$i."</option>\n";	
		}
		return $str;
	}
	function getDateSelectOptionTime2($int_start, $int_end, $int_value,$ps){
		$str = "";
		for($i=$int_start; $i<=$int_end; $i++){
		   if($i==$int_value){
		   $st="selected";
		   }
		
			$str .= "<option value='".$i."'".$st.">".$i.$ps."</option>\n";	
			$st="";
		}
		return $str;
	}
	
	//讀取資料庫產生下拉式選單(資料庫名稱,選單傳送值,選單顯示文字,下拉式預設值)	     
	function getDateSelectOption3($table, $view_value, $view_name, $int_value,$where){			
			 $sql = "SELECT * FROM "."$table"." $where";	
			 $rs=mysql_query($sql);
			 while($row=mysql_fetch_array($rs)){
			 //echo $view_value;
		     	if($row["$view_value"]==$int_value){
					 $str .="<option value='".$row["$view_value"]."' Selected>".$row["$view_name"]."</option>\n";	
			 	}else{
			 		$str .= "<option value='".$row["$view_value"]."'>".$row["$view_name"]."</option>\n";		
				}
			 }
             echo $str;
	}
	
	
	//是否選擇
	function chkSelected($value1,$value2){
		if($value1==$value2){
			echo 'selected="selected"';
		}
	}
	
	function chkStrSelected($o_str,$check_str){
		if (false !== ($rst = strpos(','.$o_str.',',','.$check_str.','))) {
			echo 'selected="selected"';
		} else {
			echo '';
		}
	}
	
	function chkIshow($value1){
		if($value1=="1"){
			echo'<span class="iconok">上架</span>';
		}else{
			echo'<span class="iconno">下架</span>';
		}
	}
	
	//是否選取
	function chkChecked($value1,$value2){
		if($value1==$value2){
			echo ' checked="checked"';
		}
	}
	
	//是否選取(checkbox)
	function chkCheckbox($value,$list){
		if(empty($list)) return;		
		$list=split (",",$list);
		if(in_array($value,$list)){
			echo " checked";
		}
	}	
	
	//輸出陣列為字串
	function array_str($list){
		if($list==NULL)return "";
		if(gettype($list)=="string")return $list;		
		
		$str="";
		foreach ($list as $value){
			$str.=",".$value;
		}
		return substr($str,1);
		
	}
	
	//輸出陣列為字元
	function array_chr($list){
		if($list==NULL)return "";		
		$chr="";
		foreach ($list as $value){
			$chr.=$value;
		}
		return $chr;
	}




	//計算字串長度(中文字長度為2)
	function mb_len($str,$type="utf8"){
		$len=mb_strlen($str,$type);
		$len2=0;
		for($i=0;$i<$len;$i++){
			$len2+=(ord(mb_substr($str,$i,1,$type))<=122)?1:2;			
		}
		return $len2;
	}
	
	// 限制顯示幾個字
	function strLimit($str, $len, $moreStr){
		$len = $len * 2;		
		if(mb_strwidth($str, 'utf8') > $len) $str = mb_strimwidth($str, 0, $len, $moreStr, 'utf8');
		return $str;
	}
	
    //???
	function uniDecode($str,$charcode){
	  $text = preg_replace_callback("/%u[0-9A-Za-z]{4}/",toUtf8,$str);
	  return mb_convert_encoding($text, $charcode, 'utf-8');
	}
	
	function toUtf8($ar){
	  foreach($ar as $val){
		$val = intval(substr($val,2),16);
		if($val < 0x7F){        // 0000-007F
			$c .= chr($val);
		}elseif($val < 0x800) { // 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		}else{                // 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
	  }
	  return $c;
	}
	



	function substring($str, $start, $length){ //比较好用字符串截取函数
	$len = $length;
	if($length < 0){
	$str = strrev($str); 
	$len = -$length;
	}
	$len= ($len < strlen($str)) ? $len*3 : strlen($str)*3;
	for ($i= $start*3; $i < $len; $i ++)
	{
		   if (ord(substr($str, $i, 1)) > 0xa0)
		   {
			 $tmpstr .= substr($str, $i, 2);
			 $i++;
		   } else {
			 $tmpstr .= substr($str, $i, 1);
		   }
	}
	if($length < 0) $tmpstr = strrev($tmpstr);
	return $tmpstr;
	}





	//=======================================================================
	
	
	
	
	
	
	
	//照片上傳處理(預設路徑)
	function uploadedPhoto($uploadFile, $id, $small=NULL){
		$filePath = SITE_ROOT."/files/";
		if(! is_dir($filePath)) mkdir ($filePath , 0777);
		$newName = $id;
		uploadedImage($filePath, $uploadFile, $newName, $small);		
	}
	//照片上傳處理(自訂路徑)
	function uploadedPhotoPath($uploadFile, $id, $path, $small=NULL){
		$filePath = $path;
		if(! is_dir($filePath)) mkdir ($filePath , 0777);
		$newName = $id;
		uploadedImage($filePath, $uploadFile, $newName, $small);		
	}

	//圖片上傳處理(jpg)
	function uploadedImage($filePath, $uploadFile, $newName, $small){
		//isset 測定變數是否設定 若參數var存在則傳回true，否則傳回false。
		if(isset($newName)){
			$fileName = $filePath.$newName;
		}else{
			$fileName = $filePath.$uploadFile['name'];		
		}

		move_uploaded_file( $uploadFile['tmp_name'], $fileName);
		chmod($fileName, 0777);
		
		//產生縮圖
		if($small != NULL){	
			// 讀入原來大圖片的資料  
			$srcImg = imagecreatefromjpeg($fileName); 
			
			// 使用ImageSX()與ImageSY()函數來取得原來大圖片的寬度與高度
			$src_X = imagesx($srcImg); 
			$src_Y = imagesy($srcImg); 		
				
			// 設定小圖片的寬度與高度
			$new_W = $small[0]; 
			$new_H = $small[1]; 	
			
			//壓縮為固定比例
			if($src_X > $src_Y){
				$new_X = $new_W; 
				$new_Y = round($src_Y * $new_W / $src_X);
			}else{			
				$new_X = round($src_X * $new_H / $src_Y);
				$new_Y = $new_H;
			} 
			
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_X, $new_Y); 
						
			// 將大圖片縮小並且複製到新的空白圖片。
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
						
			// 產生最終圖片，並存檔
			imagejpeg($newImg, $filePath.$newName, 80); 
				
			// 釋放記憶體
			imagedestroy($newImg); 
		}
	
	}
	
	//圖片上傳處理(jpg)  //$small為空=直接上傳 $small不為空 $kind=w 時以寬度為基準 $kind=wh 時以比例為基準

	//照片上傳處理(自訂路徑)
	function uploadedPhotoPathx($uploadFile, $filePath, $small=NULL){
			//echo "filePath="."$filePath"."<BR>"; 
		if(! is_dir($filePath)) mkdir ($filePath , 0777);

		$fileName = "$filePath".'opx.jpg';
		
		move_uploaded_file($uploadFile, $fileName);
		chmod($fileName, 0666);
		//echo "$fileName";echo"<BR>";		
		$pic_num=count($small)-1;
		//echo"數量3＝$pic_num";echo"<BR>";
		//echo $small[0][0];echo"<BR>";	
			
					
			
		for( $I=0; $I <= $pic_num; $I++ ){
		
		if($small[$I][1]=="a"){	
			copy($fileName, $filePath.$small["$I"][0]);
			chmod($filePath.$small["$I"][0], 0666);
		}else{	
			
        	// 讀入原來大圖片的資料  
			$srcImg = imagecreatefromjpeg($fileName); 
			
			// 使用ImageSX()與ImageSY()函數來取得原來大圖片的寬度與高度
			$src_X = imagesx($srcImg); 
			$src_Y = imagesy($srcImg); 		
	
			// 設定小圖片的寬度與高度
			$new_W = $small[$I][2]; 
			$new_H = $small[$I][3]; 	



			if($small[$I][1]=="w"){	
			//如果圖寬度不超錯限制寬度直接上傳
			if($src_X<=$new_W){
			$new_X=$src_X;
			$new_Y=$src_Y;
			}
			//如果圖寬度超過限制以寬為基準
			if($src_X>$new_W){
			$new_Y=round( $src_Y * $new_W / $src_X);
			$new_X=$new_W;
			}
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_X, $new_Y); 
						
			// 將大圖片縮小並且複製到新的空白圖片。
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
						
			// 產生最終圖片，並存檔
			imagejpeg($newImg, $filePath.$small["$I"][0], 100); 
				
			// 釋放記憶體
			imagedestroy($newImg);
			}
			
		
		
			
			//壓縮為固定比例
			if($small[$I][1]=="wh"){
			if($src_X > $src_Y){
				$new_X = $new_W; 
				$new_Y = round($src_Y * $new_W / $src_X);
			}else{			
				$new_X = round($src_X * $new_H / $src_Y);
				$new_Y = $new_H;
			} 
			
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_W, $new_H); 
			$white = imagecolorallocate($newImg, 255, 255, 255);
			imagefill ($newImg, 0, 0, $white);		
			// 將大圖片縮小並且複製到新的空白圖片。
			if($src_X > $src_Y){
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
			}else{			
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
			} 
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 

						
			// 產生最終圖片，並存檔
			imagejpeg($newImg, $filePath.$small["$I"][0], 100); 
				
			// 釋放記憶體
			imagedestroy($newImg);
			}
			

			}

        }		
		
	}
	
	
	
	//圖片上傳處理(jpg)  //$small為空=直接上傳 $small不為空 $kind=w 時以寬度為基準 $kind=wh 時以比例為基準

	//照片上傳處理(自訂路徑)
	function uploadedPhotoPathR($uploadFile, $filePath,$fileType,$rotate,$small=NULL){
			//echo "filePath="."$filePath"."<BR>"; 
		if(! is_dir($filePath)) mkdir ($filePath , 0777);

		$fileName = "$filePath".'opx.jpg';
		
		move_uploaded_file($uploadFile, $fileName);
		chmod($fileName, 0666);
		//echo "$fileName";echo"<BR>";		
		$pic_num=count($small)-1;
		//echo"數量3＝$pic_num";echo"<BR>";
		//echo $small[0][0];echo"<BR>";	
			
					
			
		for( $I=0; $I <= $pic_num; $I++ ){
		
		if($small[$I][1]=="a"){	
			copy($fileName, $filePath.$small["$I"][0]);
			chmod($filePath.$small["$I"][0], 0666);
		}else{	
			
        	// 讀入原來大圖片的資料  
			if($fileType=='image/png'){
				//echo'image/png';
				$rotate_Img = imagecreatefrompng($fileName); 
			}else{
				//echo'image/jpg';			
				$rotate_Img = imagecreatefromjpeg($fileName); 
			}

			
			if($rotate==""){$rotate=360;}
			$srcImg = @imagerotate($rotate_Img, $rotate,0);
			// 使用ImageSX()與ImageSY()函數來取得原來大圖片的寬度與高度
			$src_X = imagesx($srcImg); 
			$src_Y = imagesy($srcImg); 		
	
			// 設定小圖片的寬度與高度
			$new_W = $small[$I][2]; 
			$new_H = $small[$I][3]; 	



			if($small[$I][1]=="w"){	
			//如果圖寬度不超錯限制寬度直接上傳
			if($src_X<=$new_W){
			$new_X=$src_X;
			$new_Y=$src_Y;
			}
			//如果圖寬度超過限制以寬為基準
			if($src_X>$new_W){
			$new_Y=round( $src_Y * $new_W / $src_X);
			$new_X=$new_W;
			}
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_X, $new_Y); 
						
			// 將大圖片縮小並且複製到新的空白圖片。
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
						
			// 產生最終圖片，並存檔
			if($fileType=='image/png'){
				//echo'image/png';
			imagepng($newImg, $filePath.$small["$I"][0], 9); 
			}else{
				//echo'image/jpg';			
			imagejpeg($newImg, $filePath.$small["$I"][0], 100); 
			}

				
			// 釋放記憶體
			imagedestroy($newImg);
			}


			if($small[$I][1]=="h"){	
			//如果圖高度不超錯限制高度直接上傳
			if($src_Y<=$new_H){
			$new_X=$src_X;
			$new_Y=$src_Y;
			}
			//如果圖高度超過限制以高為基準
			if($src_Y>$new_H){
			$new_Y=$new_H;
			$new_X=round( $src_X * $new_H / $src_Y);
			}
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_X, $new_Y); 
						
			// 將大圖片縮小並且複製到新的空白圖片。
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
						
			// 產生最終圖片，並存檔
			imagejpeg($newImg, $filePath.$small["$I"][0], 100); 
				
			// 釋放記憶體
			imagedestroy($newImg);
			}			
		
		
			
			//壓縮為固定比例
			if($small[$I][1]=="wh"){
			if($src_X > $src_Y){
				$new_X = $new_W; 
				$new_Y = round($src_Y * $new_W / $src_X);
			} else{			
				$new_X = round($src_X * $new_H / $src_Y);
				$new_Y = $new_H;
			}  
			
			// 依照設定的縮圖寬度與高度比例，產生一個空白的新全彩圖形 
			$newImg = imagecreatetruecolor($new_W, $new_H); 
			$white = imagecolorallocate($newImg, 255, 255, 255);
			imagefill ($newImg, 0, 0, $white);		
			// 將大圖片縮小並且複製到新的空白圖片。
			if($src_X > $src_Y){
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
			}else{			
			imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y);
			} 
			//imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $new_X, $new_Y, $src_X, $src_Y); 


			// 產生最終圖片，並存檔
			if($fileType=='image/png'){
				//echo'image/png';
			imagepng($newImg, $filePath.$small["$I"][0], 9); 
			}else{
				//echo'image/jpg';			
			imagejpeg($newImg, $filePath.$small["$I"][0], 100); 
			}
				
			// 釋放記憶體
			imagedestroy($newImg);
			}
			

			}

        }		
		
	}
	
	
	
	
	
	
	//=======================================================================
	function ecstart_check_idcard($id) {

		 $flag = false;
		 $id = strtoupper($id); //將英文字母全部轉成大寫
		 $id_len = strlen($id); //取得字元長度
	
	
		 if($id_len <= 0) {
			return false;
			exit;
		 }
		 if ($id_len > 10) {
			return false;
			exit;
		 }
		 if ($id_len < 10 && $id_len > 0) {
			return false;
			exit;
		 }
	
		 //檢查 第一個字母是否為英文字
		 $id_sub1 = substr($id,0,1); // 從第一個字元開始 取得字串
		 $id_sub1 = ord($id_sub1); // Ord回傳字串的acsii 碼(大寫英文字母編號65-90)
		 if ($id_sub1 > 90 || $id_sub1 < 65) {
			return false;
			exit;
		 }
	
		 //檢查 身份證字號的 第二個字元 男生或女生
		 $id_sub2 = substr($id,1,1);
	
		 if($id_sub2 !="1" && $id_sub2 != "2") {
			return false;
			exit;
		 }
	
		 for ($i=1;$i<10;$i++) {
			$id_sub3 = substr($id,$i,1);
			$id_sub3 = ord($id_sub3);
			if ($id_sub3 > 57 || $id_sub3 < 48) {
			   $n=$i+1;
			   return false;
			   exit;
			}
		 }
	
		 $num=array("A" => "10","B" => "11","C" => "12","D" => "13","E" => "14",
		 "F" => "15","G" => "16","H" => "17","J" => "18","K" => "19","L" => "20",
		 "M" => "21","N" => "22","P" => "23","Q" => "24","R" => "25","S" => "26",
		 "T" => "27","U" => "28","V" => "29","X" => "30","Y" => "31","W" => "32",
		 "Z" => "33","I" => "34","O" => "35");
	
		 $d1 = substr($id,0,1); // 從第一個字元開始 取得字串
		 $n1=substr($num[$d1],0,1)+(substr($num[$d1],1,1)*9);
		 $n2=0; //初使化
		 for ($j=1;$j<9;$j++) {
			$d4=substr($id,$j,1);
			$n2=$n2+$d4*(9-$j);
		 }
		 $n3=$n1+$n2+substr($id,9,1);
		 if(($n3 % 10)!= 0) {
			return false;
			exit;
		 }
		 return true;
	}


?>
