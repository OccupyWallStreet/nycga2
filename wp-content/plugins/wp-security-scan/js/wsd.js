if(console===undefined){var console={log:function(){return;}};}


function wsdPassStrengthProvider($) {
	this.badPass=['abc123','password','computer','123456','tigger','a1b2c3','qwerty','password1','carmen','mickey','secret','summer','internet','service','canada','ranger','shadow','baseball','donald','harley','hockey','letmein','maggie','mustang','snoopy','buster','dragon','jordan','michael','michelle','patrick','123abc','andrew','calvin','changeme','diamond','fuckme','fuckyou','matthew','miller','trustno1','12345678','123456789','avalon','brandy','chelsea','coffee','falcon','freedom','gandalf','helpme','merlin','molson','newyork','soccer','thomas','wizard','Monday','asdfgh','bandit','batman','butthead','dorothy','eeyore','fishing',
'football','george','iloveyou','jennifer','jonathan','marina','master','monday','monkey','natasha','ncc1701','newpass','pamela','pepper','piglet','poohbear','pookie','rabbit','rachel','rocket','sparky','spring','steven','success','sunshine','thx1138','victoria','whatever','zapata','8675309','Internet','amanda','august','barney','biteme','boomer','cowboy','doctor','fisher','foobar','island','joshua','marley','orange','please','rascal','richard','scooter','shalom','silver','skippy','stanley','taylor','welcome','zephyr','111111','aaaaaa','access','albert','alexander','andrea','anthony','asdfjkl;','ashley','basketball',
'beavis','booboo','bradley','brandon','caitlin','camaro','charlie','chicken','cricket','dakota','dallas','daniel','debbie','dolphin','elephant','friend','fucker','ginger','goodluck','hammer','heather','iceman','jessica','joseph','jupiter','justin','knight','lacrosse','lakers','lizard','madison','mother','muffin','murphy','ncc1701d','newuser','nirvana','pentium','phoenix','picture','rainbow','saturn','shannon','shithead','skeeter','sophie','special','stephanie','stephen','sweetie','teacher','tennis','test123','topgun','tristan','william','wilson','1q2w3e','654321','666666','a12345','a1b2c3d4','angela','archie','blazer',
'bond007','booger','charles','christin','claire','control','david1','dennis','digital','disney','edward','flipper','franklin','horses','hunter','indigo','jasper','jeremy','julian','kelsey','killer','kingfish','lauren','maryjane','matrix','maverick','mayday','mercury','mitchell','morgan','mountain','niners','nothing','oliver','peanut','pearljam','phantom','popcorn','princess','psycho','pumpkin','purple','rebecca','reddog','robert','salmon','samson','sharon','sierra','smokey','startrek','steelers','stimpy','sunflower','superman','support','sydney','techno','telecom','walter','willie','willow','winner','zxcvbnm','absolut',
'alaska','alexis','animal','apples','babylon5','backup','barbara','benjamin','bird33','bluebird','bonnie','camera','chocolate','claudia','cocacola','compton','connect','cookie','cruise','deliver','douglas','dreamer','dreams','duckie','eagles','einstein','explorer','family','ferrari','flamingo','flower','foxtrot','francis','freddy','friday','froggy','galileo','giants','global','gopher','hansolo','happy1','hendrix','herman','houston','iguana','indiana','insane','inside','ironman','jasmin','jeanne','justice','katherine','kermit','leslie','martin','minnie','nascar','nelson','netware','pantera','parker','passwd','penguin',
'porsche911','prince','punkin','pyramid','raymond','rosebud','route66','running','security','sergei','sheena','sheila','skiing','snapple','snowball','sparrow','spencer','stealth','student','sylvia','tamara','taurus','teresa','theresa','thunderbird','tigers','toyota','training','travel','tuesday','victory','viper1','wesley','whisky','winnie','winter','wolves','xyz123','123123','1234567','696969','888888','Anthony','Bond007','Friday','Hendrix','Joshua','Matthew','October','Taurus','Tigger','abcdef','adidas','adrian','alexandr','alfred','arthur','athena','austin','awesome','badger','bamboo','beagle','beatles','beautiful',
'beaver','bigmac','blonde','boogie','boston','brenda','bright','bubba1','bubbles','button','buttons','cactus','captain','carlos','caroline','carrie','casper','catalog','catch22','challenge','chance','charity','charlotte','cheese','cheryl','chris1','clancy','clipper','coltrane','compaq','conrad','cooper','cooter','copper','cosmos','cougar','cracker','crawford','crystal','curtis','cyclone','cyrano','deutsch','diablo','dilbert','dollars','dookie','dumbass','dundee','e-mail','elizabeth','europe','export','farmer','firebird','fletcher','fluffy','fountain','france','freak1','friends','fuckoff','gabriel','gabriell','galaxy',
'gambit','garden','garfield','garlic','garnet','genesis','genius','godzilla','goforit','golfer','goober','grateful','greenday','groovy','grover','guitar','hacker','hector','herbert','horizon','hornet','howard','icecream','imagine','impala','informix','janice','jasmine','jason1','jeanette','jeffrey','jenifer','jesus1','jewels','julie1','junior','justin1','kathleen','kelly1','kennedy','kevin1','knicks','larry1','ledzep','leonard','lestat','library','lincoln','lionking','london','louise','lucky1','maddog','mailman','majordomo','mantra','margaret','mariposa','market','marlboro','martin1','master1','mazda1','mensuck','mercedes',
'metallic','midori','millie','mirage','money1','monica','monopoly','mookie','moroni','nathan','ncc1701e','nesbitt','nguyen','nicholas','nicole','nimrod','october','olivia','online','oxford','pacific','painter','peaches','penelope','petunia','philip','phoenix1','pickle','player','poiuyt','porsche','porter','python','quality','raquel','remember','republic','research','robbie','robert1','runner','russell','sailing','sailor','samantha','savage','scarlett','school','shadow1','shelby','simple','skipper','smiley','snickers','sniper','snoopdog','snowman','spitfire','sprite','spunky','starwars','station','stella','stingray',
'stormy','stupid','sumuinen','sunny1','sunrise','surfer','teddy1','testing','theboss','theking','thumper','tintin','tomcat','trebor','trevor','tweety','unicorn','valentine','valerie','vanilla','veronica','victor','vincent','warrior','warriors','weasel','wheels','wilbur','winston','wisdom','wombat','xanadu','xavier','yellow','zaphod','zeppelin','!@#$%^','!@#$%^&*','10sne1','1p2o3i','3bears','Andrew','Broadway','Champs','Family','Fisher','Friends','Jeanne','Killer','Knight','Master','Michael','Michelle','Pentium','Pepper','Raistlin','Sierra','Snoopy','Tennis','Tuesday','abacab','abcd1234','abcdefg','abigail','account',
'acropolis','alice1','allison','alpine','anders','andre1','andrea1','angel1','annette','antares','apache','apollo','aragorn','arizona','arnold','arsenal','asdfasdf','asdfghjk','avenger','avenir','babydoll','bailey','banana','basket','batman1','beaner','beatrice','bertha','bigben','bigdog','biggles','bigman','biology','bishop','blondie','blowfish','bluefish','bobcat','braves','brazil','bridges','brutus','buffalo','bulldog','bullet','bullshit','business','butler','butter','california','cannondale','carebear','carol1','carole','cassie','castle','catalina','catherine','catnip','cccccc','celine','center','champion','chanel',
'chelsea1','chester1','chicago','christian','christy','church','cinder','colleen','colorado','columbia','commander','connie','content','cookies','cooking','cordelia','corona','cowboys','coyote','crack1','creative','cuddles','cuervo','daisie','daniel1','danielle','database','davids','deadhead','denali','depeche','design','destiny','dickens','dickhead','digger','dodger','dougie','dragonfly','eclipse','electric','emerald','emmitt','entropy','etoile','excalibur','express','farout','farside','feedback','fender','fireman','firenze','fletch','florida','flowers','foster','fozzie','francesco','francine','francois','french','fuckface',
'gargoyle','gasman','gemini','general','gerald','germany','gilbert','goaway','golden','goldfish','gordon','graham','graphic','gregory','gretchen','gunner','hal9000','hannah','harold','harrison','harvey','hawkeye','heaven','helena','herzog','hithere','hobbit','ibanez','idontknow','integra','intern','intrepid','ireland','isabel','jackie','jackson','jaguar','jamaica','jenny1','jessie','jethrotull','jkl123','johanna1','johnny','joker1','jordan23','judith','jumanji','kangaroo','karen1','keepout','keith1','kenneth','kidder','kimberly','kingdom','kitkat','kramer','kristen','lambda','laurie','lawrence','lawyer','legend','liberty',
'lindsay','lindsey','liverpool','logical','lonely','lorrie','lovely','loveme','madonna','malcolm','malibu','marathon','marcel','maria1','mariah','mariah1','marilyn','mariner','marvin','maurice','maxine','maxwell','meggie','melanie','melissa','melody','merlot','mexico','michael1','michele','midnight','midway','miracle','mishka','mmouse','molly1','monique','montreal','moocow','morris','mortimer','mouse1','mulder','nautica','nellie','nermal','newton','nicarao','nirvana1','nissan','norman','notebook','olivier','oranges','oregon','overkill','pacers','packer','pandora','panther','passion','patricia','peewee','pencil','people',
'person','peter1','picard','picasso','pierre','pinkfloyd','polaris','police','pookie1','predator','preston','primus','prometheus','public','q1w2e3','queenie','quentin','random','rangers','raptor','rastafarian','reality','redrum','remote','reptile','reynolds','rhonda','ricardo','ricardo1','roadrunner','robinhood','robotech','rocknroll','rocky1','ronald','ruthie','sabrina','sakura','salasana','sampson','samuel','sandra','sapphire','scarecrow','scarlet','scorpio','scott1','scottie','scruffy','scuba1','seattle','serena','sergey','shanti','shogun','singer','skibum','skywalker','slacker','smashing','smiles','snowflake','snowski',
'snuffy','soccer1','soleil','spanky','speedy','spider','spooky','stacey','star69','starter','steven1','sting1','stinky','strawberry','stuart','sunbird','sundance','superfly','suzanne','suzuki','swimmer','swimming','system','tarzan','teddybear','teflon','temporal','terminal','theatre','thejudge','thunder','thursday','tinker','tootsie','tornado','tricia','trident','trojan','truman','trumpet','tucker','turtle','utopia','valhalla','voyager','warcraft','warlock','warren','williams','windsurf','winona','woofwoof','wrangler','wright','xcountry','xfiles','xxxxxx','yankees','yvonne','zenith','zigzag','zombie','zxc123','000000',
'007007','11111111','123321','171717','181818','1a2b3c','1chris','1kitty','1qw23e','4runner','57chevy','7777777','789456','7dwarfs','88888888','Abcdefg','Alexis','Animals','Bailey','Bastard','Beavis','Bismillah','Booboo','Boston','Canucks','Cardinal','Celtics','ChangeMe','Charlie','Computer','Cougar','Creative','Curtis','Daniel','Darkman','Denise','Dragon','Eagles','Elizabeth','Esther','Figaro','Fishing','Fortune','Freddy','Front242','Gandalf','Geronimo','Gingers','Golden','Goober','Gretel','HARLEY','Hacker','Hammer','Harley','Heather','Hershey','Jackson','Jennifer','Jersey','Jessica','Joanna','Johnson','Jordan','KILLER',
'Kitten','Liberty','Lindsay','Lizard','Madeline','Margaret','Maxwell','Mellon','Merlot','Metallic','Michel1','Monster','Montreal','Newton','Nicholas','Noriko','Paladin','Pamela','Password','Peaches','Peanuts','Phoenix','Piglet','Pookie','Princess','Purple','Rabbit','Raiders','Random','Rebecca','Robert','Russell','Saturn','Service','Shadow','Sidekick','Skeeter','Smokey','Sparky','Speedy','Sterling','Steven','Summer','Sunshine','Superman','Sverige','Swoosh','Taylor','Theresa','Thomas','Thunder','Vernon','Victoria','Vincent','Waterloo','Webster','Willow','Winnie','Wolverine','Woodrow','aardvark','abbott','abcd123','accord',
'active','admin1','adrock','aerobics','africa','airborne','airwolf','aki123','alfaro','alicia','aliens','alison','allegro','allstate','alpha1','altamira','althea','altima','altima1','amanda1','amazing','america','anderson','andrew!','andrew1','andromed','angels','angie1','anneli','anything','apple1','apple2','applepie','aptiva','aquarius','ariane','arlene','artemis','asdf1234','asdf;lkj','asdfjkl','ashley1','ashraf','ashton','assmunch','asterix','attila','autumn','avatar','ayelet','aylmer','baraka','barbie','barney1','barnyard','barrett','bartman','beaches','beanie','beasty','beauty','beavis1','belgium','belize','belmont',
'benson','beowulf','bernardo','betacam','bharat','bichon','bigboss','bigred','billy1','bimmer','bioboy','biochem','birdie','birthday','biscuit','bitter','blackjack','blanche','blinds','blowjob','blowme','blueeyes','bluejean','bogart','bombay','boobie','bootsie','boulder','bourbon','boxers','branch','brandi','brewster','bridge','britain','broker','bronco','bronte','brooke','brother','bubble','buddha','budgie','buffett','burton','butterfly','c00per','calendar','calgary','calvin1','camille','campbell','camping','cancer','canela','cannon','carbon','carnage','carolyn','carrot','cascade','catfish','catwoman','cecile','celica',
'cement','cessna','chainsaw','chameleon','change','chantal','charger','cherry','chiara','chiefs','chinacat','chinook','chouette','chris123','christ1','christmas','christopher','chronos','cicero','cindy1','cinema','circuit','cirque','cirrus','clapton','clarkson','claude','claudel','clueless','cobain','colette','college','colors','colt45','concept','concorde','confused','coolbean','cornflake','corvette','corwin','country','courier','crescent','crowley','crusader','cthulhu','cunningham','cupcake','current','cutlass','cynthia','daedalus','dagger','dagger1','dammit','damogran','dancer','daphne','darkstar','darren','darryl',
'darwin','datatrain','daytek','deborah','december','decker','deedee','deeznuts','delano','delete','denise','desert','deskjet','detroit','devine','dexter','dharma','dianne','diesel','dillweed','dipper','director','dodgers','dogbert','doitnow','dollar','dominique','domino','dontknow','doogie','doudou','downtown','dragon1','driver','dudley','dutchess','dwight','eagle1','easter','eastern','edmund','element','elina1','elissa','elliot','empire','engage','enigma','enterprise','ernie1','escort','escort1','estelle','eugene','evelyn','explore','faculty','fairview','family1','fatboy','felipe','fenris','ferguson','ferret','ferris',
'finance','fireball','fishes','fishhead','fishie','flanders','fleurs','flight','florida1','flowerpot','flyboy','forward','franka','freddie','frederic','freebird','freeman','frisco','froggie','froggies','front242','frontier','fugazi','funguy','funtime','future','gaelic','gambler','gammaphi','garcia','garfunkel','gaston','gateway','gateway2','gator1','george1','georgia','german','germany1','getout','ggeorge','gibbons','gibson','gilgamesh','giselle','glider1','gmoney','goblin','goblue','godiva','goethe','gofish','gollum','gramps','grandma','gravis','gremlin','gretzky','grizzly','grumpy','guitar1','gustavo','h2opolo','haggis',
'hailey','halloween','hallowell','hamilton','hamlet','hanson','happy123','happyday','hardcore','harley1','harriet','harris','harvard','hawkeye1','health','health1','heather1','heather2','hedgehog','heikki','helene','hello1','hello123','hello8','hellohello','help123','helper','hermes','heythere','highland','hillary','histoire','history','hitler','hobbes','holiday','homerj','honda1','hongkong','hoosier','hootie','hosehead','hotrod','hudson','hummer','huskies','hydrogen','ib6ub9','if6was9','iforget','ilmari','iloveu','impact','indonesia','ingvar','insight','instruct','integral','iomega','irmeli','isabelle','israel','italia',
'j1l2t3','jackie1','james1','jamesbond','jamjam','jeepster','jeffrey1','jennie','jensen','jesse1','jester','jethro','jetta1','jimbob','joanie','joanna','joelle','john316','jordie','journey','jubilee','juhani','julia2','julien','juliet','junebug','juniper','justdoit','justice4','kalamazo','karine','katerina','katie1','keeper','keller','kendall','kerala','kerrya','ketchup','kissa2','kissme','kitten','kittycat','kkkkkk','kleenex','kombat','kristi','kristine','labtec','laddie','ladybug','laserjet','lassie1','laurel','lawson','leader','leblanc','leland','lester','letter','letters','lexus1','lights','lionel','lissabon','little',
'logger','loislane','lolita','lonestar','longer','longhorn','looney','lovers','loveyou','lucifer','lucky14','macross','macse30','maddie','madmax','madoka','magic1','magnum','maiden','makeitso','mallard','manageme','manson','manuel','marcus','marielle','marine','marino','marshall','martha','matti1','mattingly','maxmax','meatloaf','mechanic','medical','meister','melina','memphis','mercer','mermaid','merrill','michal','michel','michigan','michou','mickel','mickey1','microsoft','midvale','mikael','milano','millenium','million','miranda','miriam','mission','mmmmmm','mobile','mobydick','monkey1','monroe','montana','montana3',
'montrose','moomoo','moonbeam','morecats','morpheus','motorola','movies','mowgli','mozart','mulder1','munchkin','murray','muscle','mustang1','nadine','napoleon','nation','national','nesbit','nestle','neutrino','newaccount','newlife','newyork1','nexus6','nichole','nicklaus','nightshadow','nightwind','nikita','nintendo','nomore','nopass','normal','norton','notta1','nouveau','novell','nugget','number9','numbers','nutmeg','oaxaca','obiwan','obsession','ohshit','oicu812','openup','orchid','orlando','orville','paagal','packard','packers','packrat','paloma','pancake','paradigm','parola','parrot','partner','pascal','patches',
'patriots','pauline','payton','peanuts','pedro1','perfect','performa','peterk','peterpan','phialpha','philips','phillips','phishy','piano1','pianoman','pianos','pierce','pigeon','pioneer','pipeline','piper1','pirate','pisces','playboy','poetic','poetry','pontiac','pookey','popeye','prayer','precious','prelude','premier','printing','provider','puddin','pulsar','pussy1','qqq111','quebec','qwerty12','qwertyui','rabbit1','racerx','rachelle','racoon','rafiki','raleigh','randy1','rasta1','ravens','redcloud','redfish','redman','redskins','redwing','redwood','reggae','reggie','reliant','renegade','rescue','revolution','reznor',
'rhjrjlbk','richard1','richards','richmond','ripper','ripple','roberts','robocop','robotics','rocket1','rockie','rockon','roger1','rogers','roland','rommel','rookie','rootbeer','rossigno','rugger','ruthless','sabbath','sabina','safety','safety1','saigon','samIam','samiam','sammie','samsam','sanjose','saphire','sarah1','saskia','satori','saturday','saturn5','schnapps','science','scooby','scoobydoo','scooter1','scorpion','scotch','scotty','scouts','search','secret3','seeker','september','server','services','seven7','shaggy','shanghai','shanny','shaolin','shasta','shayne','shazam','shelly','shelter','sherry','shirley',
'shorty','shotgun','sidney','sigmachi','signal','signature','simba1','simsim','sinatra','sirius','skipper1','skydive','skyler','slayer','sleepy','slider','smegma','smile1','smiths','smitty','smurfy','snakes','snapper','sober1','solomon','sonics','sophia','sparks','spartan','sphynx','spike1','sponge','sprocket','squash','starbuck','stargate','starlight','steph1','stephi','steve1','stevens','stewart','stivers','stocks','storage','stranger','strato','stretch','strong','student2','studio','stumpy','sucker','suckme','sultan','summit','sunfire','sunset','superstar','surfing','susan1','susanna','sutton','swanson','sweden',
'sweetpea','sweety','switzer','swordfish','system5','t-bone','tabatha','tacobell','taiwan','tamtam','tanner','tapani','targas','target','tarheel','tattoo','tazdevil','tequila','terry1','tester','testtest','thankyou','theend','thelorax','thisisit','thompson','thorne','thrasher','tiger2','tightend','timber','timothy','tinkerbell','topcat','topher','toshiba','tototo','toucan','transfer','transit','transport','trapper','travis','treasure','tricky','triton','trombone','trophy','trouble','trucker','tucson','turbo2','tyler1','ultimate','unique','united','upsilon','ursula','vacation','valley','vampire','vanessa','vedder',
'venice','vermont','victor1','vikram','vincent1','violet','violin','virago','virgil','virginia','vision','visual','volcano','volley','voodoo','vortex','waiting','walden','walleye','wanker','warner','water1','wayne1','webmaster','webster','weezer','wendy1','western','whale1','whitney','whocares','whoville','wibble','wildcat','william1','window','winniethepooh','wolfgang','wolverine','wombat1','wonder','x-files','xxx123','xxxxxxxx','yamaha','yankee','yogibear','yolanda','yomama','yvette','zachary','zebras','zepplin','zoltan','zoomer','zxcvbn','!@#$%^&','00000000','121212','1234qwer','131313','21122112','99999999',
'@#$%^&','ABC123','Abcdef','Asdfgh','Changeme','FuckYou','Fuckyou','JSBach','Michel','NCC1701','Qwerty','Windows','Zxcvbnm','action','amelie','anaconda','apollo13','artist','asshole','benoit','bernard','bernie','bigbird','blizzard','bluesky','bonjour','booster','byteme','caesar','cardinal','carolina','chandler','changeit','chapman','charlie1','chiquita','chocolat','christia','christoph','classroom','cloclo','corrado','cougars','courtney','dolphins','dominic','donkey','eminem','energy','fearless','fiction','forest','forever','french1','gilles','gocougs','good-luck','graymail','guinness','hilbert','homebrew','hotdog',
'indian','johnson','kristin','lorraine','m1911a1','macintosh','mailer','maxime','memory','mirror','ne1410s','ne1469','ne14a69','nebraska','nemesis','network','newcourt','notused','oatmeal','patton','planet','players','politics','portland','praise','property','protel','psalms','qwaszx','raiders','rambo1','rancid','scrooge','shelley','skidoo','softball','speedo','sports','ssssss','steele','stephani','sunday','sylvie','symbol','tiffany','toronto','trixie','undead','valentin','velvet','viking','walker','watson','zhongguo','babygirl','1234567890','pretty','hottie','987654321','naruto','spongebob','daniela','princesa',
'christ','blessed','single','qazwsx','pokemon','iloveyou1','iloveyou2','fuckyou1','hahaha','blessing','blahblah','blink182','123qwe','trinity','passw0rd','google','looking','spirit','iloveyou!','qwerty1','rotimi','onelove','mylove','222222','ilovegod','football1','loving','emmanuel','1q2w3e4r','red123','blabla','112233'];

this.showPassStrength = function(score)
{
	var ind = $('.password-meter');
	if(score == undefined)
	{
		ind.hide();
		ind.css('background-color','#ffffff');
		return;
	}
	ind.show();
	if(score == -1)
	{
		ind.html('Too Short');
		ind.css('background-color','#ee0000');
	}
	else if(score == 0)
	{
		ind.html('Obvious');
		ind.css('background-color','#ee0000');
	}
	else if(score < 34)
	{
		ind.html('Bad');
		ind.css('background-color','#eeaaaa');
	}
	else if(score < 68)
	{
		ind.html('Good');
		ind.css('background-color','#ffff00');
	}
	else
	{
		ind.html('Strong');
		ind.css('background-color','#00ff00');
	}
};

this.getPassStrength = function(pass)
{
	if(typeof pass != 'string'){ this.showPassStrength();return -1;}
	var len = pass.length;
	if(len == 0){this.showPassStrength();return -1;}
	if(len < 6)
	{
		this.showPassStrength(-1);
		return -1;
	}
	for(var i=0;i<this.badPass.length;i++)
		if(this.badPass[i] == pass)
		{
			this.showPassStrength(0);
			return 0;
		}
	var score=len*4;
	var a=0;
	var C=0;
	var n=0;
	var s=0;
	var l='';
	for(var i=0;i<len;i++)
	{
		if(l==pass.charAt(i)) score -= 1; else l=pass.charAt(i);
		if((pass.charAt(i)>='a')&&(pass.charAt(i)<='z')){a++;continue;}
		if((pass.charAt(i)>='A')&&(pass.charAt(i)<='Z')){C++;continue;}
		if((pass.charAt(i)>='0')&&(pass.charAt(i)<='9')){n++;continue;}
		s++;
	}
	if(len == a) score -= 10;
	if(len == n) score -= 10;
	if(len == C) score -= 10;
	if(n > 0) score += 5;
	if(C > 0) score += 5;
	if(s > 0) score += 5;
	if((n>0)&&(a>0)) score += 15;
	if((C>0)&&(a>0)) score += 15;
	if((s>0)&&(a>0)) score += 15;
	if(score > 100) score = 100;

	this.showPassStrength(score);
	return score;
};


// called on document.ready
this.init = function() {

	var $wsd_new_user_form = $('#wsd_new_user_form');

	// Hook for keyup events to display password strength
	$wsd_new_user_form.delegate('#wsd_new_user_password', 'keyup',
		function() {
			_wsdPassStrengthProvider.getPassStrength($('#wsd_new_user_password').val());
		});

	// Hook for submit event to prevent form sumittion if password strength is <= BAD
	$wsd_new_user_form.delegate('#wsd-new-user', 'click',
		function() {
			var $wsd_new_user_password = $('#wsd_new_user_password');
			var $wsd_new_user_password_re = $('#wsd_new_user_password_re');

			if ($wsd_new_user_password.val() != $wsd_new_user_password_re.val()) {
				alert('Passwords do not match.');
				return false;
			}

			var score = _wsdPassStrengthProvider.getPassStrength($wsd_new_user_password.val());

			if (score <= 1) {
				alert('The selected password is weak! Please select passwords with a minimum length of 6 characters, also including numbers and/or special characters is recomended.');
				$wsd_new_user_password.val('');
				$wsd_new_user_password_re.val('');
				_wsdPassStrengthProvider.showPassStrength(score);
				return false;
			}
			else {
				var password = $wsd_new_user_password.val();
				var passwordHash = wsdMD5(password);

				$wsd_new_user_password.val(passwordHash);
				$wsd_new_user_password_re.val(passwordHash);
			}
			return true;
		});
    };

}// end of wsdPassStrengthProvider


