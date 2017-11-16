window.onload = function ()
{
	reloadComAJAX();
}
function reloadComAJAX()
{
	var a;
	var sala=document.getElementById("sala").value;
	var reload=document.getElementById("reload").value;
	if (window.XMLHttpRequest)
	{// If the browser if IE7+[or]Firefox[or]Chrome[or]Opera[or]Safari
	  a=new XMLHttpRequest();
	}
   else
	{//If browser is IE6, IE5
	  a=new ActiveXObject("Microsoft.XMLHTTP");
	}

	a.onreadystatechange=function()
	{
	  if (a.readyState==4 && a.status==200)
	  {
		if(a.responseText==reload)
			setTimeout("reloadComAJAX();", 4000);
		else
			location.reload();
	  }
	}
	
	a.open("POST","reload/"+sala+".txt",true);
	a.send();
}