<?php
    require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

    @header("Content-Type:text/html; charset=utf-8"); 
	require_once("../../config.php");

	
	$index_err=0;
	$regex='/^([0-9A-Za-z]+)$/';
	
	if(preg_match($regex, $_REQUEST['order_code'] , $result)) {
    	$order_code=addslashes($_REQUEST['order_code']);
    }else{
        $index_err=1;
    }
    //echo $index_err;
	if($order_code!='' && $index_err==0){
		$sql_orderlist="SELECT * FROM `orderlist` WHERE `order_code`='$order_code'";
		$rs_orderlist=mysql_query($sql_orderlist);
		$num_orderlist=mysql_num_rows($rs_orderlist);
		$row_order=mysql_fetch_array($rs_orderlist,MYSQL_ASSOC);
	}

	$order_all_list=json_decode($row_order['order_info'],true);
	//print_r($order_all_list);
	
?>

<table width="800"  border="0" align="center" cellpadding="0" cellspacing="0">
  
      <tr>
        <td width="5%" align="lift" valign="middle"><img src="http://shopping.windmill.com.tw/imgs/logo.jpg" alt="風車圖書" width="40" height="56" style="border:0; display:block" /></td>
        <td width="69%" height="60"align="left" valign="middle"></a><span style="font-size: 24px; line-height: 10px; font-family: 'Arial Unicode MS'; color: #366095;"><b>SAN HUEI PUBLISHING CO.,LTD</b></span> <br />        </td>
        <td width="26%" align="right" style="font-size: 34px; ; font-family: 'Arial Unicode MS'; color: #95B3D7; font-weight: bold;"><p><b>INVOICE</b></p></td>
      </tr>
      </table>
<table width="800" border="0" align="center"   cellpadding="0" cellspacing="0">     
      <tr>
        <td height="22" colspan="3" align="left" valign="middle" style="font-size: 14px;  font-family: 'Arial Unicode MS';">No.23-1, Ln. 392, Fude 1st Rd., Xizhi Dist., New Taipei City 221, Taiwan (R.O.C.)</td>
        <td width="109" align="left" valign="middle" style="padding-left:10px;font-size: 14px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
      </tr>
      <tr>
        <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px;  font-family: 'Arial Unicode MS';">Tel : +886-2-2695-9502</td>
         <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px;  font-family: 'Arial Unicode MS';">DATE:</td>
        <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px;  font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo substr($row_order['buytime'],0,10); ?></td>
      </tr>
      <tr>
        <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px;  font-family: 'Arial Unicode MS';">Fax: +886-2-2695-9510</td>
         <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px;  font-family: 'Arial Unicode MS';">INVOICE #:</td>
        <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px;  font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo 'C17'.sprintf("%06d",$row_order['no']); ?></span></td>
      </tr>
      <tr>
        <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px;  font-family: 'Arial Unicode MS';">Website: http://shopping.windmill.com.tw</td>
         <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px;  font-family: 'Arial Unicode MS';">CUSTOMER ID:</td>
        <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px;  font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo 'W'.sprintf($row_order['member_no']); ?></td>
      </tr>
      <tr>
       <td height="24" colspan="2" align="left" valign="middle" bgcolor="#4F81BD" style="font-size: 14px; font-family: 'Arial Unicode MS'; color: #FFF; font-weight: bold;border:1px #eaeaea solid;">BILL TO</td>
        <td align="left" valign="middle" style="padding-left:10px;font-size: 14px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
        <td align="left" valign="middle" bgcolor="#DCE6F1" style="padding-left:10px;font-size: 14px;  font-family: 'Arial Unicode MS';border:1px #eaeaea solid;">&nbsp;</td>
      </tr>
      <tr>
        <td width="380" height="24" align="left" valign="middle" style="font-size: 15px;">Name:<span style="padding-left: 5px; "><?php echo $row_order['invoice_name']; ?></span></td>
        <td width="202" align="left" valign="middle" style="font-size: 15px; font-family: 'Arial Unicode MS';">TEL:<span style="padding-left:5px"><?php echo $row_order['invoice_tel']; ?></span></td>
        <td align="left" valign="middle" style="padding-left:10px;font-size: 12px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
        <td align="left" valign="middle" style="padding-left:10px;font-size: 12px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
      </tr>
      <tr>
        <td height="24" colspan="3" align="left" valign="middle" style="font-size: 15px; "> Address:
        <?php 
  	if($row_order['invoice_country']=='tw'){
	$addr=$row_order['invoice_zipcode'].' '.$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr'];
	}else{
	$addr=$row_order['invoice_addr'];
	}
  ?><?php echo $addr; ?></td>
   <td align="left" valign="middle" style="padding-left:10px;font-size: 12px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
      </tr>
      <tr>
        <td height="24" colspan="2" align="left" valign="middle" style=" font-size: 15px; font-family: 'Arial Unicode MS';">Email:<span style="padding-left:5px"><?php echo $row_order['invoice_email']; ?></span></td>
        <td align="left" valign="middle" style="padding-left:10px;font-size: 12px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
        <td align="left" valign="middle" style="padding-left:10px;font-size: 12px;  font-family: 'Arial Unicode MS';">&nbsp;</td>
      </tr>
      </table>
