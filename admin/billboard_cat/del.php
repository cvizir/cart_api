<? 
	require_once("../../config.php");
	include_once("../session.php");

	$page=$_GET["page"];
	$pagesize=$_GET["pagesize"];
	$total=$_GET["total"];
	
	$no = $_GET['no'];
	//echo "no=$no<br>";	
	$billboard = new Billboard($no);	
	$billboard->delete();
	
	$total--;
	if($page!=1){
		if(($page-1)*$pagesize>=$total){
			$page--;
		}
	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="三峽弘道養雞場" />
<meta name="keywords" content="三峽弘道養雞場,黑羽,放山閹雞養殖場,金鳳滴雞精,手工雞精,黑毛土雞">
<meta name="description" content="三峽弘道養雞場,黑羽,放山閹雞養殖場,金鳳滴雞精,手工雞精,黑毛土雞,海中天餐飲時尚館、馥園餐廳、貓空清心茗茶餐廳、貓空大茶壺1F茶餐廳、三峽海霸船餐廳、三峽龍水魚餐廳……皆使用品質優良的放山閹雞，土雞，作為招牌菜色！防治所定期檢驗，品質安全無虞，絕無金屬農藥殘留，無抗生素，無生長激素。
日常全以玉米、大豆粉為飼料，絕對不添加生長激素及抗生素">
<script language="JavaScript" type="text/JavaScript">
	alert("刪除成功!");
	window.location.href="list.php?page=<?=$page?>";
</script>
