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

<table width="5"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="100"><a href="https://shopping.windmill.com.tw/"><img src="http://shopping.windmill.com.tw/imgs/logo.png" alt="風車寶貝線上購物網" width="200" height="100" style="border:0; display:block" /></a></td>
  </tr>
  <tr>
    <td height="100"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100" align="center" valign="middle" bgcolor="#e1f0f8" style="font-size:16px;border-right-color:#CCC; border-right-width:1px; border-right-style:dotted;"><span style="font-size:16px;line-height:10px;"><b>感謝您在 風車寶貝線上購物網購物!</b></span> <br />
          <br />
          <b>以下是您的訂單內容，感謝您的支持。</b></td>
        <td bgcolor="#e1f0f8" style="font-size:16px;"><p><b>有訂單相關問題嗎？</b></p>
          <p><b>Email:<span style="color:#09F;"><a href="mailto:service@windmill.com.tw">service@windmill.com.tw</a></span></b></p></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>
    <table width="850" height="59" border="0" align="center" cellpadding="3" cellspacing="0" >
      <tr>
        <td width="850" bgcolor="#FFFFFF">
        <table width="850" border="0" align="left" cellpadding="0" cellspacing="0" >
  <tr>
    
  </tr>
  <tr>
    <td height="51" colspan="6" align="center" valign="middle" bgcolor="#FFFFFF" style="font-size:16px"><p><b><span style="font-size:18px">
    訂單編號&nbsp;:&nbsp;<?php echo 'C17'.sprintf("%06d",$row_order['no']); ?></span><br /><br />
    下單時間：<?php echo $row_order['buytime']; ?><br /><br /></p></td>
        
        
  </tr>
  
    <tr>
       <td height="102" colspan="6" align="center" valign="middle" bgcolor="#FFFFFF" style="font-size:16px">
    <table width="850"   border="0" cellpadding="0" cellspacing="0" style="border:1px #eaeaea solid;table-layout:fixed;word-wrap:break-word; ">
       <tr>
         <td width="118" bgcolor="#eaeaea" ><b>書號</b></td>
    <td width="350" height="30" bgcolor="#eaeaea"><b>產品</b></td>
    <td width="78" align="left" bgcolor="#eaeaea"><b>出版社</b></td>
    <td width="50" align="right" bgcolor="#eaeaea"><b>價格</b></td>
    <td width="50" align="right" bgcolor="#eaeaea" ><b>數量</b></td>
    <td width="50" align="right" bgcolor="#eaeaea" ><b>小計</b></td>
    <td width="50" align="right" bgcolor="#eaeaea" ><b>單重</b></td>
    <td width="60" align="right" bgcolor="#eaeaea" ><b>小計重</b></td>
   
        </tr>
  
  <?php if($order_all_list['order_list']!=''){ 	 ;?>
    <tr>
 
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
      <td width="146" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['bar_code']; ?></td>
    <td width="358" height="24" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><a href="http://shopping.windmill.com.tw/product.php?product_num=<?php echo $product_num; ?>" target="_blank"><?php echo $pd_name; ?></td>
    <td width="68" align="left" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_publishing['name'];; ?></td>
    <td width="50" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_price; ?></td>
    <td width="50" align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_num; ?></td>
    <td width="50" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($pd_num*$pd_price); ?></td>
    <td width="65" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['pd_weight']; ?></td>
    <td width="65" align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($pd_num*$row_product['pd_weight']); ?></td>
   
    </tr>
    <?php } ?>
    
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
        <td height="24"  style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><a href="http://shopping.windmill.com.tw/product.php?product_num=<?php echo $addpd_num1; ?>" target="_blank">(單品加價)<?php echo $addpd_name; ?></td>
        <td align="left" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $row_publishing['name'];; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $addpd_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $addpd_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($addpd_num*$addpd_price); ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $row_product['pd_weight']; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($pd_num*$row_product['pd_weight']); ?></td>
        
        </tr>
        <?php } ?>
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
        <td height="24"style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><a href="http://shopping.windmill.com.tw/product.php?product_num=<?php echo $sp_sell_pnum; ?>" target="_blank">(特價活動)<?php echo $sp_sell_name; ?></td>
        <td align="left" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_publishing['name'];; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($sp_sell_num*$sp_sell_price); ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['pd_weight']; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($sp_sell_num*$row_product['pd_weight']); ?></td>
        
    </tr> 
    <?php } ?>
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
        <td height="24" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><a href="http://shopping.windmill.com.tw/product.php?product_num=<?php echo $num_sell_pnum; ?>" target="_blank">(幾本幾折)<?php echo $num_sell_name; ?></td>
        <td align="left" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_publishing['name'];; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($num_sell_num*$num_sell_price); ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['pd_weight']; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo ($num_sell_num*$row_product['pd_weight']); ?></td>
        
    </tr> 
    <?php }?>
	<?php 
	@next($order_all_list['num_sell'][$act_sell_code]);
	}
	?>
    <tr>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp; </td>
    <td height="24" style=" word-break: break-all; padding-right: 10px; border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F60;">幾本幾折-<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['act_sell_name']; ?></td>
    <td style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td height="24" style=" word-break: break-all;  border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td align="right" style=" word-break: break-all;  border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F00;">-<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['discount_money']; ?></td>
    <td colspan="2" align="right" style=" word-break: break-all;  border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F00;">&nbsp;</td>
    
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
        <td height="24"  style="border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; "><a href="http://shopping.windmill.com.tw/product.php?product_num=<?php echo $addpd_num1; ?>" target="_blank">(滿額加購)<?php echo $name; ?></td>
        <td align="left" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $row_publishing['name'];; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $price; ?></td>
        <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $num; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($price*$num); ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $row_product['pd_weight']; ?></td>
        <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo ($num*$row_product['pd_weight']); ?></td>
        
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
        <td height="24"  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">(滿額贈品)<?php echo $row_product['name']; ?></td>
        <td height="24"  style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_publishing['name'];; ?></td>
      <td align="right" style="border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; ">0</td>
    <td align="right" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">1</td>
    <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">0</td>
    <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['pd_weight']; ?></td>
    <td align="right" style=" border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo (1*$row_product['pd_weight']); ?></td>
