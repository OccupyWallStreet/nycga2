jQuery(document).ready( function($){

	/* Calendar AJAX */
	$('.em-calendar-wrapper a').unbind("click");
	$('.em-calendar-wrapper a').die("click");
	$('a.em-calnav, a.em-calnav').live('click', function(e){
		e.preventDefault();
		$(this).closest('.em-calendar-wrapper').prepend('<div class="loading" id="em-loading"></div>');
		var url = em_ajaxify($(this).attr('href'));
		$(this).closest('.em-calendar-wrapper').load(url, function(){$(this).trigger('em_calendar_load');});
	} ); 
	
	/*
	 * ADMIN AREA AND PUBLIC FORMS (Still polishing this section up, note that form ids and classes may change accordingly)
	 */
	//Events List
		//Approve/Reject Links
		$('.em-event-delete').live('click', function(){
			if( !confirm("Are you sure you want to delete?") ){ return false; }
			var url = em_ajaxify( el.attr('href'));		
			var td = el.parents('td').first();
			td.html("Loading...");
			td.load( url );
			return false;
		});
	//Tickets
		//Tickets overlay
		if( $("#em-tickets-add").length > 0 ){
			var triggers = $("#em-tickets-add").overlay({
				mask: { 
					color: '#ebecff',
					loadSpeed: 200,
					opacity: 0.9
				},
				closeOnClick: true,
				onLoad: function(){
					$('#ui-datepicker-div').appendTo('#em-tickets-form').hide();
				},
				onClose: function(){
					$('#ui-datepicker-div').appendTo('body').hide();
				}
			});
		}
		//Submitting ticket (Add/Edit)
		$('#em-tickets-form form').submit(function(e){
			e.preventDefault();
			$('#em-tickets-intro').remove();
			//first we get the template to insert this to
			if( $('#em-tickets-form form input[name=prev_slot]').val() ){
				//grab slot and populate
				var slot = $('#'+$('#em-tickets-form form input[name=prev_slot]').val());
				var rowNo = slot.attr('id').replace('em-tickets-row-','');
				var edit = true;
			}else{
				//create copy of template slot, insert so ready for population
				var rowNo = $('#em-tickets-body').children('tr').length+1;
				var slot = $('#em-tickets-body tr').first().clone().attr('id','em-tickets-row-'+ rowNo).appendTo($('#em-tickets-body'));
				var edit = false;
				slot.show();
			}
			var postData = {};
			$.each($('#em-tickets-form form *[name]'), function(index,el){
				el = $(el);
				slot.find('input.'+el.attr('name')).attr({
					'value' : el.attr('value'),
					'name' : 'em_tickets['+rowNo+']['+el.attr('name')+']'
				});
				slot.find('span.'+el.attr('name')).text(el.attr('value'));
			});
			//sort out dates and localization masking
			var start_pub = $("#em-tickets-form input[name=ticket_start_pub]").val();
			var end_pub = $("#em-tickets-form input[name=ticket_end_pub]").val();
			$('#em-tickets-form *[name]').attr('value','');
			$('#em-tickets-form .close').trigger('click');
			return false;
		});
		//Edit a Ticket
		$('.ticket-actions-edit').live('click',function(e){
			//first, populate form, then, trigger click
			e.preventDefault();
			$('#em-tickets-add').trigger('click');
			var rowId = $(this).parents('tr').first().attr('id');
			$('#em-tickets-form *[name]').attr('value','');
			$.each( $('#'+rowId+' *[name]'), function(index,el){
				var el = $(el);
				var selector = el.attr('class');
				$('#em-tickets-form *[name='+selector+']').attr('value',el.attr('value'));
			});
			$("#em-tickets-form input[name=prev_slot]").attr('value',rowId);
			$("#em-tickets-form .start-loc").datepicker('refresh');
			$("#em-tickets-form .end-loc").datepicker('refresh');
	
			date_dateFormat =$("#em-tickets-form .start-loc").datepicker('option', 'dateFormat');
			if( $('#em-tickets-form .start').val() != '' || $('#em-tickets-form .end').val() != '' ){			
				start_date_formatted = $.datepicker.formatDate( date_dateFormat, $.datepicker.parseDate('yy-mm-dd', $('#em-tickets-form .start').val()) );
				end_date_formatted = $.datepicker.formatDate( date_dateFormat, $.datepicker.parseDate('yy-mm-dd', $('#em-tickets-form .end').val()) );
				$("#em-tickets-form .start-loc").val(start_date_formatted);
				$("#em-tickets-form .end-loc").val(end_date_formatted);
			}
			return false;
		});	
		//Delete a ticket
		$('.ticket-actions-delete').live('click',function(e){
			e.preventDefault();
			var el = $(this);
			var rowId = $(this).parents('tr').first().attr('id');
			if( $('#'+rowId+' input.ticket_id').attr('value') == '' ){
				//not saved to db yet, so just remove
				$('#'+rowId).remove();
			}else{
				//only will happen if no bookings made
				el.text('Deleting...');	
				$.getJSON( $(this).attr('href'), {'em_ajax_action':'delete_ticket', 'id':$('#'+rowId+' input.ticket_id').attr('value')}, function(data){
					if(data.result){
						$('#'+rowId).remove();
					}else{
						el.text('Delete');
						alert(data.error);
					}
				});
			}
			return false;
		});
	//Manageing Bookings
		//Widgets and filter submissions
		$('.em_bookings_events_table form, .em_bookings_pending_table form').live('submit', function(e){
			var el = $(this);
			var url = em_ajaxify( el.attr('action') );			
			el.parents('.wrap').find('.table-wrap').first().append('<div id="em-loading" />');
			$.get( url, el.serializeArray(), function(data){
				el.parents('.wrap').first().replaceWith(data);
			});
			return false;
		});
		//Pagination link clicks
		$('.em_bookings_events_table .tablenav-pages a, .em_bookings_pending_table .tablenav-pages a').live('click', function(){		
			var el = $(this);
			var url = em_ajaxify( el.attr('href') );	
			el.parents('.wrap').find('.table-wrap').first().append('<div id="em-loading" />');
			$.get( url, function(data){
				el.parents('.wrap').first().replaceWith(data);
			});
			return false;
		});
		//Approve/Reject Links
		$('.em-bookings-approve,.em-bookings-reject,.em-bookings-unapprove,.em-bookings-delete').live('click', function(){
			var el = $(this); 
			if( el.hasClass('em-bookings-delete') ){
				if( !confirm("Are you sure you want to delete?") ){ return false; }
			}
			var url = em_ajaxify( el.attr('href'));		
			var td = el.parents('td').first();
			td.html("Loading...");
			td.load( url );
			return false;
		});
		
	//Datepicker
	if( $('#em-date-start').length > 0 ){
		if( EM.locale != 'en' ){
			$.datepicker.regional['nl']={closeText:'Sluiten',prevText:'←',nextText:'→',currentText:'Vandaag',monthNames:['januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'],monthNamesShort:['jan','feb','maa','apr','mei','jun','jul','aug','sep','okt','nov','dec'],dayNames:['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'],dayNamesShort:['zon','maa','din','woe','don','vri','zat'],dayNamesMin:['zo','ma','di','wo','do','vr','za'],weekHeader:'Wk',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['af']={closeText:'Selekteer',prevText:'Vorige',nextText:'Volgende',currentText:'Vandag',monthNames:['Januarie','Februarie','Maart','April','Mei','Junie','Julie','Augustus','September','Oktober','November','Desember'],monthNamesShort:['Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des'],dayNames:['Sondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrydag','Saterdag'],dayNamesShort:['Son','Maa','Din','Woe','Don','Vry','Sat'],dayNamesMin:['So','Ma','Di','Wo','Do','Vr','Sa'],weekHeader:'Wk',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['ar']={closeText:'إغلاق',prevText:'<السابق',nextText:'التالي>',currentText:'اليوم',monthNames:['كانون الثاني','شباط','آذار','نيسان','آذار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول'],monthNamesShort:['1','2','3','4','5','6','7','8','9','10','11','12'],dayNames:['السبت','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة'],dayNamesShort:['سبت','أحد','اثنين','ثلاثاء','أربعاء','خميس','جمعة'],dayNamesMin:['سبت','أحد','اثنين','ثلاثاء','أربعاء','خميس','جمعة'],weekHeader:'أسبوع',dateFormat:'dd/mm/yy',firstDay:0,isRTL:true,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['az']={closeText:'Bağla',prevText:'<Geri',nextText:'İrəli>',currentText:'Bugün',monthNames:['Yanvar','Fevral','Mart','Aprel','May','İyun','İyul','Avqust','Sentyabr','Oktyabr','Noyabr','Dekabr'],monthNamesShort:['Yan','Fev','Mar','Apr','May','İyun','İyul','Avq','Sen','Okt','Noy','Dek'],dayNames:['Bazar','Bazar ertəsi','Çərşənbə axşamı','Çərşənbə','Cümə axşamı','Cümə','Şənbə'],dayNamesShort:['B','Be','Ça','Ç','Ca','C','Ş'],dayNamesMin:['B','B','Ç','С','Ç','C','Ş'],weekHeader:'Hf',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['bg']={closeText:'затвори',prevText:'<назад',nextText:'напред>',nextBigText:'>>',currentText:'днес',monthNames:['Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември'],monthNamesShort:['Яну','Фев','Мар','Апр','Май','Юни','Юли','Авг','Сеп','Окт','Нов','Дек'],dayNames:['Неделя','Понеделник','Вторник','Сряда','Четвъртък','Петък','Събота'],dayNamesShort:['Нед','Пон','Вто','Сря','Чет','Пет','Съб'],dayNamesMin:['Не','По','Вт','Ср','Че','Пе','Съ'],weekHeader:'Wk',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['bs']={closeText:'Zatvori',prevText:'<',nextText:'>',currentText:'Danas',monthNames:['Januar','Februar','Mart','April','Maj','Juni','Juli','August','Septembar','Oktobar','Novembar','Decembar'],monthNamesShort:['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],dayNames:['Nedelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota'],dayNamesShort:['Ned','Pon','Uto','Sri','Čet','Pet','Sub'],dayNamesMin:['Ne','Po','Ut','Sr','Če','Pe','Su'],weekHeader:'Wk',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['cs']={closeText:'Zavřít',prevText:'<Dříve',nextText:'Později>',currentText:'Nyní',monthNames:['leden','únor','březen','duben','květen','červen','červenec','srpen','září','říjen','listopad','prosinec'],monthNamesShort:['led','úno','bře','dub','kvě','čer','čvc','srp','zář','říj','lis','pro'],dayNames:['neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota'],dayNamesShort:['ne','po','út','st','čt','pá','so'],dayNamesMin:['ne','po','út','st','čt','pá','so'],weekHeader:'Týd',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['da']={closeText:'Luk',prevText:'<Forrige',nextText:'Næste>',currentText:'Idag',monthNames:['Januar','Februar','Marts','April','Maj','Juni','Juli','August','September','Oktober','November','December'],monthNamesShort:['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],dayNames:['Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag'],dayNamesShort:['Søn','Man','Tir','Ons','Tor','Fre','Lør'],dayNamesMin:['Sø','Ma','Ti','On','To','Fr','Lø'],weekHeader:'Uge',dateFormat:'dd-mm-yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['de']={closeText:'schließen',prevText:'<zurück',nextText:'Vor>',currentText:'heute',monthNames:['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],monthNamesShort:['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'],dayNames:['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],dayNamesShort:['So','Mo','Di','Mi','Do','Fr','Sa'],dayNamesMin:['So','Mo','Di','Mi','Do','Fr','Sa'],weekHeader:'Wo',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['el']={closeText:'Κλείσιμο',prevText:'Προηγούμενος',nextText:'Επόμενος',currentText:'Τρέχων Μήνας',monthNames:['Ιανουάριος','Φεβρουάριος','Μάρτιος','Απρίλιος','Μάιος','Ιούνιος','Ιούλιος','Αύγουστος','Σεπτέμβριος','Οκτώβριος','Νοέμβριος','Δεκέμβριος'],monthNamesShort:['Ιαν','Φεβ','Μαρ','Απρ','Μαι','Ιουν','Ιουλ','Αυγ','Σεπ','Οκτ','Νοε','Δεκ'],dayNames:['Κυριακή','Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο'],dayNamesShort:['Κυρ','Δευ','Τρι','Τετ','Πεμ','Παρ','Σαβ'],dayNamesMin:['Κυ','Δε','Τρ','Τε','Πε','Πα','Σα'],weekHeader:'Εβδ',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['en-GB']={closeText:'Done',prevText:'Prev',nextText:'Next',currentText:'Today',monthNames:['January','February','March','April','May','June','July','August','September','October','November','December'],monthNamesShort:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],dayNames:['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],dayNamesShort:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],dayNamesMin:['Su','Mo','Tu','We','Th','Fr','Sa'],weekHeader:'Wk',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['eo']={closeText:'Fermi',prevText:'<Anta',nextText:'Sekv>',currentText:'Nuna',monthNames:['Januaro','Februaro','Marto','Aprilo','Majo','Junio','Julio','Aŭgusto','Septembro','Oktobro','Novembro','Decembro'],monthNamesShort:['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aŭg','Sep','Okt','Nov','Dec'],dayNames:['Dimanĉo','Lundo','Mardo','Merkredo','Ĵaŭdo','Vendredo','Sabato'],dayNamesShort:['Dim','Lun','Mar','Mer','Ĵaŭ','Ven','Sab'],dayNamesMin:['Di','Lu','Ma','Me','Ĵa','Ve','Sa'],weekHeader:'Sb',dateFormat:'dd/mm/yy',firstDay:0,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['et']={closeText:'Sulge',prevText:'Eelnev',nextText:'Järgnev',currentText:'Täna',monthNames:['Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktoober','November','Detsember'],monthNamesShort:['Jaan','Veebr','Märts','Apr','Mai','Juuni','Juuli','Aug','Sept','Okt','Nov','Dets'],dayNames:['Pühapäev','Esmaspäev','Teisipäev','Kolmapäev','Neljapäev','Reede','Laupäev'],dayNamesShort:['Pühap','Esmasp','Teisip','Kolmap','Neljap','Reede','Laup'],dayNamesMin:['P','E','T','K','N','R','L'],weekHeader:'Sm',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['eu']={closeText:'Egina',prevText:'<Aur',nextText:'Hur>',currentText:'Gaur',monthNames:['Urtarrila','Otsaila','Martxoa','Apirila','Maiatza','Ekaina','Uztaila','Abuztua','Iraila','Urria','Azaroa','Abendua'],monthNamesShort:['Urt','Ots','Mar','Api','Mai','Eka','Uzt','Abu','Ira','Urr','Aza','Abe'],dayNames:['Igandea','Astelehena','Asteartea','Asteazkena','Osteguna','Ostirala','Larunbata'],dayNamesShort:['Iga','Ast','Ast','Ast','Ost','Ost','Lar'],dayNamesMin:['Ig','As','As','As','Os','Os','La'],weekHeader:'Wk',dateFormat:'yy/mm/dd',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['fa']={closeText:'بستن',prevText:'<قبلي',nextText:'بعدي>',currentText:'امروز',monthNames:['فروردين','ارديبهشت','خرداد','تير','مرداد','شهريور','مهر','آبان','آذر','دي','بهمن','اسفند'],monthNamesShort:['1','2','3','4','5','6','7','8','9','10','11','12'],dayNames:['يکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'],dayNamesShort:['ي','د','س','چ','پ','ج','ش'],dayNamesMin:['ي','د','س','چ','پ','ج','ش'],weekHeader:'هف',dateFormat:'yy/mm/dd',firstDay:6,isRTL:true,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['fo']={closeText:'Lat aftur',prevText:'<Fyrra',nextText:'Næsta>',currentText:'Í dag',monthNames:['Januar','Februar','Mars','Apríl','Mei','Juni','Juli','August','September','Oktober','November','Desember'],monthNamesShort:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des'],dayNames:['Sunnudagur','Mánadagur','Týsdagur','Mikudagur','Hósdagur','Fríggjadagur','Leyardagur'],dayNamesShort:['Sun','Mán','Týs','Mik','Hós','Frí','Ley'],dayNamesMin:['Su','Má','Tý','Mi','Hó','Fr','Le'],weekHeader:'Vk',dateFormat:'dd-mm-yy',firstDay:0,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['fr-CH']={closeText:'Fermer',prevText:'<Préc',nextText:'Suiv>',currentText:'Courant',monthNames:['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],monthNamesShort:['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],dayNames:['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],dayNamesShort:['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],dayNamesMin:['Di','Lu','Ma','Me','Je','Ve','Sa'],weekHeader:'Sm',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['fr']={closeText:'Fermer',prevText:'<Préc',nextText:'Suiv>',currentText:'Courant',monthNames:['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],monthNamesShort:['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],dayNames:['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],dayNamesShort:['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],dayNamesMin:['Di','Lu','Ma','Me','Je','Ve','Sa'],weekHeader:'Sm',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['he']={closeText:'סגור',prevText:'<הקודם',nextText:'הבא>',currentText:'היום',monthNames:['ינואר','פברואר','מרץ','אפריל','מאי','יוני','יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],monthNamesShort:['1','2','3','4','5','6','7','8','9','10','11','12'],dayNames:['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],dayNamesShort:['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],dayNamesMin:['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],weekHeader:'Wk',dateFormat:'dd/mm/yy',firstDay:0,isRTL:true,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['hu']={closeText:'Kész',prevText:'Előző',nextText:'Következő',currentText:'Ma',monthNames:['január','február','március','április','május','június','július','augusztus','szeptember','október','november','cecember'],monthNamesShort:['jan','febr','márc','ápr','máj','jún','júl','aug','szept','okt','nov','dec'],dayNames:['vasárnap','hétfő','kedd','szerda','csütörtök','péntek','szombat'],dayNamesShort:['va','hé','k','sze','csü','pé','szo'],dayNamesMin:['v','h','k','sze','cs','p','szo'],weekHeader:'Wk',dateFormat:'yy.mm.dd.',firstDay:1,isRTL:false,showMonthAfterYear:true,yearSuffix:''};
			$.datepicker.regional['hr']={closeText:'Zatvori',prevText:'<',nextText:'>',currentText:'Danas',monthNames:['Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac'],monthNamesShort:['Sij','Velj','Ožu','Tra','Svi','Lip','Srp','Kol','Ruj','Lis','Stu','Pro'],dayNames:['Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota'],dayNamesShort:['Ned','Pon','Uto','Sri','Čet','Pet','Sub'],dayNamesMin:['Ne','Po','Ut','Sr','Če','Pe','Su'],weekHeader:'Tje',dateFormat:'dd.mm.yy.',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['ja']={closeText:'閉じる',prevText:'<前',nextText:'次>',currentText:'今日',monthNames:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],dayNames:['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],dayNamesShort:['日','月','火','水','木','金','土'],dayNamesMin:['日','月','火','水','木','金','土'],weekHeader:'週',dateFormat:'yy/mm/dd',firstDay:0,isRTL:false,showMonthAfterYear:true,yearSuffix:'年'};
			$.datepicker.regional['ro']={closeText:'Închide',prevText:'« Luna precedentă',nextText:'Luna următoare »',currentText:'Azi',monthNames:['Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'],monthNamesShort:['Ian','Feb','Mar','Apr','Mai','Iun','Iul','Aug','Sep','Oct','Nov','Dec'],dayNames:['Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă'],dayNamesShort:['Dum','Lun','Mar','Mie','Joi','Vin','Sâm'],dayNamesMin:['Du','Lu','Ma','Mi','Jo','Vi','Sâ'],weekHeader:'Săpt',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['sq']={closeText:'mbylle',prevText:'<mbrapa',nextText:'Përpara>',currentText:'sot',monthNames:['Janar','Shkurt','Mars','Prill','Maj','Qershor','Korrik','Gusht','Shtator','Tetor','Nëntor','Dhjetor'],monthNamesShort:['Jan','Shk','Mar','Pri','Maj','Qer','Kor','Gus','Sht','Tet','Nën','Dhj'],dayNames:['E Diel','E Hënë','E Martë','E Mërkurë','E Enjte','E Premte','E Shtune'],dayNamesShort:['Di','Hë','Ma','Më','En','Pr','Sh'],dayNamesMin:['Di','Hë','Ma','Më','En','Pr','Sh'],weekHeader:'Ja',dateFormat:'dd.mm.yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['sr-SR']={closeText:'Zatvori',prevText:'<',nextText:'>',currentText:'Danas',monthNames:['Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar'],monthNamesShort:['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Avg','Sep','Okt','Nov','Dec'],dayNames:['Nedelja','Ponedeljak','Utorak','Sreda','Četvrtak','Petak','Subota'],dayNamesShort:['Ned','Pon','Uto','Sre','Čet','Pet','Sub'],dayNamesMin:['Ne','Po','Ut','Sr','Če','Pe','Su'],weekHeader:'Sed',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['sr']={closeText:'Затвори',prevText:'<',nextText:'>',currentText:'Данас',monthNames:['Јануар','Фебруар','Март','Април','Мај','Јун','Јул','Август','Септембар','Октобар','Новембар','Децембар'],monthNamesShort:['Јан','Феб','Мар','Апр','Мај','Јун','Јул','Авг','Сеп','Окт','Нов','Дец'],dayNames:['Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота'],dayNamesShort:['Нед','Пон','Уто','Сре','Чет','Пет','Суб'],dayNamesMin:['Не','По','Ут','Ср','Че','Пе','Су'],weekHeader:'Сед',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['sv']={closeText:'Stäng',prevText:'«Förra',nextText:'Nästa»',currentText:'Idag',monthNames:['Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'],monthNamesShort:['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],dayNamesShort:['Sön','Mån','Tis','Ons','Tor','Fre','Lör'],dayNames:['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'],dayNamesMin:['Sö','Må','Ti','On','To','Fr','Lö'],weekHeader:'Ve',dateFormat:'yy-mm-dd',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['ta']={closeText:'மூடு',prevText:'முன்னையது',nextText:'அடுத்தது',currentText:'இன்று',monthNames:['தை','மாசி','பங்குனி','சித்திரை','வைகாசி','ஆனி','ஆடி','ஆவணி','புரட்டாசி','ஐப்பசி','கார்த்திகை','மார்கழி'],monthNamesShort:['தை','மாசி','பங்','சித்','வைகா','ஆனி','ஆடி','ஆவ','புர','ஐப்','கார்','மார்'],dayNames:['ஞாயிற்றுக்கிழமை','திங்கட்கிழமை','செவ்வாய்க்கிழமை','புதன்கிழமை','வியாழக்கிழமை','வெள்ளிக்கிழமை','சனிக்கிழமை'],dayNamesShort:['ஞாயிறு','திங்கள்','செவ்வாய்','புதன்','வியாழன்','வெள்ளி','சனி'],dayNamesMin:['ஞா','தி','செ','பு','வி','வெ','ச'],weekHeader:'Не',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['th']={closeText:'ปิด',prevText:'« ย้อน',nextText:'ถัดไป »',currentText:'วันนี้',monthNames:['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฏาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],monthNamesShort:['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],dayNames:['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'],dayNamesShort:['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],dayNamesMin:['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],weekHeader:'Wk',dateFormat:'dd/mm/yy',firstDay:0,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['vi']={closeText:'Đóng',prevText:'<Trước',nextText:'Tiếp>',currentText:'Hôm nay',monthNames:['Tháng Một','Tháng Hai','Tháng Ba','Tháng Tư','Tháng Năm','Tháng Sáu','Tháng Bảy','Tháng Tám','Tháng Chín','Tháng Mười','Tháng Mười Một','Tháng Mười Hai'],monthNamesShort:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],dayNames:['Chủ Nhật','Thứ Hai','Thứ Ba','Thứ Tư','Thứ Năm','Thứ Sáu','Thứ Bảy'],dayNamesShort:['CN','T2','T3','T4','T5','T6','T7'],dayNamesMin:['CN','T2','T3','T4','T5','T6','T7'],weekHeader:'Tu',dateFormat:'dd/mm/yy',firstDay:0,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.regional['zh-TW']={closeText:'關閉',prevText:'<上月',nextText:'下月>',currentText:'今天',monthNames:['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],monthNamesShort:['一','二','三','四','五','六','七','八','九','十','十一','十二'],dayNames:['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],dayNamesShort:['周日','周一','周二','周三','周四','周五','周六'],dayNamesMin:['日','一','二','三','四','五','六'],weekHeader:'周',dateFormat:'yy/mm/dd',firstDay:1,isRTL:false,showMonthAfterYear:true,yearSuffix:'年'};
			$.datepicker.regional['es']={closeText:'Cerrar',prevText:'<Ant',nextText:'Sig>',currentText:'Hoy',monthNames:['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],monthNamesShort:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],dayNames:['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],dayNamesShort:['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],dayNamesMin:['Do','Lu','Ma','Mi','Ju','Vi','Sá'],weekHeader:'Sm',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:''};
			$.datepicker.setDefaults($.datepicker.regional[EM.locale]);
		}
		var datepicker_vals = { 
			altFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			firstDay : EM.firstDay
		};	
		
		datepicker_vals.altField = "#em-date-start";
		$("#em-date-start-loc").datepicker(datepicker_vals);
		
		datepicker_vals.altField = "#em-date-end";
		$("#em-date-end-loc").datepicker(datepicker_vals);
		
		//localize start/end dates
		if( $('#em-date-start').val() != '' ){
			if( EM.locale != 'en' && $.datepicker.regional[EM.locale] != null ){
				var date_dateFormat = $.datepicker.regional[EM.locale].dateFormat;
			}else{
				var date_dateFormat = $("#em-date-start-loc").datepicker('option', 'dateFormat');
			}
			var start_date_formatted = $.datepicker.formatDate( date_dateFormat, $.datepicker.parseDate('yy-mm-dd', $('#em-date-start').val()) );
			var end_date_formatted = $.datepicker.formatDate( date_dateFormat, $.datepicker.parseDate('yy-mm-dd', $('#em-date-end').val()) );
			$("#em-date-start-loc").val(start_date_formatted);
			$("#em-date-end-loc").val(end_date_formatted);
		}
		$('ui-datepicker-div').css();
		
		//for the tickets form too
		$(".em-ticket-form, #em-tickets-form").each(function(i, el){
			el = $(el);
			start = el.find('.start-loc');
			if(start.length > 0){
				datepicker_vals.altField = el.find('.start').first();
				start.first().datepicker(datepicker_vals);
			}
			end = el.find('.end-loc');
			if(end.length > 0){
				datepicker_vals.altField = el.find('.end').first();
				end.first().datepicker(datepicker_vals);
			}
		});
	}
	
	//previously in em-admin.php
	function updateIntervalDescriptor () { 
		$(".interval-desc").hide();
		var number = "-plural";
		if ($('input#recurrence-interval').val() == 1 || $('input#recurrence-interval').val() == "")
		number = "-singular";
		var descriptor = "span#interval-"+$("select#recurrence-frequency").val()+number;
		$(descriptor).show();
	}
	function updateIntervalSelectors () {
		$('p.alternate-selector').hide();   
		$('p#'+ $('select#recurrence-frequency').val() + "-selector").show();
	}
	function updateShowHideRecurrence () {
		if( $('input#event-recurrence').attr("checked")) {
			$("#event_recurrence_pattern").fadeIn();
			$("#event-date-explanation").hide();
			$("#recurrence-dates-explanation").show();
			$("h3#recurrence-dates-title").show();
			$("h3#event-date-title").hide();     
		} else {
			$("#event_recurrence_pattern").hide();
			$("#recurrence-dates-explanation").hide();
			$("#event-date-explanation").show();
			$("h3#recurrence-dates-title").hide();
			$("h3#event-date-title").show();   
		}
	}		 
	$("#recurrence-dates-explanation").hide();
	$("#date-to-submit").hide();
	$("#end-date-to-submit").hide();
	
	$("#localised-date").show();
	$("#localised-end-date").show();
	
	$('input.select-all').change(function(){
	 	if($(this).is(':checked'))
	 	$('input.row-selector').attr('checked', true);
	 	else
	 	$('input.row-selector').attr('checked', false);
	}); 
	
	updateIntervalDescriptor(); 
	updateIntervalSelectors();
	updateShowHideRecurrence();
	$('input#event-recurrence').change(updateShowHideRecurrence);
	   
	// recurrency elements   
	$('input#recurrence-interval').keyup(updateIntervalDescriptor);
	$('select#recurrence-frequency').change(updateIntervalDescriptor);
	$('select#recurrence-frequency').change(updateIntervalSelectors);
	
	/* Useful function for adding the em_ajax flag to a url, regardless of querystring format */
	var em_ajaxify = function(url){
		if ( url.search('em_ajax=0') != -1){
			url = url.replace('em_ajax=0','em_ajax=1');
		}else if( url.search(/\?/) != -1 ){
			url = url + "&em_ajax=1";
		}else{
			url = url + "?em_ajax=1";
		}
		return url;
	}

	/* Load any maps */	
	if( $('.em-location-map').length > 0 || $('.em-locations-map').length > 0 || $('#em-map').length > 0 ){
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://maps.google.com/maps/api/js?v=3.4&sensor=false&callback=em_maps";
		document.body.appendChild(script);
	}else{
		em_location_input_ajax();
	}
	
});

//Location functions
function em_location_input_ajax(){
	//Location stuff - only needed if inputs for location exist
	if( jQuery('select#location-select-id, input#location-name').length > 0 ){	
		//Load map
		if(jQuery('#em-map').length > 0){
			var em_LatLng = new google.maps.LatLng(0, 0);
			var map = new google.maps.Map( document.getElementById('em-map'), {
			    zoom: 14,
			    center: em_LatLng,
			    mapTypeId: google.maps.MapTypeId.ROADMAP,
			    mapTypeControl: false
			});
			var marker = new google.maps.Marker({
			    position: em_LatLng,
			    map: map
			});
			var infoWindow = new google.maps.InfoWindow({
			    content: ''
			});
			var geocoder = new google.maps.Geocoder();
			google.maps.event.addListener(infoWindow, 'domready', function() { 
				document.getElementById('location-balloon-content').parentNode.style.overflow=''; 
				document.getElementById('location-balloon-content').parentNode.parentNode.style.overflow=''; 
			});
		}
		
		//Add listeners for changes to address
		var get_map_by_id = function(id){
			if(jQuery('#em-map').length > 0){
				jQuery.getJSON(document.URL,{ em_ajax_action:'get_location', id:id }, function(data){
					if( data.location_latitude!=0 && data.location_longitude!=0 ){
						loc_latlng = new google.maps.LatLng(data.location_latitude, data.location_longitude);
						marker.setPosition(loc_latlng);
						marker.setTitle( data.location_name );
						jQuery('#em-map').show();
						jQuery('#em-map-404').hide();
						map.setCenter(loc_latlng);
						map.panBy(40,-55);
						infoWindow.setContent( '<div id="location-balloon-content">'+ data.location_balloon +'</div>');
						infoWindow.open(map, marker);
						google.maps.event.trigger(map, 'resize');
					}else{
						jQuery('#em-map').hide();
						jQuery('#em-map-404').show();
					}
				});
			}
		}
		jQuery('#location-select-id').change( function(){get_map_by_id(jQuery(this).val())} );
		jQuery('#location-town, #location-address, #location-state, #location-postcode, #location-country').change( function(){
			//build address
			var addresses = [ jQuery('#location-address').val(), jQuery('#location-town').val(), jQuery('#location-state').val(), jQuery('#location-postcode').val() ];
			var address = '';
			jQuery.each( addresses, function(i, val){
				if( val != '' ){
					address = ( address == '' ) ? address+val:address+', '+val;
				}
			});
			//do country last, as it's using the text version
			if( jQuery('#location-country option:selected').val() != 0 ){
				address = ( address == '' ) ? address+jQuery('#location-country option:selected').text():address+', '+jQuery('#location-country option:selected').text();
			}
			if( address != '' && jQuery('#em-map').length > 0 ){
				geocoder.geocode( { 'address': address }, function(results, status) {
				    if (status == google.maps.GeocoderStatus.OK) {
						marker.setPosition(results[0].geometry.location);
						marker.setTitle( jQuery('#location-name, #location-select-id').first().val() );
						jQuery('#location-latitude').val(results[0].geometry.location.lat());
						jQuery('#location-longitude').val(results[0].geometry.location.lng());
	        			jQuery('#em-map').show();
	        			jQuery('#em-map-404').hide();
	        			google.maps.event.trigger(map, 'resize');
						map.setCenter(results[0].geometry.location);
						map.panBy(40,-55);
						infoWindow.setContent( 
							'<div id="location-balloon-content"><strong>' + 
							jQuery('#location-name').val() + 
							'</strong><br/>' + 
							jQuery('#location-address').val() + 
							'<br/>' + jQuery('#location-town').val()+ 
							'</div>'
						);
						infoWindow.open(map, marker);
					} else {
	        			jQuery('#em-map').hide();
	        			jQuery('#em-map-404').show();
					}
				});
			}
		});
		
		jQuery("input#location-town, select#location-select-id").triggerHandler('change');
		
		//Finally, add autocomplete here
		//Autocomplete
		if( jQuery( "#event-form input#location-name" ).length > 0 ){
			jQuery( "#event-form input#location-name" ).autocomplete({
				source: EM.locationajaxurl,
				minLength: 2,
				focus: function( event, ui ){
					jQuery("input#location-id" ).val( ui.item.value );
					return false;
				},			 
				select: function( event, ui ){
					jQuery("input#location-id" ).val(ui.item.id);
					jQuery("input#location-name" ).val(ui.item.value);
					jQuery('input#location-address').val(ui.item.address);
					jQuery('input#location-town').val(ui.item.town);
					jQuery('input#location-state').val(ui.item.state);
					jQuery('input#location-postcode').val(ui.item.postcode);
					if( ui.item.country == '' ){
						jQuery('select#location-country option:selected').removeAttr('selected');
					}else{
						jQuery('select#location-country option[value="'+ui.item.country+'"]').attr('selected', 'selected');
					}
					get_map_by_id(ui.item.id);
					jQuery('#em-location-data input, #em-location-data select').css('background-color','#ccc');
					jQuery('#em-location-data input#location-name').css('background-color','#fff');
					jQuery('#em-location-reset').show();
					return false;
				}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				html_val = "<a>" + item.label + '<br><span style="font-size:11px"><em>'+ item.address + ', ' + item.town+"</em></span></a>";
				return jQuery( "<li></li>" ).data( "item.autocomplete", item ).append(html_val).appendTo( ul );
			};
			jQuery('#em-location-reset').click( function(){
				jQuery('#em-location-data input').css('background-color','#fff').val('');
				jQuery('#em-location-data select').css('background-color','#fff');
				jQuery('#em-location-data option:selected').removeAttr('selected');
				jQuery('#em-location-reset').hide();
			});
			if( jQuery('input#location-id').val() != '' ){
				jQuery('#em-location-data input, #em-location-data select').css('background-color','#ccc');
				jQuery('#em-location-data input#location-name').css('background-color','#fff');
				jQuery('#em-location-reset').show();
			}
		}
	}
}

/*
 * MAP FUNCTIONS
 */
var maps = {};
//Load single maps (each map is treated as a seperate map.
function em_maps() {
	//Find all the maps on this page
	jQuery('.em-location-map').each( function(index){
		el = jQuery(this);
		var map_id = el.attr('id').replace('em-location-map-','');
		em_LatLng = new google.maps.LatLng( jQuery('#em-location-map-coords-'+map_id+' .lat').text(), jQuery('#em-location-map-coords-'+map_id+' .lng').text());
		maps[map_id] = new google.maps.Map( document.getElementById('em-location-map-'+map_id), {
		    zoom: 14,
		    center: em_LatLng,
		    mapTypeId: google.maps.MapTypeId.ROADMAP,
		    mapTypeControl: false
		});
		var marker = new google.maps.Marker({
		    position: em_LatLng,
		    map: maps[map_id]
		});
		var infowindow = new google.maps.InfoWindow({ content: jQuery('#em-location-map-info-'+map_id+' .em-map-balloon').get(0) });
		infowindow.open(maps[map_id],marker);
		maps[map_id].panBy(40,-70);
		
		//JS Hook for handling map after instantiation
		//Example hook, which you can add elsewhere in your theme's JS - jQuery(document).bind('em_maps_location_hook', function(){ alert('hi');} );
		jQuery(document).trigger('em_maps_location_hook', [maps[map_id], infowindow, marker]);
	});
	jQuery('.em-locations-map').each( function(index){
		var el = jQuery(this);
		var map_id = el.attr('id').replace('em-locations-map-','');
		var em_data = jQuery.parseJSON( jQuery('#em-locations-map-coords-'+map_id).text() );
		jQuery.getJSON(document.URL, em_data , function(data){
			if(data.length > 0){
				  var myLatlng = new google.maps.LatLng(data[0].location_latitude,data[0].location_longitude);
				  var myOptions = {
				    mapTypeId: google.maps.MapTypeId.ROADMAP
				  };
				  maps[map_id] = new google.maps.Map(document.getElementById("em-locations-map-"+map_id), myOptions);
				  
				  var minLatLngArr = [0,0];
				  var maxLatLngArr = [0,0];
				  
				  for (var i = 0; i < data.length; i++) {
					  if( !(data[i].location_latitude == 0 && data[i].location_longitude == 0) ){
						var latitude = parseFloat( data[i].location_latitude );
						var longitude = parseFloat( data[i].location_longitude );
						var location = new google.maps.LatLng( latitude, longitude );
						var marker = new google.maps.Marker({
						    position: location, 
						    map: maps[map_id]
						});
						marker.setTitle(data[i].location_name);
						var myContent = '<div class="em-map-balloon"><div id="em-map-balloon-'+map_id+'" class="em-map-balloon-content">'+ data[i].location_balloon +'</div></div>';
						em_map_infobox(marker, myContent, maps[map_id]);
						
						//Get min and max long/lats
						minLatLngArr[0] = (latitude < minLatLngArr[0] || i == 0) ? latitude : minLatLngArr[0];
						minLatLngArr[1] = (longitude < minLatLngArr[1] || i == 0) ? longitude : minLatLngArr[1];
						maxLatLngArr[0] = (latitude > maxLatLngArr[0] || i == 0) ? latitude : maxLatLngArr[0];
						maxLatLngArr[1] = (longitude > maxLatLngArr[1] || i == 0) ? longitude : maxLatLngArr[1];
					  }
				  }
				  // Zoom in to the bounds
				  var minLatLng = new google.maps.LatLng(minLatLngArr[0],minLatLngArr[1]);
				  var maxLatLng = new google.maps.LatLng(maxLatLngArr[0],maxLatLngArr[1]);
				  var bounds = new google.maps.LatLngBounds(minLatLng,maxLatLng);
				  maps[map_id].fitBounds(bounds);
				//Call a hook if exists
				jQuery(document).trigger('em_maps_locations_hook', [maps[map_id]]);
			}else{
				el.children().first().html('No locations found');
			}
		});
	});
	em_location_input_ajax();
}
  
function em_map_infobox(marker, message, map) {
  var infowindow = new google.maps.InfoWindow({ content: message });
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
  });
}

/*
  * jQuery UI Datepicker 1.8.13
  *
  * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
  * Dual licensed under the MIT or GPL Version 2 licenses.
  * http://jquery.org/license
  *
  * http://docs.jquery.com/UI/Datepicker
  *
  * Depends:
  *	jquery.ui.core.js
  */
 (function(d,B){function M(){this.debug=false;this._curInst=null;this._keyEvent=false;this._disabledInputs=[];this._inDialog=this._datepickerShowing=false;this._mainDivId="ui-datepicker-div";this._inlineClass="ui-datepicker-inline";this._appendClass="ui-datepicker-append";this._triggerClass="ui-datepicker-trigger";this._dialogClass="ui-datepicker-dialog";this._disableClass="ui-datepicker-disabled";this._unselectableClass="ui-datepicker-unselectable";this._currentClass="ui-datepicker-current-day";this._dayOverClass=
 "ui-datepicker-days-cell-over";this.regional=[];this.regional[""]={closeText:"Done",prevText:"Prev",nextText:"Next",currentText:"Today",monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayNamesMin:["Su",
 "Mo","Tu","We","Th","Fr","Sa"],weekHeader:"Wk",dateFormat:"mm/dd/yy",firstDay:0,isRTL:false,showMonthAfterYear:false,yearSuffix:""};this._defaults={showOn:"focus",showAnim:"fadeIn",showOptions:{},defaultDate:null,appendText:"",buttonText:"...",buttonImage:"",buttonImageOnly:false,hideIfNoPrevNext:false,navigationAsDateFormat:false,gotoCurrent:false,changeMonth:false,changeYear:false,yearRange:"c-10:c+10",showOtherMonths:false,selectOtherMonths:false,showWeek:false,calculateWeek:this.iso8601Week,shortYearCutoff:"+10",
 minDate:null,maxDate:null,duration:"fast",beforeShowDay:null,beforeShow:null,onSelect:null,onChangeMonthYear:null,onClose:null,numberOfMonths:1,showCurrentAtPos:0,stepMonths:1,stepBigMonths:12,altField:"",altFormat:"",constrainInput:true,showButtonPanel:false,autoSize:false};d.extend(this._defaults,this.regional[""]);this.dpDiv=N(d('<div id="'+this._mainDivId+'" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>'))}function N(a){return a.delegate("button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a",
 "mouseout",function(){d(this).removeClass("ui-state-hover");this.className.indexOf("ui-datepicker-prev")!=-1&&d(this).removeClass("ui-datepicker-prev-hover");this.className.indexOf("ui-datepicker-next")!=-1&&d(this).removeClass("ui-datepicker-next-hover")}).delegate("button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a","mouseover",function(){if(!d.datepicker._isDisabledDatepicker(J.inline?a.parent()[0]:J.input[0])){d(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");
 d(this).addClass("ui-state-hover");this.className.indexOf("ui-datepicker-prev")!=-1&&d(this).addClass("ui-datepicker-prev-hover");this.className.indexOf("ui-datepicker-next")!=-1&&d(this).addClass("ui-datepicker-next-hover")}})}function H(a,b){d.extend(a,b);for(var c in b)if(b[c]==null||b[c]==B)a[c]=b[c];return a}d.extend(d.ui,{datepicker:{version:"1.8.13"}});var z=(new Date).getTime(),J;d.extend(M.prototype,{markerClassName:"hasDatepicker",log:function(){this.debug&&console.log.apply("",arguments)},
 _widgetDatepicker:function(){return this.dpDiv},setDefaults:function(a){H(this._defaults,a||{});return this},_attachDatepicker:function(a,b){var c=null;for(var e in this._defaults){var f=a.getAttribute("date:"+e);if(f){c=c||{};try{c[e]=eval(f)}catch(h){c[e]=f}}}e=a.nodeName.toLowerCase();f=e=="div"||e=="span";if(!a.id){this.uuid+=1;a.id="dp"+this.uuid}var i=this._newInst(d(a),f);i.settings=d.extend({},b||{},c||{});if(e=="input")this._connectDatepicker(a,i);else f&&this._inlineDatepicker(a,i)},_newInst:function(a,
 b){return{id:a[0].id.replace(/([^A-Za-z0-9_-])/g,"\\\\$1"),input:a,selectedDay:0,selectedMonth:0,selectedYear:0,drawMonth:0,drawYear:0,inline:b,dpDiv:!b?this.dpDiv:N(d('<div class="'+this._inlineClass+' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>'))}},_connectDatepicker:function(a,b){var c=d(a);b.append=d([]);b.trigger=d([]);if(!c.hasClass(this.markerClassName)){this._attachments(c,b);c.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker",
 function(e,f,h){b.settings[f]=h}).bind("getData.datepicker",function(e,f){return this._get(b,f)});this._autoSize(b);d.data(a,"datepicker",b)}},_attachments:function(a,b){var c=this._get(b,"appendText"),e=this._get(b,"isRTL");b.append&&b.append.remove();if(c){b.append=d('<span class="'+this._appendClass+'">'+c+"</span>");a[e?"before":"after"](b.append)}a.unbind("focus",this._showDatepicker);b.trigger&&b.trigger.remove();c=this._get(b,"showOn");if(c=="focus"||c=="both")a.focus(this._showDatepicker);
 if(c=="button"||c=="both"){c=this._get(b,"buttonText");var f=this._get(b,"buttonImage");b.trigger=d(this._get(b,"buttonImageOnly")?d("<img/>").addClass(this._triggerClass).attr({src:f,alt:c,title:c}):d('<button type="button"></button>').addClass(this._triggerClass).html(f==""?c:d("<img/>").attr({src:f,alt:c,title:c})));a[e?"before":"after"](b.trigger);b.trigger.click(function(){d.datepicker._datepickerShowing&&d.datepicker._lastInput==a[0]?d.datepicker._hideDatepicker():d.datepicker._showDatepicker(a[0]);
 return false})}},_autoSize:function(a){if(this._get(a,"autoSize")&&!a.inline){var b=new Date(2009,11,20),c=this._get(a,"dateFormat");if(c.match(/[DM]/)){var e=function(f){for(var h=0,i=0,g=0;g<f.length;g++)if(f[g].length>h){h=f[g].length;i=g}return i};b.setMonth(e(this._get(a,c.match(/MM/)?"monthNames":"monthNamesShort")));b.setDate(e(this._get(a,c.match(/DD/)?"dayNames":"dayNamesShort"))+20-b.getDay())}a.input.attr("size",this._formatDate(a,b).length)}},_inlineDatepicker:function(a,b){var c=d(a);
 if(!c.hasClass(this.markerClassName)){c.addClass(this.markerClassName).append(b.dpDiv).bind("setData.datepicker",function(e,f,h){b.settings[f]=h}).bind("getData.datepicker",function(e,f){return this._get(b,f)});d.data(a,"datepicker",b);this._setDate(b,this._getDefaultDate(b),true);this._updateDatepicker(b);this._updateAlternate(b);b.dpDiv.show()}},_dialogDatepicker:function(a,b,c,e,f){a=this._dialogInst;if(!a){this.uuid+=1;this._dialogInput=d('<input type="text" id="'+("dp"+this.uuid)+'" style="position: absolute; top: -100px; width: 0px; z-index: -10;"/>');
 this._dialogInput.keydown(this._doKeyDown);d("body").append(this._dialogInput);a=this._dialogInst=this._newInst(this._dialogInput,false);a.settings={};d.data(this._dialogInput[0],"datepicker",a)}H(a.settings,e||{});b=b&&b.constructor==Date?this._formatDate(a,b):b;this._dialogInput.val(b);this._pos=f?f.length?f:[f.pageX,f.pageY]:null;if(!this._pos)this._pos=[document.documentElement.clientWidth/2-100+(document.documentElement.scrollLeft||document.body.scrollLeft),document.documentElement.clientHeight/
 2-150+(document.documentElement.scrollTop||document.body.scrollTop)];this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px");a.settings.onSelect=c;this._inDialog=true;this.dpDiv.addClass(this._dialogClass);this._showDatepicker(this._dialogInput[0]);d.blockUI&&d.blockUI(this.dpDiv);d.data(this._dialogInput[0],"datepicker",a);return this},_destroyDatepicker:function(a){var b=d(a),c=d.data(a,"datepicker");if(b.hasClass(this.markerClassName)){var e=a.nodeName.toLowerCase();d.removeData(a,
 "datepicker");if(e=="input"){c.append.remove();c.trigger.remove();b.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)}else if(e=="div"||e=="span")b.removeClass(this.markerClassName).empty()}},_enableDatepicker:function(a){var b=d(a),c=d.data(a,"datepicker");if(b.hasClass(this.markerClassName)){var e=a.nodeName.toLowerCase();if(e=="input"){a.disabled=false;c.trigger.filter("button").each(function(){this.disabled=
 false}).end().filter("img").css({opacity:"1.0",cursor:""})}else if(e=="div"||e=="span"){b=b.children("."+this._inlineClass);b.children().removeClass("ui-state-disabled");b.find("select.ui-datepicker-month, select.ui-datepicker-year").removeAttr("disabled")}this._disabledInputs=d.map(this._disabledInputs,function(f){return f==a?null:f})}},_disableDatepicker:function(a){var b=d(a),c=d.data(a,"datepicker");if(b.hasClass(this.markerClassName)){var e=a.nodeName.toLowerCase();if(e=="input"){a.disabled=
 true;c.trigger.filter("button").each(function(){this.disabled=true}).end().filter("img").css({opacity:"0.5",cursor:"default"})}else if(e=="div"||e=="span"){b=b.children("."+this._inlineClass);b.children().addClass("ui-state-disabled");b.find("select.ui-datepicker-month, select.ui-datepicker-year").attr("disabled","disabled")}this._disabledInputs=d.map(this._disabledInputs,function(f){return f==a?null:f});this._disabledInputs[this._disabledInputs.length]=a}},_isDisabledDatepicker:function(a){if(!a)return false;
 for(var b=0;b<this._disabledInputs.length;b++)if(this._disabledInputs[b]==a)return true;return false},_getInst:function(a){try{return d.data(a,"datepicker")}catch(b){throw"Missing instance data for this datepicker";}},_optionDatepicker:function(a,b,c){var e=this._getInst(a);if(arguments.length==2&&typeof b=="string")return b=="defaults"?d.extend({},d.datepicker._defaults):e?b=="all"?d.extend({},e.settings):this._get(e,b):null;var f=b||{};if(typeof b=="string"){f={};f[b]=c}if(e){this._curInst==e&&
 this._hideDatepicker();var h=this._getDateDatepicker(a,true),i=this._getMinMaxDate(e,"min"),g=this._getMinMaxDate(e,"max");H(e.settings,f);if(i!==null&&f.dateFormat!==B&&f.minDate===B)e.settings.minDate=this._formatDate(e,i);if(g!==null&&f.dateFormat!==B&&f.maxDate===B)e.settings.maxDate=this._formatDate(e,g);this._attachments(d(a),e);this._autoSize(e);this._setDate(e,h);this._updateAlternate(e);this._updateDatepicker(e)}},_changeDatepicker:function(a,b,c){this._optionDatepicker(a,b,c)},_refreshDatepicker:function(a){(a=
 this._getInst(a))&&this._updateDatepicker(a)},_setDateDatepicker:function(a,b){if(a=this._getInst(a)){this._setDate(a,b);this._updateDatepicker(a);this._updateAlternate(a)}},_getDateDatepicker:function(a,b){(a=this._getInst(a))&&!a.inline&&this._setDateFromField(a,b);return a?this._getDate(a):null},_doKeyDown:function(a){var b=d.datepicker._getInst(a.target),c=true,e=b.dpDiv.is(".ui-datepicker-rtl");b._keyEvent=true;if(d.datepicker._datepickerShowing)switch(a.keyCode){case 9:d.datepicker._hideDatepicker();
 c=false;break;case 13:c=d("td."+d.datepicker._dayOverClass+":not(."+d.datepicker._currentClass+")",b.dpDiv);c[0]?d.datepicker._selectDay(a.target,b.selectedMonth,b.selectedYear,c[0]):d.datepicker._hideDatepicker();return false;case 27:d.datepicker._hideDatepicker();break;case 33:d.datepicker._adjustDate(a.target,a.ctrlKey?-d.datepicker._get(b,"stepBigMonths"):-d.datepicker._get(b,"stepMonths"),"M");break;case 34:d.datepicker._adjustDate(a.target,a.ctrlKey?+d.datepicker._get(b,"stepBigMonths"):+d.datepicker._get(b,
 "stepMonths"),"M");break;case 35:if(a.ctrlKey||a.metaKey)d.datepicker._clearDate(a.target);c=a.ctrlKey||a.metaKey;break;case 36:if(a.ctrlKey||a.metaKey)d.datepicker._gotoToday(a.target);c=a.ctrlKey||a.metaKey;break;case 37:if(a.ctrlKey||a.metaKey)d.datepicker._adjustDate(a.target,e?+1:-1,"D");c=a.ctrlKey||a.metaKey;if(a.originalEvent.altKey)d.datepicker._adjustDate(a.target,a.ctrlKey?-d.datepicker._get(b,"stepBigMonths"):-d.datepicker._get(b,"stepMonths"),"M");break;case 38:if(a.ctrlKey||a.metaKey)d.datepicker._adjustDate(a.target,
 -7,"D");c=a.ctrlKey||a.metaKey;break;case 39:if(a.ctrlKey||a.metaKey)d.datepicker._adjustDate(a.target,e?-1:+1,"D");c=a.ctrlKey||a.metaKey;if(a.originalEvent.altKey)d.datepicker._adjustDate(a.target,a.ctrlKey?+d.datepicker._get(b,"stepBigMonths"):+d.datepicker._get(b,"stepMonths"),"M");break;case 40:if(a.ctrlKey||a.metaKey)d.datepicker._adjustDate(a.target,+7,"D");c=a.ctrlKey||a.metaKey;break;default:c=false}else if(a.keyCode==36&&a.ctrlKey)d.datepicker._showDatepicker(this);else c=false;if(c){a.preventDefault();
 a.stopPropagation()}},_doKeyPress:function(a){var b=d.datepicker._getInst(a.target);if(d.datepicker._get(b,"constrainInput")){b=d.datepicker._possibleChars(d.datepicker._get(b,"dateFormat"));var c=String.fromCharCode(a.charCode==B?a.keyCode:a.charCode);return a.ctrlKey||a.metaKey||c<" "||!b||b.indexOf(c)>-1}},_doKeyUp:function(a){a=d.datepicker._getInst(a.target);if(a.input.val()!=a.lastVal)try{if(d.datepicker.parseDate(d.datepicker._get(a,"dateFormat"),a.input?a.input.val():null,d.datepicker._getFormatConfig(a))){d.datepicker._setDateFromField(a);
 d.datepicker._updateAlternate(a);d.datepicker._updateDatepicker(a)}}catch(b){d.datepicker.log(b)}return true},_showDatepicker:function(a){a=a.target||a;if(a.nodeName.toLowerCase()!="input")a=d("input",a.parentNode)[0];if(!(d.datepicker._isDisabledDatepicker(a)||d.datepicker._lastInput==a)){var b=d.datepicker._getInst(a);d.datepicker._curInst&&d.datepicker._curInst!=b&&d.datepicker._curInst.dpDiv.stop(true,true);var c=d.datepicker._get(b,"beforeShow");H(b.settings,c?c.apply(a,[a,b]):{});b.lastVal=
 null;d.datepicker._lastInput=a;d.datepicker._setDateFromField(b);if(d.datepicker._inDialog)a.value="";if(!d.datepicker._pos){d.datepicker._pos=d.datepicker._findPos(a);d.datepicker._pos[1]+=a.offsetHeight}var e=false;d(a).parents().each(function(){e|=d(this).css("position")=="fixed";return!e});if(e&&d.browser.opera){d.datepicker._pos[0]-=document.documentElement.scrollLeft;d.datepicker._pos[1]-=document.documentElement.scrollTop}c={left:d.datepicker._pos[0],top:d.datepicker._pos[1]};d.datepicker._pos=
 null;b.dpDiv.empty();b.dpDiv.css({position:"absolute",display:"block",top:"-1000px"});d.datepicker._updateDatepicker(b);c=d.datepicker._checkOffset(b,c,e);b.dpDiv.css({position:d.datepicker._inDialog&&d.blockUI?"static":e?"fixed":"absolute",display:"none",left:c.left+"px",top:c.top+"px"});if(!b.inline){c=d.datepicker._get(b,"showAnim");var f=d.datepicker._get(b,"duration"),h=function(){var i=b.dpDiv.find("iframe.ui-datepicker-cover");if(i.length){var g=d.datepicker._getBorders(b.dpDiv);i.css({left:-g[0],
 top:-g[1],width:b.dpDiv.outerWidth(),height:b.dpDiv.outerHeight()})}};b.dpDiv.zIndex(d(a).zIndex()+1);d.datepicker._datepickerShowing=true;d.effects&&d.effects[c]?b.dpDiv.show(c,d.datepicker._get(b,"showOptions"),f,h):b.dpDiv[c||"show"](c?f:null,h);if(!c||!f)h();b.input.is(":visible")&&!b.input.is(":disabled")&&b.input.focus();d.datepicker._curInst=b}}},_updateDatepicker:function(a){var b=d.datepicker._getBorders(a.dpDiv);J=a;a.dpDiv.empty().append(this._generateHTML(a));var c=a.dpDiv.find("iframe.ui-datepicker-cover");
 c.length&&c.css({left:-b[0],top:-b[1],width:a.dpDiv.outerWidth(),height:a.dpDiv.outerHeight()});a.dpDiv.find("."+this._dayOverClass+" a").mouseover();b=this._getNumberOfMonths(a);c=b[1];a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");c>1&&a.dpDiv.addClass("ui-datepicker-multi-"+c).css("width",17*c+"em");a.dpDiv[(b[0]!=1||b[1]!=1?"add":"remove")+"Class"]("ui-datepicker-multi");a.dpDiv[(this._get(a,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl");
 a==d.datepicker._curInst&&d.datepicker._datepickerShowing&&a.input&&a.input.is(":visible")&&!a.input.is(":disabled")&&a.input[0]!=document.activeElement&&a.input.focus();if(a.yearshtml){var e=a.yearshtml;setTimeout(function(){e===a.yearshtml&&a.yearshtml&&a.dpDiv.find("select.ui-datepicker-year:first").replaceWith(a.yearshtml);e=a.yearshtml=null},0)}},_getBorders:function(a){var b=function(c){return{thin:1,medium:2,thick:3}[c]||c};return[parseFloat(b(a.css("border-left-width"))),parseFloat(b(a.css("border-top-width")))]},
 _checkOffset:function(a,b,c){var e=a.dpDiv.outerWidth(),f=a.dpDiv.outerHeight(),h=a.input?a.input.outerWidth():0,i=a.input?a.input.outerHeight():0,g=document.documentElement.clientWidth+d(document).scrollLeft(),j=document.documentElement.clientHeight+d(document).scrollTop();b.left-=this._get(a,"isRTL")?e-h:0;b.left-=c&&b.left==a.input.offset().left?d(document).scrollLeft():0;b.top-=c&&b.top==a.input.offset().top+i?d(document).scrollTop():0;b.left-=Math.min(b.left,b.left+e>g&&g>e?Math.abs(b.left+e-
 g):0);b.top-=Math.min(b.top,b.top+f>j&&j>f?Math.abs(f+i):0);return b},_findPos:function(a){for(var b=this._get(this._getInst(a),"isRTL");a&&(a.type=="hidden"||a.nodeType!=1||d.expr.filters.hidden(a));)a=a[b?"previousSibling":"nextSibling"];a=d(a).offset();return[a.left,a.top]},_hideDatepicker:function(a){var b=this._curInst;if(!(!b||a&&b!=d.data(a,"datepicker")))if(this._datepickerShowing){a=this._get(b,"showAnim");var c=this._get(b,"duration"),e=function(){d.datepicker._tidyDialog(b);this._curInst=
 null};d.effects&&d.effects[a]?b.dpDiv.hide(a,d.datepicker._get(b,"showOptions"),c,e):b.dpDiv[a=="slideDown"?"slideUp":a=="fadeIn"?"fadeOut":"hide"](a?c:null,e);a||e();if(a=this._get(b,"onClose"))a.apply(b.input?b.input[0]:null,[b.input?b.input.val():"",b]);this._datepickerShowing=false;this._lastInput=null;if(this._inDialog){this._dialogInput.css({position:"absolute",left:"0",top:"-100px"});if(d.blockUI){d.unblockUI();d("body").append(this.dpDiv)}}this._inDialog=false}},_tidyDialog:function(a){a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")},
 _checkExternalClick:function(a){if(d.datepicker._curInst){a=d(a.target);a[0].id!=d.datepicker._mainDivId&&a.parents("#"+d.datepicker._mainDivId).length==0&&!a.hasClass(d.datepicker.markerClassName)&&!a.hasClass(d.datepicker._triggerClass)&&d.datepicker._datepickerShowing&&!(d.datepicker._inDialog&&d.blockUI)&&d.datepicker._hideDatepicker()}},_adjustDate:function(a,b,c){a=d(a);var e=this._getInst(a[0]);if(!this._isDisabledDatepicker(a[0])){this._adjustInstDate(e,b+(c=="M"?this._get(e,"showCurrentAtPos"):
 0),c);this._updateDatepicker(e)}},_gotoToday:function(a){a=d(a);var b=this._getInst(a[0]);if(this._get(b,"gotoCurrent")&&b.currentDay){b.selectedDay=b.currentDay;b.drawMonth=b.selectedMonth=b.currentMonth;b.drawYear=b.selectedYear=b.currentYear}else{var c=new Date;b.selectedDay=c.getDate();b.drawMonth=b.selectedMonth=c.getMonth();b.drawYear=b.selectedYear=c.getFullYear()}this._notifyChange(b);this._adjustDate(a)},_selectMonthYear:function(a,b,c){a=d(a);var e=this._getInst(a[0]);e._selectingMonthYear=
 false;e["selected"+(c=="M"?"Month":"Year")]=e["draw"+(c=="M"?"Month":"Year")]=parseInt(b.options[b.selectedIndex].value,10);this._notifyChange(e);this._adjustDate(a)},_clickMonthYear:function(a){var b=this._getInst(d(a)[0]);b.input&&b._selectingMonthYear&&setTimeout(function(){b.input.focus()},0);b._selectingMonthYear=!b._selectingMonthYear},_selectDay:function(a,b,c,e){var f=d(a);if(!(d(e).hasClass(this._unselectableClass)||this._isDisabledDatepicker(f[0]))){f=this._getInst(f[0]);f.selectedDay=f.currentDay=
 d("a",e).html();f.selectedMonth=f.currentMonth=b;f.selectedYear=f.currentYear=c;this._selectDate(a,this._formatDate(f,f.currentDay,f.currentMonth,f.currentYear))}},_clearDate:function(a){a=d(a);this._getInst(a[0]);this._selectDate(a,"")},_selectDate:function(a,b){a=this._getInst(d(a)[0]);b=b!=null?b:this._formatDate(a);a.input&&a.input.val(b);this._updateAlternate(a);var c=this._get(a,"onSelect");if(c)c.apply(a.input?a.input[0]:null,[b,a]);else a.input&&a.input.trigger("change");if(a.inline)this._updateDatepicker(a);
 else{this._hideDatepicker();this._lastInput=a.input[0];typeof a.input[0]!="object"&&a.input.focus();this._lastInput=null}},_updateAlternate:function(a){var b=this._get(a,"altField");if(b){var c=this._get(a,"altFormat")||this._get(a,"dateFormat"),e=this._getDate(a),f=this.formatDate(c,e,this._getFormatConfig(a));d(b).each(function(){d(this).val(f)})}},noWeekends:function(a){a=a.getDay();return[a>0&&a<6,""]},iso8601Week:function(a){a=new Date(a.getTime());a.setDate(a.getDate()+4-(a.getDay()||7));var b=
 a.getTime();a.setMonth(0);a.setDate(1);return Math.floor(Math.round((b-a)/864E5)/7)+1},parseDate:function(a,b,c){if(a==null||b==null)throw"Invalid arguments";b=typeof b=="object"?b.toString():b+"";if(b=="")return null;var e=(c?c.shortYearCutoff:null)||this._defaults.shortYearCutoff;e=typeof e!="string"?e:(new Date).getFullYear()%100+parseInt(e,10);for(var f=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,h=(c?c.dayNames:null)||this._defaults.dayNames,i=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort,
 g=(c?c.monthNames:null)||this._defaults.monthNames,j=c=-1,l=-1,u=-1,k=false,o=function(p){(p=A+1<a.length&&a.charAt(A+1)==p)&&A++;return p},m=function(p){var C=o(p);p=new RegExp("^\\d{1,"+(p=="@"?14:p=="!"?20:p=="y"&&C?4:p=="o"?3:2)+"}");p=b.substring(s).match(p);if(!p)throw"Missing number at position "+s;s+=p[0].length;return parseInt(p[0],10)},n=function(p,C,K){p=d.map(o(p)?K:C,function(w,x){return[[x,w]]}).sort(function(w,x){return-(w[1].length-x[1].length)});var E=-1;d.each(p,function(w,x){w=
 x[1];if(b.substr(s,w.length).toLowerCase()==w.toLowerCase()){E=x[0];s+=w.length;return false}});if(E!=-1)return E+1;else throw"Unknown name at position "+s;},r=function(){if(b.charAt(s)!=a.charAt(A))throw"Unexpected literal at position "+s;s++},s=0,A=0;A<a.length;A++)if(k)if(a.charAt(A)=="'"&&!o("'"))k=false;else r();else switch(a.charAt(A)){case "d":l=m("d");break;case "D":n("D",f,h);break;case "o":u=m("o");break;case "m":j=m("m");break;case "M":j=n("M",i,g);break;case "y":c=m("y");break;case "@":var v=
 new Date(m("@"));c=v.getFullYear();j=v.getMonth()+1;l=v.getDate();break;case "!":v=new Date((m("!")-this._ticksTo1970)/1E4);c=v.getFullYear();j=v.getMonth()+1;l=v.getDate();break;case "'":if(o("'"))r();else k=true;break;default:r()}if(c==-1)c=(new Date).getFullYear();else if(c<100)c+=(new Date).getFullYear()-(new Date).getFullYear()%100+(c<=e?0:-100);if(u>-1){j=1;l=u;do{e=this._getDaysInMonth(c,j-1);if(l<=e)break;j++;l-=e}while(1)}v=this._daylightSavingAdjust(new Date(c,j-1,l));if(v.getFullYear()!=
 c||v.getMonth()+1!=j||v.getDate()!=l)throw"Invalid date";return v},ATOM:"yy-mm-dd",COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y",TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd",_ticksTo1970:(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925))*24*60*60*1E7,formatDate:function(a,b,c){if(!b)return"";var e=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,f=(c?c.dayNames:null)||this._defaults.dayNames,
 h=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort;c=(c?c.monthNames:null)||this._defaults.monthNames;var i=function(o){(o=k+1<a.length&&a.charAt(k+1)==o)&&k++;return o},g=function(o,m,n){m=""+m;if(i(o))for(;m.length<n;)m="0"+m;return m},j=function(o,m,n,r){return i(o)?r[m]:n[m]},l="",u=false;if(b)for(var k=0;k<a.length;k++)if(u)if(a.charAt(k)=="'"&&!i("'"))u=false;else l+=a.charAt(k);else switch(a.charAt(k)){case "d":l+=g("d",b.getDate(),2);break;case "D":l+=j("D",b.getDay(),e,f);break;
 case "o":l+=g("o",(b.getTime()-(new Date(b.getFullYear(),0,0)).getTime())/864E5,3);break;case "m":l+=g("m",b.getMonth()+1,2);break;case "M":l+=j("M",b.getMonth(),h,c);break;case "y":l+=i("y")?b.getFullYear():(b.getYear()%100<10?"0":"")+b.getYear()%100;break;case "@":l+=b.getTime();break;case "!":l+=b.getTime()*1E4+this._ticksTo1970;break;case "'":if(i("'"))l+="'";else u=true;break;default:l+=a.charAt(k)}return l},_possibleChars:function(a){for(var b="",c=false,e=function(h){(h=f+1<a.length&&a.charAt(f+
 1)==h)&&f++;return h},f=0;f<a.length;f++)if(c)if(a.charAt(f)=="'"&&!e("'"))c=false;else b+=a.charAt(f);else switch(a.charAt(f)){case "d":case "m":case "y":case "@":b+="0123456789";break;case "D":case "M":return null;case "'":if(e("'"))b+="'";else c=true;break;default:b+=a.charAt(f)}return b},_get:function(a,b){return a.settings[b]!==B?a.settings[b]:this._defaults[b]},_setDateFromField:function(a,b){if(a.input.val()!=a.lastVal){var c=this._get(a,"dateFormat"),e=a.lastVal=a.input?a.input.val():null,
 f,h;f=h=this._getDefaultDate(a);var i=this._getFormatConfig(a);try{f=this.parseDate(c,e,i)||h}catch(g){this.log(g);e=b?"":e}a.selectedDay=f.getDate();a.drawMonth=a.selectedMonth=f.getMonth();a.drawYear=a.selectedYear=f.getFullYear();a.currentDay=e?f.getDate():0;a.currentMonth=e?f.getMonth():0;a.currentYear=e?f.getFullYear():0;this._adjustInstDate(a)}},_getDefaultDate:function(a){return this._restrictMinMax(a,this._determineDate(a,this._get(a,"defaultDate"),new Date))},_determineDate:function(a,b,
 c){var e=function(h){var i=new Date;i.setDate(i.getDate()+h);return i},f=function(h){try{return d.datepicker.parseDate(d.datepicker._get(a,"dateFormat"),h,d.datepicker._getFormatConfig(a))}catch(i){}var g=(h.toLowerCase().match(/^c/)?d.datepicker._getDate(a):null)||new Date,j=g.getFullYear(),l=g.getMonth();g=g.getDate();for(var u=/([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,k=u.exec(h);k;){switch(k[2]||"d"){case "d":case "D":g+=parseInt(k[1],10);break;case "w":case "W":g+=parseInt(k[1],10)*7;break;case "m":case "M":l+=
 parseInt(k[1],10);g=Math.min(g,d.datepicker._getDaysInMonth(j,l));break;case "y":case "Y":j+=parseInt(k[1],10);g=Math.min(g,d.datepicker._getDaysInMonth(j,l));break}k=u.exec(h)}return new Date(j,l,g)};if(b=(b=b==null||b===""?c:typeof b=="string"?f(b):typeof b=="number"?isNaN(b)?c:e(b):new Date(b.getTime()))&&b.toString()=="Invalid Date"?c:b){b.setHours(0);b.setMinutes(0);b.setSeconds(0);b.setMilliseconds(0)}return this._daylightSavingAdjust(b)},_daylightSavingAdjust:function(a){if(!a)return null;
 a.setHours(a.getHours()>12?a.getHours()+2:0);return a},_setDate:function(a,b,c){var e=!b,f=a.selectedMonth,h=a.selectedYear;b=this._restrictMinMax(a,this._determineDate(a,b,new Date));a.selectedDay=a.currentDay=b.getDate();a.drawMonth=a.selectedMonth=a.currentMonth=b.getMonth();a.drawYear=a.selectedYear=a.currentYear=b.getFullYear();if((f!=a.selectedMonth||h!=a.selectedYear)&&!c)this._notifyChange(a);this._adjustInstDate(a);if(a.input)a.input.val(e?"":this._formatDate(a))},_getDate:function(a){return!a.currentYear||
 a.input&&a.input.val()==""?null:this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay))},_generateHTML:function(a){var b=new Date;b=this._daylightSavingAdjust(new Date(b.getFullYear(),b.getMonth(),b.getDate()));var c=this._get(a,"isRTL"),e=this._get(a,"showButtonPanel"),f=this._get(a,"hideIfNoPrevNext"),h=this._get(a,"navigationAsDateFormat"),i=this._getNumberOfMonths(a),g=this._get(a,"showCurrentAtPos"),j=this._get(a,"stepMonths"),l=i[0]!=1||i[1]!=1,u=this._daylightSavingAdjust(!a.currentDay?
 new Date(9999,9,9):new Date(a.currentYear,a.currentMonth,a.currentDay)),k=this._getMinMaxDate(a,"min"),o=this._getMinMaxDate(a,"max");g=a.drawMonth-g;var m=a.drawYear;if(g<0){g+=12;m--}if(o){var n=this._daylightSavingAdjust(new Date(o.getFullYear(),o.getMonth()-i[0]*i[1]+1,o.getDate()));for(n=k&&n<k?k:n;this._daylightSavingAdjust(new Date(m,g,1))>n;){g--;if(g<0){g=11;m--}}}a.drawMonth=g;a.drawYear=m;n=this._get(a,"prevText");n=!h?n:this.formatDate(n,this._daylightSavingAdjust(new Date(m,g-j,1)),this._getFormatConfig(a));
 n=this._canAdjustMonth(a,-1,m,g)?'<a class="ui-datepicker-prev ui-corner-all" onclick="DP_jQuery_'+z+".datepicker._adjustDate('#"+a.id+"', -"+j+", 'M');\" title=\""+n+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+n+"</span></a>":f?"":'<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="'+n+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+n+"</span></a>";var r=this._get(a,"nextText");r=!h?r:this.formatDate(r,this._daylightSavingAdjust(new Date(m,
 g+j,1)),this._getFormatConfig(a));f=this._canAdjustMonth(a,+1,m,g)?'<a class="ui-datepicker-next ui-corner-all" onclick="DP_jQuery_'+z+".datepicker._adjustDate('#"+a.id+"', +"+j+", 'M');\" title=\""+r+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+r+"</span></a>":f?"":'<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="'+r+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+r+"</span></a>";j=this._get(a,"currentText");r=this._get(a,"gotoCurrent")&&
 a.currentDay?u:b;j=!h?j:this.formatDate(j,r,this._getFormatConfig(a));h=!a.inline?'<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" onclick="DP_jQuery_'+z+'.datepicker._hideDatepicker();">'+this._get(a,"closeText")+"</button>":"";e=e?'<div class="ui-datepicker-buttonpane ui-widget-content">'+(c?h:"")+(this._isInRange(a,r)?'<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" onclick="DP_jQuery_'+
 z+".datepicker._gotoToday('#"+a.id+"');\">"+j+"</button>":"")+(c?"":h)+"</div>":"";h=parseInt(this._get(a,"firstDay"),10);h=isNaN(h)?0:h;j=this._get(a,"showWeek");r=this._get(a,"dayNames");this._get(a,"dayNamesShort");var s=this._get(a,"dayNamesMin"),A=this._get(a,"monthNames"),v=this._get(a,"monthNamesShort"),p=this._get(a,"beforeShowDay"),C=this._get(a,"showOtherMonths"),K=this._get(a,"selectOtherMonths");this._get(a,"calculateWeek");for(var E=this._getDefaultDate(a),w="",x=0;x<i[0];x++){for(var O=
 "",G=0;G<i[1];G++){var P=this._daylightSavingAdjust(new Date(m,g,a.selectedDay)),t=" ui-corner-all",y="";if(l){y+='<div class="ui-datepicker-group';if(i[1]>1)switch(G){case 0:y+=" ui-datepicker-group-first";t=" ui-corner-"+(c?"right":"left");break;case i[1]-1:y+=" ui-datepicker-group-last";t=" ui-corner-"+(c?"left":"right");break;default:y+=" ui-datepicker-group-middle";t="";break}y+='">'}y+='<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix'+t+'">'+(/all|left/.test(t)&&x==0?c?
 f:n:"")+(/all|right/.test(t)&&x==0?c?n:f:"")+this._generateMonthYearHeader(a,g,m,k,o,x>0||G>0,A,v)+'</div><table class="ui-datepicker-calendar"><thead><tr>';var D=j?'<th class="ui-datepicker-week-col">'+this._get(a,"weekHeader")+"</th>":"";for(t=0;t<7;t++){var q=(t+h)%7;D+="<th"+((t+h+6)%7>=5?' class="ui-datepicker-week-end"':"")+'><span title="'+r[q]+'">'+s[q]+"</span></th>"}y+=D+"</tr></thead><tbody>";D=this._getDaysInMonth(m,g);if(m==a.selectedYear&&g==a.selectedMonth)a.selectedDay=Math.min(a.selectedDay,
 D);t=(this._getFirstDayOfMonth(m,g)-h+7)%7;D=l?6:Math.ceil((t+D)/7);q=this._daylightSavingAdjust(new Date(m,g,1-t));for(var Q=0;Q<D;Q++){y+="<tr>";var R=!j?"":'<td class="ui-datepicker-week-col">'+this._get(a,"calculateWeek")(q)+"</td>";for(t=0;t<7;t++){var I=p?p.apply(a.input?a.input[0]:null,[q]):[true,""],F=q.getMonth()!=g,L=F&&!K||!I[0]||k&&q<k||o&&q>o;R+='<td class="'+((t+h+6)%7>=5?" ui-datepicker-week-end":"")+(F?" ui-datepicker-other-month":"")+(q.getTime()==P.getTime()&&g==a.selectedMonth&&
 a._keyEvent||E.getTime()==q.getTime()&&E.getTime()==P.getTime()?" "+this._dayOverClass:"")+(L?" "+this._unselectableClass+" ui-state-disabled":"")+(F&&!C?"":" "+I[1]+(q.getTime()==u.getTime()?" "+this._currentClass:"")+(q.getTime()==b.getTime()?" ui-datepicker-today":""))+'"'+((!F||C)&&I[2]?' title="'+I[2]+'"':"")+(L?"":' onclick="DP_jQuery_'+z+".datepicker._selectDay('#"+a.id+"',"+q.getMonth()+","+q.getFullYear()+', this);return false;"')+">"+(F&&!C?"&#xa0;":L?'<span class="ui-state-default">'+q.getDate()+
 "</span>":'<a class="ui-state-default'+(q.getTime()==b.getTime()?" ui-state-highlight":"")+(q.getTime()==u.getTime()?" ui-state-active":"")+(F?" ui-priority-secondary":"")+'" href="#">'+q.getDate()+"</a>")+"</td>";q.setDate(q.getDate()+1);q=this._daylightSavingAdjust(q)}y+=R+"</tr>"}g++;if(g>11){g=0;m++}y+="</tbody></table>"+(l?"</div>"+(i[0]>0&&G==i[1]-1?'<div class="ui-datepicker-row-break"></div>':""):"");O+=y}w+=O}w+=e+(d.browser.msie&&parseInt(d.browser.version,10)<7&&!a.inline?'<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>':
 "");a._keyEvent=false;return w},_generateMonthYearHeader:function(a,b,c,e,f,h,i,g){var j=this._get(a,"changeMonth"),l=this._get(a,"changeYear"),u=this._get(a,"showMonthAfterYear"),k='<div class="ui-datepicker-title">',o="";if(h||!j)o+='<span class="ui-datepicker-month">'+i[b]+"</span>";else{i=e&&e.getFullYear()==c;var m=f&&f.getFullYear()==c;o+='<select class="ui-datepicker-month" onchange="DP_jQuery_'+z+".datepicker._selectMonthYear('#"+a.id+"', this, 'M');\" onclick=\"DP_jQuery_"+z+".datepicker._clickMonthYear('#"+
 a.id+"');\">";for(var n=0;n<12;n++)if((!i||n>=e.getMonth())&&(!m||n<=f.getMonth()))o+='<option value="'+n+'"'+(n==b?' selected="selected"':"")+">"+g[n]+"</option>";o+="</select>"}u||(k+=o+(h||!(j&&l)?"&#xa0;":""));if(!a.yearshtml){a.yearshtml="";if(h||!l)k+='<span class="ui-datepicker-year">'+c+"</span>";else{g=this._get(a,"yearRange").split(":");var r=(new Date).getFullYear();i=function(s){s=s.match(/c[+-].*/)?c+parseInt(s.substring(1),10):s.match(/[+-].*/)?r+parseInt(s,10):parseInt(s,10);return isNaN(s)?
 r:s};b=i(g[0]);g=Math.max(b,i(g[1]||""));b=e?Math.max(b,e.getFullYear()):b;g=f?Math.min(g,f.getFullYear()):g;for(a.yearshtml+='<select class="ui-datepicker-year" onchange="DP_jQuery_'+z+".datepicker._selectMonthYear('#"+a.id+"', this, 'Y');\" onclick=\"DP_jQuery_"+z+".datepicker._clickMonthYear('#"+a.id+"');\">";b<=g;b++)a.yearshtml+='<option value="'+b+'"'+(b==c?' selected="selected"':"")+">"+b+"</option>";a.yearshtml+="</select>";k+=a.yearshtml;a.yearshtml=null}}k+=this._get(a,"yearSuffix");if(u)k+=
 (h||!(j&&l)?"&#xa0;":"")+o;k+="</div>";return k},_adjustInstDate:function(a,b,c){var e=a.drawYear+(c=="Y"?b:0),f=a.drawMonth+(c=="M"?b:0);b=Math.min(a.selectedDay,this._getDaysInMonth(e,f))+(c=="D"?b:0);e=this._restrictMinMax(a,this._daylightSavingAdjust(new Date(e,f,b)));a.selectedDay=e.getDate();a.drawMonth=a.selectedMonth=e.getMonth();a.drawYear=a.selectedYear=e.getFullYear();if(c=="M"||c=="Y")this._notifyChange(a)},_restrictMinMax:function(a,b){var c=this._getMinMaxDate(a,"min");a=this._getMinMaxDate(a,
 "max");b=c&&b<c?c:b;return b=a&&b>a?a:b},_notifyChange:function(a){var b=this._get(a,"onChangeMonthYear");if(b)b.apply(a.input?a.input[0]:null,[a.selectedYear,a.selectedMonth+1,a])},_getNumberOfMonths:function(a){a=this._get(a,"numberOfMonths");return a==null?[1,1]:typeof a=="number"?[1,a]:a},_getMinMaxDate:function(a,b){return this._determineDate(a,this._get(a,b+"Date"),null)},_getDaysInMonth:function(a,b){return 32-this._daylightSavingAdjust(new Date(a,b,32)).getDate()},_getFirstDayOfMonth:function(a,
 b){return(new Date(a,b,1)).getDay()},_canAdjustMonth:function(a,b,c,e){var f=this._getNumberOfMonths(a);c=this._daylightSavingAdjust(new Date(c,e+(b<0?b:f[0]*f[1]),1));b<0&&c.setDate(this._getDaysInMonth(c.getFullYear(),c.getMonth()));return this._isInRange(a,c)},_isInRange:function(a,b){var c=this._getMinMaxDate(a,"min");a=this._getMinMaxDate(a,"max");return(!c||b.getTime()>=c.getTime())&&(!a||b.getTime()<=a.getTime())},_getFormatConfig:function(a){var b=this._get(a,"shortYearCutoff");b=typeof b!=
 "string"?b:(new Date).getFullYear()%100+parseInt(b,10);return{shortYearCutoff:b,dayNamesShort:this._get(a,"dayNamesShort"),dayNames:this._get(a,"dayNames"),monthNamesShort:this._get(a,"monthNamesShort"),monthNames:this._get(a,"monthNames")}},_formatDate:function(a,b,c,e){if(!b){a.currentDay=a.selectedDay;a.currentMonth=a.selectedMonth;a.currentYear=a.selectedYear}b=b?typeof b=="object"?b:this._daylightSavingAdjust(new Date(e,c,b)):this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));
 return this.formatDate(this._get(a,"dateFormat"),b,this._getFormatConfig(a))}});d.fn.datepicker=function(a){if(!this.length)return this;if(!d.datepicker.initialized){d(document).mousedown(d.datepicker._checkExternalClick).find("body").append(d.datepicker.dpDiv);d.datepicker.initialized=true}var b=Array.prototype.slice.call(arguments,1);if(typeof a=="string"&&(a=="isDisabled"||a=="getDate"||a=="widget"))return d.datepicker["_"+a+"Datepicker"].apply(d.datepicker,[this[0]].concat(b));if(a=="option"&&
 arguments.length==2&&typeof arguments[1]=="string")return d.datepicker["_"+a+"Datepicker"].apply(d.datepicker,[this[0]].concat(b));return this.each(function(){typeof a=="string"?d.datepicker["_"+a+"Datepicker"].apply(d.datepicker,[this].concat(b)):d.datepicker._attachDatepicker(this,a)})};d.datepicker=new M;d.datepicker.initialized=false;d.datepicker.uuid=(new Date).getTime();d.datepicker.version="1.8.13";window["DP_jQuery_"+z]=d})(jQuery);
 ;
/*
 * jQuery UI Autocomplete 1.8.13
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Autocomplete
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *	jquery.ui.position.js
 */
(function(d){var e=0;d.widget("ui.autocomplete",{options:{appendTo:"body",autoFocus:false,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null},pending:0,_create:function(){var a=this,b=this.element[0].ownerDocument,g;this.element.addClass("ui-autocomplete-input").attr("autocomplete","off").attr({role:"textbox","aria-autocomplete":"list","aria-haspopup":"true"}).bind("keydown.autocomplete",function(c){if(!(a.options.disabled||a.element.attr("readonly"))){g=
false;var f=d.ui.keyCode;switch(c.keyCode){case f.PAGE_UP:a._move("previousPage",c);break;case f.PAGE_DOWN:a._move("nextPage",c);break;case f.UP:a._move("previous",c);c.preventDefault();break;case f.DOWN:a._move("next",c);c.preventDefault();break;case f.ENTER:case f.NUMPAD_ENTER:if(a.menu.active){g=true;c.preventDefault()}case f.TAB:if(!a.menu.active)return;a.menu.select(c);break;case f.ESCAPE:a.element.val(a.term);a.close(c);break;default:clearTimeout(a.searching);a.searching=setTimeout(function(){if(a.term!=
a.element.val()){a.selectedItem=null;a.search(null,c)}},a.options.delay);break}}}).bind("keypress.autocomplete",function(c){if(g){g=false;c.preventDefault()}}).bind("focus.autocomplete",function(){if(!a.options.disabled){a.selectedItem=null;a.previous=a.element.val()}}).bind("blur.autocomplete",function(c){if(!a.options.disabled){clearTimeout(a.searching);a.closing=setTimeout(function(){a.close(c);a._change(c)},150)}});this._initSource();this.response=function(){return a._response.apply(a,arguments)};
this.menu=d("<ul></ul>").addClass("ui-autocomplete").appendTo(d(this.options.appendTo||"body",b)[0]).mousedown(function(c){var f=a.menu.element[0];d(c.target).closest(".ui-menu-item").length||setTimeout(function(){d(document).one("mousedown",function(h){h.target!==a.element[0]&&h.target!==f&&!d.ui.contains(f,h.target)&&a.close()})},1);setTimeout(function(){clearTimeout(a.closing)},13)}).menu({focus:function(c,f){f=f.item.data("item.autocomplete");false!==a._trigger("focus",c,{item:f})&&/^key/.test(c.originalEvent.type)&&
a.element.val(f.value)},selected:function(c,f){var h=f.item.data("item.autocomplete"),i=a.previous;if(a.element[0]!==b.activeElement){a.element.focus();a.previous=i;setTimeout(function(){a.previous=i;a.selectedItem=h},1)}false!==a._trigger("select",c,{item:h})&&a.element.val(h.value);a.term=a.element.val();a.close(c);a.selectedItem=h},blur:function(){a.menu.element.is(":visible")&&a.element.val()!==a.term&&a.element.val(a.term)}}).zIndex(this.element.zIndex()+1).css({top:0,left:0}).hide().data("menu");
d.fn.bgiframe&&this.menu.element.bgiframe()},destroy:function(){this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete").removeAttr("role").removeAttr("aria-autocomplete").removeAttr("aria-haspopup");this.menu.element.remove();d.Widget.prototype.destroy.call(this)},_setOption:function(a,b){d.Widget.prototype._setOption.apply(this,arguments);a==="source"&&this._initSource();if(a==="appendTo")this.menu.element.appendTo(d(b||"body",this.element[0].ownerDocument)[0]);a==="disabled"&&
b&&this.xhr&&this.xhr.abort()},_initSource:function(){var a=this,b,g;if(d.isArray(this.options.source)){b=this.options.source;this.source=function(c,f){f(d.ui.autocomplete.filter(b,c.term))}}else if(typeof this.options.source==="string"){g=this.options.source;this.source=function(c,f){a.xhr&&a.xhr.abort();a.xhr=d.ajax({url:g,data:c,dataType:"json",autocompleteRequest:++e,success:function(h){this.autocompleteRequest===e&&f(h)},error:function(){this.autocompleteRequest===e&&f([])}})}}else this.source=
this.options.source},search:function(a,b){a=a!=null?a:this.element.val();this.term=this.element.val();if(a.length<this.options.minLength)return this.close(b);clearTimeout(this.closing);if(this._trigger("search",b)!==false)return this._search(a)},_search:function(a){this.pending++;this.element.addClass("ui-autocomplete-loading");this.source({term:a},this.response)},_response:function(a){if(!this.options.disabled&&a&&a.length){a=this._normalize(a);this._suggest(a);this._trigger("open")}else this.close();
this.pending--;this.pending||this.element.removeClass("ui-autocomplete-loading")},close:function(a){clearTimeout(this.closing);if(this.menu.element.is(":visible")){this.menu.element.hide();this.menu.deactivate();this._trigger("close",a)}},_change:function(a){this.previous!==this.element.val()&&this._trigger("change",a,{item:this.selectedItem})},_normalize:function(a){if(a.length&&a[0].label&&a[0].value)return a;return d.map(a,function(b){if(typeof b==="string")return{label:b,value:b};return d.extend({label:b.label||
b.value,value:b.value||b.label},b)})},_suggest:function(a){var b=this.menu.element.empty().zIndex(this.element.zIndex()+1);this._renderMenu(b,a);this.menu.deactivate();this.menu.refresh();b.show();this._resizeMenu();b.position(d.extend({of:this.element},this.options.position));this.options.autoFocus&&this.menu.next(new d.Event("mouseover"))},_resizeMenu:function(){var a=this.menu.element;a.outerWidth(Math.max(a.width("").outerWidth(),this.element.outerWidth()))},_renderMenu:function(a,b){var g=this;
d.each(b,function(c,f){g._renderItem(a,f)})},_renderItem:function(a,b){return d("<li></li>").data("item.autocomplete",b).append(d("<a></a>").text(b.label)).appendTo(a)},_move:function(a,b){if(this.menu.element.is(":visible"))if(this.menu.first()&&/^previous/.test(a)||this.menu.last()&&/^next/.test(a)){this.element.val(this.term);this.menu.deactivate()}else this.menu[a](b);else this.search(null,b)},widget:function(){return this.menu.element}});d.extend(d.ui.autocomplete,{escapeRegex:function(a){return a.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,
"\\$&")},filter:function(a,b){var g=new RegExp(d.ui.autocomplete.escapeRegex(b),"i");return d.grep(a,function(c){return g.test(c.label||c.value||c)})}})})(jQuery);
(function(d){d.widget("ui.menu",{_create:function(){var e=this;this.element.addClass("ui-menu ui-widget ui-widget-content ui-corner-all").attr({role:"listbox","aria-activedescendant":"ui-active-menuitem"}).click(function(a){if(d(a.target).closest(".ui-menu-item a").length){a.preventDefault();e.select(a)}});this.refresh()},refresh:function(){var e=this;this.element.children("li:not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role","menuitem").children("a").addClass("ui-corner-all").attr("tabindex",
-1).mouseenter(function(a){e.activate(a,d(this).parent())}).mouseleave(function(){e.deactivate()})},activate:function(e,a){this.deactivate();if(this.hasScroll()){var b=a.offset().top-this.element.offset().top,g=this.element.scrollTop(),c=this.element.height();if(b<0)this.element.scrollTop(g+b);else b>=c&&this.element.scrollTop(g+b-c+a.height())}this.active=a.eq(0).children("a").addClass("ui-state-hover").attr("id","ui-active-menuitem").end();this._trigger("focus",e,{item:a})},deactivate:function(){if(this.active){this.active.children("a").removeClass("ui-state-hover").removeAttr("id");
this._trigger("blur");this.active=null}},next:function(e){this.move("next",".ui-menu-item:first",e)},previous:function(e){this.move("prev",".ui-menu-item:last",e)},first:function(){return this.active&&!this.active.prevAll(".ui-menu-item").length},last:function(){return this.active&&!this.active.nextAll(".ui-menu-item").length},move:function(e,a,b){if(this.active){e=this.active[e+"All"](".ui-menu-item").eq(0);e.length?this.activate(b,e):this.activate(b,this.element.children(a))}else this.activate(b,
this.element.children(a))},nextPage:function(e){if(this.hasScroll())if(!this.active||this.last())this.activate(e,this.element.children(".ui-menu-item:first"));else{var a=this.active.offset().top,b=this.element.height(),g=this.element.children(".ui-menu-item").filter(function(){var c=d(this).offset().top-a-b+d(this).height();return c<10&&c>-10});g.length||(g=this.element.children(".ui-menu-item:last"));this.activate(e,g)}else this.activate(e,this.element.children(".ui-menu-item").filter(!this.active||
this.last()?":first":":last"))},previousPage:function(e){if(this.hasScroll())if(!this.active||this.first())this.activate(e,this.element.children(".ui-menu-item:last"));else{var a=this.active.offset().top,b=this.element.height();result=this.element.children(".ui-menu-item").filter(function(){var g=d(this).offset().top-a+b-d(this).height();return g<10&&g>-10});result.length||(result=this.element.children(".ui-menu-item:first"));this.activate(e,result)}else this.activate(e,this.element.children(".ui-menu-item").filter(!this.active||
this.first()?":last":":first"))},hasScroll:function(){return this.element.height()<this.element[d.fn.prop?"prop":"attr"]("scrollHeight")},select:function(e){this._trigger("selected",e,{item:this.active})}})})(jQuery);
;

 /* jQuery Tools 1.2.5 Overlay & Expose */
 (function(a){function t(d,b){var c=this,j=d.add(c),o=a(window),k,f,m,g=a.tools.expose&&(b.mask||b.expose),n=Math.random().toString().slice(10);if(g){if(typeof g=="string")g={color:g};g.closeOnClick=g.closeOnEsc=false}var p=b.target||d.attr("rel");f=p?a(p):d;if(!f.length)throw"Could not find Overlay: "+p;d&&d.index(f)==-1&&d.click(function(e){c.load(e);return e.preventDefault()});a.extend(c,{load:function(e){if(c.isOpened())return c;var h=q[b.effect];if(!h)throw'Overlay: cannot find effect : "'+b.effect+
 '"';b.oneInstance&&a.each(s,function(){this.close(e)});e=e||a.Event();e.type="onBeforeLoad";j.trigger(e);if(e.isDefaultPrevented())return c;m=true;g&&a(f).expose(g);var i=b.top,r=b.left,u=f.outerWidth({margin:true}),v=f.outerHeight({margin:true});if(typeof i=="string")i=i=="center"?Math.max((o.height()-v)/2,0):parseInt(i,10)/100*o.height();if(r=="center")r=Math.max((o.width()-u)/2,0);h[0].call(c,{top:i,left:r},function(){if(m){e.type="onLoad";j.trigger(e)}});g&&b.closeOnClick&&a.mask.getMask().one("click",
 c.close);b.closeOnClick&&a(document).bind("click."+n,function(l){a(l.target).parents(f).length||c.close(l)});b.closeOnEsc&&a(document).bind("keydown."+n,function(l){l.keyCode==27&&c.close(l)});return c},close:function(e){if(!c.isOpened())return c;e=e||a.Event();e.type="onBeforeClose";j.trigger(e);if(!e.isDefaultPrevented()){m=false;q[b.effect][1].call(c,function(){e.type="onClose";j.trigger(e)});a(document).unbind("click."+n).unbind("keydown."+n);g&&a.mask.close();return c}},getOverlay:function(){return f},
 getTrigger:function(){return d},getClosers:function(){return k},isOpened:function(){return m},getConf:function(){return b}});a.each("onBeforeLoad,onStart,onLoad,onBeforeClose,onClose".split(","),function(e,h){a.isFunction(b[h])&&a(c).bind(h,b[h]);c[h]=function(i){i&&a(c).bind(h,i);return c}});k=f.find(b.close||".close");if(!k.length&&!b.close){k=a('<a class="close"></a>');f.prepend(k)}k.click(function(e){c.close(e)});b.load&&c.load()}a.tools=a.tools||{version:"1.2.5"};a.tools.overlay={addEffect:function(d,
 b,c){q[d]=[b,c]},conf:{close:null,closeOnClick:true,closeOnEsc:true,closeSpeed:"fast",effect:"default",fixed:!a.browser.msie||a.browser.version>6,left:"center",load:false,mask:null,oneInstance:true,speed:"normal",target:null,top:"10%"}};var s=[],q={};a.tools.overlay.addEffect("default",function(d,b){var c=this.getConf(),j=a(window);if(!c.fixed){d.top+=j.scrollTop();d.left+=j.scrollLeft()}d.position=c.fixed?"fixed":"absolute";this.getOverlay().css(d).fadeIn(c.speed,b)},function(d){this.getOverlay().fadeOut(this.getConf().closeSpeed,
 d)});a.fn.overlay=function(d){var b=this.data("overlay");if(b)return b;if(a.isFunction(d))d={onBeforeLoad:d};d=a.extend(true,{},a.tools.overlay.conf,d);this.each(function(){b=new t(a(this),d);s.push(b);a(this).data("overlay",b)});return d.api?b:this}})(jQuery);
 (function(b){function k(){if(b.browser.msie){var a=b(document).height(),d=b(window).height();return[window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,a-d<20?d:a]}return[b(document).width(),b(document).height()]}function h(a){if(a)return a.call(b.mask)}b.tools=b.tools||{version:"1.2.5"};var l;l=b.tools.expose={conf:{maskId:"exposeMask",loadSpeed:"slow",closeSpeed:"fast",closeOnClick:true,closeOnEsc:true,zIndex:9998,opacity:0.8,startOpacity:0,color:"#fff",onLoad:null,
 onClose:null}};var c,i,e,g,j;b.mask={load:function(a,d){if(e)return this;if(typeof a=="string")a={color:a};a=a||g;g=a=b.extend(b.extend({},l.conf),a);c=b("#"+a.maskId);if(!c.length){c=b("<div/>").attr("id",a.maskId);b("body").append(c)}var m=k();c.css({position:"absolute",top:0,left:0,width:m[0],height:m[1],display:"none",opacity:a.startOpacity,zIndex:a.zIndex});a.color&&c.css("backgroundColor",a.color);if(h(a.onBeforeLoad)===false)return this;a.closeOnEsc&&b(document).bind("keydown.mask",function(f){f.keyCode==
 27&&b.mask.close(f)});a.closeOnClick&&c.bind("click.mask",function(f){b.mask.close(f)});b(window).bind("resize.mask",function(){b.mask.fit()});if(d&&d.length){j=d.eq(0).css("zIndex");b.each(d,function(){var f=b(this);/relative|absolute|fixed/i.test(f.css("position"))||f.css("position","relative")});i=d.css({zIndex:Math.max(a.zIndex+1,j=="auto"?0:j)})}c.css({display:"block"}).fadeTo(a.loadSpeed,a.opacity,function(){b.mask.fit();h(a.onLoad);e="full"});e=true;return this},close:function(){if(e){if(h(g.onBeforeClose)===
 false)return this;c.fadeOut(g.closeSpeed,function(){h(g.onClose);i&&i.css({zIndex:j});e=false});b(document).unbind("keydown.mask");c.unbind("click.mask");b(window).unbind("resize.mask")}return this},fit:function(){if(e){var a=k();c.css({width:a[0],height:a[1]})}},getMask:function(){return c},isLoaded:function(a){return a?e=="full":e},getConf:function(){return g},getExposed:function(){return i}};b.fn.mask=function(a){b.mask.load(a);return this};b.fn.expose=function(a){b.mask.load(a,this);return this}})(jQuery);