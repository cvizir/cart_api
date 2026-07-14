<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


	$cat_gp_id = $_REQUEST["cat_gp_id"];	
	$added_no_value = $_REQUEST["added_no_value"];	
	
    //echo '$cat_gp_id='."$cat_gp_id ".'<br><br>';
    //echo '$added_no_value='."$added_no_value ".'<br><br>';

	$sql_cat="SELECT no,name FROM `cat` WHERE `cat_gp_id` LIKE '%,".$_REQUEST['cat_gp_id'].",%'";		
	echo"$sql_cat";
	$rs_cat=@mysql_query($sql_cat);
	while($row_cat=@mysql_fetch_array($rs_cat,MYSQL_ASSOC)){ 
	echo $row_cat['name'].'<br>';
        
		$sql_cat_sup="SELECT no,name FROM `cat_sup` WHERE `cat_sup_id` LIKE '%,".$row_cat['no'].",%'";		
		$rs_cat_sup=mysql_query($sql_cat_sup);
		while($row_cat_sup=mysql_fetch_array($rs_cat_sup,MYSQL_ASSOC)){ 
			if (strExist($_REQUEST['added_no'],$row_cat_sup['no'])) {
			$value_exist='checked';
			}else{
			$value_exist='';
			} 
			?>
          <input name="added_no[]" type="checkbox" id="tag_no" value="<?php echo $row_cat_sup['no']; ?>" <?php echo "$value_exist";?>>
          <?php echo $row_cat_sup['name']; ?>&nbsp;&nbsp;
          <?php 
		}
		echo'<br>';
	} 
?>