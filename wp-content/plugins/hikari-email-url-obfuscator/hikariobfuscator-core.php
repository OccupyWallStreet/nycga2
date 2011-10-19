<?php

//mail regex:
// [a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b
// \b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b

// http://scribu.net/wordpress/optimal-script-loading.html

global $hkMuob_Op;

class HkMuob extends HkMuob_HkTools{

	private $ids;

	private $specialParameters;
	private $jsObfuscation;
	private $cssObfuscation;
	public $true_page;
	
	private $links;
	private $op;
	private $validJS=array();
	private $js;


	/*
		Class constructor, it is run when the plugin is loaded (object is instantiated on the bottom of the code)
		It defines the plugin URL path (so that js and css can be loaded by the browser) and adds our filter to proper hooks.
	*/
	public function __construct(){
		parent::__construct();
		
		global $hkMuob_Op;
		$this->op = $hkMuob_Op->optionsDBValue;
		
		switch($this->op['js_markup']){
			case 'html':
				$this->validJS = array(
						'the_content' => array(
							'open'  => ' /* <!-- */ ',
							'close' => ' /* --> */ '
						),
						'other' => array(
							'open'  => ' /* <!-- */ ',
							'close' => ' /* --> */ '
						)
					);
				break;
			case 'cdata':
				$this->validJS = array(
						'the_content' => array(
							'open'  => ' /* <![CDATA[ */ ',
							'close' => ' /* ]]> */ '
						),
						'other' => array(
							'open'  => ' /* <![CDATA[ */ ',
							'close' => ' /* ]]> */ '
						)
					);
				break;
			case 'none':
				$this->validJS = array(
						'the_content' => array(
							'open'  => '',
							'close' => ''
						),
						'other' => array(
							'open'  => '',
							'close' => ''
						)
					);
				break;
			
			case 'html_cdata':
			default:
				$this->validJS = array(
						'the_content' => array(
							'open'  => ' /* <!-- */ ',
							'close' => ' /* --> */ '
						),
						'other' => array(
							'open'  => ' /* <![CDATA[ */ ',
							'close' => ' /* ]]> */ '
						)
					);
		}
		

		$this->js = $this->validJS['other'];
		
		
		
		$this->plugin_dir_path = plugin_dir_path(__FILE__);
		$this->plugin_dir_url = plugin_dir_url(__FILE__);
	
		$this->setFilters();
	}
	

	// add our filter to proper hooks
	// priority must be > 1000 to not conflict with CodeColorer http://kpumuk.info/projects/wordpress-plugins/codecolorer/
	// priority must be > 1001 to not conflict with raw-html http://w-shadow.com/blog/2007/12/13/raw-html-in-wordpress/
	private function setFilters(){
	
		// post content hooks
		add_filter('the_content',array($this,'the_content_filter'),1002);
		add_filter('the_excerpt',array($this,'the_content_filter'),1002);

		// comment content hooks
		add_filter('comment_text',array($this,'filter'),1002);
		add_filter('comment_text_rss',array($this,'filter'),1002);
		add_filter('comment_excerpt',array($this,'filter'),1002);
		add_filter('comment_url',array($this,'filter'),1002);

		// comment author hooks
		add_filter('get_comment_author_url_link',array($this,'filter'),1002);
		add_filter('get_comment_author_link',array($this,'filter'),1002);
		add_filter('get_comment_author_url',array($this,'filter'),1002);
		
		// text widget hook
		add_filter('widget_text',array($this,'filter'),1002);
		
		
		// plugins and themes
		add_filter('widget_execphp',array($this,'filter'),1002);
		add_filter('navt_codeblock',array($this,'filter'),1002);
                add_filter('groups_ga_custom_tab',array($this,'filter'),1002);
		add_filter('hksmtc_twitter_link',array($this,'filter'),1002);
		add_filter('HkTC_get_comment_title',array($this,'filter'),1002);
		
		
		// adds our js and css to be loaded by browser
		add_action('wp_print_styles', array($this,'stylesAction'));
		add_action('wp_head', array($this,'header'),0);
		
		if($this->op['inline_js'] == 'inline'){
			add_action('wp_head', array($this,'inline_js_preparation'),5);
		}else{
			add_action('wp_print_footer_scripts', array($this,'footer_js_insersion'), 5);
		}
		
		
		remove_filter( 'pre_comment_content',  'wp_rel_nofollow',              15    );
		
		
		$this->links=array();
		$this->ids=array();
		$this->true_page=false;
	
	}
	
