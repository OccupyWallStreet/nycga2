<?php
// HkMuob


global $hkMuob_Op;
$hkMuob_Op = new HkMuob_Op();



class HkMuob_Op extends HkMuob_HkToolsOptions{

	public $optionsName = 'hkMuob';
	protected $pluginfile = HkMuob_pluginfile;
	protected $optionsDBVersion = 1;
	//public $debug = true;
	
	
	public $opStructure = array(
	
		'whitelist' => array( "name" => 'Whitelist',
				"desc" => "These URLs and emails won't be obfuscated. Add one item per line, case unsensitive.",
				"largeDesc" => "<p class=\"description\">Exemple: 'Hikari.ws' will whitelist email@hikari.ws and http://wordpress.Hikari.ws/plugins/ and they won't be obfuscated.</p>",
				"id" => 'whitelist',
				"default" => null,
				"type" => "textarea",
				"options" => array(	"rows" => "7",
									"cols" => "100",
									"full_width" => true,
									"stripslashes" => false)
		),
		
		'blacklist' => array( "name" => 'Blacklist',
				"desc" => "These URLs and emails will be always obfuscated (unless they are also in Whitelist!). Add one item per line, case unsensitive.",
				"largeDesc" => "<p class=\"description\">Exemple: 'Hikari.ws' will blacklist email@hikari.ws and http://wordpress.Hikari.ws/plugins/ and they will be obfuscated.</p>",
				"id" => 'blacklist',
				"default" => null,
				"type" => "textarea",
				"options" => array(	"rows" => "7",
									"cols" => "100",
									"full_width" => true,
									"stripslashes" => false)
		),
		
		'inline_js' => array( "name" => "Inline js calls position",
				"desc" => "Defines where each link's JavaScript call, with data to rebuild the link, should be placed.",
				"largeDesc" => '<p class="description">Inline calls make it harder for spambots to detect them, but it MAY generate invalid XHTML. Adding all calls in the footer makes them easier to be found, but is secure for validation</p>',
				"id" => 'inline_js',
				"default" => 'footer',
				"type" => "radio",
				"options" => array(
						'inline'	=> "All JavaScript calls should be added inline, together with each link",
						'footer'	=> "JavaScript calls should be added all together in the page's footer (default)"
				)
		),
	
		'js_markup' => array( "name" => 'JavaScript markup',
				"desc" => "How JavaScript should be marked up by HTML",
				"largeDesc" => "<p class=\"description\">Wordpress doesn't like <code>&lt;![CDATA[ ]]></code>, which would be the best way of marking up JavaScript. Because of that, we must tweak our code to make it valid and Wordpress-friendly.</p><p class=\"description\">For detailed information about each option, please read the tutorial at <a href=\"http://hikari.ws/dev/wordpress/plugin/hikari-email-url-obfuscator/760/javascript-markup/\" title=\"Hikari Email &amp; URL Obfuscator &ndash; JavaScript markup\">Hikari Email &amp; URL Obfuscator &ndash; JavaScript markup</a>.</p>",
				"id" => 'js_markup',
				"default" => 'html_cdata',
				"type" => "radio",
				"options" => array(
						'html'	=> "Use <code>&lt;!-- --&gt;</code> in all scripts",
						'cdata'	=> "Use <code>&lt;![CDATA[ ]]&gt;</code> in all scripts",
						'html_cdata' => "Use <code>&lt;![CDATA[ ]]&gt;</code>, with exception of the_content() areas, where <code>&lt;!-- --&gt;</code> is used (default)",
						'none' => "Use nothing to delimit scripts"
				)
		),
	
	
	);




	public function __construct(){
		parent::__construct();
	
		$this->uninstallArgs = array(
				'name' => $this->optionspageName,
				'plugin_basename' => HkMuob_basename,
				'options' => array(
						array(
							'opType' => 'wp_options',
							'itemNames' => array($this->optionsDBName)
						)
					)
			);

		
	}
	
	public function pluginLinks(){
?>
			<li><a href="http://hikari.ws/dev/wordpress/plugin/hikari-email-url-obfuscator/748/advanced-usage/" title="In this tutorial you see all features that can be used to tweak how links are obfuscated and make they behave exactaly as you want">Hikari Email &amp; URL Obfuscator &ndash; Advanced Usage</a></li>
<?php	}
	
	




}

