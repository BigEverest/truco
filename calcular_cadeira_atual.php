<?php
	function calcular_cadeira_atual($id,$cadeira,$sala){
		require_once("conexao.php");
		$select="SELECT cadeira FROM cartas where sala=$sala and idJog=$id";  
		$result= mysqli_query($conn,$select);
		$cadeira[1]=$select["cadeira"];
		$select="SELECT cadeira FROM cartas where sala=$sala and idJog!=$id";  
		$result= mysqli_query($conn,$select);
		while($select=mysqli_fetch_array($result)){
			$i=$cadeira[1]-$select["cadeira"];
			if($i<0)$i+=4;
			$cadeira[$i]=$select["cadeira"];
		
		}
			
	}

?>