<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="layoutDoJogo.css">
		<script src="mesa.js"></script>
	</head>
	<body>
		<?php
			echo "<div class='creditos'>Truco By Vinícius Clemente.</div>";
			
			$id=isset($_GET["id"])?$_GET["id"]:"0";
			$sala=isset($_GET["sala"])?$_GET["sala"]:"0";
			require("funcoes_de_exibir.php");
			if($id!=0&&$sala!=0)
			{
				exibirMesa($id,$sala);
				exibirCartajogAtual($id,$sala);
				verrificaSePodeJogar($id,$sala);
				verrificaSePodeTruco($id,$sala);
				verrificaMaoDe11($id,$sala);
				ReloadComAjax($sala);
			}
			
		?>
	</body>
	




</html>