<table width="800"   border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #eaeaea solid;table-layout:fixed;word-wrap:break-word;font-size: 15px;  font-family: 'Arial Unicode MS'; ">
       <tr>
         <td width="134" bgcolor="#eaeaea" ><b>Bar Code</b></td>
    <td width="427" height="30" bgcolor="#eaeaea"><b>Description</b></td>
    <td width="84" align="right" bgcolor="#eaeaea"><b>Unit Price</b></td>
    <td width="73" align="right" bgcolor="#eaeaea" ><b>Pcs.</b></td>
    <td width="80" align="right" bgcolor="#eaeaea" ><b>Total</b></td>
     </tr>
  
  <?php if($order_all_list['order_list']!=''){ 	 ;?>
   
 
    <?php 
    for( $I=0; $I < (@sizeof($order_all_list['order_list'])); $I++ ){
    $key_no=@key($order_all_list['order_list']);  
    //echo '$key_no='.$key_no;
	    $product_num=$order_all_list['order_list']["$key_no"]['product_num'];
    $pd_name=$order_all_list['order_list']["$key_no"]['pd_name'];
	$pd_price=$order_all_list['order_list']["$key_no"]['pd_price'];
	$pd_num=$order_all_list['order_list']["$key_no"]['pd_buy_num'];
    ?>
    
    <?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['order_list']["$key_no"]['product_num']))."')";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
	$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
							//echo"$sql_publishing"
    ?>
    <tr>
      <td width="134" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
    <td width="427" height="24" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_name; ?></td>
    <td width="84" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_price; ?></td>
    <td width="73" align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_num; ?></td>
    <td width="80" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($pd_num*$pd_price); ?></td>
    
   
    </tr><?php } ?>
    
		<?php 
		if(@$order_all_list['addpd'][$key_no]!=''){
        $addpd_array=$order_all_list['addpd'][$key_no];
        for( $J=0; $J < @sizeof($addpd_array); $J++ ){
        $key_no2=@key($addpd_array); 
        $addpd_num1=$addpd_array["$key_no2"]['product_num'];    
        $addpd_name='&nbsp;&nbsp;(加價購)-'.$addpd_array["$key_no2"]['addpd_name'];
        $addpd_price=$addpd_array["$key_no2"]['addpd_price'];
        $addpd_num=1;
        ?>
      <?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$addpd_num1=$addpd_array["$key_no2"]['product_num']))."')";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
    $sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
							//echo"$sql_publishing"
	?>  
        <tr>
          <td  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
        <td height="24"  style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $addpd_name; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $addpd_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $addpd_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($addpd_num*$addpd_price); ?></td>
       
        
        </tr><?php } ?>
         <?php 
        @next($addpd_array);
        }
		}
        ?>
    
    <?php 
	@next($order_all_list['order_list']);
    }
	?>
    <?php } ?>
    
    <?php if($order_all_list['sp_sell']!=''){ ?>
    
	<?php 
    for( $I=0; $I < (sizeof($order_all_list['sp_sell'])); $I++ ){
    $key_no=@key($order_all_list['sp_sell']);  
    //echo '$key_no='.$key_no;
	$sp_sell_pnum=@$order_all_list['sp_sell']["$key_no"]['product_num'];
    $sp_sell_name=@$order_all_list['sp_sell']["$key_no"]['pd_name'];
    $sp_sell_price=@$order_all_list['sp_sell']["$key_no"]['pd_price'];
    $sp_sell_num=@$order_all_list['sp_sell']["$key_no"]['pd_buy_num'];
    ?>
	<?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['sp_sell']["$key_no"]['product_num']))."')";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
   $sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
							//echo"$sql_publishing"
    ?>
    
    <tr>
      <td  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
        <td height="24"style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($sp_sell_num*$sp_sell_price); ?></td>
       
        
    </tr> <?php } ?>
    <?php 
	@next($order_all_list['sp_sell']);
    }
	?>
    <?php }?>
    
    <?php if($order_all_list['act_sell_info']!=''){ ?>
    <?php 
	for( $I=0; $I <(@sizeof($order_all_list['act_sell_info'])); $I++ ){
	$act_sell_code=@key($order_all_list['act_sell_info']);  
	?>
 
  
    <?php 
	for( $J=0; $J < @sizeof($order_all_list['num_sell'][$act_sell_code]); $J++ ){ 
	$product_code=@key($order_all_list['num_sell'][$act_sell_code]);
	 $num_sell_pnum=@
	 $order_all_list['num_sell']["$act_sell_code"]["$product_code"]['product_num']; 
	     $num_sell_name=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_name']; 
		 
		 $num_discount=$order_all_list['act_sell_info'][$act_sell_code]['num_discount'];
		 
		 $book_num=$order_all_list['act_sell_info'][$act_sell_code]['book_num'];
		 
if ($book_num<$num_discount[0])
{$num_sell_price=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['mem_price'];}else 
	{$num_sell_price=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_price'];}
	
    $num_sell_num=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_buy_num']; 
	?>
    <?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['product_num']))."')";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
   $sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
							//echo"$sql_publishing"
    ?>
    <tr>
      <td style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
        <td height="24" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_name; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($num_sell_num*$num_sell_price); ?></td>
        
        
    </tr> <?php }?>
	<?php 
	@next($order_all_list['num_sell'][$act_sell_code]);
	}
	?>
    <tr>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp; </td>
    <td height="24" style=" word-break: break-all; padding-right: 10px; border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F60;">幾本幾折-<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['act_sell_name']; ?></td>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td align="right" style=" word-break: break-all;  border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F00;">-<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['discount_money']; ?></td>
    
    
    </tr>      
    <?php 
	@next($order_all_list['act_sell_info']);
    }
	?>
    <?php } ?>
    
