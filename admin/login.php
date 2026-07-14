<?php
ob_start();
session_start();
header("Content-Type:text/html;charset=utf-8");
require_once "../config.php";
?>
<?php

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

if (($_POST["admin_id"] != "") && ($_POST["admin_psw"] != "")) {
    $sql = "select * from admin where admin_id='" . $_POST["admin_id"] . "' and admin_psw='" . $_POST["admin_psw"] . "'";
    $res = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($res);
    //echo"$sql";
    if ($num == 1) {
        $row                      = mysqli_fetch_array($res);
        $_SESSION["admin_no"]     = $row[0];
        $_SESSION["admin_id"]     = $row[1];
        $_SESSION["admin_psw"]    = $row[2];
        $_SESSION["admin_access"] = $row[3];
        header("location:main.php");
    } else {
        echo "<script language=javascript>
		alert('您輸入的帳號或密碼有誤，請重新輸入！');
		window.location='" . WEB_ROOT . "/admin/index.php';
		</script>";
    }
} else {
    echo "<script language=javascript>
		alert('您輸入的帳號或密碼有誤，請重新輸入！');
		window.location='" . WEB_ROOT . "/admin/index.php';
		</script>";
}