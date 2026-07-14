<? 
	include_once("../session.php");
	require_once("../../config.php");

	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=30;	
	
	$display=array('0'=>'隱藏','1'=>'顯示');

    $pid=$_POST['pid'];
	$CLA=$_REQUEST['CLA'];
	$GDSNO=$_REQUEST['GDSNO'];
	$bymkerno=$_REQUEST['bymkerno'];
	$dmkerno=$_REQUEST['dmkerno'];
	$dvalue=$_REQUEST['dvalue'];
	if($dmkerno<>"" && $dvalue<>""){
	$dsql="UPDATE `goodslst` SET `Discount` = '$dvalue' WHERE `MKERNO` =$dmkerno";
	mysql_query($dsql);
	}
	if($CLA<>""){
	if($CLA=="1"){
	$where=" Where CLA1NO<>'0'";
	}else{
	    if($CLA=="0"){
		$where=" Where CLA1NO='0'";
		}else{
		$cat_array=explode("-",$CLA);
			if($cat_array[2]<>"0"){
			$where=" Where CLA3NO='".$cat_array[2]."'";
			}else{
				if($cat_array[1]<>"0"){
				$where=" Where CLA2NO='".$cat_array[1]."'";
				}else{
				$where=" Where CLA1NO='".$cat_array[0]."'";
				}
			}
		}
	}
	}
    if($GDSNO<>""){
	$where=" Where `no`='$GDSNO' OR `GDSNO`='$GDSNO' OR `GDSNO2`='$GDSNO' OR `GDSNAME` LIKE '%$GDSNO%' OR `GDSDESC` LIKE '%$GDSNO%' OR `GDSDESC_2` LIKE '%$GDSNO%' OR `GDSDESC_3` LIKE '%$GDSNO%' OR `GDSDESC_4` LIKE '%$GDSNO%'"; 
	}
	if($bymkerno<>""){
	$where=" Where `MKERNO`='$bymkerno'"; 
	}
	
    //echo "$where";
	//上下頁參數
	$argument='&CLA='."$CLA".'&GDSNO='."$GDSNO".'&bymkerno='."$bymkerno";	
	
 	$goodsClass = new GoodsList($page, $pagesize);
	$goodsList = $goodsClass->getAllList2($where);
	$total = $goodsClass->total;//count($goodsList) 
    //echo "$total";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/JavaScript">

	function setStock(name, no){
	 	if(window.confirm("確定要更新 [ "+name+" ] 這筆資料嗎?")){
	 		window.location.href="get_stock.php?page=<?=$page?>&pagesize=<?=$pagesize?>&total=<?=$goodsClass->total?>&GDSNO="+no;
			return true;
	 	}
		return false;
	}
	
    function onEdit(no){	 	
	 	window.location.href="new.php?<?php echo "$argument"; ?>&page=<?=$page?>&GDSNO="+no;
	}
	
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php");?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <br />
            <table width="1003" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="1070"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td width="46%" bgcolor="#666666" class="title_2">商品資料</td>
                      <td width="54%" align="right" bgcolor="#666666" class="title_2"><input name="Submit" type="button" value="編輯商品" onClick="javascript:window.location.href='list.php'" /></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="97%">