<?php if($order_all_list['m_addpd']!=''){ ?>
<?php for( $J=0; $J < @sizeof($order_all_list['m_addpd']); $J++ ){ 
	$key=@key($order_all_list['m_addpd']);
	
    $name=@$order_all_list['m_addpd']["$key"]['name']; 
    $price=@$order_all_list['m_addpd']["$key"]['price']; 
    $num=@$order_all_list['m_addpd']["$key"]['pd_buy_num'];
	$pdnum=@$order_all_list['m_addpd']["$key"]['product_num']; 
    ?>
      <?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$addpd_num2=$order_all_list['m_addpd']["$key"]['product_num']))."')";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
    $sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
							//echo"$sql_publishing"
	?>  
        <tr>
          <td  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
        <td height="24"  style="border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F60;"><?php echo $name; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($price*$num); ?></td>
        
        
        </tr>
<?php }?>
<?php @next($order_all_list['m_addpd']);}?>
<?php }?> 
 
 <?php if($order_all_list['order_addpd']!=''){ ?>
 <?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['order_addpd']))."') AND `ishow`='1'";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){ 
	$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
                            $rs_publishing=mysql_query($sql_publishing);
                            $num_publishing=mysql_num_rows($rs_publishing);
                            $row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
 //echo"$sql_publishing" ?>
<tr>
    <td style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted""><?php echo $row_product['bar_code']; ?></td>
        <td height="24"  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['name']; ?></td>
        <td align="right" style="border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F60;">贈品</td>
    <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">1</td>
    <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">0</td>
    
    <?php }?>
    <?php }?>


      <?php if($row_order['pay_mode_price']!=0){ ?>      <tr>
        <td height="26" align="right">&nbsp;</td>
        <td align="left">超商付款手續費</td>
        <td align="left">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right" style=""><?php echo @$row_order['pay_mode_price']; ?></td>
           
          </tr>
         <?php } ?>
      
      
              
