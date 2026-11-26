<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

function reqv($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$trace    = 0;
$no       = reqv('no');
$cms_mode = reqv('cms_mode');
if ($cms_mode === '') {
  $cms_mode = ($no === '') ? 'add' : 'edit';
}
$sh_post  = $_SERVER['QUERY_STRING'];

// 前端權限判斷（真正的檢查在 save.php）
$admin_pv = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
$can_edit = empty($admin_pv_check) || strExist($admin_pv, 'orderlist_et');

// 訂單狀態、國家清單（國家清單是把原本一長串 <option> 整理成陣列）
$order_states = array('未付款', '確認運費', '待付款', '刷卡失敗', '完成訂購', '訂單處理中',
                      '已出貨', '先出貨待付款', '取消訂單', '退貨訂單', '失效訂單');
$country_options = array(
  'tw' => '台灣',
  'Afghanistan' => 'Afghanistan',
  'Alaska' => 'Alaska',
  'Albania' => 'Albania',
  'Algeria' => 'Algeria',
  'American+Samoa' => 'American Samoa',
  'Andorra' => 'Andorra',
  'Angola' => 'Angola',
  'Anguilla' => 'Anguilla',
  'Antarctica' => 'Antarctica',
  'Antigua' => 'Antigua',
  'Argentina' => 'Argentina',
  'Armenia' => 'Armenia',
  'Aruba' => 'Aruba',
  'Ascension' => 'Ascension',
  'Australia' => 'Australia',
  'Austria' => 'Austria',
  'Azerbaijan' => 'Azerbaijan',
  'Bahamas' => 'Bahamas',
  'Bahrain' => 'Bahrain',
  'Bangladesh' => 'Bangladesh',
  'Barbados' => 'Barbados',
  'Belarus' => 'Belarus',
  'Belgium' => 'Belgium',
  'Belize' => 'Belize',
  'Benin' => 'Benin',
  'Bermuda' => 'Bermuda',
  'Bhutan' => 'Bhutan',
  'Bolivia' => 'Bolivia',
  'Bosnia' => 'Bosnia',
  'Botswana' => 'Botswana',
  'Brazil' => 'Brazil',
  'British+Virgin+IS.' => 'British Virgin IS.',
  'Brunei' => 'Brunei',
  'Bulgaria' => 'Bulgaria',
  'Burkina+Faso' => 'Burkina Faso',
  'Burundi' => 'Burundi',
  'Cambodia' => 'Cambodia',
  'Cameroon' => 'Cameroon',
  'Canada' => 'Canada',
  'Cape+Verde+IS.' => 'Cape Verde IS.',
  'Cayman+IS.' => 'Cayman IS.',
  'Central+African+REP.' => 'Central African REP.',
  'Chad' => 'Chad',
  'Chile' => 'Chile',
  'China' => '中國',
  'Colombia' => 'Colombia',
  'Comoros+IS.' => 'Comoros IS.',
  'Congo+REP.' => 'Congo REP.',
  'Cook+IS.' => 'Cook IS.',
  'Costa+Rica' => 'Costa Rica',
  'Croatia' => 'Croatia',
  'Cuba' => 'Cuba',
  'Cyprus' => 'Cyprus',
  'Czech+Republic' => 'Czech Republic',
  'DEM.+REP.+Of+Congo' => 'DEM. REP. Of Congo',
  'Denmark' => 'Denmark',
  'Diego+Garcia' => 'Diego Garcia',
  'Djibouti+REP.' => 'Djibouti REP.',
  'Dominica+IS.' => 'Dominica IS.',
  'Dominican+REP.' => 'Dominican REP.',
  'East+Timor' => 'East Timor',
  'Ecuador' => 'Ecuador',
  'Egypt' => 'Egypt',
  'El+Salvador' => 'El Salvador',
  'Equatorial+Guinea' => 'Equatorial Guinea',
  'Eritrea' => 'Eritrea',
  'Estonia' => 'Estonia',
  'Ethiopia' => 'Ethiopia',
  'Falkland' => 'Falkland',
  'Faroe+IS.' => 'Faroe IS.',
  'Fiji+IS.' => 'Fiji IS.',
  'Finland' => 'Finland',
  'France' => 'France',
  'French+Antilles' => 'French Antilles',
  'French+Guiana' => 'French Guiana',
  'French+Polynesia' => 'French Polynesia',
  'Gabon' => 'Gabon',
  'Gambia' => 'Gambia',
  'Georgia' => 'Georgia',
  'Germany' => 'Germany',
  'Ghana' => 'Ghana',
  'Gibraltar' => 'Gibraltar',
  'Greece' => 'Greece',
  'Greenland' => 'Greenland',
  'Grenada' => 'Grenada',
  'Guam' => 'Guam',
  'Guatemala' => 'Guatemala',
  'Guinea+REP.' => 'Guinea REP.',
  'Guinea-Bissau' => 'Guinea-Bissau',
  'Guyana' => 'Guyana',
  'Haiti' => 'Haiti',
  'Hawaii' => 'Hawaii',
  'Honduras+REP.' => 'Honduras REP.',
  'Hong+Kong' => '香港',
  'Hungary' => 'Hungary',
  'Iceland' => 'Iceland',
  'India' => 'India',
  'Indonesia' => 'Indonesia',
  'Iran' => 'Iran',
  'Iraq' => 'Iraq',
  'Iridium' => 'Iridium',
  'Irish+REP.' => 'Irish REP.',
  'Israel' => 'Israel',
  'Italy' => 'Italy',
  'Ivory+Coast' => 'Ivory Coast',
  'Jamaica' => 'Jamaica',
  'Japan' => 'Japan',
  'Jordan' => 'Jordan',
  'Kazakhstan' => 'Kazakhstan',
  'Kenya' => 'Kenya',
  'Kiribati' => 'Kiribati',
  'Korea-North' => 'Korea-North',
  'Kuwait' => 'Kuwait',
  'Kyrgyzstan' => 'Kyrgyzstan',
  'Laos' => 'Laos',
  'Latvia' => 'Latvia',
  'Lebanon' => 'Lebanon',
  'Lesotho' => 'Lesotho',
  'Liberia' => 'Liberia',
  'Libya' => 'Libya',
  'Liechtenstein' => 'Liechtenstein',
  'Lithuania' => 'Lithuania',
  'Luxembourg' => 'Luxembourg',
  'Macau' => 'Macau',
  'Macedonia' => 'Macedonia',
  'Madagascar' => 'Madagascar',
  'Malawi' => 'Malawi',
  'Malaysia' => 'Malaysia',
  'Maldive+IS.' => 'Maldive IS.',
  'Mali' => 'Mali',
  'Malta' => 'Malta',
  'Mariana+IS.' => 'Mariana IS.',
  'Marshall+IS.' => 'Marshall IS.',
  'Mauritania' => 'Mauritania',
  'Mauritius' => 'Mauritius',
  'Mayotte' => 'Mayotte',
  'Mexico' => 'Mexico',
  'Micronesia' => 'Micronesia',
  'Moldova' => 'Moldova',
  'Monaco' => 'Monaco',
  'Mongolia' => 'Mongolia',
  'Montenegro' => 'Montenegro',
  'Montserrat' => 'Montserrat',
  'Morocco' => 'Morocco',
  'Mozambique' => 'Mozambique',
  'Myanmar' => 'Myanmar',
  'Namibia' => 'Namibia',
  'Nauru+IS.' => 'Nauru IS.',
  'Nepal' => 'Nepal',
  'Netherlands' => 'Netherlands',
  'Netherlands+Antilles' => 'Netherlands Antilles',
  'New+Caledonia' => 'New Caledonia',
  'New+Zealand' => 'New Zealand',
  'Nicargua' => 'Nicargua',
  'Niger' => 'Niger',
  'Nigeria' => 'Nigeria',
  'Niue+IS.' => 'Niue IS.',
  'Norfolk+IS.' => 'Norfolk IS.',
  'Norway' => 'Norway',
  'Oman' => 'Oman',
  'Pakistan' => 'Pakistan',
  'Palau' => 'Palau',
  'Palestine' => 'Palestine',
  'Panama' => 'Panama',
  'Papua+New+Guinea' => 'Papua New Guinea',
  'Paraguay' => 'Paraguay',
  'Peru' => 'Peru',
  'Philippines' => 'Philippines',
  'Poland' => 'Poland',
  'Portugal' => 'Portugal',
  'Puerto+Rico' => 'Puerto Rico',
  'Qatar' => 'Qatar',
  'Reunion+IS.' => 'Reunion IS.',
  'Romania' => 'Romania',
  'Russia' => 'Russia',
  'Rwanda' => 'Rwanda',
  'S.+Africa' => 'S. Africa',
  'S.+Korea' => 'S. Korea',
  'ST.+Helena' => 'ST. Helena',
  'ST.+Kitts' => 'ST. Kitts',
  'ST.+Lucia' => 'ST. Lucia',
  'ST.+Maarten' => 'ST. Maarten',
  'ST.+Pierre+%26+Miquelon' => 'ST. Pierre & Miquelon',
  'ST.+Vincent' => 'ST. Vincent',
  'San+Marino' => 'San Marino',
  'Sao+Tome%26+Principe' => 'Sao Tome& Principe',
  'Saudi+Arabia' => 'Saudi Arabia',
  'Senegal' => 'Senegal',
  'Serbia' => 'Serbia',
  'Seychelles' => 'Seychelles',
  'Sierra+Leone' => 'Sierra Leone',
  'Singapore' => 'Singapore',
  'Slovak+Republic' => 'Slovak Republic',
  'Slovenia' => 'Slovenia',
  'Solomon+IS.' => 'Solomon IS.',
  'Somalia' => 'Somalia',
  'South+Sudan' => 'South Sudan',
  'Spain' => 'Spain',
  'Srilanka' => 'Srilanka',
  'Sudan' => 'Sudan',
  'Surinam' => 'Surinam',
  'Swaziland' => 'Swaziland',
  'Sweden' => 'Sweden',
  'Switzerland' => 'Switzerland',
  'Syria' => 'Syria',
  'Tajikistan' => 'Tajikistan',
  'Tanzania' => 'Tanzania',
  'Thailand' => 'Thailand',
  'Thuraya' => 'Thuraya',
  'Togo' => 'Togo',
  'Tonga+IS.' => 'Tonga IS.',
  'Trinidad+%26+Tobago' => 'Trinidad & Tobago',
  'Tunisia' => 'Tunisia',
  'Turkey' => 'Turkey',
  'Turkmenistan' => 'Turkmenistan',
  'Turks+%26+Caicos+IS.' => 'Turks & Caicos IS.',
  'Tuvalu+IS.' => 'Tuvalu IS.',
  'UK' => 'UK',
  'USA' => 'USA',
  'Uganda' => 'Uganda',
  'Ukraine' => 'Ukraine',
  'United+Arab+Emirates' => 'United Arab Emirates',
  'Uruguay' => 'Uruguay',
  'Uzbekistan' => 'Uzbekistan',
  'Vanuatu' => 'Vanuatu',
  'Venezuela' => 'Venezuela',
  'Vietnam' => 'Vietnam',
  'Virgin+IS.' => 'Virgin IS.',
  'Wallis+%26+Futuna+IS.' => 'Wallis & Futuna IS.',
  'Western+Samoa' => 'Western Samoa',
  'Yemen+Republic' => 'Yemen Republic',
  'Zambia' => 'Zambia',
  'Zimbabwe' => 'Zimbabwe',
);
$city_options = array('臺北市', '基隆市', '新北市', '宜蘭縣', '新竹縣市', '桃園市', '苗栗縣', '臺中市',
                      '彰化縣', '南投縣', '嘉義縣', '雲林縣', '臺南市', '高雄市', '澎湖縣', '屏東縣',
                      '臺東縣', '花蓮縣', '金門縣', '連江縣', '南海諸島');
if (!isset($mem_lv_array)) { $mem_lv_array = array(); }

/* ------------------ 國外運費 / 其他費用 送出 ------------------ */
$tr_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && reqv('m_action') === 'add_tr') {
  $add_order_code = reqv('add_order_code');
  $tr_money = reqv('tr_money');
  $ot_money = reqv('ot_money');
  if ($ot_money === '') { $ot_money = 0; }

  if (!$can_edit) {
    $tr_msg = '您沒有編輯的權限';
  } elseif ($add_order_code === '' || !is_numeric($tr_money) || !is_numeric($ot_money)) {
    $tr_msg = '請輸入正確的金額';
  } else {
    // 原本是直接把輸入組進 SQL，這裡改用預處理語句
    $stmt = $pdo->prepare("UPDATE `orderlist` SET `transportation` = ?, `transportation_ps` = ?, `other_price` = ?, `other_ps` = ?, `total_amount` = `total_amount` + ? WHERE `order_code` = ?");
    $stmt->execute(array($tr_money, reqv('tr_ps'), $ot_money, reqv('ot_ps'), $tr_money + $ot_money, $add_order_code));
    $tr_msg = '已更新運費與其他費用';
  }
}

