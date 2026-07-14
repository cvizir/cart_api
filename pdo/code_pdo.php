<?php
ini_set('default_charset', 'utf-8');
date_default_timezone_set("Asia/Taipei");
$select_db = $_REQUEST['select_db'];
$method_state = $_REQUEST['method_state'];


define("DB_HOST", "localhost");
define("DB_NAME", "root");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
//define("DB_PASSWORD","peter919");
define("WEB_ROOT", "http://book.winnie.com.tw/"); //http://neocity.com.tw/
define("WEB_ADMIN", WEB_ROOT . 'admin/'); //http://neocity.com.tw/


mysql_pconnect(DB_HOST, DB_USER, DB_PASSWORD);
//mysql_select_db(DB_NAME);
mysql_query("set names utf8");

// 	$tables = array();
// $stmt = $db->query("SHOW TABLES");
// while($row = $stmt->fetch(PDO::FETCH_NUM)){
//     $tables[] = $row[0];
// }

// var_dump($tables);



$tab_1 = '&nbsp;&nbsp;&nbsp;&nbsp;';
$tab_2 = "$tab_1$tab_1";
$tab_3 = "$tab_1$tab_2";
$tab_4 = "$tab_2$tab_2";
$tab_5 = "$tab_2$tab_3";
$br_1 = '<br/>';
$br_2 = '<br/><br/>';
$br_3 = '<br/><br/><br/>';
$line_str = '------------------------------------------------';

if ($select_db <> "" && $method_state <> "") {
  mysql_select_db($select_db);
  $result = mysql_list_tables($select_db);

  $tables_num = mysql_num_rows($result);
  while ($row_tables = mysql_fetch_row($result)) {
    $row[] = $row_tables[0];
  }
}

