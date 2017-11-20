<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="layoutDoJogo.css">
		<script src="index.js"></script>
	</head>
	<body>
	
			<div class='creditos'>Truco By Vinícius Clemente.</div>
			
			<div class='BlankSpace400'></div>
			
			<div id="bt_inicio">
				<input type="button" class="bt_importante" value="Criar Sala" onclick="selecionarQtdJog('0')"/>
				<input type="button" class="bt_importante" value="Entrar em uma Sala" onclick="selecionarSala('0')"/>
			</div>
			
			<div id="bt_sala" style="display:none">
				<div class="msgGrande">
					Digite o número da sala:
				</div>
				<input type="number" class="bt_importante" id="input-sala"/>
				<input type="button" class="bt_importante" value="Entrar" onclick="selecionarSala('1')"/>
				<input type="button" class="bt_importante" value="Voltar" onclick="selecionarSala('-1')"/>
			</div>
			<form action="lobby.php" method="get" name="formLobby">
				<input type="hidden" id="sala" name="sala" value=""/>
			</form>
			
			<div id="bt_qtdJog" style="display:none">
				<div class="msgGrande">
					Seleciona a quantidade de jogs:
				</div>
				<div class="msgGrande"></div>
				<center>
					<select class="selectGrande" id="input-qtdJog">
						<option>2</option>
						<option>4</option>
					</select>
				</center>
				<div class="msgGrande"></div>
				<input type="button" class="bt_importante" value="Entrar" onclick="selecionarQtdJog('1')"/>
				<input type="button" class="bt_importante" value="Voltar" onclick="selecionarQtdJog('-1')"/>
			</div>			
			<form action="criarSala.php" method="post" name="formCriarSala">
				<input type="hidden" id="qtdJog" name="qtdJog" value=""/>
			</form>
		<?php
			
		?>
	</body>
	




</html>