<tr>
                      <td valign="bottom"><div align="right">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="412" height="20" valign="middle">
                            <form name="form2" method="post" action="list.php">選擇顯示類別:<span class="text_1">
                            <select name="CLA" id="CLA">
                              <option value="1" selected>已設定類別</option>
                              <option value="0">未設定類別</option>
                              <?php     
								
							  echo $row['CLA1NO'];                           	
                              
                              $category_sublist = $goodsClass->categoryList();
                              $category_sublist2 = $goodsClass->category2List();
							  $category_sublist3 = $goodsClass->category3List();
							  if($row['CLA1NO']<>"" || $row['CLA2NO']<>"" || $row['CLA3NO']<>""){
							  $op=$row['CLA1NO'].'-'.$row['CLA2NO'].'-'.$row['CLA3NO'];
							  }else{
							  $op="$CLA1NO";
							  }
							  echo 'op='."$op";
                                 while($crow3=mysql_fetch_array($category_sublist3['0'])){
							  	 if($CLA==$crow3['c1_no'].'-'.$crow3['c2_no'].'-'.$crow3['c3_no']){
							 	 echo'<option value="'.$crow3['c1_no'].'-'.$crow3['c2_no'].'-'.$crow3['c3_no'].'" selected>'.$crow3['c1_name'].'-'.$crow3['c2_name'].'-'.$crow3['c3_name'].'</option>';
							 	 }else{
								 echo'<option value="'.$crow3['c1_no'].'-'.$crow3['c2_no'].'-'.$crow3['c3_no'].'" >'.$crow3['c1_name'].'-'.$crow3['c2_name'].'-'.$crow3['c3_name'].'</option>';
							 	 }
								 }
								 
								 
								 while($crow2=mysql_fetch_array($category_sublist2['0'])){
							  	 if($CLA==$crow2['c1_no'].'-'.$crow2['c2_no'].'-0'){
							 	 echo'<option value="'.$crow2['c1_no'].'-'.$crow2['c2_no'].'-0'.'" selected>'.$crow2['c1_name'].'-'.$crow2['c2_name'].'</option>';
							 	 }else{
								 echo'<option value="'.$crow2['c1_no'].'-'.$crow2['c2_no'].'-0'.'" >'.$crow2['c1_name'].'-'.$crow2['c2_name'].'</option>';
							 	 } 
								 }
								 
								 
								 while($crow=mysql_fetch_array($category_sublist['0'])){
							  	 if($CLA==$crow2['c1_no'].'-0-0'){
							 	 echo'<option value="'.$crow['no'].'-0-0'.'" selected>'.$crow['name'].'</option>';
							 	 }else{
								 echo'<option value="'.$crow['no'].'-0-0'.'" >'.$crow['name'].'</option>';
							 	 } 
								 }
								 
                                ?>
                            </select>
                            </span>
                              <input type="submit" name="button2" id="button2" value="類別搜尋" >
                            </form></td>
                            <td width="358" valign="middle"><form name="form2" method="post" action="list.php">
                              <div align="right">搜尋商品ID:                        
                                <input type="text" name="GDSNO" id="GDSNO">
                                <input type="submit" name="button" id="button" value="ID搜尋" >
                              </div>
                            </form></td>
                          </tr>
                          <tr>
                            <td height="20" align="left" valign="middle"><form name="form3" method="post" action="list.php">
                              選擇品牌:<span class="text_1">
                              <select name="bymkerno" id="bymkerno">
                                <?php                                	
                              
                              $brand_sublist = $goodsClass->brandList();
								 
								 while($brow=mysql_fetch_array($brand_sublist['0'])){
							  	 if($bymkerno==$brow['no']){
							 	 echo'<option value="'.$brow['no'].'" selected>'.$brow['name'].'</option>';
							 	 }else{
								 echo'<option value="'.$brow['no'].'" >'.$brow['name'].'</option>';
							 	 } 
								 }
								 
                                ?>
                              </select>
                              </span>
                              <input type="submit" name="button3" id="button3" value="品牌搜尋" >
                            </form>                            </td>
                            <td align="right" valign="middle"><?php /* <form name="form4" method="post" action="list.php">
                              選擇打折品牌:<span class="text_1">
                              <select name="dmkerno" id="dmkerno">
                                <?php                                	
                              
                              $brand_sublist3 = $goodsClass->brandList();
								 
								 while($brow3=mysql_fetch_array($brand_sublist3['0'])){
							  	 if($dmkerno==$brow3['no']){
							 	 echo'<option value="'.$brow3['no'].'" selected>'.$brow3['name'].'</option>';
							 	 }else{
								 echo'<option value="'.$brow3['no'].'" >'.$brow3['name'].'</option>';
							 	 } 
								 }
								 
                                ?>
                              </select>
                              <label>
                              <select name="dvalue" id="dvalue">
                                <option value="1">打折</option>
                                <option value="0">不打折</option>
                              </select>
                              </label>
                              </span>
                              <input type="submit" name="button4" id="button4" value="品牌打折" >

                                                                                      </form>                             */?></td>
                          </tr>
                        </table>
                      </div></td>
                  </tr>
                    <tr>
                      <td align="center" valign="top"><form action="" method="post" name="form1" id="form1">
                          <table width="974" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td width="92" height="28" align="center" class="title_3">商品NO</td>
                              <td width="145" align="center" class="title_3">商品貨號</td>
                              <td width="159" align="center" class="title_3">商品名稱</td>
                              <td width="284" align="center" class="title_3">商品說明</td>
                              <td width="99" align="center" class="title_3">上下架</td>
                              <td width="157" align="center" class="title_3">編輯</td>
                            </tr>
                            <?
			$no = ($page-1) * $pagesize;
			foreach($goodsList as $goods){
				$no++;
?>
                            <tr>
                              <td align="center" class="text_1"><?=$goods->no?></td>
                              <td align="center" class="text_1"><? 
							  if($goods->GDSNO<>"" &&  $goods->GDSNO2<>""){
							  echo $goods->GDSNO.'<BR>'.$goods->GDSNO2;
							  }else{
							  echo $goods->GDSNO.$goods->GDSNO2;
							  }
							  
							  ?></td>
                              <td align="center" class="text_1"><?=$goods->GDSNAME?></td>
                              <td align="center" class="text_1"><?=$goods->GDSDESC?>
                              &nbsp;</td>
                              <td align="center" class="text_1">
                              <?php if($goods->ISHOW=="0"){?>
                              <input type="button" onClick="location='show_tr.php?no=<?php echo $goods->no ?>&page=<?php echo "$page"; ?>&ishow=1<?php echo "$argument"; ?>'" name="button4" id="button4" value="下架">
                              <?php }else{?>
                              <input type="button" onClick="location='show_tr.php?no=<?php echo $goods->no ?>&page=<?php echo "$page"; ?>&ishow=0<?php echo "$argument"; ?>'" name="button4" id="button4" value="上架">
                              <?php }?>                              </td>
                              <td align="center" class="text_1"><input name="Submit2" type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit('<?=$goods->GDSNO?>')" />
                              <input name="Submit3" type="button" class="btn_text" value="更新庫存" onClick="setStock('<?=$goods->GDSNAME?>','<?=$goods->GDSNO?>')" /></td>
                            </tr>
                            <? }?>
                          </table>
                      </form></td>
                    </tr>
                    <tr>
                      <td align="center" class="page"><span class="wd_white_12">
                        <?=$goodsClass->showPageMenu($argument)?>
                      </span> </td>
                    </tr>
                </table></td>
              </tr>
            </table>
        <br />
    </div></td>
  </tr>
</table>
</body>
</html>





