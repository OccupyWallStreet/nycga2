// Outbrain 2008 
// Wordpress js support file
//version 7.0.0.0

var langs_div = "langs_list";
var user_lang_div = "user_lang_div";
var translator_div = "translator_div";
var current;

//containers varaiables
var right_arrow_class 	= "option_board_right";
var down_arrow_class  	= "option_board_down";

//	claiming script variables
var currKeyCode		=	'';
var keyCodeChanged	=	false;
var ajaxUrl			=	null;

var enableAllelements 			  = new Array('block_language','block_claim','block_pages','block_recommendations','getWidget','block_submit');
var claimModeElements 			  = new Array('block_claim',true,'block_additonal_setting',true,'block_custom_settings',true,'block_MP',true,'block_settings',false,'block_pages',true,'block_recommendations',true,'block_submit',true);
var noClaimModeElements			  = new Array('block_language',true,'block_claim',true,'block_additonal_setting',true,'block_additonal_instruction',true,'block_MP',true,'block_settings',false,'block_pages',true,'block_recommendations',true,'block_submit',true);
var claimModeElements_hide 		= new Array('block_loader',true,'claim_key',true);
var noClaimModeElements_hide 	= new Array('block_loader',true,'claim_title',true);

var isPageLoadMode				;//to verify that the return data trigers the page load display
var mainTimeOut 				= null;
var timeOutDelay 				= 60000;

//Console Class
var outbrainConsole 			= function(){
	this.self_logger			= this;	
	this.obStageCounter 		= 0;
	this.obConsoleStr			= "";
	this.obDocumentLogger		= null;
	this.toString 				= function(){return (this.getVersion() + this.obConsoleStr);}
	this.obStage				= function(stage){this.obStageCounter = count;}
	this.write					= function(str,level){
		if (level == null || level.length <= 0 ) level = "Debug";
		this.obConsoleStr += this.getCurrentTime() + " " + level +": "+ str +  '\n';
		if ((this.obDocumentLogger != null) && (typeof(this.obDocumentLogger) == 'object')){
			try{obDocumentLogger.value = this.obConsoleStr;}catch(ex){}
		}
	}
	this.getVersion 			= function (){
		var str ="";
		obPluginsVersion = document.getElementById("obVersion").value;
		obCurrentKey	 = document.getElementById("obCurrentKey").value;
		str += "Outbrain Wordpress Plugins version: "	+obPluginsVersion 	+"\n";
		str += "Outbrain Current(saved) Claim Key: "		 	+obCurrentKey 		+"\n";
		str += "plugins location: " + location.href + "\n";
		str +  "start time:" + Date(); 
		return (str);
	}
	this.getCurrentTime				= function(){
		var nowDate = new Date();
		var h=nowDate.getHours();
		var m=nowDate.getMinutes();
		var s=nowDate.getSeconds();
		var ms=nowDate.getMilliseconds();
		var div = ":"; 
		return (h + div + m + div + s + div + ms);
	}
}

var obConsole = new outbrainConsole();



function outbrain_$(id){
	return document.getElementById(id);
}

function outbrain_changeLang( langInfo ){
	var name = langInfo[0];
	var path = langInfo[1];
	var translator = unescape(langInfo[2]);
	
	if (translator != ''){
		translator = "translator/s: " + translator;
		outbrain_$(translator_div).innerHTML = translator;
		outbrain_$(translator_div).style.display="inline";
	}else{
		outbrain_$(translator_div).style.display="none";
	}
}

function outbrain_admin_onload(current){
	obConsole.write("Admin language loaded with language :" + current,'Info') ;
	//outbrain_admin_recommendationsOptionChanged();
	
	var langInfo = null;
	var con = '';
	var selectedIdx = -1;
	var defaultIdx = 0;
	
	var langSelect =  outbrain_$('langs_list');
	for (i=0;i<language_list.length;i++){
		var option = document.createElement('option');
		option.value = language_list[i][1];
		option.text = language_list[i][0];
		try{
			langSelect.add( option , null );// standards compliant
		}catch(ex){
			langSelect.add( option ); // IE only
		}
		
		if (current == language_list[i][1]){
			selectedIdx = i;
		}
		
		if( language_list[i][3] == true ){
			defaultIdx = i;
		}
	}			
	
	if( selectedIdx == -1 ){
		selectedIdx = defaultIdx;
	}
	
	langSelect.options[selectedIdx].selected = true;
	langInfo = language_list[selectedIdx];
	
	outbrain_changeLang( langInfo );
}

function outbrainReset(){
  $('#reset').val("true");
  $('#outbrain_form').submit();

}

function outbrainKeySave(){
  $('#keySave').val("true");
  $('#outbrain_form').submit();

}