	public function header(){
		$this->true_page=true;
	}
	
	public function stylesAction(){
		if (!is_admin()){
			wp_enqueue_style('HkMuob_styling', $this->plugin_dir_url.'HkMuob.css',null,null,"all");
		}
	}
	
	
	public function inline_js_preparation(){
	
		if (is_admin() || !$this->true_page) return;
		
		// please leave copyright printed
		echo "\n<!-- Emails and URLs obfuscated by \n Hikari Email & URL Obfuscator - http://Hikari.ws/email-url-obfuscator/ -->\n";
	
		wp_enqueue_script('HkMuob_jscript', $this->plugin_dir_url.'HkMuob.js');
		
	
	}

	public function footer_js_insersion(){
	
		// please leave copyright printed
		echo "\n\n<!-- Emails and URLs obfuscated by \n Hikari Email & URL Obfuscator - http://Hikari.ws/email-url-obfuscator/ -->\n";
	
		// only add our JavaScript if there are links to be obfuscated
		if(empty($this->links)) return;

		// first we add js file with decrypting code
		//wp_register_script('HkMuob_jscript', $this->plugin_dir_url.'HkMuob.js',null,null,true);
		//wp_enqueue_script( 'HkMuob_jscript' );
		//wp_print_scripts('HkMuob_jscript');
		echo "<script type=\"text/javascript\" src=\"".$this->plugin_dir_url.'HkMuob.js'."\"></script>";

		// now we add links' data, a js call for each link, passing data to decrypting code work on it
		$script .= "<script type='text/javascript'>\n".$this->js['open'];
			foreach($this->links as $link){
				$script .= "\n$link";
			}
		$script .= "\n{$this->js['close']}\n</script>\n\n";

		print $script;
	}



	
	
	
	public function the_content_filter($content){
		$this->js = $this->validJS['the_content'];
		$content = $this->filter($content);
		$this->js = $this->validJS['other'];
		return $content;
	}

/*
	$matches mapping:
	
	0: full link string
	1: before href
	2: href URL
	3: after href
	4: content

*/
	
	// this is our main filter, it receives a string as parameter and searches for <a> link patterns on it,
	//returning the string with those links obfuscated and with JavaScript calls to convert them back to links in JavaScript enabled browsers
	public function filter($content){
	
		// first whitelist level
		if($this->hasNeedle($content,HkMuob_no_obfuscate_comment)) return $content;
	
	
		$this->specialParameters=array(
			has_special_parameter => false,
			force_obfuscate => false,
			no_obfuscate => false,
			obfuscate_content => false,
			content_only => false,
			url_only => false,
			invisible_for_noscript => false
		
		);
		
	
		// these chars break JavaScript parameters, let's just replace them for simple space
		//$nl   = array("\n", "\r", "\t");
		//$content = str_replace($nl,' ',$content);
	
		/*
			i = Ignore case in matching
			s = . to match everything including new line (otherwise it matches everything except new line)
			see http://www.wellho.net/regex/perl.html
		*/
		$pattern = '/<a ([^>]*?)href=[\"\'](.*?)[\"\'](.*?)>(.*?)<\/a>/is';
		$content = preg_replace_callback($pattern,array($this,'parser'),$content);
		
		
		unset($this->specialParameters);
		return $content;
	
	}
	
