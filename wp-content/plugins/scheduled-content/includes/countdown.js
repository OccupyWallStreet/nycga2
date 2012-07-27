/*
	Original Author:		Robert Hashemian (http://www.hashemian.com/)
*/
function countdown(obj) {
	this.obj		= obj;
	this.Div		= "countdown";
	this.TargetDate		= "12/31/2020 5:00 AM";
	this.DisplayFormat	= "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
	this.CountActive	= true;
	
	this.DisplayStr;

	this.Calcage		= cd_Calcage;
	this.CountBack		= cd_CountBack;
	this.Setup		= cd_Setup;
}
function cd_Calcage(secs, num1, num2) {
  s = ((Math.floor(secs/num1))%num2).toString();
  //if (s.length < 2) s = "0" + s;
  return (s);
}
function cd_CountBack(secs) {
	if (secs <= 0) {
    location.reload(true);
    return;
  }

  this.DisplayStr = this.DisplayFormat.replace(/%%D%%/g,	this.Calcage(secs,86400,100000));
  this.DisplayStr = this.DisplayStr.replace(/%%H%%/g,		this.Calcage(secs,3600,24));
  this.DisplayStr = this.DisplayStr.replace(/%%M%%/g,		this.Calcage(secs,60,60));
  this.DisplayStr = this.DisplayStr.replace(/%%S%%/g,		this.Calcage(secs,1,60));

  document.getElementById(this.Div).innerHTML = this.DisplayStr;
  if (this.CountActive) setTimeout(this.obj +".CountBack(" + (secs-1) + ")", 990);
}
function cd_Setup() {
	var dthen	= new Date(this.TargetDate);
  	var dnow	= new Date();
	ddiff		= new Date(dthen-dnow);
	gsecs		= Math.floor(ddiff.valueOf()/1000);
	this.CountBack(gsecs);
}