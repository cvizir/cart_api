<?
	require_once("../include/config.inc.php");
	@session_start();
	session_unset();
	session_destroy();
?>
<script language="javascript">
<!--
    top.location.href='<?=WEB_ROOT?>admin/';
-->
</script>