	// our parser is called once for each found link, converts it to an obfuscated link that will only be understanded by humans
	// and also prepares the JavaScript call with required info to convert this obfuscated link back to original link
	private function parser($matches){
	
		// first, let's convert this $matches array to more meaningful named variables 
		$attibutes = $matches[1].' '.$matches[3];
		$original_URL = $matches[2];
		$original_content = $matches[4];
		
		
		// reseting, so that later we know if parseSpecialParameters() was called and they were set
		$this->specialParameters['has_special_parameter'] = false;
		$this->jsObfuscation	= 'not_set';
		$this->cssObfuscation	= 'not_set';
		
		
		
		// now let's search for special parameters that may had been passed specially to our plugin
		// if any parameter is passed, it will be parsed to be used later
		
		// this code is deprecated
		$specialParametersPattern = '/(.*?)hkmuob=[\"\'](.*?)[\"\'](.*?)/i';
		$attibutes = preg_replace_callback($specialParametersPattern,
				array($this,'parseLegacySpecialParameters'),$attibutes);
				
		// this is the correct one
		$specialParametersPattern = '/(.*?)class=[\"\'](.*?)[\"\'](.*?)/i';
		$attibutes = preg_replace_callback($specialParametersPattern,
				array($this,'parseSpecialParameters'),$attibutes);
		
		
		
		
		// if this link doesn't have any special parameter, it won't trigger parseSpecialParameters()
		//so we must set them back to their default, or those parameters will remain from last link that had any
		if(!$this->specialParameters['has_special_parameter']){
			$this->specialParameters['force_obfuscate'] 	= false;
			$this->specialParameters['no_obfuscate'] 		= false;
			$this->specialParameters['obfuscate_content'] 	= false;
			$this->specialParameters['content_only'] 		= false;
			$this->specialParameters['url_only'] 			= false;
			$this->specialParameters['invisible_for_noscript'] 	= false;
		}
		
		
		if($this->jsObfuscation	== 'not_set'){
			$this->jsObfuscation='cc8b';
			if(rand(0,1)==1)
				$this->jsObfuscation='rot13';
		}
		
		// reversion is currently broken, &---; identifiers and '=' chars invalidate XHTML when reverted
		// until I find a solution for it, we'll have to stick with display:none obfuscation
		/*
		if($this->cssObfuscation	== 'not_set'){
			$this->cssObfuscation='display_none';
			if(rand(0,1)==1)
				$this->cssObfuscation='reversion';
		}
		*/
		$this->cssObfuscation='display_none';
		
		
		// now we test if this specific link should be excluded and left alone, there are  bunch of cases that make it happen
		if($this->isExclude($matches, $attibutes, $original_URL, $original_content))
				// this is legacy code needed to support deprecated code above, once deprecated is removed this one should just return $matches[0], the raw and unchanged original link
				return '<a href="' . $original_URL . '" ' . $attibutes . ' >' . $original_content . '</a>';
		// we must rebuild the link because original raw one could have HkMuob special parameters,
		//and they must be removed because their attribute isn't HTML valid
		
		

		/*
			ok, all preparations are set and now we can start our obfuscation work
			basically what we will do is 4 things: obfuscate the link in a way CSS can de-obfuscate, and it will be used on browsers that have JavaScript disabled
				this will be done in 2 possible ways: reverting characters ordering or adding strings that CSS will make not be rendered
			and obfuscate it again using ROT13 or cc8b encoding, which will be later (in user's browser) decoded back by JavaScript
		*/
		
		
		/*
			first we will encode-obfuscate our stuff, whether it will be ROT13 or cc8b was already defined by parameter or randomly
			when we verified any special parameters, so we are already ready here
		*/

		
		$js_content=$original_content;
		
		if($this->jsObfuscation=='rot13'){
		
			// $key=0 sets to JavaScript we are using ROT13, since only cc8b used a variable key (ROT13's key is always 13 :P )
			$key=0;
			
			// PHP has a buil't in ROT13 encoding function, so we can just call it, yay!
			$js_url = str_rot13($original_URL);
			$js_attibutes = str_rot13($attibutes);
			
			// if we were required to obfuscate content too, now is the time to finish the job encoding it with ROT13
			if($this->specialParameters['obfuscate_content']){
				$js_content=str_rot13($original_content);
			}
			
		// now, if cc8b was the chosed encoding algorythm...
		}
		else{
		
			//get a random number from 10 to 254, which defines to JavaScript we are using cc8b
			$key = rand(10, 254);
		
			// get our URL encoded by our custom function (yeah I know the right name is method!)
			$js_url = $this->cc8b_encode($key,$original_URL);
			$js_attibutes = $this->cc8b_encode($key,$attibutes);
			
			// and if content obfuscation was required...
			if($this->specialParameters['obfuscate_content']){
				$js_content = $this->cc8b_encode($key,$original_content);
			}
			
		}

		
		
		
		/*
			now we'll obfuscate our link in a way that CSS can de-obfuscate it
			the disadivantage of this approach is that  we can't have a clickable link, it will be just visible text in the screen,
				and sometimes this text can't be copied
		*/
		
		$css_content=$original_content;
		
		if($this->cssObfuscation=='reversion'){
			
			$css_url = '<span class="HkMuob_revert">'.strrev(
							str_replace('mailto:','',$original_URL)		// reverted url
							).'</span>';
			
			if($this->specialParameters['obfuscate_content']){
				$css_content = '<span class="HkMuob_revert">'.
							strrev($original_content).'</span>';
							
				$css_both = '<span class="HkMuob_revert">'.
							strrev($original_content.' )'.str_replace('mailto:','',$original_URL).'(')
					.'</span>';
			}else{
				$css_both = $original_content . ' ('. $css_url . ')';
			}
		
		}
		else{
		
			// first we remove any 'mailto:', it's not needed when we don't have a <a> link
			$css_url = str_replace('mailto:','',$original_URL);
			
			// now we add text around any '@' and '.'
			// I could use rand(1,10000) to generate a random text, but I don't think worthy the required processing time to do it
			$css_url = str_replace('.','<span class="HkMuob_display"> NULL</span>.',$css_url);
			$css_url = str_replace('@',
				'<span class="HkMuob_display"> null</span>@<span class="HkMuob_display">null </span>'
				,$css_url);
			
			if($this->specialParameters['obfuscate_content']){
				$css_content = str_replace('.',
					'<span class="HkMuob_display"> NULL</span>.',$css_content);
				$css_content = str_replace('@',
					'<span class="HkMuob_display"> null</span>@<span class="HkMuob_display">null </span>'
					,$css_content);
			}
			
			$css_both = $css_content . ' ('. $css_url . ')';
		
		}
		
		
		
		
		
		
		
		
		
		// all obfuscation set, now let's start building our non-JavaScript code!
		
		
		// first, the code that will be seen by our non-JavaScript visitors
		$noscript_text='&nbsp;';
		
		// if it is meant to be invisible to them,  $noscript_text will remain empty
		if(!$this->specialParameters['invisible_for_noscript']){
		
			if($this->specialParameters['url_only'] && !$this->specialParameters['content_only']){
				$noscript_text = $css_url;
			}elseif($this->specialParameters['content_only'] && !$this->specialParameters['url_only']){
				$noscript_text = $css_content;
			}else{
				$noscript_text = $css_both;
			}
		
		}
		
		
		// this ID will be used by JavaScript to find the right place to include our decoded link,
		// so each link should have a unique ID in the whole document (HTML also forbids 2 elements with equal ID
		$id = 'hkmuob-id-' . $this->generateId();
		
		// here is the span that will hold our revert-obfuscated string, and also that will receive our JavaScript-decoded link
		// if by a special parameter we were required to totally hide the link from non-JavaScript user-agents, we will only add a blank span to be used by JavaScript
		// otherwise we'll do our normal job
		$noscript = '<span id="'.$id.'" class="hkmuob_noscript">' . $noscript_text . '</span>';
	
	
	
	
		// these chars break JavaScript parameters, let's just replace them for simple space
		$nl   = array("\n", "\r", "\t");
		$js_url = str_replace($nl,' ',$js_url);
		$js_attibutes = str_replace($nl,' ',$js_attibutes);
		$js_content = str_replace($nl,' ',$js_content);
		
	
		// noscript obfuscation done,
		//now let's work on our JavaScript call that will later (in user's browser) finish our magic bringing original link back to life!
		$script = 'HkMuobJS.write("' . 
				$id . '",' .				//id
				$key . ",'" .				//key
				str_replace("'","\'",$js_url) . "','".			//addr
				str_replace("'","\'",$js_attibutes) . "','".	//attributes
				str_replace("'","\'",$js_content)."'," .		//content
				$this->bTOs($this->specialParameters['obfuscate_content']).
				');';
		
		
		
		// if we are set to add each js call inline, just after each link, let's add the <script> tag
		// this one is better to confuse spambots, but Wordpress converts CDATA's ']]>' to ']]&gt;', which breaks XHTML validation; it's hardcoded in the_content() function and they are afraid of removing the breaking code, damn
		if($this->op['inline_js'] == 'inline'){
		
			$script = '<script type="text/javascript">'.$this->js['open'] . $script . $this->js['close']."</script>";
		
		// else, if we were set to add all js calls in the footer, let's add it to the array and clean $script
		}else{
		
			$this->links[] = $script;
			$script = '';
		
		}
		
		
		// everything done, weeee!!
		// let's return both our noscript, revert-obfuscated string, together with our JavaScript call
		// this returned string will be put in place of original link
		return $noscript.$script;
	
	}



