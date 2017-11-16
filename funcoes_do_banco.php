<?php
require("conexao.php");

function selecionarBanco($select){
		global $conn;
		$result=mysqli_query($conn,$select);
		if(!$result)
		{
			echo "<font style='color:red'>$select<br>".mysqli_error($conn)."</font><br>";
			return 0;
			}
		//else echo "$select<br>";
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
		
		$result=mysqli_query($conn,$querry);
		if(!$result)
		{
			echo "<font style='color:red'>$querry<br>".mysqli_error($conn)."</font><br>";
			return 0;
		}
		//else echo "$querry<br>";
		
		return 1;
}


?>


