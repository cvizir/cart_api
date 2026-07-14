<?php 

	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$member_code=$_REQUEST["member_code"];
	$order_code=$_REQUEST["order_code"];	
	$no=$_REQUEST["no"];
		
    $sql_orderlist="SELECT * FROM `orderlist` WHERE `no`='$no' AND `order_code`='$order_code' AND `member_code`='$member_code'";
    $rs_orderlist=mysql_query($sql_orderlist);
    $num_orderlist=mysql_num_rows($rs_orderlist);
    $row_orderlist=mysql_fetch_array($rs_orderlist,MYSQL_ASSOC);
	
	$order_num='C17'.sprintf("%06d",$row_orderlist['no']); 
	
	
	if($no!='' && $order_code!='' && $num_orderlist>=1){

 	// 建立CURL連線
	$ch = curl_init();
	// 設定擷取的URL網址
	curl_setopt($ch, CURLOPT_URL, "https://shopping.windmill.com.tw/email_order.php?order_code=$order_code&member_code=$member_code");
	curl_setopt($ch, CURLOPT_HEADER, false);
	//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//設定要傳的 變數A=值A & 變數B=值B (中間要用&符號串接)
	curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // CURLOPT_NOSIGNAL 設為 1
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 900); // 設定最長執行 900 毫秒
	//$PostData = "a=abc&b=def";
	$PostData = "order_code=$order_code&member_code=$member_code";
	//設定CURLOPT_POST 為 1或true，表示要用POST方式傳遞
	curl_setopt($ch, CURLOPT_POST, 1);
	//CURLOPT_POSTFIELDS 後面則是要傳接的POST資料。
	curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
	// 執行
	$html_code_order=curl_exec($ch);
	// 關閉CURL連線
	curl_close($ch);



	$to = trim($row_orderlist['invoice_email']);
	
    $mail = "『風車寶貝』 線上購物網<service@windmill.com.tw>";
	
	$subject_tr = base64_encode('完成運費審核，待付款通知-訂單編號: '."$order_num");   
	$subject = '=?utf8?B?'.$subject_tr.'?=';  
	
	
			
	$html=$html.'感謝您訂購『風車寶貝』線上購物網的商品，您可 <a href="https://shopping.windmill.com.tw/inquireContent.php?order_code='.$order_code.'">點此查看</a> 訂單明細。 '.'<br /><br />';
	$html=$html.'此信件為訂單通知，待付款金額確認無誤後，商品預計3~5個工作天送達；若您為國外訂單，待確認運費後，請至「訂單查詢」頁面選擇付款，確認無誤後即為您安排出貨。';
	

	$html=$html.$html_code_order;
	
	$html=$html.'';

	$html='<div style="color:#000;">'.$html.'</div>';

	// 寄送 HTML 格式郵件, 必需設定 Content-type 
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";

	//sleep for 5 seconds
	sleep(5);
	
	mail($to, $subject, $html, "From:".$mail."\n".$headers);	
	//mail('hoplionservice@gmail.com', $subject, $html, "From:".$mail."\n".$headers);	 */

	}


?>