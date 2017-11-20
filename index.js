var status;
function selecionarSala(status)
{
	if(status==0)
	{
		var div=document.getElementById("bt_inicio");
		div.style.display="none";
		var divSala=document.getElementById("bt_sala");
		divSala.style.display="block";
	}
	if(status==-1)
	{
		var div=document.getElementById("bt_inicio");
		div.style.display="block";
		var divSala=document.getElementById("bt_sala");
		divSala.style.display="none";
	}
	if(status==1)
	{
		var sala=document.getElementById("input-sala").value;
		document.getElementById("sala").value=sala;
		formLobby.submit();
		
	}
}
function selecionarQtdJog(status)
{
	if(status==0)
	{
		document.getElementById("bt_inicio").style.display="none";
		divSelecSala=document.getElementById("bt_qtdJog").style.display="block";
	}
	if(status==-1)
	{
		document.getElementById("bt_inicio").style.display="block";
		divSelecSala=document.getElementById("bt_qtdJog").style.display="none";
	}
	if(status==1)
	{
		var qtdJog=document.getElementById("input-qtdJog").value;
		document.getElementById("qtdJog").value=qtdJog;
		formCriarSala.submit();
		
		
	}
}