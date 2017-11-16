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
			
			$sala=isset($_GET["sala"])?$_GET["sala"]:"";
			$nome=isset($_GET["nome"])?$_GET["nome"]:"";
			if(verificaSala($sala)==0)
				return;
			
			if(verificaNome($nome,$sala)==0)
				return;
			$qtdJogadores=pegaQtdJogadores($sala);
			echo "<div class='msgGrande'>Bem Vindo a sala:$sala<br></div>";
			
			$jogsNome=pegarNomesJogs($sala);
			exibirJogadores($jogsNome,$qtdJogadores);
			
			function pegaQtdJogadores($sala)
			{
				$select="SELECT qtdJogadores FROM salas WHERE id=$sala";
				$select=selecionarBanco($select);
				$qtdJogadores=$select[0]["qtdJogadores"];
				return $qtdJogadores;
			}
			
			function pegarNomesJogs($sala)
			{
				$select="SELECT idJog FROM cartas WHERE sala=$sala ORDER BY `cartas`.`cadeira` ASC ";
				$select=selecionarBanco($select);
				if($select==0)
					return;
				$idJogs[1]=$select[0]["idJog"];
				$idJogs[2]=$select[1]["idJog"];
				$idJogs[3]=$select[2]["idJog"];
				$idJogs[4]=$select[3]["idJog"];
				for($i=1;$i<=4;$i++)
				{
					$select="SELECT nome FROM jogadores WHERE id=".$idJogs[$i];
					$select=selecionarBanco($select);
					if($select!=0)
						$jogsNome[$i]=$select[0]["nome"];
					else 
						$jogsNome[$i]="";
				}
				return $jogsNome;
			}
			
			function exibirJogadores($jogsNome,$qtdJogadores)
			{

				if($qtdJogadores==4)
				{
					echo "<div class='msgGrande'>Time 1:</div>";
					if($jogsNome[1]!="")
						echo "<div class='msgGrande'>".$jogsNome[1]."<br>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					if($jogsNome[3]!="")
						echo $jogsNome[3]."<br></div>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					
					echo "<div class='msgGrande'>Time 2:</div>";
					if($jogsNome[2]!="")
						echo "<div class='msgGrande'>".$jogsNome[2]."<br>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					if($jogsNome[4]!="")
						echo $jogsNome[4]."<br></div>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					
				}
				if($qtdJogadores==2)
				{
					echo "<div class='msgGrande'>Time 1:</div>";
					if($jogsNome[1]!="")
						echo "<div class='msgGrande'>".$jogsNome[1]."</div>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					
					echo "<div class='msgGrande'>Time 2:</div>";
					if($jogsNome[2]!="")
						echo "<div class='msgGrande'>".$jogsNome[2]."</div>";
					else
						echo "<input type='button' class='bt_importante' value='entrar'/>";
					
				}
				
			}
			
			function verificaNome($nome,$sala)
			{
				$nomeApenasLetrasENumeros=preg_replace('/[^[:alnum:]_]/', '',$nome);
				if($nome!=$nomeApenasLetrasENumeros)
				{
					echo "<div class='msgGrande'>Nome não valído.</div> ";
					echo "<a href='lobby.php?sala=$sala'><input type='button' class='bt_importante' value='voltar'/></a>";
					return 0;
				}
				if($nome=="")
				{
					echo "<form action='lobby.php' method='GEt'>";
					echo "<div class='msgGrande'>Digite um Nome</div> ";
					echo "<input type='text' class='bt_importante' name='nome' required='required' pattern='[a-zA-Z0-9]+$' placeholder='Apenas letras e números sem espaço' />";
					echo "<input type='hidden' name='sala' value='$sala'/>";
					echo "<input type='submit' class='bt_importante' value='enviar'/>";
					echo "</form>";
					return 0;
				}
				return 1;
					
			}
			
			function verificaSala($sala)
			{
				if($sala==""||!is_numeric($sala))
				{
					echo "<div class='msgGrande'>Sala não encontrada</div> ";
					return 0;
				}
				
				$select="SELECT id from salas WHERE id=$sala";
				if(selecionarBanco($select)==0)
				{
					echo "<div class='msgGrande'>Sala não encontrada</div> ";
					return 0;
				}
				return 1;
			}
			
			
		?>
	</body>
	




</html>