<?php
// 選單項目集中在這個陣列，要新增或調整只改這裡
$_menu_items = array(
  'billboard_cat/list.php' => '版面管理',
  'orderlist/list.php'     => '訂單管理',
  'pdcat_t/list.php'       => '產品分類',
  'product/list.php'       => '產品管理',
  'spec_group/list.php'    => '規格群組管理',
  'spec_info/list.php'     => '規格管理',
  'article/list.php'       => 'BLOG',
  'member/list.php'        => '會員',
  'contact/list.php'       => '聯絡我們',
  'news/list.php'          => '新聞公告',
  'qa/list.php'            => 'Q&A',
);
$_menu_base = htmlspecialchars(WEB_ADMIN, ENT_QUOTES, 'UTF-8');
?>
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="6" bgcolor="#525252">
  <tr>
    <td align="center" class="menu_title"><br />管理選單</td>
  </tr>
  <?php foreach ($_menu_items as $_path => $_label) { ?>
  <tr>
    <td align="center"><a class="menu_url" href="<?php echo $_menu_base . htmlspecialchars($_path, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($_label, ENT_QUOTES, 'UTF-8'); ?></a></td>
  </tr>
  <?php } ?>
  <tr>
    <td height="650" align="center"><br /></td>
  </tr>
</table>
