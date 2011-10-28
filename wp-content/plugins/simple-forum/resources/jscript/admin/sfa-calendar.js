/* ---------------------------------
Simple:Press 
Admin Javascript/ Calendar
$LastChangedDate: 2008-09-04 11:00:53 +0100 (Thu, 04 Sep 2008) $
$Rev: 592 $
------------------------------------ */


/* ----------------------------------*/
/* sfjCalendar Date Picker              */
/* ----------------------------------*/

/*Javascript name: My Date Time Picker
  Date created: 16-Nov-2003 23:19
  Scripter: TengYong Ng
  Website: http://www.rainforestnet.com
  Copyright (c) 2003 TengYong Ng
  FileName: DateTimePicker.js
  Version: 0.8
  Contact: contact@rainforestnet.com
   Note: Permission given to use this script in ANY kind of applications if
         header lines are left unchanged. */

/* Global variables */
var winCal;
var dtToday=new Date();
var Cal;
var docCal;
var MonthName=["January", "February", "March", "April", "May", "June","July", 
	"August", "September", "October", "November", "December"];
var WeekDayName=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];	
var exDateTime;

/* Configurable parameters */
var cnTop="200";
var cnLeft="500";
var WindowTitle ="DateTime Picker";
var WeekChar=2;
var CellWidth=20;
var DateSeparator=" ";
var TimeMode=24;

var ShowLongMonth=true;
var ShowMonthYear=true;
var MonthYearColor="#cc0033";
var WeekHeadColor="#0099CC";
var SundayColor="#6699FF";
var SaturdayColor="#CCCCFF";
var WeekDayColor="white";
var FontColor="blue";
var TodayColor="#FFFF33";
var SelDateColor="#FFFF99";
var YrSelColor="#cc0033";
var ThemeBg="";

function sfjNewCal(pCtrl,pFormat,pShowTime,pTimeMode)
{
	Cal=new sfjCalendar(dtToday);
	if ((pShowTime!=null) && (pShowTime))
	{
		Cal.ShowTime=true;
		if ((pTimeMode!=null) &&((pTimeMode=='12')||(pTimeMode=='24')))
		{
			TimeMode=pTimeMode;
		}		
	}	
	if (pCtrl!=null)
		Cal.Ctrl=pCtrl;
	if (pFormat!=null)
		Cal.Format=pFormat.toUpperCase();
	
	exDateTime=document.getElementById(pCtrl).value;
	if (exDateTime!="")
	{
		var Sp1;
		var Sp2;
		var tSp1;
		var tSp1;
		var strMonth;
		var strDate;
		var strYear;
		var intMonth;
		var YearPattern;
		var strHour;
		var strMinute;
		var strSecond;
		
		Sp1=exDateTime.indexOf(DateSeparator,0)
		Sp2=exDateTime.indexOf(DateSeparator,(parseInt(Sp1)+1));
		
		if ((Cal.Format.toUpperCase()=="DDMMYYYY") || (Cal.Format.toUpperCase()=="DDMMMYYYY"))
		{
			strMonth=exDateTime.substring(Sp1+1,Sp2);
			strDate=exDateTime.substring(0,Sp1);
		}
		else if ((Cal.Format.toUpperCase()=="MMDDYYYY") || (Cal.Format.toUpperCase()=="MMMDDYYYY"))
		{
			strMonth=exDateTime.substring(0,Sp1);
			strDate=exDateTime.substring(Sp1+1,Sp2);
		}
		if (isNaN(strMonth))
			intMonth=Cal.sfjGetMonthIndex(strMonth);
		else
			intMonth=parseInt(strMonth,10)-1;	
		if ((parseInt(intMonth,10)>=0) && (parseInt(intMonth,10)<12))
			Cal.Month=intMonth;
		
		if ((parseInt(strDate,10)<=Cal.sfjGetMonDays()) && (parseInt(strDate,10)>=1))
			Cal.Date=strDate;
		
		strYear=exDateTime.substring(Sp2+1,Sp2+5);
		YearPattern=/^\d{4}$/;
		if (YearPattern.test(strYear))
			Cal.Year=parseInt(strYear,10);
		
		if (Cal.ShowTime==true)
		{
			tSp1=exDateTime.indexOf(":",0)
			tSp2=exDateTime.indexOf(":",(parseInt(tSp1)+1));
			strHour=exDateTime.substring(tSp1,(tSp1)-2);
			Cal.sfjSetHour(strHour);
			strMinute=exDateTime.substring(tSp1+1,tSp2);
			Cal.sfjSetMinute(strMinute);
			strSecond=exDateTime.substring(tSp2+1,tSp2+3);
			Cal.sfjSetSecond(strSecond);
		}	
	}
	winCal=window.open("","DateTimePicker","toolbar=0,status=0,menubar=0,fullscreen=no,width=195,height=245,resizable=0,top="+cnTop+",left="+cnLeft);
	docCal=winCal.document;
	sfjRenderCal();
}