	// verifies each and every situation where a link should be excluded from obfuscation and left alone
	private function isExclude($matches, $attibutes, $original_URL, $original_content){
	
		// custom parameters have top priority
		if($this->specialParameters['force_obfuscate']) return false;
		if($this->specialParameters['no_obfuscate']) return true;
		
		// error checks
		if(empty($matches)) 	return true;
		if(!is_array($matches))	return true;
		if(!$this->true_page)	return true;
		
	
		// hardcoded whitelist of my domains, please leave it here and don't obfuscate them, plz :)
		$whitelist = array('Hikari.ws', 'ConscienciaPlanetaria.com', 'hikarinet.info', 'PlanetaryConscience.com', 'hikari.me', 'spampoison.com');
		if( $this->hasNeedleInsensitive($original_URL, $whitelist)  )	return true;
		
		
		$temp = explode("\n",$this->op['whitelist']);
		if( $this->hasNeedleInsensitive($original_URL, $temp)  )	return true;
		
		$temp = explode("\n",$this->op['blacklist']);
		if( $this->hasNeedleInsensitive($original_URL, $temp)  )	return false;
		
		unset($temp);
		
	
	
		// if this URL points to an internal link
		if( $this->hasNeedle($original_URL, get_bloginfo("url"))  )	return true;
		if( $this->hasNeedle($original_URL, get_bloginfo("home")) )	return true;
		
		// if this URL doesn't have an '://' (from 'http://' or ''https://' or 'ftp://'), then it is a relative link to a internal resource
		// furst we must test if it is not a mail lint (mailto:)
		if( !$this->hasNeedle($original_URL, "mailto:") && !$this->hasNeedle($original_URL, '://') )
				return true;

		


	
	
		// if it passes from all tests, then it should not be excluded from obfuscation
		return false;
	}
	
	
	// our custom cc8b encoding code
	//Cc8b originally written by Debugged Interactive Designs www.debuggeddesigns.com
	//adapted by Hikari at http://Hikari.WS
	public function cc8b_encode($key,$plain_text){
		$cipher_text = '';
		//turn each character in the string into its hexidecimal ascii value
		$ascii_plain_text = (string) bin2hex($plain_text);
		//get each acii octet 
		for ($i = 0; $i <= strlen($ascii_plain_text) - 2; $i += 2) {
			//get next octet
			$temp = substr($ascii_plain_text, $i, 2);
			//turn it to decimal
			$temp = hexdec($temp);

			//add key value to temp
			$temp += $key;
			//mod temp by 255 so its value is at most FF
			$temp = $temp % 255;
			
			//concatinate the hex value to ecrypted email
			//check to make sure we have 2 digits
			//if its 1 digit place a leading 0 infront of it (0 -> F)
			if (strlen((string) dechex($temp)) < 2) {
				$cipher_text .= '0' . (string) dechex($temp);
			}
			//else just concate the 2 digit string
			else {
				$cipher_text .= (string) dechex($temp);
			}
		}
		
		return $cipher_text;		
	}
	
	
/*
	$matches mapping:
	
	0: full attributes string
	1: before special attribute
	2: special parameters
	3: after special attribute

*/
	
