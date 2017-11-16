var carta;
	var mudouCor=0;
	function mudar_cor(carta){
		if(mudouCor!=carta){
			document.getElementById("carta1").style.background = "white";
			document.getElementById("carta2").style.background = "white";
			document.getElementById("carta3").style.background = "white";
			document.getElementById("carta"+carta).style.background = "rgba(255, 0, 0, 0.48)";
			mudouCor=carta;
			document.getElementById("carta").value=carta;
		}else{
			document.getElementById("carta").value="0";
			document.getElementById("carta"+carta).style.background = "white";
			mudouCor=0;
		}
	}
	function enviar_carta(){
		carta_selecionada_form=document.getElementById("carta").value;
		if(carta_selecionada_form!=0)document.formDeCarta.submit();
	}
var status;
	function confirmar_truco(status){
		if(status==0)
		{
			document.getElementById("btTruco").style.display="none";
			document.getElementById("enviar").style.display="none";
			document.getElementById("confirmarTruco").style.display="block";
		}
		if(status==-1)
		{
			document.getElementById("btTruco").style.display="block";
			document.getElementById("enviar").style.display="block";
			document.getElementById("confirmarTruco").style.display="none";
		}
		if(status==1)
		{
			document.getElementById("trucoResp").value="pedidoDeTruco";
			document.formDeTruco.submit();
		}
		if(status==2)
		{
			document.getElementById("trucoResp").value="pedidoAceito";
			document.formDeTruco.submit();
		}
		if(status==3)
		{
			document.getElementById("trucoResp").value="pedidoAumentado";
			document.formDeTruco.submit();
		}
		if(status==-2)
		{
			document.getElementById("trucoResp").value="pedidoNegado";
			document.formDeTruco.submit();
		}
	}
	function confirmar_maoDe11(status){
		
		if(status==2)
		{
			document.getElementById("maoDe11Resp").value="2";
			document.formMaoDe11.submit();
		}
		if(status==-2)
		{
			document.getElementById("maoDe11Resp").value="-2";
			document.formMaoDe11.submit();
		}
	}