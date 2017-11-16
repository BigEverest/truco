<?php
	
	$id=isset($_GET["id"])?$_GET["id"]:"0";
	$sala=isset($_GET["sala"])?$_GET["sala"]:"0";
	if($id!="0"&&$sala!="0"){
		require_once("conexao.php");
		$cadeira=array("","","","","");
		$cadeira=calcular_cadeira_atual($id,$sala,$cadeira);
		echo "1:".$cadeira[0];
		echo "<br>2:".$cadeira[1];
		echo "<br>3:".$cadeira[2];
		echo "<br>4:".$cadeira[3];
		/*while($select=mysqli_fetch_array($result)){
			$cartaJogada=$select["cartaJogada"];
			
		}*/
	}
	function calcular_cadeira_atual($id,$sala,array $cadeira){
		global $conn;
		//echo$id;
		//echo$sala;
		$select="SELECT cadeira FROM cartas where sala=$sala and idJog=$id";  
		$result= mysqli_query($conn,$select);
		while($select=mysqli_fetch_array($result)){$cadeira[1]=$select["cadeira"];}
		$select="SELECT cadeira FROM cartas where sala=$sala and idJog!=$id";  
		$result= mysqli_query($conn,$select);
		while($select=mysqli_fetch_array($result)){
			$i=$cadeira[1]+$select["cadeira"];
			echo "i:$i<br>";
			if($i>4)$i-=4;
			echo "i:$i<br>";
			$cadeira[$i]=$select["cadeira"];
		
		}
			
	}



?>