	// for each link, there may be a custom attribute with special parameters to be used by Hikari Email & URL Obfuscator plugin
	// here we parte and set each of those parameters, and return a string with all original link attributes this non-standard attribute stripped out
	private function parseSpecialParameters($matches){
	
		$sp=$matches[2];
		
		$this->specialParameters['has_special_parameter'] = true;
	
		$this->specialParameters['force_obfuscate'] 		= $this->hasNeedle($sp,'hkmuob_force_obfuscate');
		$this->specialParameters['no_obfuscate'] 		= $this->hasNeedle($sp,'hkmuob_no_obfuscate');
		$this->specialParameters['obfuscate_content'] 	= $this->hasNeedle($sp,'hkmuob_obfuscate_content');
		$this->specialParameters['content_only'] 		= $this->hasNeedle($sp,'hkmuob_content_only');
		$this->specialParameters['url_only'] 			= $this->hasNeedle($sp,'hkmuob_url_only');
		$this->specialParameters['invisible_for_noscript'] 	= $this->hasNeedle($sp,'hkmuob_invisible_for_noscript');
	
	
		if($this->hasNeedle($sp,'hkmuob_cc8b')){
			$this->jsObfuscation='cc8b';
		}elseif($this->hasNeedle($sp,'hkmuob_rot13')){
			$this->jsObfuscation='rot13';
		}
		
		if($this->hasNeedle($sp,'hkmuob_display_none')){
			$this->cssObfuscation='display_none';
		}elseif($this->hasNeedle($sp,'hkmuob_reversion')){
			$this->cssObfuscation='reversion';
		}
	
	
		return $matches[0];
	}
	
