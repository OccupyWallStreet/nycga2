nextItem = 0; bprotatetime = 7000; // Set how often it rotates. 1000 = 1 Second
function rotate () {
var list = document.getElementById('rotation').getElementsByTagName('LI');
var e, i;
for (i = 0; e = list[i]; i++) {e.className = i == nextItem ? '' : 'hide'};
nextItem++;
if (nextItem == list.length) nextItem = 0;
}
if (document.getElementById) onload = function () {
document.getElementById("footeractivity").style.display = "block";
document.getElementById('rotation').className = 'rotate';
rotate(); setInterval ('rotate()', bprotatetime);
}
function rotateback () {
if (nextItem == null) { nextItem = 1; }
if (nextItem == 0) { nextItem = 1; }
nextItem--;
var list = document.getElementById('rotation').getElementsByTagName('LI');
var e, i;
for (i = 0; e = list[i]; i++) {e.className = i == nextItem ? '' : 'hide'};
if (nextItem == list.length) nextItem = 0;
}
function bpactbarclose(){document.getElementById("footeractivity").style.display = "none";document.getElementById("innerbpclose").style.display = "none";document.getElementById("innerbpopen").style.display = "block";}
function bpactbaropen(){document.getElementById("footeractivity").style.display = "block";document.getElementById("innerbpclose").style.display = "block";document.getElementById("innerbpopen").style.display = "none";}
