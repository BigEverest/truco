var sala=document.getElementById("sala").value;
var id=document.getElementById("id").value;
var uri=document.getElementById("uri").value;
window.location.replace(uri+"/mesa.php?id="+id+"&sala="+sala);