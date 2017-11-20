<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="layoutDoJogo.css">
		<script src="mesa.js"></script>
	</head>
	<body>
	
			<div class='creditos'>Truco By Vinícius Clemente.</div>
		<?php
			require("funcoes_do_banco.php");
			
			//verrifica se qtdJogadores é valida
			$qtdJogadores=isset($_POST["qtdJog"])?$_POST["qtdJog"]:"0";
			if($qtdJogadores!=2&&$qtdJogadores!=4)
				return;
			//limitador de 100 salas
			$select="SELECT id FROM `salas` ORDER BY `salas`.`id` DESC";
			$select=(selecionarBanco($select));
			$sala=$select[0]["id"]+1;
			if($sala>=100)
				return;
			echo "$sala<br>";
			
			//Criar Sala
			$query=
			"INSERT INTO `salas` 
			(`id`,
			`quedasTime1`,
			`quedasTime2`,
			`vitoriasTime1`,
			`vitoriasTime2`,
			`cadeiraMao`,
			`valendo`,
			`jogada1`,
			`jogada2`,
			`jogada3`,
			`jogadaAtual`,
			`qtdJogadores`,
			`reload`)
			VALUES ('$sala', '0', '0', '0', '0', '1', '1', '0', '0', '0', '1', '$qtdJogadores', '0');";
			if(mudarBanco($query)==0)
				return;
			$query=
			"INSERT INTO `jogadas` 
			(`id`,
			`sala`,
			`carta1`,
			`carta2`,
			`carta3`,
			`carta4`) 
			VALUES (NULL, '$sala', '0', '0', '0', '0');";
			if(mudarBanco($query)==0)
				return;
			
			//id dos jogadores
			for($cont=1;$cont<=4;$cont++)
			{
				do
				{
					$id=rand(1,100000);//1 a 100.000
					$select="SELECT id FROM cartas WHERE id=$id;";
					echo "$id<br>";
				}while(selecionarBanco($select)!=0);
				$query=
				"INSERT INTO `cartas` 
				(`id`,
				`idJog`,
				`carta1`,
				`carta2`,
				`carta3`,
				`sala`,
				`cadeira`,
				`cartaJogada`,
				`podeJogar`,
				`podeTruco`,
				`maoDe11`)
				VALUES (NULL, '$id', '0', '0', '0', '$sala', '$cont', '0', '0', '0', '0');";
				if(mudarBanco($query)==0)
					return;
			}
			//redirect
			if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) 
			{
				$uri = 'https://';
			} else {
				$uri = 'http://';
			}
			$uri .= $_SERVER['HTTP_HOST'];
			echo "<script type='text/javascript'>";
			echo "window.location.assign('$uri/truco/lobby.php?sala=$sala');";
			echo "</script>";
			
		?>
		
		
	</body>
	




</html>