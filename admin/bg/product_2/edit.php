<?php

require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");


$product_code = $_REQUEST["product_code"];
$cms_mode = $_REQUEST["cms_mode"];
$sh_post = $_SERVER['QUERY_STRING'];
startDB();

if ($product_code <> "") {
  $sql_edit = "SELECT * FROM `product` WHERE `product_code`='$product_code'";
  $rs_edit = mysql_query($sql_edit);
  $product = mysql_fetch_array($rs_edit, MYSQL_ASSOC);
}

?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?= ADMIN_TITLE ?></title>
  <script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
  <script type="text/javascript" src="../../script/jquery.min.js"></script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../script/jquery-1.6.3.min.js"></script>
  <script type="text/javascript">
    alert($('#pdcatSelect'));
    $('#pdcatSelect').on('change', function() {
      alert('選擇改變了！');
      const selectedValue = $(this).val();
      const $targetDiv = $('#pdcat_info');

      if (!selectedValue) {
        $targetDiv.empty();
        return;
      }

      $targetDiv.html('<p>資料載入中...</p>');

      // 發送 GET 請求，成功後將 HTML 塞入 div
      $.get(`pdcat_api.php?pdcat_code=${selectedValue}`)
        .done(function(htmlContent) {
          $targetDiv.html(htmlContent);
        })
        .fail(function(error) {
          console.error('API 請求失敗:', error);
          $targetDiv.html('<p style="color: red;">發生錯誤，無法載入資料。</p>');
        });
    });
  </script>