</tr>
    <?php }?>
    <?php }?>


      <?php if($row_order['pay_mode_price']!=0){ ?>      <tr>
        <td height="26" align="right">&nbsp;</td>
        <td align="left">超商付款手續費</td>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right" style=""><?php echo @$row_order['pay_mode_price']; ?></td>
          <td width="130" height="26" colspan="2" align="right" style="">&nbsp;</td> 
          </tr>
         <?php } ?>
      
           
<?php if($row_order['transportation']!=0){ ?>   
      <tr>   
              <td width="146" height="26" align="right">&nbsp;</td>
              <td width="358" align="left">運費</td>
              <td width="68" align="left">&nbsp;</td>
              <td width="50" align="left">&nbsp;</td>
              <td width="50" align="right">&nbsp;</td>
              <td width="50" align="right" style=""><?php echo @$row_order['transportation']; ?></td>
              <td width="130" height="26" colspan="2" align="right" style="">&nbsp;</td>        
              </tr>
                <?php }?>
                
<?php if($order_all_list['promo']['promo_total']!=0){ ?>              <tr>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="left" >活動-滿額折抵</td>
              <td align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td align="right" style=" color: #F00;">-<span style="color: #F00"></span><?php echo @$order_all_list['promo']['promo_total'] ?></td>
              <td height="26" colspan="2" align="right" style="color: #F00;">&nbsp;</td>
              </tr>
              <?php }?>
              
