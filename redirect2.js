var sala=document.getElementById("sala").value;
var id=document.getElementById("id").value;
var uri=document.getElementById("uri").value;
window.location.assign(uri+"/truco/index.php?id="+id+"&sala="+sala);
//window.location.replace(uri+"/index.php?id="+id+"&sala="+sala);