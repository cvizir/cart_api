<?php
 
    @header("Content-Type:text/html; charset=utf-8"); 
    require_once("config.php");
	
	
	
	$index_err=0;
	$regex='/^([0-9A-Za-z]+)$/';
	$order_code=addslashes($_REQUEST['order_code']);
  $member_code=addslashes($_REQUEST['member_code']);


  if(preg_match($regex, $order_code, $result) && preg_match($regex, $member_code, $result) && strlen($order_code)=='11' && strlen($member_code)=='11'){
		$sql_orderlist="SELECT * FROM `orderlist` WHERE `order_code`='$order_code'";
		$rs_orderlist=mysql_query($sql_orderlist);
		$num_orderlist=mysql_num_rows($rs_orderlist);
		$row_order=mysql_fetch_array($rs_orderlist,MYSQL_ASSOC);
    $order_all_list=json_decode($row_order['order_info'],true);
	}

	
	//print_r($order_all_list);
	
?>

<table width="800"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="100"><a href="https://shopping.windmill.com.tw/"><img src="http://shopping.windmill.com.tw/imgs/logo.png" alt="風車寶貝線上購物網" width="200" height="100" style="border:0; display:block" /></a></td>
  </tr>
  <tr>
    <td height="100"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100" align="center" valign="middle" bgcolor="#e1f0f8" style="font-size:16px;border-right-color:#CCC; border-right-width:1px; border-right-style:dotted;"><span style="font-size:16px;line-height:10px;"><b>感謝您在 風車寶貝童書網!</b></span> <br />
          <br />
          <b>以下是您的訂單內容，感謝您的支持。</b></td>
        <td bgcolor="#e1f0f8" style="font-size:16px;padding-left:10px;"><p><b>有訂單相關問題嗎？</b></p>
          <p><b>Email:<span style="color:#09F;"><a href="mailto:service@windmill.com.tw">service@windmill.com.tw</a></span></b></p></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>
    <table width="800" height="59" border="0" align="center" cellpadding="3" cellspacing="0" >
      <tr>
        <td width="800" bgcolor="#FFFFFF">
        <table width="800" border="0" align="left" cellpadding="0" cellspacing="0" >
  <tr>
    
  </tr>
  <tr>
    <td height="102" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF" style="font-size:16px"><p><b><span style="font-size:18px">
    訂單編號&nbsp;:&nbsp;<?php echo 'C17'.sprintf("%06d",$row_order['no']); ?></span><br /><br />
    下單時間：<?php echo $row_order['buytime']; ?></p></td>
  </tr>
  <?php if($order_all_list['order_list']!=''){ ?>
  <tr>
    <td height="102" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF" style="font-size:16px">
    <table width="800"   border="0" cellpadding="0" cellspacing="0" style="border:1px #eaeaea solid;table-layout:fixed;word-wrap:break-word; ">
    <tr>
    <td width="490" height="30" bgcolor="#eaeaea" style="padding-left:10px;"><b>產品</b></td>
    <td width="90" bgcolor="#eaeaea" style="padding-left:10px"><b>會員價</b></td>
    <td width="100" align="center" bgcolor="#eaeaea"><b>數量</b></td>
    <td width="90" align="right" bgcolor="#eaeaea" style="padding-right:10px"><b>小計</b></td>
    </tr>
    <?php 
    for( $I=0; $I < (@sizeof($order_all_list['order_list'])); $I++ ){
    $key_no=@key($order_all_list['order_list']);  
    //echo '$key_no='.$key_no;
    $pd_name=$order_all_list['order_list']["$key_no"]['pd_name'];
	$pd_price=$order_all_list['order_list']["$key_no"]['pd_price'];
	$pd_num=$order_all_list['order_list']["$key_no"]['pd_buy_num'];
    ?>
    <tr>
    <td width="490" height="24" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_name; ?></td>
    <td width="90" style="padding-left:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo $pd_price; ?></td>
    <td width="100" align="center" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $pd_num; ?></td>
    <td width="90" align="right" style="padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo ($pd_num*$pd_price); ?></td>
    </tr>
    
		<?php 
		if(@$order_all_list['addpd'][$key_no]!=''){
        $addpd_array=$order_all_list['addpd'][$key_no];
        for( $J=0; $J < @sizeof($addpd_array); $J++ ){
        $key_no2=@key($addpd_array); 
    
        $addpd_name='&nbsp;&nbsp;(加價購)-'.$addpd_array["$key_no2"]['addpd_name'];
        $addpd_price=$addpd_array["$key_no2"]['addpd_price'];
        $addpd_num=1;
        ?>
        <tr>
        <td height="24" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $addpd_name; ?></td>
        <td style="padding-left:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;">$<?php echo $addpd_price; ?></td>
        <td align="center" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;"><?php echo $addpd_num; ?></td>
        <td align="right" style="padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;">$<?php echo ($addpd_num*$addpd_price); ?></td>
        </tr>
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
    <tr>
    <td height="36" colspan="4" valign="bottom" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:solid; color:#F60; font-size:16px;">特賣活動</td>
    </tr>
    <tr>
        <td height="30" bgcolor="#eaeaea" style="padding-left:10px;"><b>產品</b></td>
        <td bgcolor="#eaeaea" style="padding-left:10px"><b>會員價</b></td>
        <td align="center" bgcolor="#eaeaea"><b>數量</b></td>
        <td align="right" bgcolor="#eaeaea" style="padding-right:10px;"><b>小計</b></td>
    </tr>
	<?php 
    for( $I=0; $I < (sizeof($order_all_list['sp_sell'])); $I++ ){
    $key_no=@key($order_all_list['sp_sell']);  
    //echo '$key_no='.$key_no;
    $sp_sell_name=@$order_all_list['sp_sell']["$key_no"]['pd_name'];
    $sp_sell_price=@$order_all_list['sp_sell']["$key_no"]['pd_price'];
    $sp_sell_num=@$order_all_list['sp_sell']["$key_no"]['pd_buy_num'];
    ?>
    <tr>
        <td height="24" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_name; ?></td>
        <td style="padding-left:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo $sp_sell_price; ?></td>
        <td align="center" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $sp_sell_num; ?></td>
        <td align="right" style="padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo ($sp_sell_num*$sp_sell_price); ?></td>
    </tr>
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
    <tr>
    <td height="36" colspan="4" valign="bottom" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:solid; color:#F60; font-size:16px">
    	幾本幾折-<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['act_sell_name']; ?>
       
    </td>
    </tr>
    <tr>
      <td width="490" height="30" bgcolor="#eaeaea" style="padding-left:10px;"><b>產品</b></td>
   
        <td width="90" bgcolor="#eaeaea" style="padding-left:10px"><b><?php
		 $num_discount=$order_all_list['act_sell_info'][$act_sell_code]['num_discount'];
		 		
		$book_num=$order_all_list['act_sell_info'][$act_sell_code]['book_num'];
		  
	    if ($book_num<$num_discount[0]){ echo "會員價";}else {
  echo "定價";
}?>  </b></td>
	
        <td width="100" align="center" bgcolor="#eaeaea"><b>數量</b></td>
        <td width="90" align="right" bgcolor="#eaeaea" style="padding-right:10px;"><b>小計</b></td>
    </tr>
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
    <tr>
        <td width="490" height="24" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_name; ?></td>
        <td width="90" style="padding-left:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo $num_sell_price; ?></td>
        <td width="100" align="center" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num_sell_num; ?></td>
        <td width="90" align="right" style="padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo ($num_sell_num*$num_sell_price); ?></td>
    </tr>
	<?php 
	@next($order_all_list['num_sell'][$act_sell_code]);
	}
	?>
    <tr>
    <td height="24" colspan="4" style="padding-left:10px; word-break: break-all; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">
    (折扣採定價金額計算，依活動折扣計價)</span>&nbsp;&nbsp;共&nbsp;<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['book_num']; ?>&nbsp;本 &nbsp;&nbsp;
      折扣&nbsp;: -<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['discount_money']; ?>&nbsp;&nbsp;&nbsp;
      商品小計&nbsp;:&nbsp;<?php echo @$order_all_list['act_sell_info'][$act_sell_code]['amount']; ?>
      </td>
    </tr>      
    <?php 
	@next($order_all_list['act_sell_info']);
    }
	?>
    <?php } ?>
    
    <?php if($order_all_list['order_addpd']!=''){ ?>
    <tr>
    <td height="36" colspan="4" valign="bottom" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:solid; color:#F60; font-size:16px;">贈品</td>
    </tr>
    <tr>
        <td height="30" colspan="4" bgcolor="#eaeaea" style="padding-left:10px;"><b>產品</b></td>
        </tr>
	<?php 
	$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['order_addpd']))."') AND `ishow`='1'";
	//echo"$sql_product";
	$rs_product=mysql_query($sql_product);
	$num_product=mysql_num_rows($rs_product);
	while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
    ?>
    <tr>
        <td height="24" colspan="4" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $row_product['name']; ?></td>
        </tr>
    <?php 

    }
	?>
    <?php }?>
    
    <?php if($order_all_list['m_addpd']!=''){ ?>
    <tr>
    <td height="36" colspan="4" valign="bottom" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:solid; color:#F60; font-size:16px;">滿額加價購</td>
    </tr>
    <tr>
      <td width="490" height="30" bgcolor="#eaeaea" style="padding-left:10px;"><b>產品</b></td>
        <td width="90" bgcolor="#eaeaea" style="padding-left:10px"><b>加購價</b></td>
        <td width="100" align="center" bgcolor="#eaeaea"><b>數量</b></td>
        <td width="90" align="right" bgcolor="#eaeaea" style="padding-right:10px;"><b>小計</b></td>
    </tr>
	<?php 
	for( $J=0; $J < @sizeof($order_all_list['m_addpd']); $J++ ){ 
	$key=@key($order_all_list['m_addpd']);
	
    $name=@$order_all_list['m_addpd']["$key"]['name']; 
    $price=@$order_all_list['m_addpd']["$key"]['price']; 
    $num=@$order_all_list['m_addpd']["$key"]['pd_buy_num'];
    ?>
    <tr>
        <td width="490" height="24" style="padding-left:10px; padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $name; ?></td>
        <td width="90" style="padding-left:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo $price; ?></td>
        <td width="100" align="center" style="border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted"><?php echo $num; ?></td>
        <td width="90" align="right" style="padding-right:10px; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">$<?php echo ($price*$num); ?></td>
    </tr>
    <?php 
	@next($order_all_list['m_addpd']);
    }
	?>
    <?php }?> 
    
      <tr>
        <td colspan="4">
          <table width="800" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="650" height="26" align="right"><b>商品合計：</b></td>
              <td width="150" height="26" align="right" style="padding-right:10px;">$<?php echo $order_all_list['total_price'] ?></td>
            </tr>
            <?php if($order_all_list['total_m_addpd']>0){ ?>  
            <tr>
              <td height="26" align="right"><b>滿額加價購：</b></td>
              <td height="26" align="right" style="padding-right:10px;">$<?php echo @$order_all_list['total_m_addpd']; ?></td>
            </tr>
            <?php } ?>
           <?php if($order_all_list['promo']['promo_total']!=0){ ?> 
            <tr>
              <td height="26" align="right" ><b>滿額折抵：</b></td>
              <td height="26" align="right" style="padding-right:10px;">-$<?php echo @$order_all_list['promo']['promo_total'] ?></td>
              </tr>
               <?php }?>
               <?php if($order_all_list['gift_card']['gift_price']!=0){ ?>
            <tr>
              <td height="26" align="right" ><b>折價券折抵：</b></td>
              <td height="26" align="right" style="padding-right:10px;">-$<?php echo @$order_all_list['gift_card']['gift_price'] ?></td>
              </tr>
               <?php }?>
            <?php if($row_order['pay_mode_price']>0){ ?>  
            <tr>
              <td height="26" align="right"><b>超商付款手續費：</b></td>
              <td height="26" align="right" style="padding-right:10px;">$<?php echo @$row_order['pay_mode_price']; ?></td>
            </tr>
            <?php } ?>
             <?php if($row_order['other_price']!=0){ ?>
            <tr>
              <td height="26" align="right"><b><?php echo @$row_order['other_ps']; ?>：</b></td>
              <td height="26" align="right" style="padding-right:10px;">$<?php echo @$row_order['other_price']; ?></td>
            </tr>
                 <?php }?>
                 <?php if($row_order['transportation']!=0){ ?> 
            <tr>
              <td height="26" align="right"><b>運費：</b></td>
              <td height="26" align="right" style="padding-right:10px;">$<?php echo @$row_order['transportation']; ?></td>
              </tr>
               <?php }?>
            <tr>
              <td height="27" align="right"><b>總計：</b></td>
              <td height="27" align="right" style="padding-right:10px;">$<?php echo @$row_order['total_amount']; ?></td>
              </tr>
  </table>
          </td>
      </tr>
      </table>
     <table width="800" border="0" cellpadding="0" cellspacing="0"  style="margin-top:20px">
  <tr>
    <td width="400"><table width="400" border="0" cellspacing="0">
  <tr>
    <td style=" color: #3696cd; padding-left:10px">配送地址<span style="color: #0000FF;padding-left:5px">【
        <?php if($row_order['invoice_country']=='tw'){
	echo str_replace("tw", "Taiwan", $row_order['invoice_country']);}else{
	echo str_replace("+", " ", $row_order['invoice_country']);} ?>
        】</span></td>
  </tr>
  <tr>
    <td style="padding-left:10px"><?php echo mb_substr( $row_order['invoice_name'],0,1,"utf-8").'**'; ?></td>
  </tr>
  <?php 
  	$addr_mark= mb_substr( $row_order['invoice_addr'],0,3,"utf-8").'*****'.mb_substr($row_order['invoice_addr'],-2,2,"utf-8");
  	if($row_order['invoice_country']=='tw'){
	$addr=$row_order['invoice_zipcode'].' '.$row_order['invoice_city'].$row_order['invoice_area'].$addr_mark;
	}else{
	$addr=$addr_mark;
	}
	 
  ?>
  <tr>
    <td style="padding-left:10px"><?php echo $addr; ?></td>
  </tr>
  <tr>
    <td style="padding-left:10px">TEL:<?php echo mb_substr( $row_order['invoice_tel'],0,3,"utf-8").'*****'.mb_substr( $row_order['invoice_tel'],-2,2,"utf-8"); ?></td>
  </tr>