</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="101%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2"><?php require_once("../header.php"); ?></td>
    </tr>
    <tr>
      <td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php"); ?></td>
      <td width="90%" valign="top">
        <div align="center"><br />
          <br />
          <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="100%">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td width="49%" class="tb_menu_title">商品管理</td>
                    <td width="51%" align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <table width="100%">
                  <tr>
                    <td>
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>&nbsp;</td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <?php echo '<form action="save.php?' . $sh_post . '" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                      <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                        <tr>
                          <td width="15%" class="tb_edit_title">標題</td>
                          <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $product['name']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">名稱</td>
                          <td class="tb_edit_info"><input name="name2" type="text" id="name2" value="<?php echo $product['name']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">商品類型</td>
                          <td class="tb_edit_info">周邊&單一分類
                            <input type="radio" value="1" name="pd_mode" id="pd_mode" <?php chkChecked('1', $product['pd_mode']); ?>>
                            &nbsp;&nbsp;&nbsp;
                            一般
                            <input type="radio" value="0" name="pd_mode" id="pd_mode" <?php chkChecked('0', $product['pd_mode']); ?>>
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">款式</td>
                          <td class="tb_edit_info"><input name="style_name" type="text" id="style_name" value="<?php echo $product['style_name']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">上次修改時見</td>
                          <td class="tb_edit_info"><?php echo $product['uptime']; ?>&nbsp;</td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">商品類別</td>
                          <td class="tb_edit_info">
                            <select name="pdcat_code" id="pdcatSelect">
                              <option value="">請選擇</option>
                              <?php
                              $sql_pdcat = "SELECT * FROM `pdcat` ORDER  BY `name`";
                              $rs_pdcat = mysql_query($sql_pdcat);
                              while ($row_pdcat = mysql_fetch_array($rs_pdcat, MYSQL_ASSOC)) {
                              ?>
                                <option value="<?php echo $row_pdcat['pdcat_code'];  ?>" <?php chkSelected($row_pdcat['pdcat_code'], $product['pdcat_code']) ?>>
                                  <?php echo $row_pdcat['name'];  ?>
                                </option>
                              <?php } ?>
                            </select>
                            <div id="pdcat_info"></div>
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">原價</td>
                          <td class="tb_edit_info"><input type="text" name="o_price" id="o_price" value="<?php echo $product['o_price']; ?>"></td>
                        </tr>
                        <?php /*     <tr>
      <td class="tb_edit_title">首頁說明</td>
      <td class="tb_edit_info"><textarea class="ckeditor" id="index_info'" name="index_info" cols="" rows=""><?php echo $product['index_info']; ?></textarea></td>
    </tr> */ ?>
                        <?php /*       <tr>
        <td class="tb_edit_title">商品摘要</td>
        <td class="tb_edit_info"><textarea class="ckeditor" id="summary" name="summary" cols="" rows=""><?php echo $product['summary']; ?></textarea></td>
      </tr> */ ?>
                        <tr>
                          <td class="tb_edit_title">售價</td>
                          <td class="tb_edit_info"><input type="text" name="price" id="price" value="<?php echo $product['price']; ?>"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">折扣</td>
                          <td class="tb_edit_info">
                            <input name="discount_rate" type="radio" id="radio" value="0" <?php chkChecked('0', $product['discount_rate']); ?>>無折扣
                            <input type="radio" name="discount_rate" id="radio" value="10" <?php chkChecked('10', $product['discount_rate']); ?>><img src="../../images/10.png" width="34" height="34">
                            <input type="radio" name="discount_rate" id="radio" value="15" <?php chkChecked('15', $product['discount_rate']); ?>><img src="../../images/15.png" width="34" height="34">
                            <input type="radio" name="discount_rate" id="radio" value="20" <?php chkChecked('20', $product['discount_rate']); ?>><img src="../../images/20.png" width="34" height="34">
                            <input type="radio" name="discount_rate" id="radio" value="25" <?php chkChecked('25', $product['discount_rate']); ?>><img src="../../images/25.png" width="34" height="34">
                            <input type="radio" name="discount_rate" id="radio" value="30" <?php chkChecked('30', $product['discount_rate']); ?>><img src="../../images/30.png" width="34" height="34">
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">上下架</td>
                          <td class="tb_edit_info">上架<input type="radio" value="1" name="ishow" id="ishow" <?php chkChecked('1', $product['ishow']); ?>>
                            &nbsp;&nbsp;&nbsp;
                            下架<input type="radio" value="0" name="ishow" id="ishow" <?php chkChecked('0', $product['ishow']); ?>></td>
                        </tr>
                        <?php /*     <tr>
      <td class="tb_edit_title">排序</td>
      <td class="tb_edit_info"><select name="m_sort" id="m_sort">
        <?php getDateSelectOption('0','9',$product['m_sort']); ?>
        </select></td>
    </tr> */ ?>
                        <tr>
                          <td align="center" class="tb_edit_title">產品說明</td>
                          <td class="tb_edit_info"><textarea class="ckeditor" name="pd_info" cols="60" rows="6"><?php echo $product['pd_info']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">退貨說明</td>
                          <td class="tb_edit_info"><textarea class="ckeditor" name="no_buy_info" cols="60" rows="6"><?php echo $product['no_buy_info']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">使用說明</td>
                          <td class="tb_edit_info"><textarea class="ckeditor" name="user_info" cols="60" rows="6"><?php echo $product['user_info']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">單一商品說明</td>
                          <td class="tb_edit_info"><textarea class="ckeditor" name="unit_info" cols="60" rows="6"><?php echo $product['unit_info']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td rowspan="2" class="tb_edit_title">尺寸說明上下架</td>
                          <td class="tb_edit_info">上架
                            <input type="radio" value="1" name="size_pic" id="size_pic" <?php chkChecked('1', $product['size_pic']); ?>>
                            &nbsp;&nbsp;&nbsp;
                            下架
                            <input type="radio" value="0" name="size_pic" id="size_pic" <?php chkChecked('0', $product['size_pic']); ?>>
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_info">
                            <input type="file" name="size_file" id="size_file">
                            <br><img src="../../images/upload/product/product_size_<?php echo sprintf("%06d", $product['no']); ?>.jpg" alt="" width="240">
                          </td>
                        </tr>
                        <tr>
                          <td rowspan="2" class="tb_edit_title">使用說明上下架</td>
                          <td class="tb_edit_info">上架
                            <input type="radio" value="1" name="user_pic" id="user_pic" <?php chkChecked('1', $product['user_pic']); ?>>
                            &nbsp;&nbsp;&nbsp;下架
                            <input type="radio" value="0" name="user_pic" id="user_pic" <?php chkChecked('0', $product['user_pic']); ?>>
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_info">
                            <input type="file" name="user_file" id="userfile"><br>
                            <img src="../../images/upload/product/product_user_<?php echo sprintf("%06d", $product['no']); ?>.jpg" alt="" width="240">
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">備註</td>
                          <td class="tb_edit_info"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo $product['ps']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">&nbsp;</td>
                          <td class="tb_edit_info">
                            <script type="text/javascript">
                              function clearFile(id_name) {
                                var obj = document.getElementById(id_name);
                                obj.value = '' //FF下
                                obj.select(); //IE下
                                document.execCommand('Delete');
                              }
                            </script>
                            <div id="div_photo_upload">
                              <?php
                              $photo_array = explode(",", $product['photo']);
                              $photo_ps = array("", "", "");
                              for ($I = 1; $I <= 1; $I++) {
                                $pix = ($I - 1);
                                $filename = '../../images/upload/product/' . $product['photo'];
                                //echo"$filename";
                                if (!file_exists($filename)) {
                                  $filename = 'images/no_pic.gif';
                                }
                              ?>
                                <div style="width:45%; height:auto; float:left; margin:10px;">
                                  <ul>
                                    <li><input type="checkbox" name="del_pic[<?php echo "$pix"; ?>]" value="1">刪除此產品圖片<?php if ($photo_ps[$I] <> "") {
                                                                                                                        echo '(' . $photo_ps[$I] . ')';
                                                                                                                      } ?></li>
                                    <li style="width:240px; height:auto;"><img src="<?php echo "$filename";  ?>" alt="" width="240"></li>
                                    <li>
                                      <input id="input_file_<?php echo "$pix"; ?>" name="add_pic[<?php echo "$pix"; ?>]" type="file" size="19">
                                      <input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$pix"; ?>');" />
                                    </li>
                                  </ul>
                                </div>
                              <?php } ?>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" align="center">
                            <input type="hidden" value="<?php echo $product['no']; ?>" name="no" id="no" />
                            <input type="hidden" value="<?php echo $product['product_code']; ?>" name="product_code" id="product_code" />
                            <input type="hidden" value="<?php echo $product['photo']; ?>" name="photo" id="photo" />
                            <input type="hidden" value="<?= $page ?>" name="page" id="page" />
                            <input type="submit" name="button" id="button" value="<?php if ($cms_mode == "add") {
                                                                                    echo '新增資料';
                                                                                  } else {
                                                                                    echo '修改資料';
                                                                                  } ?>">
                          </td>
                        </tr>
                      </table>
                      </form>
                      <br><br>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br />
        </div>
      </td>
    </tr>
  </table>
</body>

</html>