<?php if($order_all_list['gift_card']['gift_price']!=0){ ?>
            <tr>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="left" >折價券-<?php echo @$order_all_list['gift_card']['name']; ?></td>
              <td align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td height="26" align="right" >&nbsp;</td>
              <td align="right" style=" color: #F00;">-<span style="color: #F00"></span><?php echo @$order_all_list['gift_card']['gift_price'] ?></td>
              <td height="26" colspan="2" align="right" style=" color: #F00;">&nbsp;</td>
              </tr>
               <?php }?>
               
              <?php if($row_order['other_price']!=0){ ?>
            <tr>
              <td height="26" align="right">&nbsp;</td>
              <td height="26" align="left">其他費用-<?php echo @$row_order['other_ps']; ?></td>
              <td align="right">&nbsp;</td>
              <td height="26" align="right">&nbsp;</td>
              <td height="26" align="right">&nbsp;</td>
              <td align="right" style=""><?php echo @$row_order['other_price']; ?></td>
              <td height="26" colspan="2" align="right" style="">&nbsp;</td>
              </tr>
              <?php }?>
             
 
            <tr>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="right">&nbsp;</td>
              <td align="right">&nbsp;</td>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="right"><b>總計：</b></td>
              <td align="right" style=""><?php echo @$row_order['total_amount']; ?></td>
              <td height="27" colspan="2" align="right" style="">&nbsp;</td>

      </tr>
      <?php 
  
  $sql_member="SELECT * FROM `member` WHERE `no`='".$row_order['member_no']."'";
		$rs_member=mysql_query($sql_member);
		$num_member=mysql_num_rows($rs_member);
		$row_member=mysql_fetch_array($rs_member,MYSQL_ASSOC); ?>
              <?php if($row_member['spc_discount']!="" & $row_order['other_ps']==""){ ?>
            <tr>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="left">特殊折扣：&nbsp;<?php echo @$row_member['spc_discount']; ?></td>
              <td align="left">&nbsp;</td>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="right">&nbsp;</td>
              <td align="right" style=""><?php echo round(@$order_all_list['total_price']*(@$row_member['spc_discount']/100)); ?></td> </tr>
               <tr>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="left">&nbsp;</td>
              <td align="left">&nbsp;</td>
              <td height="27" align="right">&nbsp;</td>
              <td height="27" align="right"><b>金額：</b></td>
              <td align="right" style=""><?php echo@$order_all_list['total_price']+(round(@$order_all_list['total_price']*(@$row_member['spc_discount']/100))); ?></td>
              <td height="27" colspan="2" align="right" style="">&nbsp;</td>

      </tr> <?php }?>
      </table>
     <table width="850" border="0" cellpadding="0" cellspacing="0"  style="margin-top:20px">
  <tr>
    <td width="425"><table width="425" border="0" cellspacing="0">
  <tr>
    <td style=" color: #3696cd; padding-left:10px">配送地址<span style="color: #0000FF;padding-left:5px">【
        <?php if($row_order['invoice_country']=='tw'){
	echo str_replace("tw", "Taiwan", $row_order['invoice_country']);}else{
	echo str_replace("+", " ", $row_order['invoice_country']);} ?>
        】</span></td>
  </tr>
  <tr>
    <td style="padding-left:10px"><?php echo $row_order['invoice_name']; ?></td>
  </tr>
  <?php 
  	if($row_order['invoice_country']=='tw'){
	$addr=$row_order['invoice_zipcode'].' '.$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr'];
	}else{
	$addr=$row_order['invoice_addr'];
	}
  ?>
  <tr>
    <td height="40" valign="top" style="padding-left:10px"><?php echo $addr; ?></td>
  </tr>
  <tr>
    <td style="padding-left:10px">TEL:<?php echo $row_order['invoice_tel']; ?></td>
  </tr>
  <tr>
    <td style="padding-left:10px">Email:<?php echo $row_order['invoice_email']; ?></td>
  </tr>
</table>
    </td>
      <td width="425"><table width="425" border="0" cellspacing="0">
  <tr>
  <td style=" color: #3696cd; padding-left:10px">配送方式</td>
  </tr>
  <tr>
    <td style="padding-left:10px"><?php echo $get_mode_array[$row_order['get_mode']]; ?></td>
  </tr>
  <tr>
     <td height="40" valign="top" style="padding-left:10px"><?php echo $row_order['transportation_ps']; ?></td>
  </tr>
  <tr>
     <td style="padding-left:10px">&nbsp;</td>
  </tr>
  <tr>
     <td style="padding-left:10px">&nbsp;</td>
  </tr>
  </table>
    </td>
  </tr>
  <tr>
    <td width="425">
    <table width="425" border="0" cellspacing="0" style="margin-top:10px; margin-bottom:0px">
        <tr>
          <td style=" color: #3696cd; padding-left:10px">發票資訊</td>
        </tr>
        <tr>
          <td style="padding-left:10px"><?php echo $invoice_type_array[$row_order['invoice_type']]; ?></td>
        </tr>
        <?php if($row_order['invoice_type']=='3'){  ?>
        <tr>
          <td style="padding-left:10px">發票抬頭：<?php echo $row_order['invoice_title']; ?></td>
        </tr>
        <tr>
          <td style="padding-left:10px">統一編號：<?php echo $row_order['invoice_num']; ?></td>
        </tr>
        <?php }?>
    </table></td>
    <td width="425"><table width="425" border="0" cellspacing="0" style="margin-top:10px;margin-bottom:0px">
      <tr>
        <td style=" color: #3696cd; padding-left:10px">付款方式</td>
      </tr>
        <tr>
        <td style="padding-left:10px"><?php echo $pay_mode_array[$row_order['pay_mode']]; ?></td>
      </tr>
      <?php if($row_order['pay_mode']=='store_pay'){  ?>
      <tr>
        <td style="padding-left:10px">超商代碼&nbsp;:&nbsp;<?php echo $row_order['store_num']; ?></td>
      </tr>
      <tr>
          <td style="padding-left:10px">&nbsp;</td>
        </tr>
      <?php }?>
      </table></td>
  </tr>
  <tr>
    <td height="20" colspan="2"><table width="100%" border="0" cellspacing="0" style="margin-top:10px; margin-bottom:0px">
      <tr>
        <td style=" color: #3696cd; padding-left:10px">購物備註</td>
      </tr>
      <tr>
        <td style="padding-left:10px"><?php echo $row_order['ps']; ?></td>
      </tr>
    </table></td>
    </tr>
</table>

       </td>
    </tr>
</table></td>
      </tr
    ></table> 
    </td>
  </tr>
</table>