/* ------------------ 讀取訂單與會員 ------------------ */
$orderlist = array(
  'no' => '', 'order_code' => '', 'transaction_Id' => '', 'uptime' => '', 'buytime' => '',
  'order_state' => '未付款', 'post_cpy' => '', 'pre_post_date' => '', 'post_date' => '', 'post_num' => '',
  'order_ps' => '', 'atm_num' => '', 'store_num' => '', 'member_no' => '', 'member_code' => '',
  'invoice_name' => '', 'invoice_tel' => '', 'invoice_email' => '', 'invoice_country' => '',
  'invoice_zipcode' => '', 'invoice_city' => '', 'invoice_area' => '', 'invoice_addr' => '',
  'pay_mode' => '', 'get_mode' => '', 'invoice_type' => '', 'invoice_title' => '', 'invoice_num' => '',
  'amount' => '', 'transportation' => '', 'transportation_ps' => '', 'pay_mode_price' => '',
  'other_price' => '', 'other_ps' => '', 'total_amount' => '', 'order_info' => '', 'ps' => '', 'pay_info' => ''
);
$row_member = array();

if ($no !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `orderlist` WHERE `no` = ?");
  $stmt->execute(array($no));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $orderlist = array_merge($orderlist, $row);
    $stmt = $pdo->prepare("SELECT * FROM `member` WHERE `no` = ?");
    $stmt->execute(array($orderlist['member_no']));
    $m = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($m) { $row_member = $m; }
  }
}
// 日期欄位若是 0000-00-00 就顯示空白
foreach (array('pre_post_date', 'post_date') as $f) {
  if (substr((string)$orderlist[$f], 0, 4) === '0000') { $orderlist[$f] = ''; }
}

