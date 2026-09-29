// JavaScript By Away
// http://www.dfuns.idv.tw

window.onload=function(){
	
	var num=1;
	var tNum=4;
	var duration=4800;
	
	run();
	document.getElementById("box").onmouseover=stopRun;
	document.getElementById("box").onmouseout=run;
	
	for(var i=1; i<=tNum; i++){
		document.getElementById("tab"+i).onclick=show;
		document.getElementById("con"+i).style.display="none";
	}
	document.getElementById("con1").style.display="block";
	document.getElementById("tab1").className="now-tab";

//===========================================
	function autoShow(){
		for(var i=1; i<=tNum; i++){
			document.getElementById("con"+i).style.display="none";
			document.getElementById("tab"+i).className="";
		}
		if(num<tNum){ num++;}else{ num=1;}
		document.getElementById("con"+num).style.display="block";
		document.getElementById("tab"+num).className="now-tab";
	}
	
	function show(){
		num=this.id.substr(2)-1;
		autoShow();
	}
	
	function stopRun(){ clearInterval(myInterval);}
	
	function run(){ myInterval= setInterval( autoShow, duration);}
	
}