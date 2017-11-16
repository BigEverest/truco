<?php
	$id=isset($_GET["id"])?$_GET["id"]:"0";
	$carta=isset($_GET["carta"])?$_GET["carta"]:"0";
	$sala=isset($_GET["sala"])?$_GET["sala"]:"0";
	$truco=isset($_GET["truco"])?$_GET["truco"]:"0";
	$maoDe11=isset($_GET["maoDe11Resp"])?$_GET["maoDe11Resp"]:"0";
	
	require("conexao.php");
	
	$select="SELECT reload FROM salas WHERE id=$sala";
	$select=selecionarBanco($select);	
	$reload=$select[0]["reload"];
	
	if($carta=="-1")embaralhar($sala);
	if($carta>"0")jogarCarta($carta,$sala,$id);		
	if($truco!="0"&&VerrificaSePodeTruco($id,$sala,$truco)==1)truco($id,$sala,$truco);
	if($maoDe11!=0)verrificaMaoDe11($id,$sala,0,$maoDe11);
	if($carta=="-10")reiniciar($sala);
	
	modificaReload($reload,$sala);	
	voltar($id,$sala);
	
	function embaralhar($sala){
		echo "embaralhar<br>";
		if($sala!="0"){	
			
			global $conn;
			
			//pega a CadeiraMao
			$select="SELECT cadeiraMao,qtdJogadores FROM salas where id=$sala";
			$select=selecionarBanco($select);
			$mao=$select[0]["cadeiraMao"];
			$qtdJogadores=$select[0]["qtdJogadores"];
			
			
			//seleciona as cartas
			for($i=1;$i<=27;$i++){
				$verrificaCarta[$i]=0;
			}
			$contador=1;
			while($contador<=12){
				$carta=rand(1,27);
				if($verrificaCarta[$carta]==0){
					$verrificaCarta[$carta]=1;
					$cartaSeleciona[$contador]=$carta;
					$contador++;
				}				
			}
			
			
			//calcula cadeira de acordo com a mao
			$cadeiras=calcularCadeira($cadeira,$mao,$sala);
			
			
			//salva no banco a mao do jogador da cadeira[2];
			$query="UPDATE `cartas` SET `carta1` = '$cartaSeleciona[1]', `carta2` = '$cartaSeleciona[2]', `carta3` = '$cartaSeleciona[3]' WHERE cadeira=$cadeira[2]";
			if(!mudarBanco($query))return;
			
			if($qtdJogadores==4)
			{
				//salva no banco a mao do jogador da cadeira[3];
				$query="UPDATE `cartas` SET `carta1` = '$cartaSeleciona[4]', `carta2` = '$cartaSeleciona[5]', `carta3` = '$cartaSeleciona[6]' WHERE cadeira=$cadeira[3]";
				if(!mudarBanco($query))return;
				
				//salva no banco a mao do jogador da cadeira[4];
				$query="UPDATE `cartas` SET `carta1` = '$cartaSeleciona[7]', `carta2` = '$cartaSeleciona[8]', `carta3` = '$cartaSeleciona[9]' WHERE cadeira=$cadeira[4]";			
				if(!mudarBanco($query))return;
			}
			
			//salva no banco a mao do jogador da cadeira[1];
			$query="UPDATE `cartas` SET `carta1` = '$cartaSeleciona[10]', `carta2` = '$cartaSeleciona[11]', `carta3` = '$cartaSeleciona[12]' WHERE cadeira=$cadeira[1]";
			if(!mudarBanco($query))return;
			
			//muda a mao;
			$query="UPDATE `salas` SET `cadeiraMao` = '$cadeira[2]' WHERE id=$sala";
			if(!mudarBanco($query))return;
			
			//limpa quem começa
			$query="UPDATE `cartas` SET `podeJogar` = '0',`podeTruco`='0' WHERE sala=$sala";
			if(!mudarBanco($query))return;
			
			//define quem começa a jogar
			$query="UPDATE `cartas` SET `podeJogar` = '1',`podeTruco`='1' WHERE sala=$sala and cadeira=$cadeira[2]";
			if(!mudarBanco($query))return;
			
			//limpa mao de 11
			$query="UPDATE `cartas` SET `maoDe11` = '0' WHERE sala=$sala";
			if(!mudarBanco($query))return;
			
			$id=0;
			$maoDe11=0;
			verrificaMaoDe11($id,$sala,$cadeira,$maoDe11);
			
		}else echo"Sala nao recebida<br>";
		
	}

	function jogarCarta($carta,$sala,$id){
		echo "jogarCarta<br>";
		//verifica de carta sala e id foram enviados
		if($id=="0"||$sala=="0"){echo"ID e ou Sala nao recebidos<br>";return;}
		
		global $conn;
		
		//Seleciona cadeira,podeJogar,carta do jogador
		$select="SELECT cadeira,podeJogar,carta$carta FROM cartas where idJog=$id and sala=$sala";
		$select=selecionarBanco($select);			
		//verifica se e sua vez de jogar
		if($select[0]["podeJogar"]==0){echo "não é a sua vez de jogar<br> ";return;}
		$cartaSeleciona=$select[0]["carta$carta"];
		$cadeira=$select[0]["cadeira"];
			
		
		
		//limpa carta e substitui cartaJogada e devine que não pode Jogar
		$query="UPDATE `cartas` SET `carta$carta` = '0',`cartaJogada` = '$cartaSeleciona',`podeJogar` = '0' WHERE idJog=$id and sala=$sala";
		if(!mudarBanco($query))return;
		
		$proxJog=calcularProxJog($cadeira,$sala);
		
		//Muda jogador
		$query="UPDATE `cartas` SET `podeJogar` = '1' WHERE sala=$sala and cadeira=$proxJog";
		if(!mudarBanco($query))return;
		
		$select="SELECT valendo FROM salas where id=$sala";
		$select=selecionarBanco($select);		
		if($select[0]["valendo"]==1)
		{
			$query="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";
			if(!mudarBanco($query))return;
			$query="UPDATE `cartas` SET `podeTruco` = '1' WHERE sala=$sala and cadeira=$proxJog";
			if(!mudarBanco($query))return;
		}
		
		//verifica se acabou a jogada
		$select="SELECT cartaJogada FROM cartas where cadeira=$proxJog and sala=$sala";
		$select=selecionarBanco($select);
		if($select[0]["cartaJogada"]!="0")pontuar($sala);
	}

	function pontuar($sala){
		echo "pontuar<br>";
		global $conn;
		
		//Seleciona cartaJogada e cadeira
		$select="SELECT cartaJogada,cadeira FROM cartas where sala=$sala;"; 
		$select=selecionarBanco($select);
		for($i=0;$i<4;$i++){
			if($select[$i]["cadeira"]==1)$cartaCadeira1=$select[$i]["cartaJogada"];
			if($select[$i]["cadeira"]==2)$cartaCadeira2=$select[$i]["cartaJogada"];
			if($select[$i]["cadeira"]==3)$cartaCadeira3=$select[$i]["cartaJogada"];
			if($select[$i]["cadeira"]==4)$cartaCadeira4=$select[$i]["cartaJogada"];		
			
		}
		
		//seleciona jogadaAtual que é para saber se está na jogada 1 2 ou 3
		$select="SELECT jogadaAtual FROM salas where id=$sala"; 
		$select=selecionarBanco($select);		
		$jogadaAtual=$select[0]["jogadaAtual"];		
		
		//verifica qual é a maior carta Time1
		if($cartaCadeira1>$cartaCadeira3){
			$cartaMaiorTime1=$cartaCadeira1;
			$cadeiraMaiorTime1=1;
		}else{
		$cartaMaiorTime1=$cartaCadeira3;
			$cadeiraMaiorTime1=3;
		}		
		//verifica qual é a maior carta Time2
		if($cartaCadeira2>$cartaCadeira4){
			$cartaMaiorTime2=$cartaCadeira2;
			$cadeiraMaiorTime2=2;
		}else{
			$cartaMaiorTime2=$cartaCadeira4;
			$cadeiraMaiorTime2=4;
		}
		
		//conveter cartas
		echo "<td><img class='imgMesa' src='imagens/$cartaMaiorTime1.jpg'></td><br>";
		echo "<td><img class='imgMesa' src='imagens/$cartaMaiorTime2.jpg'></td><br>";
		$cartaMaiorTime1=converterCartas($cartaMaiorTime1);
		$cartaMaiorTime2=converterCartas($cartaMaiorTime2);
		
		//Pontua
		
			//carta1 eh maior
			if($cartaMaiorTime1>$cartaMaiorTime2){				
				
				//modifica a jogada
				$query ="UPDATE `salas` SET `jogada$jogadaAtual` = '1' ,`jogadaAtual`=".($jogadaAtual+1)."  WHERE id=$sala;";
				if(!mudarBanco($query))return;
				
				//defini quem inicia jogando
				$query ="UPDATE `cartas` SET `podeJogar` = '0' WHERE sala=$sala;";
				if(!mudarBanco($query))return;
				$query ="UPDATE `cartas` SET `podeJogar` = '1' WHERE sala=$sala and cadeira=".$cadeiraMaiorTime1.";";
				if(!mudarBanco($query))return;
				$select="SELECT valendo FROM salas where id=$sala";
				$select=selecionarBanco($select);		
				if($select[0]["valendo"]==1)
				{
					$query="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";
					if(!mudarBanco($query))return;
					$query="UPDATE `cartas` SET `podeTruco` = '1' WHERE sala=$sala and cadeira=".$cadeiraMaiorTime1.";";
					if(!mudarBanco($query))return;
				}
				
				
			}
			//carta2 eh maior
			if($cartaMaiorTime1<$cartaMaiorTime2){
				
				
				//modifica jogada
				$query ="UPDATE `salas` SET `jogada$jogadaAtual` = '2', `jogadaAtual`=".($jogadaAtual+1)."  WHERE id=$sala;";
				if(!mudarBanco($query))return;
				
				//defini quem inicia jogando
				$query ="UPDATE `cartas` SET `podeJogar` = '0' WHERE sala=$sala;";
				if(!mudarBanco($query))return;
				$query ="UPDATE `cartas` SET `podeJogar` = '1' WHERE sala=$sala and cadeira=".$cadeiraMaiorTime2.";";
				if(!mudarBanco($query))return;
				$select="SELECT valendo FROM salas where id=$sala";
				$select=selecionarBanco($select);		
				if($select[0]["valendo"]==1)
				{
					$query="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";
					if(!mudarBanco($query))return;
					$query="UPDATE `cartas` SET `podeTruco` = '1' WHERE sala=$sala and cadeira=".$cadeiraMaiorTime2.";";
					if(!mudarBanco($query))return;
				}
				
			}
			// carta1 == carta2
			if($cartaMaiorTime1==$cartaMaiorTime2){
				//se for a primeira mao
				
				if($jogadaAtual==1){	
				
					//empata o jogo
					$query ="UPDATE `salas` SET `jogada1` = '1',`jogada2` = '2',`jogadaAtual`='3' WHERE id=$sala;";
					if(!mudarBanco($query))return;				
					
				}else{
					
					//se não for a primeira mao quem ganhou a primeira mao ganha a jogada atual
					$select="SELECT jogada1 FROM salas where id=$sala"; 
					$select= selecionarBanco($select);
					$jogada1=$select[0]["jogada1"];
					if($jogada1==1){
						
						//time 1 ganha jogada Atual
						$query ="UPDATE `salas` SET `jogada$jogadaAtual` = '1',`jogadaAtual`='1' WHERE id=$sala";
						if(!mudarBanco($query))return;
					}else{
						//time 2 ganha jogada Atual
						$query ="UPDATE `salas` SET `jogada$jogadaAtual` = '2',`jogadaAtual`='1' WHERE id=$sala";
						if(!mudarBanco($query))return;
					}					
				}
			}
		
		//verifica se precisa termina jogadas
		$select="SELECT jogada1,jogada2,jogada3 FROM salas where id=$sala;"; 
		$select= selecionarBanco($select);
		$pontosTime1=0;
		$pontosTime2=0;
		//conta pontos time1 e 2
		for($i=1;$i<=3;$i++){
			if($select[0]["jogada$i"]==1)$pontosTime1++;
			if($select[0]["jogada$i"]==2)$pontosTime2++;				
		}
		
		//cartaAnterior
		$select="SELECT qtdJogadores FROM `salas` WHERE id=$sala ";
		$select=selecionarBanco($select);
		$qtdJogadores=$select[0]["qtdJogadores"];
		
		
		$select="SELECT cartaJogada FROM `cartas` WHERE sala=$sala ORDER BY `cartas`.`cadeira` ASC";
		$select=selecionarBanco($select);
		for($i=1;$i<=$qtdJogadores;$i++)
		{
			$query ="UPDATE `jogadas` SET `carta$i` = '".$select[$i-1]["cartaJogada"]."' WHERE sala=$sala";
			if(!mudarBanco($query))return;
		}
		
		
		if($pontosTime1>1)calcularVitoria($sala,1);
		if($pontosTime2>1)calcularVitoria($sala,2);
		limparMesa($sala);
		
		
		
		
	}
	
	function converterCartas($carta){
		echo "converterCartas<br>";
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
	}
	
	function calcularVitoria($sala,$timeVitorioso){
		echo "calcularVitoria<br>";
		
		//seleciona vitoriasTimeVitorioso e valendo
		$select="SELECT vitoriasTime$timeVitorioso,valendo FROM salas WHERE id=$sala";
		$select=selecionarBanco($select);
		$vitoriasTimeVitorioso=$select[0]["vitoriasTime$timeVitorioso"];
		$valendo=$select[0]["valendo"];
	
		
		//muda vitorias time vitorioso e valendo
		$query ="UPDATE `salas` SET `vitoriasTime$timeVitorioso` = '".($vitoriasTimeVitorioso+$valendo)."', `valendo`='1'  WHERE id=$sala;";
		if(!mudarBanco($query))return;
		if(($vitoriasTimeVitorioso+$valendo)>=12)calcularQueda($sala,$timeVitorioso);
		limparJogadas($sala);
		embaralhar($sala);
		
	}
	
	function calcularQueda($sala,$timeVitorioso){
		echo "calcularQueda<br>";
		
		$select="SELECT quedasTime$timeVitorioso FROM salas WHERE id=$sala";
		$select=selecionarBanco($select);
		$quedasTimeVitorioso=$select[0]["quedasTime$timeVitorioso"];
		
		//muda vitorias time vitorioso e valendo
		$query ="UPDATE `salas` SET `quedasTime$timeVitorioso` = '".($quedasTimeVitorioso+1)."', `vitoriasTime1`='0' ,`vitoriasTime2`='0'   WHERE id=$sala;";
		if(!mudarBanco($query))return;
		
		
	
	}
	
	function limparMesa($sala){
		echo "limparMesa<br>";
		$query="UPDATE `cartas` SET `cartaJogada` = '0' WHERE sala=$sala";
		if(!mudarBanco($query))return;
	
	}

	function calcularCadeira(&$a,$cadeira,$sala){
		echo "calcularCadeira<br>";
		$select="SELECT `qtdJogadores` FROM `salas` WHERE id=$sala";
		$select=selecionarBanco($select);		
		$qtdJogadores=$select[0]["qtdJogadores"];
		
		$a[1]=$cadeira;
		for($i=2;$i<=$qtdJogadores;$i++){
			$a[$i]=$cadeira+$i-1;
			if($a[$i]>$qtdJogadores)$a[$i]-=$qtdJogadores;
		}
		
	
	}

	function calcularProxJog($cadeira,$sala){
		echo "calcularProxJog<br>";
		$select="SELECT `qtdJogadores` FROM `salas` WHERE id=$sala";
		$select=selecionarBanco($select);		
		$qtdJogadores=$select[0]["qtdJogadores"];
		
		$cadeira++;
		if($cadeira>$qtdJogadores)$cadeira=1;
		return $cadeira;
	}

	function limparJogadas($sala){
		echo "limparJogadas<br>";
		$query ="UPDATE `salas` SET `jogada1` = '0' ,`jogada2` = '0',`jogada3` = '0',`jogadaAtual`='1'  WHERE id=$sala;";
		if(!mudarBanco($query))return;		
	}

	function selecionarBanco($select){
		global $conn;
		$result=mysqli_query($conn,$select);
		if(!$result)
		{
			echo "<font style='color:red'>$select<br>".mysqli_error($conn)."</font><br>";
			return 0;
			}
		else echo "$select<br>";
		$resp[0][0]=0;
		$i=0;
		while($select=mysqli_fetch_array($result)){
			
			$resp[$i]=$select;
			$i++;
		}
		if($i==0)
			return 0;
		return $resp;
	}

	function mudarBanco($querry){
		global $conn;
		global $reload;
		$result=mysqli_query($conn,$querry);
		if(!$result)
		{
			echo "<font style='color:red'>$querry<br>".mysqli_error($conn)."</font><br>";
			return 0;
		}
		else echo "$querry<br>";
		$reload++;
		return 1;
	}
	
	function reiniciar($sala){
		echo "reiniciar<br>";
		
		$query="UPDATE `jogadas` SET `carta1`='0',`carta2`='0',`carta3`='0',`carta4`='0' WHERE sala=$sala";
		if(!mudarBanco($query))return;	
		$query="UPDATE `salas` SET `quedasTime1`='0',`quedasTime2`='0',`vitoriasTime1`='0',`vitoriasTime2`='0',`cadeiraMao`='1',`valendo`='1' WHERE id=$sala";
		if(!mudarBanco($query))return;	
		limparJogadas($sala);
		limparMesa($sala);
		embaralhar($sala);
		
		
	}
	
	function truco($id,$sala,$truco){
		echo "truco<br>";
		
		
		if($truco=="pedidoDeTruco"){
			//verificarSePodeTruco
				//depois
			
			//mudar PodeTruco do jogador Atual
			$query ="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";
			if(!mudarBanco($query))return;
			
			$select="SELECT `cadeira` FROM `cartas` WHERE sala=$sala and idJog=$id;";
			$select=selecionarBanco($select);			
			
			calcularCadeira($cadeira,$select[0]["cadeira"],$sala);
			
			//mudaPodeTruco dos jogadores rivais
			$select="SELECT `qtdJogadores` FROM `salas` WHERE id=$sala";
			$select=selecionarBanco($select);		
			$qtdJogadores=$select[0]["qtdJogadores"];
			
			$query ="UPDATE `cartas` SET `podeTruco` = '-1' WHERE sala=$sala and cadeira=$cadeira[2];";
			if(!mudarBanco($query))return;
			if($qtdJogadores==4)
			{
				$query ="UPDATE `cartas` SET `podeTruco` = '-1' WHERE sala=$sala and cadeira=$cadeira[4];";
				if(!mudarBanco($query))return;
			}
			
			
		}
		if($truco=="pedidoAceito")
		{
			
			//qtdJogadores
			$select="SELECT `qtdJogadores`,valendo FROM `salas` WHERE id=$sala";
			$select=selecionarBanco($select);		
			$qtdJogadores=$select[0]["qtdJogadores"];
			$valendo=$select[0]["valendo"];
			
			//mudar PodeTruco do jogadores
			$query ="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";			
			if(!mudarBanco($query))return;
			
			$select="SELECT `cadeira` FROM `cartas` WHERE sala=$sala and idJog=$id;";
			$select=selecionarBanco($select);	
			calcularCadeira($cadeira,$select[0]["cadeira"],$sala);
			
			//mudaPodeTruco dos jogadores rivais			
			
			$query ="UPDATE `cartas` SET `podeTruco` = '1' WHERE sala=$sala and cadeira=$cadeira[1];";
			if(!mudarBanco($query))return;
			
			if($qtdJogadores==4)
			{
				$query ="UPDATE `cartas` SET `podeTruco` = '1' WHERE sala=$sala and cadeira=$cadeira[3];";
				if(!mudarBanco($query))return;
			}
			
			//mudaValendo
			if($valendo==1)
				$valendo=3;
			else
				$valendo+=3;
			
			$query ="UPDATE `salas` SET `valendo` = '$valendo' WHERE id=$sala";
			if(!mudarBanco($query))return;
			
		}
		if($truco=="pedidoAumentado")
		{
			//qtdJogadores
			$select="SELECT `qtdJogadores`,valendo FROM `salas` WHERE id=$sala";
			$select=selecionarBanco($select);		
			$qtdJogadores=$select[0]["qtdJogadores"];
			$valendo=$select[0]["valendo"];
			
			//mudar PodeTruco do jogadores
			$query ="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala";			
			if(!mudarBanco($query))return;
			
			$select="SELECT `cadeira` FROM `cartas` WHERE sala=$sala and idjog=$id;";
			$select=selecionarBanco($select);	
			calcularCadeira($cadeira,$select[0]["cadeira"],$sala);
			
			//mudaPodeTruco dos jogadores rivais			
			
			$query ="UPDATE `cartas` SET `podeTruco` = '-1' WHERE sala=$sala and cadeira=$cadeira[2];";
			if(!mudarBanco($query))return;
			
			if($qtdJogadores==4)
			{
				$query ="UPDATE `cartas` SET `podeTruco` = '-1' WHERE sala=$sala and cadeira=$cadeira[4];";
				if(!mudarBanco($query))return;
			}
			
			//mudaValendo
			if($valendo==1)
				$valendo=3;
			else
				$valendo+=3;
			
			$query ="UPDATE `salas` SET `valendo` = '$valendo' WHERE id=$sala";
			if(!mudarBanco($query))return;
			
		}
		if($truco=="pedidoNegado")
		{
			$esperarAmigo=0;
			
			//qtdJogadores
			$select="SELECT `qtdJogadores`,valendo FROM `salas` WHERE id=$sala";
			$select=selecionarBanco($select);		
			$qtdJogadores=$select[0]["qtdJogadores"];
			$valendo=$select[0]["valendo"];
			
			//mudar PodeTruco do jogador Atual
			$query ="UPDATE `cartas` SET `podeTruco` = '0' WHERE sala=$sala and idJog=$id;";			
			if(!mudarBanco($query))return;
			
			//verrifica se o outro jogador se tiver tb respondeu pedido negado
			$select="SELECT `cadeira` FROM `cartas` WHERE sala=$sala and idJog=$id;";
			$select=selecionarBanco($select);	
			calcularCadeira($cadeira,$select[0]["cadeira"],$sala);
			
			if($qtdJogadores==4)
			{
				$select="SELECT `podeTruco` FROM `cartas` WHERE sala=$sala and cadeira=$cadeira[3];";
				$select=selecionarBanco($select);
				if($select[0]["podeTruco"]==-1)
					$esperarAmigo=1;
			}
			if($esperarAmigo)
				return;
			
			
			//dá pontos para o time inimigo
			calcularCadeira($cadeira,$select[0]["cadeira"],$sala);
			limparJogadasAnteriores($sala);
			calcularVitoria($sala,($cadeira[1]%2+1));
			limparMesa($sala);
		}
		echo "-->$truco<br>";
	}
	
	function verrificaMaoDe11($id,$sala,$cadeira,$maoDe11)	{
		echo "verrificaMaoDe11<br>";
		$select=" SELECT `vitoriasTime1`,`vitoriasTime2`,`qtdJogadores` FROM `salas` WHERE id=$sala; " ;
		$select=selecionarBanco($select);	
		$vitoriasTime1=$select[0]["vitoriasTime1"];
		$vitoriasTime2=$select[0]["vitoriasTime2"];
		$qtdJogadores=$select[0]["qtdJogadores"];
		
		//embaralhar que chamou
		if($maoDe11==0)
		{
			//verrifica se é mao de 11
			if($vitoriasTime1==11||$vitoriasTime2==11)
			{
				echo"vitoriasTime1=$vitoriasTime1<br>";
				echo"vitoriasTime2=$vitoriasTime2<br>";
				echo "mao de 11<br>";
				
				if( $vitoriasTime1==11)
					$timeVitorioso=1;
				if( $vitoriasTime2==11)
					$timeVitorioso=2;
				echo "timeVitorioso=$timeVitorioso<br>";
				if($timeVitorioso== 1)
				{
					//maoDe11 == -1 para time 1 
					$query ="UPDATE `cartas` SET `maoDe11` = '-1' WHERE sala=$sala and cadeira=1;";			
					if(!mudarBanco($query))return;
					if($qtdJogadores==4)
					{
						$query ="UPDATE `cartas` SET `maoDe11` = '-1' WHERE sala=$sala and cadeira=3;";			
						if(!mudarBanco($query))return;
					}
				}
				else
				{
					//maoDe11 == -1 para time 2 
					$query ="UPDATE `cartas` SET `maoDe11` = '-1' WHERE sala=$sala and cadeira=2;";			
					if(!mudarBanco($query))return;
					if($qtdJogadores==4)
					{
						$query ="UPDATE `cartas` SET `maoDe11` = '-1' WHERE sala=$sala and cadeira=4;";			
						if(!mudarBanco($query))return;
					}
				}
			}
			else
				echo "não e mao de 11<br>";
		}
		if($maoDe11==2 || $maoDe11==-2)
		{
				$select="SELECT `maoDe11` FROM `cartas` WHERE idJog=$id and sala=$sala";
				$select=selecionarBanco($select);
				
				if($select[0]["maoDe11"]!=-1)
					return;
				
				if($maoDe11==2)
				{
						$query ="UPDATE `cartas` SET `maoDe11` = '0' WHERE sala=$sala";			
						if(!mudarBanco($query))return;
						$truco="pedidoDeTruco";
						truco($id,$sala,$truco);
						
				}
				else
				{
					$esperarAmigo=0;
					$query ="UPDATE `cartas` SET `maoDe11` = '0' WHERE sala=$sala and idJog=$id;";			
					if(!mudarBanco($query))return;
					
					$select="SELECT `maoDe11` FROM `cartas` WHERE sala=$sala";
					$select=selecionarBanco($select);
					for($i=0;$i<$qtdJogadores;$i++)
					{
						if($select[$i]["maoDe11"]==-1)
						{
							$esperarAmigo=1;
							break;
						}
					}
					if($esperarAmigo)
						return;
					
					$truco="pedidoNegado";
					truco($id,$sala,$truco);
					
					
				}
			
		}	
		
	}
	
	function VerrificaSePodeTruco($id,$sala,$truco)	{
		echo "VerrificaSePodeTruco<br>";
		$select=" SELECT `PodeTruco` FROM `cartas` WHERE idJog=$id and sala=$sala; " ;
		$select=selecionarBanco($select);
		
		
		if($select[0]["PodeTruco"]==-1 && $truco=="pedidoAceito")return 1;
		if($select[0]["PodeTruco"]==-1 && $truco=="pedidoAumentado")return 1;
		if($select[0]["PodeTruco"]==-1 && $truco=="pedidoNegado")return 1;
		if($select[0]["PodeTruco"]==1 && $truco=="pedidoDeTruco")return 1;
		
		return 0;
		
		
	}
	
	function limparJogadasAnteriores($sala)	{
		echo"limparJogadasAnteriores<br>";
		$query="UPDATE `jogadas` SET `carta1` = '0',`carta2` = '0',`carta3` = '0',`carta4` = '0' WHERE sala=$sala";
		if(!mudarBanco($query))return;
	}
	
	function modificaReload($reload,$sala) {
		
		//modifica no banco o reload
		$query ="UPDATE `salas` SET `reload` = '$reload' WHERE id=$sala";			
		if(!mudarBanco($query))return;
		
		//grava no arquivo reload
		$reload;
		$f = fopen("reload/$sala.txt", "w");
		fwrite($f,$reload);
		fclose($f);
	}

	function voltar($id,$sala){
		//voltar proxJog
		$select="SELECT idJog FROM cartas WHERE sala=$sala and podeJogar=1";
		$select=selecionarBanco($select);	
		echo "<br><a href='mesa.php?id=".$select[0]['idJog']."&sala=$sala'>voltar</a><br>";
		
		//voltar truco
		$select="SELECT idJog FROM cartas WHERE sala=$sala and podeTruco=-1";
		if($select=selecionarBanco($select))	
			echo "<br><a href='mesa.php?id=".$select[0]['idJog']."&sala=$sala'>voltar Truco</a>";
		
		
		//redirect
		if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) 
		{
			$uri = 'https://';
		} else {
			$uri = 'http://';
		}
		$uri .= $_SERVER['HTTP_HOST'];
		echo "<input type='hidden' id='sala' value='$sala'>";
		echo "<input type='hidden' id='id' value='$id'>";
		echo "<input type='hidden' id='uri' value='$uri'>";
		//echo "<script src='redirect.js'></script>";
		
		//voltar jg atual
		echo "<br><a href='mesa.php?id=$id&sala=$sala'>voltar Jg Atual</a>";
	}
	
?>