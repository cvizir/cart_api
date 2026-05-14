<?php
	//DomainObjectList類別	
	class DBClass{
		
		var $page=1;
		var $pageSize=20;
		var $menuSize=10;
		var $total;
		var $pageTotal;
		var $db_table;
		
		function DBClass($db_table,$page=1,$pageSize=20){
			$this->db_table=$db_table;
			$this->page=$page;
			$this->pageSize=$pageSize;	
		}



		function getAdminList($where="",$order="",$trace=0){	
		   	$sqlTotal="SELECT COUNT(*) AS total FROM ".$this->db_table. $where;	
			$sql="SELECT * FROM `".$this->db_table."` ".$where.$order;
			if($trace=="1"){echo "<br>&nbsp;&nbsp;&nbsp;getAdminList_SQL=".$sql."<br><br>";}
			return $this->query($sql, $sqlTotal, true);	
		}
		
		function getAdminList2($where="",$order="",$trace=0){	
		   	$sqlTotal="SELECT COUNT(*) AS total FROM  product a LEFT JOIN  product_type b ON a.product_code = b.product_code ".$where;	
			//SELECT * FROM  product a RIGHT OUTER JOIN  product_type b ON a.product_code = b.product_code
			$sql="SELECT a.*,b.pd_price FROM  product a LEFT JOIN  product_type b ON a.product_code = b.product_code ".$where.$order;
			if($trace=="1"){echo "<br>&nbsp;&nbsp;&nbsp;getAdminList_SQL2=".$sql."<br><br>";}
			return $this->query($sql, $sqlTotal, true);	
		}
		

		function query($sql, $sqlTotal, $pageFlag=true){
			startDB();
			$rs=mysql_query($sqlTotal);
			$row=mysql_fetch_array($rs,MYSQL_ASSOC);
			$this->total=$row["total"];
			if($this->total > 0){	
				if($pageFlag){						
					$start=($this->page-1)*$this->pageSize;
					if($start < $this->total){
						$start = max(0, $start);
					}else{
						$start = ($this->getPageTotal()-1) * $this->pageSize;
					}
					$sql=$sql." LIMIT ".$start.",".$this->pageSize;
				}
				$rs=mysql_query($sql);
				return $rs;
			}else{			
				return array();	
			}
		}
		
		function showPageMenu($argument=""){
			if(empty($this->pageSize)){
				return;
			}
			
			$pageTotal=ceil($this->total/$this->pageSize);
			
//			if($pageTotal<=1){
//				return;
//			}
			
			if($this->page % $this->menuSize == 0){
				$pageStart=$this->page-($this->menuSize)+1;
			}else{
				$pageStart=$this->page-($this->page%$this->menuSize)+1;
			}
			
			$pageEnd=$pageStart+$this->menuSize-1;
			$pageEnd=($pageEnd>$pageTotal)?$pageTotal:$pageEnd;
			
			
			$htm="";	
			//$htm.="共有<strong> ".$this->total."</strong> 筆資料 &nbsp;&nbsp;";
			if($this->page > 1){
				$htm.="<a class=\"linka\" href=\"?page=".($this->page-1).$argument."\">上一頁</a> ";
			}else{
				$htm.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			for($i=$pageStart;$i<=$pageEnd;$i++){
				if($i==$this->page){
					$htm.=" <strong>".$i."</strong>";
				}else{
					$htm.=" <a class=\"linka\" href=\"?page=".$i.$argument."\">".$i."</a>";
				}
			} 
			if($this->page<$pageTotal){
				$htm.=" <a class=\"linka\" href=\"?page=".($this->page+1).$argument."\">下一頁</a>";
			}else{
				if($pageTotal != "1"){
				$htm.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
			}
			return $htm;
		}

			function showPageDMenu($argument="",$list_mode='all'){
			if(empty($this->pageSize)){
				return;
			}
			
			$pageTotal=ceil($this->total/$this->pageSize);
			if($this->page % $this->menuSize == 0){
				$pageStart=$this->page-($this->menuSize)+1;
			}else{
				$pageStart=$this->page-($this->page%$this->menuSize)+1;
			}
			$pageEnd=$pageStart+$this->menuSize-1;
			$pageEnd=($pageEnd>$pageTotal)?$pageTotal:$pageEnd;

			$htm='<div class="menu_div"><ul>';	
			//$htm.="共有<strong> ".$this->total."</strong> 筆資料 &nbsp;&nbsp;";
			if($this->page > 1){
				$htm.="<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=".($this->page-1).$argument."\">上一頁</a></li>";
			}else{
				$htm.="<li style=\"float:left;width:60px;\">&nbsp;</li>";
			}
			if($list_mode=="all"){
			for($i=$pageStart;$i<=$pageEnd;$i++){
				if($i==$this->page){
					$htm.="<li style=\"float:left;width:30px;\"><strong>".$i."</strong></li>";
				}else{
					$htm.="<li style=\"float:left;width:30px;\"><a class=\"linka\" href=\"?page=".$i.$argument."\">".$i."</a></li>";
				}
			} 
			}
			if($this->page<$pageTotal){
				$htm.="<li style=\"float:left;width:60px;\"><a class=\"linka\" href=\"?page=".($this->page+1).$argument."\">下一頁</a></li>";
			}else{
				if($pageTotal != "1"){
				$htm.="<li style=\"float:left;width:60px;\">&nbsp;</li>";}
			}
			$htm=$htm."</ul></div>";
			return $htm;
		}
		

		
			function showPageMenu2($argument="",$list_mode='all'){
			if(empty($this->pageSize)){
				return;
			}
			
			$pageTotal=ceil($this->total/$this->pageSize);
			if($this->page % $this->menuSize == 0){
				$pageStart=$this->page-($this->menuSize)+1;
			}else{
				$pageStart=$this->page-($this->page%$this->menuSize)+1;
			}
			$pageEnd=$pageStart+$this->menuSize-1;
			$pageEnd=($pageEnd>$pageTotal)?$pageTotal:$pageEnd;

					
			$htm='<ul class="page-numbers">'.$htm;	
			
			if($this->page > 1){
				$htm.="<li class=\"prev page-numbers\"><a href=\"?page=".($this->page-1).$argument."\">上一頁</a></li>";
			}
			

			for($i=$pageStart;$i<=$pageEnd;$i++){
				if($i==$this->page){
					$htm.="<li><span class=\"page-numbers current\">".$i."</span></li>";
				}else{
					$htm.="<li><a class=\"page-numbers\" href=\"?page=".$i.$argument."\">".$i."</a></li>";
				}
			} 

			if($this->page<$pageTotal){
				$htm.="<li class=\"next page-numbers\"><a href=\"?page=".($this->page+1).$argument."\">下一頁</a></li>";
			}
			
			$htm=$htm."</ul>";
			
			
			return $htm;
		}	

		
		

				
	}//<!-- end .class DBList -->
		


?>