function outbrain_admin_recommendationsOptionChanged(){
	var recommendationsInput		=	document.outbrain_form.outbrain_rater_show_recommendations;
	var selfRecommendationsInput	=	document.outbrain_form.outbrain_rater_self_recommendations;
	if (recommendationsInput.checked){
		selfRecommendationsInput.disabled	=	false;
		outbrain_$("selfRecommendationsOnlyOption").style.color	=	"#000000";
	} else {
		selfRecommendationsInput.checked	=	false;
		selfRecommendationsInput.disabled	=	true;
		outbrain_$("selfRecommendationsOnlyOption").style.color	=	"#CCCCCC";
	}
}

function callExportPage(){
	var formElement = document.getElementById("outbrain_form");
	document.getElementById("export").value = "true";
	formElement.submit();
}

//-------------------------------------------------------------------------------------------------------------------------------------
//	containers function
//-------------------------------------------------------------------------------------------------------------------------------------



//take the fater and validate it
function toggleStateValidate(element,forceState){
	if (!element) 
		return 
	//get the Li
	var parentElement = element.parentNode;
	if (!parentElement) 
		return; 
	
	if (parentElement.id <= 0) 
		return;//validations
	
	toggleState(parentElement);
}
//change the current state of the container
//forceState - make it change to a spesific state - open - to open container //close to close
function toggleState(element,forceState)
{

	var container = document.getElementById(element.id+"_inner");//get inner div
	if (container == null || container == 'undefined' )
		return;


	if (forceState == 'open'){
		container.style.display = "";
		element.className		= down_arrow_class;
	}
	else if (forceState == 'close'){
		container.style.display = "none"; 
		element.className		= right_arrow_class;
	}
	else if (element.className == right_arrow_class){
		container.style.display = "";
		element.className		= down_arrow_class;		
	}
	else{
		container.style.display = "none"; 
		element.className		= right_arrow_class;		
	}
}

//-------------------------------------------------------------------------------------------------------------------------------------
//	claim function
//-------------------------------------------------------------------------------------------------------------------------------------
function isClaimKeyChanged(newKey){
	return (currKeyCode != newKey);
}

function thereIsNewKey(newKey){
	return (newKey != '');
}

function saveClaimStatusResponseIntoDB(status,statusString){
	obConsole.write("function saveClaimStatusResponseIntoDB - Been called with status:"+ status +" and statusString" + statusString,"Info") ;
	try{
		// do ajax to insert code
		jQuery.ajax({
			type: "POST",
			url: ajaxUrl,
			data: 'saveClaimStatus=true&status=' + status + '&statusString=' + statusString,
			success: function(){obConsole.write("function saveClaimStatusResponseIntoDB - Ajax successful","Info")},
			error: function(){obConsole.write("function saveClaimStatusResponseIntoDB - Ajax failed","Error")}
		 });
	} catch(ex){
		obConsole.write("function saveClaimStatusResponseIntoDB - Ajax catch","Error")
	}
	return true;
}

function returnedClaimData(status,statusString){
	obConsole.write("function returnedClaimData - Call returned (Pageload is "+ isPageLoadMode +")with status:"+ status + ", and StatusString:"+statusString,"Info");
	var element	=	outbrain_$('after_claiming');
		element.innerHTML	=	statusString;//fill the div let other decide about visibilty
	if (isPageLoadMode && (status == 10 || status == 12) ){
		//clearTimeout(mainTimeOut);//end of proccess
		//obConsole.write("mainTimeOut cleared","Info");		
		outbrain_claimMode();  // this blog is claimed show the appropriate  
	}else if (isPageLoadMode){
		clearTimeout(mainTimeOut);//end of proccess
		obConsole.write("mainTimeOut cleared","Info");
		outbrain_noClaimMode();
	}else{//button pressed
		obConsole.write("function returnedClaimData - returned from ajax call with status :"+status,"Info")
		//	after-claiming text (write the response and display it)
		element.style.display = "block";
		clearTimeout(mainTimeOut);//end of proccess
		obConsole.write("mainTimeOut cleared","Info");
		toggleLoadingDisplay(false);//dont show loading
		
		//	save response - not so important 
		saveClaimStatusResponseIntoDB(status,statusString);
		
	}
	isPageLoadMode = false;//return to claiming mode
}
function jsLoaded(){
	obConsole.write("function JSloaded - call for js Loaded","Info");
}


function doClaim(key){
	obConsole.write("function doClaim - Claim with Key :"+key,"Info");
	var keyScriptElementId	=	"outbrainClaimBlog";
	var rnd					=	Math.random();
	var claimPath			=	'http://odb.outbrain.com/blogutils/Claim.action?key=' + encodeURIComponent(key) +'&type=meta&cbk=returnedClaimData&random=' + rnd;
	var element				=	outbrain_$(keyScriptElementId);
	try{	
		if (element){
			element.parentNode.removeChild(element);
		}
		
		var newSE = document.createElement("script");
		newSE.setAttribute('type','text/javascript');
		newSE.setAttribute('id', keyScriptElementId);
		newSE.setAttribute('src', claimPath);
		newSE.setAttribute('onload', jsLoaded);
		var heads = document.getElementsByTagName("head");
		if (heads.length > 0){
			heads[0].insertBefore(newSE, heads[0].firstChild);
			obConsole.write("function doClaim - claim inserted to head element","Info");
		}
	}
	catch(ex){
		obConsole.write("function doClaim - catch error of claim insertion :"+ex,"Error")
	}
	
}	

