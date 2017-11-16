<?php
	
	
	$id=isset($_GET["id"])?$_GET["id"]:"0";
	$sala=isset($_GET["sala"])?$_GET["sala"]:"0";
	if($id!="0"&&$sala!="0"){
		require_once("conexao.php");
		$select="SELECT carta1,carta2,carta3 FROM cartas where idjog=$id and sala=$sala";  
		$result= mysqli_query($conn,$select);
		
		while($select=mysqli_fetch_array($result)){
			for($i=1;$i<=3;$i++){
				$carta[$i]=$select["carta$i"];
			}
			
		}
		echo "<table id='cartasJog'>";
		echo "<tr>";
			for($i=1;$i<=3;$i++){
				echo "<td>";
					echo "<img class='imgJogAtual'src='imagens/$carta[$i].jpg'>";
				echo "</td>";
			}
		echo "</tr>";
		echo "</table>";
	}


?>