function sfjRenderCal()
{
	var vCalHeader;
	var vCalData;
	var vCalTime;
	var i;
	var j;
	var SelectStr;
	var vDayCount=0;
	var vFirstDay;

	docCal.open();
	docCal.writeln("<html><head><title>"+WindowTitle+"</title>");
	docCal.writeln("<script>var winMain=window.opener;</script>");
	docCal.writeln("</head><body background='"+ThemeBg+"' link="+FontColor+" vlink="+FontColor+"><form name='sfjCalendar'>");

	vCalHeader="<table border=1 cellpadding=1 cellspacing=1 width='100%' align=\"center\" valign=\"top\">\n";
	
	vCalHeader+="<tr>\n<td colspan='7'><table border=0 width='100%' cellpadding=0 cellspacing=0><tr><td align='left'>\n";
	vCalHeader+="<select name=\"MonthSelector\" onChange=\"javascript:winMain.Cal.sfjSwitchMth(this.selectedIndex);winMain.sfjRenderCal();\">\n";
	for (i=0;i<12;i++)
	{
		if (i==Cal.Month)
			SelectStr="Selected";
		else
			SelectStr="";	
		vCalHeader+="<option "+SelectStr+" value >"+MonthName[i]+"\n";
	}
	vCalHeader+="</select></td>";
	
	vCalHeader+="\n<td align='right'><a href=\"javascript:winMain.Cal.sfjDecYear();winMain.sfjRenderCal()\"><b><font color=\""+YrSelColor+"\"><</font></b></a><font face=\"Verdana\" color=\""+YrSelColor+"\" size=2><b> "+Cal.Year+" </b></font><a href=\"javascript:winMain.Cal.sfjIncYear();winMain.sfjRenderCal()\"><b><font color=\""+YrSelColor+"\">></font></b></a></td></tr></table></td>\n";	
	vCalHeader+="</tr>";
	
	if (ShowMonthYear)
		vCalHeader+="<tr><td colspan='7'><font face='Verdana' size='2' align='center' color='"+MonthYearColor+"'><b>"+Cal.sfjGetMonthName(ShowLongMonth)+" "+Cal.Year+"</b></font></td></tr>\n";
	
	vCalHeader+="<tr bgcolor="+WeekHeadColor+">";
	for (i=0;i<7;i++)
	{
		vCalHeader+="<td align='center'><font face='Verdana' size='2'>"+WeekDayName[i].substr(0,WeekChar)+"</font></td>";
	}
	vCalHeader+="</tr>";	
	docCal.write(vCalHeader);
	
	CalDate=new Date(Cal.Year,Cal.Month);
	CalDate.setDate(1);
	vFirstDay=CalDate.getDay();
	vCalData="<tr>";
	for (i=0;i<vFirstDay;i++)
	{
		vCalData=vCalData+sfjGenCell();
		vDayCount=vDayCount+1;
	}
	for (j=1;j<=Cal.sfjGetMonDays();j++)
	{
		var strCell;
		vDayCount=vDayCount+1;
		if ((j==dtToday.getDate())&&(Cal.Month==dtToday.getMonth())&&(Cal.Year==dtToday.getFullYear()))
			strCell=sfjGenCell(j,true,TodayColor);
		else
		{
			if (j==Cal.Date)
			{
				strCell=sfjGenCell(j,true,SelDateColor);
			}
			else
			{	 
				if (vDayCount%7==0)
					strCell=sfjGenCell(j,false,SaturdayColor);
				else if ((vDayCount+6)%7==0)
					strCell=sfjGenCell(j,false,SundayColor);
				else
					strCell=sfjGenCell(j,null,WeekDayColor);
			}		
		}						
		vCalData=vCalData+strCell;

		if((vDayCount%7==0)&&(j<Cal.sfjGetMonDays()))
		{
			vCalData=vCalData+"</tr>\n<tr>";
		}
	}
	docCal.writeln(vCalData);	
	
	if (Cal.ShowTime)
	{
		var showHour;
		showHour=Cal.sfjgetShowHour();		
		vCalTime="<tr>\n<td colspan='7' align='center'>";
		vCalTime+="<input type='text' name='hour' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+showHour+" onchange=\"javascript:winMain.Cal.sfjSetHour(this.value)\">";
		vCalTime+=" : ";
		vCalTime+="<input type='text' name='minute' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+Cal.Minutes+" onchange=\"javascript:winMain.Cal.sfjSetMinute(this.value)\">";
		vCalTime+=" : ";
		vCalTime+="<input type='text' name='second' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+Cal.Seconds+" onchange=\"javascript:winMain.Cal.sfjSetSecond(this.value)\">";
		if (TimeMode==12)
		{
			var SelectAm =(parseInt(Cal.Hours,10)<12)? "Selected":"";
			var SelectPm =(parseInt(Cal.Hours,10)>=12)? "Selected":"";

			vCalTime+="<select name=\"ampm\" onchange=\"javascript:winMain.Cal.sfjSetAmPm(this.options[this.selectedIndex].value);\">";
			vCalTime+="<option "+SelectAm+" value=\"AM\">AM</option>";
			vCalTime+="<option "+SelectPm+" value=\"PM\">PM<option>";
			vCalTime+="</select>";
		}	
		vCalTime+="\n</td>\n</tr>";
		docCal.write(vCalTime);
	}	
	
	docCal.writeln("\n</table>");
	docCal.writeln("</form></body></html>");
	docCal.close();
}

