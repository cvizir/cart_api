<?php

require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
startDB();

$pdcat_code = $_REQUEST["pdcat_code"];

if ($pdcat_code <> "") {
  $sql_spec_group = "SELECT * FROM `spec_group` WHERE `pdcat_code`='$pdcat_code'";
  $rs_spec_group = mysql_query($sql_spec_group);
  $spec_group = mysql_fetch_array($rs_spec_group, MYSQL_ASSOC);
}
  echo "<p>規格群組名稱：".$spec_group['name']."</p>";
?>