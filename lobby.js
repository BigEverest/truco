function enviar(nome,cadeira,sala)
{
	document.getElementById("nome").value=nome;
	document.getElementById("cadeira").value=cadeira;
	document.getElementById("sala").value=sala;
	formEntrar.submit();
}