function sfjGenCell(pValue,pHighLight,pColor)
{
	var PValue;
	var PCellStr;
	var vColor;
	var vHLstr1;
	var vHlstr2;
	var vTimeStr;
	
	if (pValue==null)
		PValue="";
	else
		PValue=pValue;
	
	if (pColor!=null)
		vColor="bgcolor=\""+pColor+"\"";
	else
		vColor="";	
	if ((pHighLight!=null)&&(pHighLight))
		{vHLstr1="color='red'><b>";vHLstr2="</b>";}
	else
		{vHLstr1=">";vHLstr2="";}	
	
	if (Cal.ShowTime)
	{
		vTimeStr="winMain.document.getElementById('"+Cal.Ctrl+"').value+=' '+"+"winMain.Cal.sfjgetShowHour()"+"+':'+"+"winMain.Cal.Minutes"+"+':'+"+"winMain.Cal.Seconds";
		if (TimeMode==12)
			vTimeStr+="+' '+winMain.Cal.AMorPM";
	}	
	else
		vTimeStr="";		
	PCellStr="<td "+vColor+" width="+CellWidth+" align='center'><font face='verdana' size='2'"+vHLstr1+"<a href=\"javascript:winMain.document.getElementById('"+Cal.Ctrl+"').value='"+Cal.sfjFormatDate(PValue)+"';"+vTimeStr+";window.close();\">"+PValue+"</a>"+vHLstr2+"</font></td>";
	return PCellStr;
}

function sfjCalendar(pDate,pCtrl)
{
	
	this.Date=pDate.getDate();
	this.Month=pDate.getMonth();
	this.Year=pDate.getFullYear();
	this.Hours=pDate.getHours();	
	
	if (pDate.getMinutes()<10)
		this.Minutes="0"+pDate.getMinutes();
	else
		this.Minutes=pDate.getMinutes();
	
	if (pDate.getSeconds()<10)
		this.Seconds="0"+pDate.getSeconds();
	else		
		this.Seconds=pDate.getSeconds();
		
	this.MyWindow=winCal;
	this.Ctrl=pCtrl;
	this.Format="ddMMyyyy";
	this.Separator=DateSeparator;
	this.ShowTime=false;
	if (pDate.getHours()<12)
		this.AMorPM="AM";
	else
		this.AMorPM="PM";	
}

function sfjGetMonthIndex(shortMonthName)
{
	for (i=0;i<12;i++)
	{
		if (MonthName[i].substring(0,3).toUpperCase()==shortMonthName.toUpperCase())
		{	return i;}
	}
}
sfjCalendar.prototype.sfjGetMonthIndex=sfjGetMonthIndex;

function sfjIncYear()
{	Cal.Year++;}
sfjCalendar.prototype.sfjIncYear=sfjIncYear;

function sfjDecYear()
{	Cal.Year--;}
sfjCalendar.prototype.sfjDecYear=sfjDecYear;
	
function sfjSwitchMth(intMth)
{	Cal.Month=intMth;}
sfjCalendar.prototype.sfjSwitchMth=sfjSwitchMth;