	// deprecated, don't use these parameters
	private function parseLegacySpecialParameters($matches){
	
		$sp=$matches[2];
		
		$this->specialParameters['has_special_parameter'] = true;
	
		$this->specialParameters['force_obfuscate'] 		= $this->hasNeedle($sp,'force_obfuscate');
		$this->specialParameters['no_obfuscate'] 		= $this->hasNeedle($sp,'no_obfuscate');
		$this->specialParameters['obfuscate_content'] 	= $this->hasNeedle($sp,'obfuscate_content');
		$this->specialParameters['content_only'] 		= $this->hasNeedle($sp,'content_only');
		$this->specialParameters['url_only'] 			= $this->hasNeedle($sp,'url_only');
		$this->specialParameters['invisible_for_noscript'] 	= $this->hasNeedle($sp,'invisible_for_noscript');
	
	
		if($this->hasNeedle($sp,'cc8b')){
			$this->jsObfuscation='cc8b';
		}elseif($this->hasNeedle($sp,'rot13')){
			$this->jsObfuscation='rot13';
		}
		
		if($this->hasNeedle($sp,'display_none')){
			$this->cssObfuscation='display_none';
		}elseif($this->hasNeedle($sp,'reversion')){
			$this->cssObfuscation='reversion';
		}
	
	
		$newAttributes = $matches[1].' '.$matches[3];
	
		return trim($newAttributes);
	}

	// imagine what would happen if rand() renerated the same ID twice in the same filter loop...
	// no, we can't let that happen! can we? :D
	private function generateId(){
		do{
			$new_id = rand(1,10000);
		}while(in_array($new_id,$this->ids));
	
		$this->ids[] = $new_id;
		return $new_id;
	}



}

global $hkMuob;
$hkMuob = new HkMuob();