function keySaved(key){
	obConsole.write("function keySaved - with key "+ key ,"Info")
	//setTimeout(function(){doClaim(key);},1500);
	//currKeyCode		=	key;
	//claimChanged(currKeyCode);
}
function failedMsg(){
	//document.getElementById("block_logger").style.display = "block";
	//errorLogger = document.getElementById("outbrainLogger");
	//errorLogger.value = obConsole.toString();
}

function toggleLoadingDisplay(showMode){
	//	hide loading image
		var loadingImage	=	outbrain_$("claimLoadingImage");
		if ( showMode == false ){
			loadingImage.style.display = "none";
		}else{
			loadingImage.style.display = "inline";
		}
}

function processFailed(){
	obConsole.write("System time out after "+ (timeOutDelay/1000) + " seconds" ,"Error")
	clearTimeout(mainTimeOut);
	
	returnedClaimData(0,"Claiming proccess failled, please contact <a href=\'mailto:support@outbrain.com\' >Outbrain Support</a>");
	obConsole.write("mainTimeOut cleared" ,"Info")
	failedMsg();
	toggleLoadingDisplay(false);
}

function claimClicked(url,key){
	isPageLoadMode = false;
	//mainTimeOut = setTimeout('processFailed()',timeOutDelay)//one minute
	obConsole.write("mainTimeOut fired" ,"Info")
	obConsole.write("function claimClicked - Called with url:"+ url +" and key:"+key,"Info")
		
	if (key==''){
		alert('No key');
		return false;
	}
	
	if (ajaxUrl == null){
		ajaxUrl	=	url;
	}
	
	toggleLoadingDisplay(true);// show loading image
	try{
		// do ajax to insert code
		jQuery.ajax({
			type: "POST",
			url: ajaxUrl,
			data: 'claim=true&key=' + encodeURIComponent(key),
			success: function(){obConsole.write("function claimClicked - Ajax success" ,"Info");keySaved(key);},
			error: function(){obConsole.write("function claimClicked - Ajax failed","Error");}
		 });
	}
	catch(ex){
		obConsole.write("function claimClicked - Ajax catch" ,"Info")
	}
	return true;
}

function claimChanged(newKey){
	if (currKeyCode != newKey){
		keyCodeChanged = true;
	} else {
		keyCodeChanged = true;
	}
	return true;
}

function outbrain_options_submit(newKey){	
  if ((isClaimKeyChanged(newKey)) && (thereIsNewKey(newKey)) && (keyCodeChanged)){
		return confirm('your claiming code will not be verify and save until you will click the claim button. continue anyway?');
	} else {
		return true;
	}
}
function pageLoadFailed(){
	clearTimeout(mainTimeOut);
	obConsole.write("mainTimeOut cleared" ,"Info")
	obConsole.write("pageLoad connection Failed, time out after "+ (timeOutDelay/1000) + " seconds" ,"Error")
	var element	=	outbrain_$('after_claiming');
		element.style.display = "block";
	returnedClaimData(0,"Connection to Outbrain Server failed, please contact <a href=\'mailto:support@outbrain.com\' >Outbrain Support</a>");
	obConsole.write("Connection to Outbrain Server failed","Error")	
	failedMsg();
	
}

function outbrain_isUserClaim(key){
	obConsole.write("function isUserClaim - page load with key :"+key,"Info")
	//mainTimeOut = setTimeout("pageLoadFailed()",timeOutDelay)
	obConsole.write("mainTimeOut fired" ,"Info")
	isPageLoadMode = true;
	doClaim(key)
}

function outbrain_elementsShowHide(arr,isShow) {
	for (t = 0;t < arr.length; t+=2){		
		var currentElement = document.getElementById(arr[t]);
		if (currentElement != null || currentElement != 'undefined' ){
			try{
				whatToDo = (isShow) ?  "" : "none";
				currentElement.style.display  	= whatToDo;
				currentContainerState 			= (arr[t+1])? "open" : "close" ;
				
				if (isShow) toggleState(currentElement,currentContainerState);
			}
			catch(ex){
				//alert('show Elements '+ex+':'+ currentElement +'('+ arr[t] +')')
			}
		}
	}
}

function outbrain_claimMode(){
	outbrain_elementsShowHide(claimModeElements_hide,false);//arr of elments and isShow
	outbrain_elementsShowHide(claimModeElements,true);
}

function outbrain_noClaimMode(){
	outbrain_elementsShowHide(noClaimModeElements_hide,false);
	outbrain_elementsShowHide(noClaimModeElements,true);
}