function sfjSetHour(intHour)
{	
	var MaxHour;
	var MinHour;
	if (TimeMode==24)
	{	MaxHour=23;MinHour=0}
	else if (TimeMode==12)
	{	MaxHour=12;MinHour=1}
	else
		alert("TimeMode can only be 12 or 24");		
	var HourExp=new RegExp("^\\d\\d$");
	if (HourExp.test(intHour) && (parseInt(intHour,10)<=MaxHour) && (parseInt(intHour,10)>=MinHour))
	{	
		if ((TimeMode==12) && (Cal.AMorPM=="PM"))
		{
			if (parseInt(intHour,10)==12)
				Cal.Hours=12;
			else	
				Cal.Hours=parseInt(intHour,10)+12;
		}	
		else if ((TimeMode==12) && (Cal.AMorPM=="AM"))
		{
			if (intHour==12)
				intHour-=12;
			Cal.Hours=parseInt(intHour,10);
		}
		else if (TimeMode==24)
			Cal.Hours=parseInt(intHour,10);	
	}
}
sfjCalendar.prototype.sfjSetHour=sfjSetHour;

function sfjSetMinute(intMin)
{
	var MinExp=new RegExp("^\\d\\d$");
	if (MinExp.test(intMin) && (intMin<60))
		Cal.Minutes=intMin;
}
sfjCalendar.prototype.sfjSetMinute=sfjSetMinute;

function sfjSetSecond(intSec)
{	
	var SecExp=new RegExp("^\\d\\d$");
	if (SecExp.test(intSec) && (intSec<60))
		Cal.Seconds=intSec;
}
sfjCalendar.prototype.sfjSetSecond=sfjSetSecond;

function sfjSetAmPm(pvalue)
{
	this.AMorPM=pvalue;
	if (pvalue=="PM")
	{
		this.Hours=(parseInt(this.Hours,10))+12;
		if (this.Hours==24)
			this.Hours=12;
	}	
	else if (pvalue=="AM")
		this.Hours-=12;	
}
sfjCalendar.prototype.sfjSetAmPm=sfjSetAmPm;

function sfjgetShowHour()
{
	var finalHour;
    if (TimeMode==12)
    {
    	if (parseInt(this.Hours,10)==0)
		{
			this.AMorPM="AM";
			finalHour=parseInt(this.Hours,10)+12;	
		}
		else if (parseInt(this.Hours,10)==12)
		{
			this.AMorPM="PM";
			finalHour=12;
		}		
		else if (this.Hours>12)
		{
			this.AMorPM="PM";
			if ((this.Hours-12)<10)
				finalHour="0"+((parseInt(this.Hours,10))-12);
			else
				finalHour=parseInt(this.Hours,10)-12;	
		}
		else
		{
			this.AMorPM="AM";
			if (this.Hours<10)
				finalHour="0"+parseInt(this.Hours,10);
			else
				finalHour=this.Hours;	
		}
	}
	else if (TimeMode==24)
	{
		if (this.Hours<10)
			finalHour="0"+parseInt(this.Hours,10);
		else	
			finalHour=this.Hours;
	}	
	return finalHour;	
}				
sfjCalendar.prototype.sfjgetShowHour=sfjgetShowHour;		

function sfjGetMonthName(IsLong)
{
	var Month=MonthName[this.Month];
	if (IsLong)
		return Month;
	else
		return Month.substr(0,3);
}
sfjCalendar.prototype.sfjGetMonthName=sfjGetMonthName;

function sfjGetMonDays()
{
	var DaysInMonth=[31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if (this.sfjIsLeapYear())
	{
		DaysInMonth[1]=29;
	}	
	return DaysInMonth[this.Month];	
}
sfjCalendar.prototype.sfjGetMonDays=sfjGetMonDays;

function sfjIsLeapYear()
{
	if ((this.Year%4)==0)
	{
		if ((this.Year%100==0) && (this.Year%400)!=0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}
sfjCalendar.prototype.sfjIsLeapYear=sfjIsLeapYear;

function sfjFormatDate(pDate)
{
	if (this.Format.toUpperCase()=="DDMMYYYY")
		return (pDate+DateSeparator+(this.Month+1)+DateSeparator+this.Year);
	else if (this.Format.toUpperCase()=="DDMMMYYYY")
		return (pDate+DateSeparator+this.sfjGetMonthName(false)+DateSeparator+this.Year);
	else if (this.Format.toUpperCase()=="MMDDYYYY")
		return ((this.Month+1)+DateSeparator+pDate+DateSeparator+this.Year);
	else if (this.Format.toUpperCase()=="MMMDDYYYY")
		return (this.sfjGetMonthName(false)+DateSeparator+pDate+DateSeparator+this.Year);			
}
sfjCalendar.prototype.sfjFormatDate=sfjFormatDate;