</table>
    </td>
    <td width="400">
    <table width="400" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><span style=" color: #3696cd">配送方式</span></td>
  </tr>
  <?php 
  if($row_order['pre_post_date']!='' && $row_order['pre_post_date']!='0000-00-00'){
  ?>
  <tr>
    <td>預定寄送日期 ： <?php echo $row_order['pre_post_date']; ?></td>
  </tr>
  <?php } ?>
  
  <tr>
    <td><?php echo $get_mode_array[$row_order['get_mode']]; ?></td>
  </tr>
  <tr>
    <td><?php echo $row_order['transportation_ps']; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
    </td>
  </tr>
  <tr>
    <td height="19" valign="top">
    <table width="400" border="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px">
      <tr>
        <td valign="top" style=" color: #3696cd; padding-left:10px">發票資訊</td>
      </tr>
      <tr>
        <td valign="top" style="padding-left:10px"><?php echo $invoice_type_array[$row_order['invoice_type']]; ?></td>
      </tr>
      <?php if($row_order['invoice_type']=='3'){  ?>
      <tr>
        <td valign="top" style="padding-left:10px">發票抬頭：<?php echo $row_order['invoice_title']; ?></td>
      </tr>
      <tr>
        <td valign="top" style="padding-left:10px">統一編號：<?php echo $row_order['invoice_num']; ?></td>
      </tr>
      <?php }?>
    </table></td>
    <td><table width="400" border="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px">
      <tr>
        <td style=" color: #3696cd">付款方式</td>
      </tr>
      <tr>
        <td><?php echo $pay_mode_array[$row_order['pay_mode']]; ?></td>
      </tr>
      <?php if($row_order['pay_mode']=='store_pay'){  ?>
      <tr>
        <td>超商代碼&nbsp;:&nbsp;<?php echo $row_order['store_num']; ?></td>
      </tr>
      <tr>
        <td>繳費期限&nbsp;:&nbsp;<?php echo date('Y-m-d H:i:s', strtotime($row_order['buytime'] . ' +7 day')); ?></td>
      </tr>
          <tr>
        <td>繳費超商&nbsp;:&nbsp;7-11/全家/萊爾富/OK</td>
      </tr>
       <tr>
        <td>(繳費期限為7天，請務必於期限內進行繳款)</td>
      </tr>
      <?php }?>
      <?php if($row_order['pay_mode']=='post'){  ?>
      <tr>
        <td>郵政劃撥帳號：14957898</td>
      </tr>
      <tr>
        <td>戶名：三暉圖書發行有限公司</td>
      </tr>
      <?php }?>
      <?php if($row_order['pay_mode']=='atm'){  ?>
      <tr>
        <td>銀行：台北富邦商業銀行(瑞湖分行)</td>
      </tr>
      <tr>
        <td>代碼：012</td>
      </tr>
      <tr>
        <td>帳號：741102000706</td>
      </tr>
      <?php }?>
    </table></td>
  </tr>
  <tr>
    <td height="20" colspan="2"><table width="100%" border="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px">
      <tr>
        <td valign="top" style=" color: #3696cd; padding-left:10px">購物備註</td>
      </tr>
      <tr>
        <td valign="top" style="padding-left:10px"><?php echo $row_order['ps']; ?></td>
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
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="80" align="center" valign="middle" style="color:#3696c2; font-size:22px">Thank you,&nbsp;風車寶貝童書網!</td>
  </tr>
</table>

