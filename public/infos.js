
function afficher(id) {
var idiv = document.getElementById(id)
if (getComputedStyle(idiv).display != "none") {
idiv.style.display = "none";
} else {
idiv.style.display = "block";
}
}