function dot_check($now_value, $max_value, $add_str)
{
  if ($now_value <> $max_value) {
    return "$add_str";
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>程式碼產生器</title>

  <style type="text/css">
    .body_center {
      text-align: left;
    }

    .db_name {
      color: #0000ff;
      /*色彩*/
      font-family: Arial, '微軟正黑體', sans-serif;
      /*文字字型*/
      font-size: 25px;
      /*文字大小*/
      font-weight: bold;
      /*文字粗體*/
      line-height: 200%;
      /*設定行高*/
    }

    .mode_name {
      color: #f00;
      /*色彩*/
      font-family: Arial, '微軟正黑體', sans-serif;
      /*文字字型*/
      font-size: 20px;
      /*文字大小*/
      font-weight: bold;
      /*文字粗體*/
      line-height: 200%;
      /*設定行高*/
    }

    body {
      margin-left: 0px;
      margin-top: 0px;
      margin-right: 0px;
      margin-bottom: 0px;
      font-family: Arial, 新細明體, sans-serif;
      /*文字字型*/

    }

    ul {
      margin: 0;
      padding: 0;
      list-style-type: none;
    }

    li {
      margin: 0;
      padding: 0;
    }
  </style>

</head>

<body class="body_center"><br />

  <form id="form1" name="form1" method="post" action="code_pdo.php">
    <table width="532" border="0" align="left" cellpadding="0" cellspacing="0" style="padding: 20px 0 0 0;  text-align: center;">
      <tr>
        <td width="80" align="left">&nbsp;&nbsp;&nbsp;&nbsp;資料庫</td>
        <td width="84" align="left">
          <select name="select_db" id="select_db">
            <?php
            $db_list = mysql_list_dbs();
            while ($row_dbs = mysql_fetch_array($db_list)) {
            ?>
              <option value="<?php echo $row_dbs['Database']; ?>" <?php if ($row_dbs['Database'] == $select_db) {
                                                                    echo 'selected="selected"';
                                                                  } ?>><?php echo $row_dbs['Database']; ?></option>
            <?php } ?>
          </select>
        </td>
        <td width="80" align="left">傳送方法</td>
        <td width="86" align="left"><select name="method_state" id="method_state">
            <option value="REQUEST" <?php if ("REQUEST" == $method_state) {
                                      echo 'selected="selected"';
                                    } ?>>REQUEST</option>
            <option value="POST" <?php if ("POST" == $method_state) {
                                    echo 'selected="selected"';
                                  } ?>>POST</option>
            <option value="GET" <?php if ("GET" == $method_state) {
                                  echo 'selected="selected"';
                                } ?>>GET</option>
          </select></td>
        <td width="60" align="center"><input type="submit" name="button" id="button" value="送出" /></td>
      </tr>
    </table>
  </form>
  <table width="1000" border="0" align="left" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <div style="width:900px; padding-left:10px;">
          <ul>
            <?php for ($K = 0; $K < count($row); $K++) { ?>
              <li style="float:left; padding:10px 0 0 10px; font-size:20px;"><a href="#" onclick="div_open(<?php echo $K ?>)"><?php echo $row[$K]; ?></a>&nbsp;&nbsp;</li>
            <?php } ?>
          </ul>
        </div>
      </td>
    </tr>
    <?php
    if ($select_db <> "" && $method_state <> "") {
      echo  "<br /><br /><br />";
      //echo "$tab_1<span>".'資料庫 '.$select_db.' 傳送方法 '.$method_state.'</span>'."$br_2";
      for ($K = 0; $K < count($row); $K++) {
        if ($K == "0") {
          $display_state = 'block';
        } else {
          $display_state = 'none';
        }
        echo '<tr><td id="div_' . "$K" . '" style="display:' . $display_state . ';">';
        $result2 = mysql_query("select * from " . $row[$K]);
        echo "<br />$tab_1" . '<span class="db_name">' . $row[$K] . '</span>' . "$br_1";
        for ($i = 0; $i < mysql_num_fields($result2); $i++) {
          $field_name[] = mysql_field_name($result2, $i);
        }


        /* ------------------------------------------------ 傳統直述句語法 -------------------------------------------------------------	 */

        echo "$tab_1" . '<span class="mode_name">' . "$line_str" . '傳統直述句語法' . "$line_str" . "</span>" . "$br_2";
        for ($J = 0; $J < count($field_name); $J++) {
          echo "$tab_1" . '$' . $field_name[$J] . '=trim($_' . "$method_state" . '[\'' . $field_name[$J] . '\']);' . "$br_1";
        }
        echo "$br_1";
        for ($J = 0; $J < count($field_name); $J++) {
          echo "$tab_1" . '$_SESSION[\'' . $row[$K] . '\'][\'' . $field_name[$J] . '\']=$row_' . $row[$K] . '[\'' . $field_name[$J] . '\'];' . "$br_1";
        }

        echo "$br_1";
        echo "$tab_1" . '$sql_' . $row[$K] . '="SELECT * FROM `' . $row[$K] . '` WHERE `no`=\'$no\' AND `ishow`=\'1\' ORDER BY  m_sort ASC , no DESC , uptime ASC";' . "$br_1";
        echo "$tab_1" . '$rs_' . $row[$K] . '=' . 'mysql_query($sql_' . $row[$K] . ');' . "$br_1";
        echo "$tab_1" . '$num_' . $row[$K] . '=' . 'mysql_num_rows($rs_' . $row[$K] . ');' . "$br_1";
        echo "$tab_1" . '$row_' . $row[$K] . '=' . 'mysql_fetch_array($rs_' . $row[$K] . ',MYSQL_ASSOC);' . "$br_1";
        echo "$tab_1" . 'while($row_' . $row[$K] . '=' . 'mysql_fetch_array($rs_' . $row[$K] . ',MYSQL_ASSOC)){' . "$br_2";
        echo "$tab_1" . '}' . "$br_1";


        echo "$br_1";
        echo "$tab_1" . '$sql_add="INSERT INTO `' . $row[$K] . '` VALUES (\'$' . join("', '$", $field_name) . '\')";' . "$br_1";
        echo "$tab_1" . 'mysql_query($sql_add);' . "$br_2";

        for ($J = 1; $J < count($field_name); $J++) {
          if ($J < (count($field_name) - 1)) {
            $cut = ',';
          } else {
            $cut = '';
          }
          $sql_update_string = $sql_update_string . '`' . $field_name[$J] . '` = \'$' . $field_name[$J] . '\' ' . "$cut" . ' ';
        }
        echo "$tab_1" . '$sql_update="UPDATE `' . $row[$K] . '` SET ' . $sql_update_string . " WHERE `no` ='\$no'\";" . "$br_1";
        echo "$tab_1" . 'mysql_query($sql_update);' . "$br_2";


        echo "$tab_1" . '$sql_del=DELETE FROM `' . $row[$K] . '` WHERE `no` =\'$no\' ;' . "$br_1";
        echo "$tab_1" . 'mysql_query($sql_del);' . "$br_2";

        //if($pid=="" || $name=="" || $tel=="" || $take_date=="" || $take_start=="" || $take_end=="" || $full_price=="" || $twofer=="" || $excursion=="" ){
        echo "$tab_1" . 'if( $' . join("==\"\" || $", $field_name) . '=="" ){' . "$br_2";
        echo "$tab_1" . '}' . "$br_2";
        //print_r($field_name);
        echo  "<br /><br /><br />";
        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_1" . "echo '" . $field_name[$J] . "='.&quot;$" . $field_name[$J] . "&quot;.&quot;&lt;br /&gt;&lt;br /&gt;&quot;;";
          echo '<br />';
        }
        echo  "<br /><br /><br />";


        /*   ------------------------------------------------ 陣列語法 -------------------------------------------------------------	 */


        echo "$tab_1" . '<span class="mode_name">' . "$line_str" . '陣列語法' . "$line_str" . "</span>" . "$br_2";
        echo "$tab_1" . '$sql_data=array(' . '<br />';
        for ($J = 1; $J < count($field_name); $J++) {
          $dot = dot_check($J, (count($field_name) - 1), ',');
          echo "$tab_2" . "'" . $field_name[$J] . '\'=&gt;$_' . "$method_state" . '[\'' . $field_name[$J] . '\']' . "$dot" . '<br />';
        }
        echo "$tab_1" . ');';
        echo "$br_3";

        $random = '_' . date("Ymd_H_i");
        echo "$tab_1" . '$sql_data=array(' . '<br />';
        for ($J = 1; $J < count($field_name); $J++) {
          $dot = dot_check($J, (count($field_name) - 1), ',');
          echo "$tab_2" . '\'' . $field_name[$J] . '\'' . '=&gt;\'' . $field_name[$J] . "$random" . '\'' . $dot . '<br />';
        }
        echo "$tab_1" . ');<br /><br />';
        echo "$br_3";

        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_3" . '\'' . $field_name[$J] . '\'' . '=&gt;\'\',<br />';
        }
        echo  "$br_3";

        echo "$tab_3" . '$verification = array(' . join(",", $field_name) . ');<br />';
        echo "$tab_3" . '$verification = array(<br />' . $tab_4 . join(",<br />" . $tab_4, $field_name) . '<br />' . $tab_3 . ');<br />';
        echo "$tab_3" . 'for( $I=0; $I <(sizeof($verification)); $I++ ){<br />';
        echo "$tab_4" . 'if( has_special_str($_REQUEST[$verification[$I]]) && $_REQUEST[$verification[$I] !==\'\' ){ <br />';
        echo "$tab_4" . '	$error_index = $error_index + 1; <br />';
        echo "$tab_4" . '}<br />';
        echo "$tab_3" . '}<br /><br />';
        echo "$br_4";

        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_3" . 'has_special_str($_REQUEST[\'' . $field_name[$J] . '\']);<br />';
        }
        echo  "$br_3";


        /*   ------------------------------------------------ PDO語法 -------------------------------------------------------------	 */


        echo "$tab_1" . '<span class="mode_name">' . "$line_str" . 'PDO語法' . "$line_str" . "</span>" . "$br_2";
        echo "$tab_1" . '$data_to_update=[' . '<br />';
        for ($J = 1; $J < count($field_name); $J++) {
          $dot = dot_check($J, (count($field_name) - 1), ',');
          echo "$tab_2" . "'" . $field_name[$J] . '\' =&gt; $_' . "$method_state" . '[\'' . $field_name[$J] . '\']' . "$dot" . '<br />';
        }
        echo "$tab_1" . '];';
        echo "$br_3";

        echo "$tab_1" . '$data_to_update=[' . '<br />';
        for ($J = 1; $J < count($field_name); $J++) {
          $dot = dot_check($J, (count($field_name) - 1), ',');
          echo "$tab_2" . "'" . $field_name[$J] . '\' =&gt; $' .$field_name[$J]. "$dot" . '<br />';
        }
        echo "$tab_1" . '];';
        echo "$br_3";


        $random = '_' . date("Ymd_H_i");
        echo "$tab_1" . '$data_to_update=[' . '<br />';
        for ($J = 1; $J < count($field_name); $J++) {
          $dot = dot_check($J, (count($field_name) - 1), ',');
          echo "$tab_2" . '\'' . $field_name[$J] . '\'' . ' =&gt; \'' . $field_name[$J] . "$random" . '\'' . $dot . '<br />';
        }
        echo "$tab_1" . '];<br /><br />';
        echo "$br_3";

        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_3" . '\'' . $field_name[$J] . '\'' . '=&gt;\'\',<br />';
        }
        echo  "$br_3";

        echo "$tab_3" . '$verification = array(' . join(",", $field_name) . ');<br />';
        echo "$tab_3" . '$verification = array(<br />' . $tab_4 . join(",<br />" . $tab_4, $field_name) . '<br />' . $tab_3 . ');<br />';
        echo "$tab_3" . 'for( $I=0; $I <(sizeof($verification)); $I++ ){<br />';
        echo "$tab_4" . 'if( has_special_str($_REQUEST[$verification[$I]]) && $_REQUEST[$verification[$I] !==\'\' ){ <br />';
        echo "$tab_4" . '	$error_index = $error_index + 1; <br />';
        echo "$tab_4" . '}<br />';
        echo "$tab_3" . '}<br /><br />';
        echo "$br_4";

        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_3" . 'has_special_str($_REQUEST[\'' . $field_name[$J] . '\']);<br />';
        }
        echo  "$br_3";


        /* ------------------------------------------------ 類別語法 -------------------------------------------------------------	 */


        echo "$tab_1" . '<span class="mode_name">' . "$line_str" . '類別語法' . "$line_str" . "</span>" . "$br_2";
        echo "$tab_2" . 'var  $db_table = "' . $row[$K] . '";';
        echo '<br /><br />';
        for ($J = 0; $J < count($field_name); $J++) {
          echo "$tab_2" . "var  $" . $field_name[$J] . ";";
          echo '<br />';
        }

        echo  "<br /><br /><br />";
        for ($J = 0; $J < count($field_name); $J++) {
          echo "$tab_3" . '$this-&gt;' . $field_name[$J] . '= $row["' . $field_name[$J] . '"];';
          echo '<br />';
        }



        echo  "<br /><br /><br />";

        for ($J = 1; $J < count($field_name); $J++) {
          if ($J <> (count($field_name) - 1)) {
            echo "$tab_5" . $field_name[$J] . '=\'&quot;.$this-&gt;' . $field_name[$J] . ".&quot;',";
          } else {
            echo "$tab_5" . $field_name[$J] . '=\'&quot;.$this-&gt;' . $field_name[$J] . ".&quot;'&quot;;";
          }
          echo '<br />';
        }
        echo '<br />';

        echo "$tab_1" . '$' . ucfirst($row[$K]) . ' = new ' . ucfirst($row[$K]) . '($no);';
        echo '<br /><br />';
        for ($J = 1; $J < count($field_name); $J++) {
          echo "$tab_2" . '$' . ucfirst($row[$K]) . '-&gt;' . $field_name[$J] . '= trim($_' . $method_state . '[&quot;' . $field_name[$J] . '&quot;]);';
          echo '<br />';
        }
        echo '<br /><br />';

        echo '<form id="form_f' . $row[$K] . '" name="form_f' . $row[$K] . '" method="post" action="#">';
        echo "$tab_1";
        for ($I = 0; $I < count($field_name); $I++) {
          if ($field_name[$I] <> "no" && $I <> 0) {
            echo '<input name="f' . $row[$K] . '" type="checkbox" id="checkbox" value="' . $field_name[$I] . '" />' . $field_name[$I] . '&nbsp;';
          } else {
          }
        }
        echo "<br /><br />$tab_1" . '<input type="button" name="' . $row[$K] . '" id="' . $row[$K] . '" onclick="sql_crt(\'' . $row[$K] . '\',\'f' . $row[$K] . '\')" value="產生SQL" /><input type="button" name="' . $row[$K] . '" id="' . $row[$K] . '" onclick="chk_all(\'true\',\'f' . $row[$K] . '\')" value="全選" /><input type="button" name="' . $row[$K] . '" id="' . $row[$K] . '" onclick="chk_all(\'false\',\'f' . $row[$K] . '\')" value="取消" />';
        echo '</form>';




        echo  "<br /><br /><br />";



        $sql_update_string = "";
        $field_name = NULL;
        //echo $row[$K]."<BR>";
      }

      echo '</td></tr>';
    }


    echo "$tab_1" . '<span class="mode_name">' . "$line_str" . 'PDO語法' . "$line_str" . "</span>" . "$br_2";



    ?>
    <tr>
      <td>
        <div id="sql_code"></div>
      </td>
    </tr>
    <tr>
      <td height="500">&nbsp;</td>
    </tr>
  </table>





  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />










  <script language="JavaScript" type="text/JavaScript">
    function div_open(menuid){

	for (i=0; i<<?php echo "$tables_num"; ?>;i=i+1){
		var div_id='div_'+i;
		if(i==menuid){
			document.getElementById(div_id).style.display = 'block';
			}else{
			document.getElementById(div_id).style.display = 'none';
		}
	}


}