// 地址下拉選單的預設值（新增時預設臺北市中正區）
if ($no === '') {
  $city_def = '臺北市';
  $area_def = '中正區';
} else {
  $city_def = $orderlist['invoice_city'];
  $area_def = $orderlist['invoice_area'];
}
$lv_code = isset($row_member['mem_lv']) ? $row_member['mem_lv'] : '';
$lv_name = isset($mem_lv_array[$lv_code]) ? $mem_lv_array[$lv_code] : $lv_code;
$mem_name = isset($row_member['name']) ? $row_member['name'] : '';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<script type="text/javascript" src="../script/jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="../script/tw_zip.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  // 重寄訂單 Email（resend.php 會回傳 JSON）
  function resend(no, order_code, member_code) {
    if (!window.confirm('確定要重寄訂單 Email 嗎?')) { return; }
    $.ajax({
      url: "resend.php",
      type: "POST",
      dataType: "json",
      data: { no: no, member_code: member_code, order_code: order_code },
      success: function (res) {
        alert(res && res.ok ? '已寄出!!' : ((res && res.msg) ? res.msg : '寄信失敗'));
      },
      error: function () {
        alert('寄信失敗，請稍後再試');
      }
    });
  }

  function validator(f) {
    if (f.invoice_name.value.replace(/\s/g, '') === '') {
      alert('請輸入收件人姓名');
      f.invoice_name.focus();
      return false;
    }
    return true;
  }
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10%" align="left" bgcolor="#525252" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><br />
      <br />
      <table width="845" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td class="tb_menu_title">訂單資料</td>
              <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%">
            <tr>
              <td>
                <form name="form1" method="post" action="">
                  <br>
                  <table width="90%" style="font-size:16px;" border="0" align="center" cellpadding="0" cellspacing="0">
                    <?php if ($tr_msg !== '') { ?>
                    <tr><td colspan="3" style="color:#d00;"><?php echo h($tr_msg); ?></td></tr>
                    <?php } ?>
                    <tr>
                      <td width="39%" align="left">國外運費&nbsp;:&nbsp;<input type="text" name="tr_money" id="tr_money"></td>
                      <td width="34%" align="left">備註&nbsp;:&nbsp;<input type="text" name="tr_ps" id="tr_ps"></td>
                      <td width="27%" align="left">
                        <input type="submit" name="button2" id="button2" value="運費送出">
                        <input name="m_action" type="hidden" value="add_tr">
                        <input name="add_order_code" type="hidden" value="<?php echo h($orderlist['order_code']); ?>">
                      </td>
                    </tr>
                    <tr>
                      <td align="left">其他費用&nbsp;:&nbsp;<input type="text" name="ot_money" id="ot_money"></td>
                      <td align="left">備註&nbsp;:&nbsp;<input type="text" name="ot_ps" id="ot_ps"></td>
                      <td align="left">&nbsp;</td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
            <tr>
              <td align="center" valign="top">
                <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form2" name="CodeForm" onSubmit="return validator(this)">
                  <table width="770" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">訂單狀態</td>
                      <td width="109">
                        <select name="order_state" id="order_state">
                          <?php foreach ($order_states as $st) { ?>
                          <option value="<?php echo h($st); ?>" <?php chkSelected($st, $orderlist['order_state']); ?>><?php echo h($st); ?></option>
                          <?php } ?>
                        </select>
                      </td>
                      <td width="136" align="center">
                        <?php if ($orderlist['no'] !== '') { ?>
                        <input name="Submit5" type="button" value="重寄Email訂單" onClick="resend(<?php echo h(json_encode((string)$orderlist['no'])); ?>, <?php echo h(json_encode((string)$orderlist['order_code'])); ?>, <?php echo h(json_encode((string)$orderlist['member_code'])); ?>)" />
                        <?php } ?>
                      </td>
                      <td width="389" align="right">
                        <input type="hidden" value="<?php echo h($orderlist['no']); ?>" name="no" id="no" />
                        <input type="hidden" value="<?php echo h($orderlist['order_code']); ?>" name="order_code" id="order_code" />
                        <?php if ($can_edit) { ?>
                        <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                        <?php } ?>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">修改日期</td>
                      <td colspan="3"><?php echo h($orderlist['uptime']); ?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">訂單日期</td>
                      <td colspan="3"><?php echo h($orderlist['buytime']); ?></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">訂單編號</td>
                      <td colspan="3"><?php if ($orderlist['no'] !== '') { echo 'C17' . sprintf("%06d", $orderlist['no']); } ?></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">訂單編碼</td>
                      <td colspan="3">
                        <?php echo h($orderlist['order_code']); ?>
                        <?php if ($orderlist['order_code'] !== '') { ?>
                        &nbsp;<a href="https://shopping.windmill.com.tw/email_order-T.php?order_code=<?php echo urlencode($orderlist['order_code']); ?>" target="_blank">訂單連結</a>
                        <?php } ?>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">LINE PAY訂單編碼</td>
                      <td colspan="3"><?php echo h($orderlist['transaction_Id']); ?></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">物流公司</td>
                      <td colspan="3">
                        <?php foreach (array('新竹物流', '郵政物流', '順豐快遞', '通盈通運', '海外船運') as $cpy) { ?>
                        <label><?php echo h($cpy); ?><input type="radio" value="<?php echo h($cpy); ?>" name="post_cpy" <?php chkChecked($cpy, $orderlist['post_cpy']); ?>></label>&nbsp;&nbsp;&nbsp;
                        <?php } ?>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">預購寄送日期</td>
                      <td colspan="3">
                        <input type="text" value="<?php echo h($orderlist['pre_post_date']); ?>" name="pre_post_date" id="pre_post_date">
                        <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'pre_post_date'})">
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">寄送日期</td>
                      <td colspan="3">
                        <input type="text" value="<?php echo h($orderlist['post_date']); ?>" name="post_date" id="post_date">
                        <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'post_date'})">
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">寄送貨號</td>
                      <td colspan="3"><input name="post_num" type="text" id="post_num" value="<?php echo h($orderlist['post_num']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" class="tb_edit_title">訂單備註<br>(管理員填寫)<br></td>
                      <td colspan="3"><textarea name="order_ps" id="order_ps" cols="45" rows="5"><?php echo h($orderlist['order_ps']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">匯款號碼</td>
                      <td colspan="3"><input name="atm_num" type="text" id="atm_num" value="<?php echo h($orderlist['atm_num']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">超商代碼</td>
                      <td colspan="3"><input name="store_num" type="text" id="store_num" value="<?php echo h($orderlist['store_num']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td width="94" align="center" class="tb_edit_title">會員編號</td>
                      <td colspan="3">
                        <input name="member_no" type="text" id="member_no" value="<?php echo h($orderlist['member_no']); ?>" size="30" readonly />
                        <?php echo h($lv_name); ?>-<?php echo h($mem_name); ?>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">會員代碼</td>
                      <td colspan="3"><input name="member_code" type="text" id="member_code" value="<?php echo h($orderlist['member_code']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">收件人姓名</td>
                      <td height="30" colspan="3"><input name="invoice_name" type="text" id="invoice_name" value="<?php echo h($orderlist['invoice_name']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">收件人電話</td>
                      <td height="30" colspan="3"><input name="invoice_tel" type="text" id="invoice_tel" value="<?php echo h($orderlist['invoice_tel']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">訂單信箱</td>
                      <td height="30" colspan="3"><input name="invoice_email" type="text" id="invoice_email" value="<?php echo h($orderlist['invoice_email']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">收件人地址</td>
                      <td height="30" colspan="3">
                        <span class="accInfMainContentForm">
                          <select name="invoice_country" id="invoice_country" class="accInfAddWh">
                            <option value="">請選擇</option>
                            <?php foreach ($country_options as $val => $label) { ?>
                            <option value="<?php echo h($val); ?>" <?php chkSelected($val, $orderlist['invoice_country']); ?>><?php echo h($label); ?></option>
                            <?php } ?>
                          </select>
                          <input name="invoice_zipcode" class="accInfFormZip" id="invoice_zipcode" size="4" value="<?php echo h($orderlist['invoice_zipcode']); ?>" />
                        </span>
                        <select name="invoice_city" id="invoice_city" class="accInfAddWh" onChange="addOption_list(this.options[this.options.selectedIndex].text,'invoice_area','1','invoice_zipcode');">
                          <option value="" selected="selected">請選擇</option>
                          <?php foreach ($city_options as $city) { ?>
                          <option value="<?php echo h($city); ?>"><?php echo h($city); ?></option>
                          <?php } ?>
                        </select>
                        <select name="invoice_area" id="invoice_area" class="accInfAddWh" onChange="changZipcode('invoice_zipcode',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
                          <option value="1" selected="selected">請選擇</option>
                        </select>
                        <span class="accInfMainContentForm">
                          <script language="JavaScript" type="text/javascript">
                            // 預設值填入：addOption_list(城市名稱, 地區欄位名稱, 地區預設值, 郵遞區號欄位名稱)
                            addOption_list(<?php echo json_encode((string)$city_def); ?>, 'invoice_area', <?php echo json_encode((string)$area_def); ?>, 'invoice_zipcode');
                            // citySelect(城市欄位名稱, 城市預設值)
                            citySelect('invoice_city', <?php echo json_encode((string)$city_def); ?>);
                          </script>
                          <br />
                          <input name="invoice_addr" type="text" id="invoice_addr" class="accInfFormWh" value="<?php echo h($orderlist['invoice_addr']); ?>" size="40" />
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">付款方式</td>
                      <td colspan="3" class="tb_edit_info">
                        <?php
                        $pay_modes = array('card' => '信用卡', 'atm' => 'ATM轉帳', 'store_pay' => '超商付款',
                                           'line_pay' => 'Line Pay付款', 'post' => '郵政劃撥', 'foreign' => '國外訂購');
                        foreach ($pay_modes as $val => $label) { ?>
                        <label><?php echo h($label); ?><input type="radio" value="<?php echo h($val); ?>" name="pay_mode" <?php chkChecked($val, $orderlist['pay_mode']); ?>></label>&nbsp;&nbsp;&nbsp;
                        <?php } ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">配送方式</td>
                      <td colspan="3" class="tb_edit_info">
                        <label>宅配<input type="radio" value="post" name="get_mode" <?php chkChecked('post', $orderlist['get_mode']); ?>></label>&nbsp;&nbsp;&nbsp;
                        <label>超商取貨<input type="radio" value="store" name="get_mode" <?php chkChecked('store', $orderlist['get_mode']); ?>></label>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">發票</td>
                      <td colspan="3" class="tb_edit_info">
                        <label>二聯<input type="radio" value="2" name="invoice_type" <?php chkChecked('2', $orderlist['invoice_type']); ?>></label>&nbsp;&nbsp;&nbsp;
                        <label>三聯<input type="radio" value="3" name="invoice_type" <?php chkChecked('3', $orderlist['invoice_type']); ?>></label>
                      </td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">公司抬頭</td>
                      <td height="30" colspan="3"><input name="invoice_title" type="text" id="invoice_title" value="<?php echo h($orderlist['invoice_title']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">統一編號</td>
                      <td height="30" colspan="3"><input name="invoice_num" type="text" id="invoice_num" value="<?php echo h($orderlist['invoice_num']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">商品小計</td>
                      <td height="30" colspan="3"><input name="amount" type="text" id="amount" value="<?php echo h($orderlist['amount']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">運費</td>
                      <td colspan="3"><input name="transportation" type="text" id="transportation" value="<?php echo h($orderlist['transportation']); ?>" size="30" readonly /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">運費備註</td>
                      <td height="30" colspan="3"><input name="transportation_ps" type="text" id="transportation_ps" value="<?php echo h($orderlist['transportation_ps']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">超商付款手續費</td>
                      <td colspan="3"><input name="pay_mode_price" type="text" id="pay_mode_price" value="<?php echo h($orderlist['pay_mode_price']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">其他費用</td>
                      <td colspan="3"><input name="other_price" type="text" id="other_price" value="<?php echo h($orderlist['other_price']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">其他費用備註</td>
                      <td height="30" colspan="3"><input name="other_ps" type="text" id="other_ps" value="<?php echo h($orderlist['other_ps']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" class="tb_edit_title">訂單金額</td>
                      <td height="30" colspan="3"><input name="total_amount" type="text" id="total_amount" value="<?php echo h($orderlist['total_amount']); ?>" size="30" /></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" class="tb_edit_title">訂單備註<br>(客戶填寫)</td>
                      <td colspan="3"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo h($orderlist['ps']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">付款資訊</td>
                      <td colspan="3">
                        <textarea name="pay_info" cols="45" rows="5" readonly id="pay_info"><?php echo h($orderlist['pay_info']); ?></textarea>
                        <input type="hidden" name="order_info" id="order_info" value="<?php echo h($orderlist['order_info']); ?>" />
                      </td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
          </table></td>
        </tr>
      </table>
      <br />
    </td>
  </tr>
</table>
</body>
</html>
