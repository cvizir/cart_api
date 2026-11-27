<?php
// 登出連結用 WEB_ADMIN（與 menu.php 一致），並顯示目前登入的管理員
$_hdr_admin = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <div class="header">
      <div class="header_admin_id">
        <?php if ($_hdr_admin !== '') { ?><?php echo'管理者 : '.htmlspecialchars($_hdr_admin, ENT_QUOTES, 'UTF-8'); ?><?php } ?>
      </div>
      <div class="header_logout_div">
        <a href="<?php echo htmlspecialchars(WEB_ADMIN, ENT_QUOTES, 'UTF-8'); ?>logout.php" class="header_logout">登出</a>
      </div>
    </ㄎ>
  </tr>
</table>