function sql_crt(dbn,db_name){
        var html_str ="";
		var html_str2 ="";
		var space_str="<?php echo "$tab_5"; ?>";
		var chk_num=0;
		var for_i=0;
		var select_str="";
    //var db_name_chk=document.form_admin.db_name.length;
        var objchk   =document.forms['form_'+db_name].elements[db_name];
        //var   objchk   =   tform.checkbox;
        //alert(objchk);
		for(i=0;i <objchk.length;i++){
			if(objchk[i].checked==true){ 
			chk_num=chk_num+1;
			if(chk_num==1){
				select_str=objchk[i].value;
				}else{
				select_str=select_str+","+objchk[i].value;	
			}
			}
		}
		if(chk_num==0){
		alert("妳沒有選擇任何欄位!!");	
		}else{
			for(i=0;i <objchk.length;i++){   
                if(objchk[i].checked==true){
					for_i++;	
						if(for_i==(chk_num)){
						html_str=html_str+space_str+objchk[i].value+'=\'".$this-&gt;'+objchk[i].value+'."\'";<BR>';
						html_str2=html_str2+'`'+objchk[i].value+'`=\'$'+objchk[i].value+'\'';
						}else{
						html_str=html_str+space_str+objchk[i].value+'=\'".$this-&gt;'+objchk[i].value+'."\',<BR>';
						html_str2=html_str2+'`'+objchk[i].value+'`=\'$'+objchk[i].value+'\',';
						}				
				}
				var sql_list= "$sql_list=\"SELECT "+select_str+" FROM `"+dbn+"`\";"+'<BR><BR>'+space_str+'mysql_query($sql_list);';
                var sql_update="$sql_update=\"UPDATE "+dbn+" SET "+html_str2+" WHERE no='$no'\";"+'<BR><BR>'+space_str+'mysql_query($sql_update);';
				var sql_inster="$sql_add=\"INSERT INTO "+dbn+" SET "+html_str2+'";<BR><BR>'+space_str+'mysql_query($sql_add);';	
		}

				
		}  
         document.getElementById("sql_code").innerHTML = html_str+'<BR><BR>'+space_str+sql_list+'<BR><BR>'+space_str+sql_update+'<BR><BR>'+space_str+sql_inster;


}

	function chk_all(chk_state,db_name){
		alert(chk_state+" and "+db_name);
		var objchk=document.forms['form_'+db_name].elements[db_name];

		for(i=0;i<objchk.length;i++){ 
		    if(chk_state=='true'){
			   	objchk[i].checked = true;
				}else{
				objchk[i].checked = false;
			}
		}
	}
</script>
</body>

</html>