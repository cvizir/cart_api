<? 
	require_once("../../config.php");
	require_once("../session.php");
    $db = new DomainObject;
    $db->dbinit();

	$bd_id=$_REQUEST['bd_id'];
	$no_id=$_REQUEST['no_id'];
	if(count($no_id)>=2){
		for( $I=0; $I <= count($no_id); $I++ ){
			$sql_sort="UPDATE `billboard` SET `m_csort` = '".($I+1)."' WHERE `no` ='".$no_id[$I]."'";
			mysql_query($sql_sort);
		}
	}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="favicon3.ico" rel="shortcut icon">
<link href="../style.css" rel="stylesheet" type="text/css" />

<style type="text/css">

	ul, li { margin: 0; padding: 0; list-style: none; }
	.clear { clear: both; width: 1px; height: 0px; line-height: 0px; font-size: 1px; }
	.box { width: 600px; height: auto; margin: 25px 0 0 0;  border: 1px solid #fff; }
	.main { cursor:pointer; position: static; width: 600px; height: 20px; margin-bottom: 5px; padding: 5px; border: 1px dashed #00f; background: #fff; }
	.maindash { position: absolute; width: 600px; height: 20px; margin-bottom: 5px; border: 1px dashed blue; background: #ececec; opacity: 0.7; }
	.hide { width: 600px; height: 20px; margin-bottom: 5px; }
	.dash { position: sta;tic; width: 600px; height: 20px; margin-bottom: 5px; border: 1px dashed #f00; };
</style>
<script type="text/javascript" src="../../script/jquery-1.7.1.js"></script>
<script type="text/javascript">
$(document).ready( function () {
	var range = { x: 0, y: 0 };//滑鼠元素偏移量
	var lastPos = { x: 0, y: 0, x1: 0, y1: 0 }; //拖拽對象的四個座標
	var tarPos = { x: 0, y: 0, x1: 0, y1: 0 }; //目標元素物件的座標初始化
	var theDiv = null, move = false;//拖拽物件 拖拽狀態
	var theDivId =0, theDivHeight = 0, theDivHalf = 0; tarFirstY = 0; //拖拽物件的索引、高度、的初始化。
	var tarDiv = null, tarFirst, tempDiv; //要插入的目標元素的物件, 臨時的虛線物件
	$(".main").each(function(){
		
		$(this).mousedown(function (event){
		//拖拽對象
		theDiv = $(this);
		//滑鼠元素相對偏移量
		range.x = event.pageX - theDiv.offset().left;
		range.y = event.pageY - theDiv.offset().top;
		theDivId = theDiv.index();
		theDivHeight = theDiv.height();
		theDivHalf = theDivHeight/2;
		move = true;
		theDiv.attr("class","maindash");
		// 創建新元素 插入拖拽元素之前的位置(虛線框)
		$("<div class='dash'></div>").insertBefore(theDiv);
		});
	});
	
	$(document).mousemove(function(event) {
	if (!move) return false;
	lastPos.x = event.pageX - range.x;
	lastPos.y = event.pageY - range.y;
	lastPos.y1 = lastPos.y + theDivHeight;
	// 拖拽元素隨滑鼠移動
	theDiv.css({left: lastPos.x + 'px',top: lastPos.y + 'px'});
	// 拖拽元素隨滑鼠移動 查找插入目標元素
	var $main = $('.main'); // 局部變數：按照重新排列過的順序 再次獲取 各個元素的座標，
	tempDiv = $(".dash"); //獲得臨時 虛線框的對象
	$main.each(function () {
	tarDiv = $(this);
	tarPos.x = tarDiv.offset().left;
	tarPos.y = tarDiv.offset().top;
	tarPos.y1 = tarPos.y + tarDiv.height()/2;
	tarFirst = $main.eq(0); // 獲得第一個元素
	tarFirstY = tarFirst.offset().top + theDivHalf ; // 第一個元素物件的中心縱坐標
		//拖拽對象 移動到第一個位置
		if (lastPos.y <= tarFirstY) {
		tempDiv.insertBefore(tarFirst);
		}
		//判斷要插入目標元素的 座標後， 直接插入
		if (lastPos.y >= tarPos.y - theDivHalf && lastPos.y1 >= tarPos.y1 ) {
		tempDiv.insertAfter(tarDiv);
		}
	});
	}).mouseup(function(event) {
	theDiv.insertBefore(tempDiv); // 拖拽元素插入到 虛線div的位置上
	theDiv.attr("class", "main"); //恢復物件的初始樣式
	tempDiv.remove(); // 刪除新建的虛線div
	move=false;
	});
	});
</script>
<script language="JavaScript">
function chkall(input1,input2)
{
    var objForm = document.forms[input1];
    var objLen = objForm.length;
    for (var iCount = 0; iCount < objLen; iCount++)
    {
        if (input2.checked == true)
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = true;
            }
        }
        else
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = false;
            }
        }
    }
}
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="MM_preloadImages('../btn/back_over.gif')">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php");?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <table width="850" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td height="17">&nbsp;</td>
                <td height="30" valign="top"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
              </tr>
              <tr>
                <td width="15" height="17">&nbsp;</td>
                <td width="835"><form name="form1" method="post" action="sort.php">
                  <div class="box" id="box">
                    <?php 
			   $sql_cat="SELECT * FROM `billboard` WHERE `cat_id` ='$bd_id' ORDER BY `m_csort` ASC , `no` DESC";
			   //echo"$sql_cat";
               $rs_cat=mysql_query($sql_cat);
               while($row_cat=mysql_fetch_array($rs_cat,MYSQL_ASSOC)){   
			   ?>
                    <div class="main" >
                      <?php if($row_cat['web_code']<>""){?>
                      <?php echo $row_cat['web_code']; ?>
                      <?php }else{?>
                      <img src="../../images/upload/billboard/billboard_<?php echo sprintf("%06d", $Billboard->no); ?>.jpg" width="110">
                      <?php }?>
                      
                      <input name="no_id[]" type="hidden" id="hiddenField" value="<?php echo $row_cat['no']; ?>">
                    </div>
                    <?php
               } 
			   ?>
                  </div>
                  <br>
                  <input name="Submit" type="submit" value="送出" />
                </form></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td height="17">&nbsp;</td>
                <td height="30" valign="top"><div class="btnblack"></div></td>
              </tr>
            </table>
            <br />
        <br />
    </div></td>
  </tr>
</table>
</body>
</html>





