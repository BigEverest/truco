<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="layoutDoJogo.css">
		<script src="mesa.js"></script>
	</head>
	<body>
	
		<?php
		
			require("funcoes_do_banco.php");
			
			$nome=isset($_POST["nome"])?$_POST["nome"]:"0";
			$cadeira=isset($_POST["cadeira"])?$_POST["cadeira"]:"0";
			$sala=isset($_POST["sala"])?$_POST["sala"]:"0";
			
			$select="SELECT idJog FROM cartas WHERE sala=$sala and cadeira=$cadeira";
			$select=selecionarBanco($select);
			$idJog=$select[0]["idJog"];
			
			$querry="INSERT INTO `jogadores` 
			(`id`,
			`nome`)
			VALUES ('$idJog', '$nome');";
			if(mudarBanco($querry)==0)
					return;
			
			//redirect
			if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) 
			{
				$uri = 'https://';
			} else {
				$uri = 'http://';
			}
			$uri .= $_SERVER['HTTP_HOST'];
			echo "<script type='text/javascript'>";
			echo "window.location.assign('$uri/truco/lobby.php?nome=$nome&sala=$sala&id=$idJog');";
			echo "</script>";
			
		?>
	</body>
	




</html>