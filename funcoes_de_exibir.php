<?php

	require_once("conexao.php");

	function exibirCartajogAtual($id,$sala){
			
			if($id!="0"&&$sala!="0"){
			global $conn;
			$select="SELECT carta1,carta2,carta3 FROM cartas where idjog=$id and sala=$sala";  
			$result= mysqli_query($conn,$select);
			
			while($select=mysqli_fetch_array($result)){
				for($i=1;$i<=3;$i++){
					$carta[$i]=$select["carta$i"].".jpg";
					if($carta[$i]=="0.jpg")$carta[$i]="nada.png";
				}
				
			}
			echo "<table id='cartasJog'>";
			echo "<tr>";
				for($i=1;$i<=3;$i++){
					echo "<td>";
						if($carta[$i]=="nada.png")echo "<img class='imgJogAtual' id='carta$i' src='imagens/$carta[$i]'>";
						else echo "<img class='imgJogAtual' id='carta$i' onclick=\"mudar_cor('$i')\" src='imagens/$carta[$i]'>";
					echo "</td>";
				}
			echo "</tr>";
			echo "</table>";
			
		}
	}
	
	function exibirMesa($id,$sala){	
		
		if($id!="0"&&$sala!="0")
		{
			global $conn;
			
			//pega as cadeiras
			$select="SELECT `qtdJogadores` FROM `salas` WHERE id=$sala";
			$select=selecionarBanco($select);		
			$qtdJogadores=$select[0]["qtdJogadores"];
			
			$select="SELECT cadeira FROM cartas where idjog=$id and sala=$sala";  
			$select=selecionarBanco($select);
			if(!$select)return;
			
			$cadeira[1]=$select[0]["cadeira"];
			for($i=2;$i<=$qtdJogadores;$i++){
				$cadeira[$i]=$cadeira[1]+$i-1;
				if($cadeira[$i]>$qtdJogadores)$cadeira[$i]-=$qtdJogadores;
				
			}
			
			$qtdCartasNadas=0;
			//seleciona carta jogada
			$select="SELECT cartaJogada,cadeira FROM cartas where sala=$sala";  
			$result= mysqli_query($conn,$select);
			while($select=mysqli_fetch_array($result)){
				for($i=1;$i<=$qtdJogadores;$i++){
					if($select["cadeira"]==$cadeira[$i]){
						$cartaJogada[$i]=$select["cartaJogada"].".jpg";
						if($cartaJogada[$i]=="0.jpg")
						{
							$qtdCartasNadas++;
							$cartaJogada[$i]="nada.png";
						}
						break;
					}
				}
			}
			
			
			
			//pega o placar
			$select="SELECT jogada1,jogada2,jogada3 FROM salas where id=$sala";  
			$select=selecionarBanco($select);
			$jogada[1]=$select[0]["jogada1"];
			$jogada[2]=$select[0]["jogada2"];
			$jogada[3]=$select[0]["jogada3"];
				
			for($i=1;$i<=3;$i++){
				if($jogada[$i]==0)$jogada[$i]="-";
				elseif(($jogada[$i]%2)==($cadeira[1]%2))$jogada[$i]="<font style='color:green'>X</font>";
					else $jogada[$i]="<font style='color:red'>O</font>";
				//	echo ($jogada[$i]%2)."==".($cadeira[1]%2)."<br>";
			}
			
			
			//calculo de vitorias
			$select="SELECT vitoriasTime1,vitoriasTime2,valendo,qtdJogadores,quedasTime1,quedasTime2 FROM salas where id=$sala;"; 
			$select= selecionarBanco($select);
			$vitoriasTimeAtual=$select[0]["vitoriasTime".(($cadeira[1]+1)%2+1)];
			$vitoriasTimeRival=$select[0]["vitoriasTime".($cadeira[1]%2+1)];
			$quedasTimeAtual=$select[0]["quedasTime".(($cadeira[1]+1)%2+1)];
			$quedasTimeRival=$select[0]["quedasTime".($cadeira[1]%2+1)];
			$valendoPartida=$select[0]["valendo"];
			$qtdJogadores=$select[0]["qtdJogadores"];
			
			
			//Carta Anterior
			if($qtdCartasNadas==$qtdJogadores)
			{
				$qtdCartasNadas=0;
				
				//seleciona carta jogada anterior
				$select="SELECT carta1,carta2,carta3,carta4 FROM jogadas where sala=$sala";  
				$select= selecionarBanco($select);
				for($i=1;$i<=$qtdJogadores;$i++)
				{
						$cartaJogada[$i]=$select[0]["carta".$cadeira[$i]].".jpg";
						if($cartaJogada[$i]=="0.jpg")
						{
							$qtdCartasNadas++;
							$cartaJogada[$i]="nada.png";
						}
						
				}
				//calculo maiorCarta
				$maiorCarta=0;
				if($qtdCartasNadas==0)
				{
					$maiorCarta=1;
					for($i=1;$i<=$qtdJogadores;$i++)
					{
						if(converterCartas($select[0]["carta".$i])>=converterCartas($select[0]["carta$maiorCarta"]))
							$maiorCarta=$i;
					}
				}
				if($maiorCarta!=0)$maiorCarta=converterCartas($select[0]["carta$maiorCarta"]);
				echo "<table id='mesa'>";
					echo "<tr>";
						echo"<td></td>";
						if($qtdJogadores==4)
						{
							if($maiorCarta==converterCartas($cartaJogada[3]))
							echo"<td><img class='imgMesa' id='cartaWin' src='imagens/$cartaJogada[3]'></td>";
							else echo"<td><img class='imgMesa' src='imagens/$cartaJogada[3]'></td>";
						}
						else
						{
							if($maiorCarta==converterCartas($cartaJogada[2]))echo"<td><img class='imgMesa' id='cartaWin' src='imagens/$cartaJogada[2]'></td>";
							else echo"<td><img class='imgMesa' src='imagens/$cartaJogada[2]'></td>";
						}
						echo"<td></td>";
					echo "</tr>";
					echo "<tr>";
						echo"<td>";
						if($qtdJogadores==4)
						{	
							if($maiorCarta==converterCartas($cartaJogada[2]))echo"<img class='imgMesa' id='cartaWin' src='imagens/$cartaJogada[2]'>";
							else echo"<img class='imgMesa' src='imagens/$cartaJogada[2]'>";
						}
						echo"</td>";
						echo"<td>";
							echo "Quedas:(<font class='placarTimeAtual'>$quedasTimeAtual</font>";
							echo "|<font class='placarTimeRival'>$quedasTimeRival</font>)<br>";
							echo "Vitórias:(<font class='placarTimeAtual'>$vitoriasTimeAtual</font>";
							echo "|<font class='placarTimeRival'>$vitoriasTimeRival</font>)<br>";						
							echo "$jogada[1]$jogada[2]$jogada[3]<br>";
							echo "($cadeira[1])<br>";
							echo "Valendo:$valendoPartida<br>";
							
						echo"</td>";
						echo"<td>";
						if($qtdJogadores==4)
						{
							if($maiorCarta==converterCartas($cartaJogada[4]))echo"<td><img class='imgMesa' id='cartaWin' src='imagens/$cartaJogada[4]'></td>";
							else echo"<img class='imgMesa' src='imagens/$cartaJogada[4]'>";
						}
						echo"</td>";					
					echo "</tr>";
					echo "<tr>";
						echo"<td></td>";
						if($maiorCarta==converterCartas($cartaJogada[1]))echo"<td><img class='imgMesa' id='cartaWin' src='imagens/$cartaJogada[1]'></td>";
						else echo"<td><img class='imgMesa' src='imagens/$cartaJogada[1]'></td>";
						echo"<td></td>";
					echo "</tr>";
				echo"<table>";
			
			}
			else
			{
				echo "<table id='mesa'>";
					echo "<tr>";
						echo"<td></td>";
						if($qtdJogadores==4)
							echo"<td><img class='imgMesa' src='imagens/$cartaJogada[3]'></td>";
						else
							echo"<td><img class='imgMesa' src='imagens/$cartaJogada[2]'></td>";
						echo"<td></td>";
					echo "</tr>";
					echo "<tr>";
						echo"<td>";
						if($qtdJogadores==4)
							echo"<img class='imgMesa' src='imagens/$cartaJogada[2]'>";
						echo"</td>";
						echo"<td>";
							echo "Quedas:(<font class='placarTimeAtual'>$quedasTimeAtual</font>";
							echo "|<font class='placarTimeRival'>$quedasTimeRival</font>)<br>";
							echo "Vitórias:(<font class='placarTimeAtual'>$vitoriasTimeAtual</font>";
							echo "|<font class='placarTimeRival'>$vitoriasTimeRival</font>)<br>";						
							echo "$jogada[1]$jogada[2]$jogada[3]<br>";
							echo "($cadeira[1])<br>";
							echo "Valendo:$valendoPartida<br>";
							
						echo"</td>";
						echo"<td>";
						if($qtdJogadores==4)
							echo"<img class='imgMesa' src='imagens/$cartaJogada[4]'>";
						echo"</td>";					
					echo "</tr>";
					echo "<tr>";
						echo"<td></td>";
						echo"<td><img class='imgMesa' src='imagens/$cartaJogada[1]'></td>";
						echo"<td></td>";
					echo "</tr>";
				echo"<table>";
			
			}
		}
	}
			
		
	
	
	function verrificaSePodeJogar($id,$sala){
		
		if($id==0||$sala==0)return;
		
		//verifica se pode jogar
		$select="SELECT podeJogar FROM cartas where idjog=$id and sala=$sala";  
		$select= selecionarBanco($select);
		if($select[0]["podeJogar"]==0)return;
		//verifica se tem alguem com pedido de truco
		$select="SELECT podeTruco,maoDe11 FROM cartas where sala=$sala";  
		$select= selecionarBanco($select);
		
		for($i=0;$i<4;$i++)
		{
			if($select[$i]["podeTruco"]==-1)return;
			if($select[$i]["maoDe11"]==-1)return;
			
		}
		
		//botao
		echo "<input class='bt_importante' id='enviar' type='Button' value='Enviar' onclick='enviar_carta();'/>";
		//formulario
		echo "<form method='get' action='truco.php' name='formDeCarta' id='formDeCarta'>";
		echo "<input type='hidden' id='carta' name='carta' value='0'>";
		echo "<input type='hidden' name='sala' value='$sala'>";
		echo "<input type='hidden' name='id' value='$id'>";		
		echo "</form>";
	}
	
	function verrificaSePodeTruco($id,$sala){

		if($id==0||$sala==0)return;
		
		//verifica se e mao de 11
		$alguemTemMaoDe11=0;
		$select="SELECT maoDe11 FROM cartas where sala=$sala";  
		$select= selecionarBanco($select);
		
		for($i=0;$i<4;$i++)
		{
			if($select[$i]["maoDe11"]==-1)$alguemTemMaoDe11=1;			
		}
		
		//verifica se pode trucar
		$select="SELECT podeTruco,podeJogar FROM cartas where idjog=$id and sala=$sala";  
		$select= selecionarBanco($select);
		$podeTruco=$select[0]["podeTruco"];
		$podeJogar=$select[0]["podeJogar"];
		if($podeTruco==0)return;
		$select="SELECT valendo FROM salas where id=$sala";  
		$select= selecionarBanco($select);
		$valendo=$select[0]["valendo"];
		if($podeTruco==-1){
			if($valendo==1)echo "<div class='msgGrande'>Pedido de Truco:</div>";
			if($valendo==3)echo "<div class='msgGrande'>Pedido de 6:</div>";
			if($valendo==6)echo "<div class='msgGrande'>Pedido de 9:</div>";
			if($valendo==9)echo "<div class='msgGrande'>Pedido de 12:</div>";
			echo "<input class='bt_importante' type='Button' value='Aceitar' onclick=\"confirmar_truco('2');\" />";
			if($valendo<9)echo "<input class='bt_importante' type='Button' value='Aumentar' onclick=\"confirmar_truco('3');\" />";
			echo "<input class='bt_importante' type='Button' value='Correr'  onclick=\"confirmar_truco('-2');\" />";
		}
		if($podeTruco==1&&$alguemTemMaoDe11==0&&$valendo<12&&$podeJogar==1)
		{
		
		if($valendo==1)echo "<input class='bt_importante' id='btTruco' type='Button' value='Truco' onclick=\"confirmar_truco('0');\"/>";
		if($valendo==3)echo "<input class='bt_importante' id='btTruco' type='Button' value='Pedir 6' onclick=\"confirmar_truco('0');\"/>";
		if($valendo==6)echo "<input class='bt_importante' id='btTruco' type='Button' value='Pedir 9' onclick=\"confirmar_truco('0');\"/>";
		if($valendo==9)echo "<input class='bt_importante' id='btTruco' type='Button' value='Pedir 12' onclick=\"confirmar_truco('0');\"/>";
		
		//div de confirmar
		echo "<div id='confirmarTruco'>";
			if($valendo==1)echo "<font class='msgAvissoGrande'>Tem certeza que quer pedir Truco?</font>";
			if($valendo==3)echo "<font class='msgAvissoGrande'>Tem certeza que quer pedir 6?</font>";
			if($valendo==6)echo "<font class='msgAvissoGrande'>Tem certeza que quer pedir 9?</font>";
			if($valendo==9)echo "<font class='msgAvissoGrande'>Tem certeza que quer pedir 12?</font>";
			echo "<input class='bt_importante'  type='Button' value='Continuar' onclick=\"confirmar_truco('1');\"/>";
			echo "<input class='bt_importante'  type='Button' value='Voltar' onclick=\"confirmar_truco('-1');\"/>";
		echo "</div>";
		}
		//formulario
			echo "<form method='get' action='truco.php' name='formDeTruco' id='formDeTruco'>";
			echo "<input type='hidden' id='trucoResp' name='truco' value='pedidoDeTruco'>";
			echo "<input type='hidden' name='sala' value='$sala'>";
			echo "<input type='hidden' name='id' value='$id'>";			
			echo "</form>";
		
		
				
		
				
	}
	
	function verrificaMaoDe11($id,$sala)	{
		//verifica se e mao de 11
		$select="SELECT maoDe11 FROM cartas where sala=$sala and idJog=$id";  
		$select= selecionarBanco($select);
		$maoDe11=$select[0]["maoDe11"];
		if($maoDe11!=-1)
			return;
		
		echo "<div class='msgGrande'>Mao de 11:</div>";
		echo "<input class='bt_importante' type='Button' value='Aceitar' onclick=\"confirmar_maoDe11('2');\" />";
		echo "<input class='bt_importante' type='Button' value='Correr'  onclick=\"confirmar_maoDe11('-2');\" />";
		
		//formulario
			echo "<form method='get' action='truco.php' name='formMaoDe11' id='formMaoDe11'>";
			echo "<input type='hidden' id='maoDe11Resp' name='maoDe11Resp' value=''>";
			echo "<input type='hidden' name='sala' value='$sala'>";
			echo "<input type='hidden' name='id' value='$id'>";			
			echo "</form>";
		
	
	}
	
	function ReloadComAjax($sala){
		$select="SELECT reload FROM salas where id=$sala";  
		$select= selecionarBanco($select);
		$reload=$select[0]["reload"];
		
		echo "<script src='ajax.js'></script>";
		
		echo "<input type='hidden' id='sala' value='$sala'/>";
		echo "<input type='hidden' id='reload' value='$reload'/>";
	}
	
	function selecionarBanco($select){
		global $conn;
		$result=mysqli_query($conn,$select);
		if(!$result){echo mysqli_error($conn);return 0;}
		//else echo "$select<br>";
		$resp[0][0]=0;
		$i=0;
		while($select=mysqli_fetch_array($result)){
			
			$resp[$i]=$select;
			$i++;
		}
		
		return $resp;
	}
	
	function converterCartas($carta){

		if($carta>=1&&$carta<=4)return 1;
		if($carta>=5&&$carta<=8)return 2;
		if($carta>=9&&$carta<=12)return 3;
		if($carta>=13&&$carta<=15)return 4;
		if($carta>=16&&$carta<=19)return 5;
		if($carta>=20&&$carta<=23)return 6;
		if($carta==24)return 7;
		if($carta==25)return 8;
		if($carta==26)return 9;
		if($carta==27)return 10;
		return -1;
	}
?>