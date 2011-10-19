function testPassword(passwd){
var description = new Array();
description[0] = '<p class="indicator indicator-1"></p> <p><strong>Weakest</strong></p>';
description[1] = '<p class="indicator indicator-2"></p> <p><strong>Weak</strong></p>';
description[2] = '<p class="indicator indicator-3"></p> <p><strong>Improving</strong></p>';
description[3] = '<p class="indicator indicator-4"></p> <p><strong>Strong</strong></p>';
description[4] = '<p class="indicator indicator-5"></p> <p><strong>Strongest</strong></p>';
description[5] = '<p class="indicator indicator-6"></p> <p><strong>Begin Typing</strong></p>';

var base = 0
var combos = 0
if (passwd.match(/[a-z]/))base = (base+26);
if (passwd.match(/[A-Z]/))base = (base+26);
if (passwd.match(/\d+/))base = (base+10);
if (passwd.match(/[>!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~]/))base = (base+33);

combos=Math.pow(base,passwd.length);

if(combos == 1)strVerdict = description[5];
else if(combos > 1 && combos < 1000000)strVerdict = description[0];
else if (combos >= 1000000 && combos < 1000000000000)strVerdict = description[1];
else if (combos >= 1000000000000 && combos < 1000000000000000000)strVerdict = description[2];
else if (combos >= 1000000000000000000 && combos < 1000000000000000000000000)strVerdict = description[3];
else strVerdict = description[4];

document.getElementById("Words").innerHTML= (strVerdict);
}