<?php if($order_all_list['promo']['promo_total']!=0){ ?>              <tr>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="left" >活動-滿額折抵</td>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td align="right" style=" color: #F00;">-<span style="color: #F00"></span><?php echo @$order_all_list['promo']['promo_total'] ?></td>
             
              </tr>
              <?php }?>
              
<?php if($order_all_list['gift_card']['gift_price']!=0){ ?>
            <tr>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="left" >折價券-<?php echo @$order_all_list['gift_card']['name']; ?></td>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td align="right" style=" color: #F00;">-<span style="color: #F00"></span><?php echo @$order_all_list['gift_card']['gift_price'] ?></td>
             
              </tr>
              
               <?php }?>
                </table> 
  <table width="800"   border="0" align="center" cellpadding="0" cellspacing="0" style="border: 1px #eaeaea solid; table-layout: fixed; word-wrap: break-word; font-size: 15px; font-family: 'Arial Unicode MS', '微軟正黑體 Light', '新細明體-ExtB'; ">           
               
            
              <tr>
                <td width="562" height="24" align="left">&nbsp;</td>
                <td width="156" height="24" align="right">Subtotal：</td>
                <td width="80" align="right" style="border:1px #eaeaea solid;"><?php echo @$row_order['amount']; ?></td>
              </tr>
                <?php if($row_order['other_price']!=0){ ?>
              <tr>
               <td width="562" height="24" align="left">&nbsp;</td>
              <td height="24" align="right">Discount：</td>
              <td align="right" style="border:1px #eaeaea solid;"><?php echo @$row_order['other_price']; ?></td>
              
              </tr>
              <?php }?>
              
      <tr>   
               <td width="562" height="24" align="left">If you have any questions about this invoice, please contact</td>
              <td height="24" align="right">Shipping Fee：</td>
              <td width="80" align="right" style="border:1px #eaeaea solid;"><?php echo @$row_order['transportation']; ?></td>
                      
              </tr>
              
              
            <tr>
               <td width="562" height="24" align="left" >Ricky_LAN (+886-2-26959502#235)</td>
              <td height="24" align="right"><b>TOTAL：</b></td>
              <td align="right" style="border:1px #eaeaea solid;"><?php echo @$row_order['total_amount']; ?></td>
              
     </tr>
      <tr>
               <td width="562" height="24" align="left">ricky@sanhuei.com.tw</td>
              <td height="24" colspan="2" align="right"  style="font-size: 13px; ">Make all checks payable to</td>
     </tr>
      <tr>
               <td width="562" height="24" align="left" style="font-style: italic;font-weight: bold;">Thank You For Your Business!</td>
              <td height="24" colspan="2" align="right" style="font-size: 14px; ">SAN HUEI PUBLISHING CO.,LTD</td>
     </tr>
      </table>
      

      
    


