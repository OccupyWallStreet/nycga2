<?php
/*
 * RSS Page
 * This page handles the even RSS feed.
 * You can override this file by and copying it to yourthemefolder/plugins/events-manager/templates/ and modifying as necessary.
 * 
 */ 
header ( "Content-type: application/rss+xml; charset=UTF-8" );
echo "<?xml version='1.0' encoding='utf-8' ?>\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo esc_html(get_option ( 'dbem_rss_main_title' )); ?></title>
		<link><?php	echo EM_URI; ?></link>
		<description><?php echo esc_html(get_option('dbem_rss_main_description')); ?></description>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<pubDate><?php echo get_option('em_rss_pubdate', date('D, d M Y H:i:s T')); ?></pubDate>
		<atom:link href="<?php echo esc_attr(EM_RSS_URI); ?>" rel="self" type="application/rss+xml" />
		<?php
		$description_format = str_replace ( ">", "&gt;", str_replace ( "<", "&lt;", get_option ( 'dbem_rss_description_format' ) ) );
		//$EM_Events = new EM_Events( array('limit'=>5, 'owner'=>false) );
		$EM_Events = EM_Events::get( array('scope'=>'future', 'owner'=>false ) );
		
		foreach ( $EM_Events as $EM_Event ) {
			/* @var $EM_Event EM_Event */
			$description = $EM_Event->output( get_option ( 'dbem_rss_description_format' ), "rss");
			$description = ent2ncr(convert_chars($description)); //Some RSS filtering
			$event_url = $EM_Event->output('#_EVENTURL');
			?>
			<item>
				<title><?php echo $EM_Event->output( get_option('dbem_rss_title_format'), "rss" ); ?></title>
				<link><?php echo $event_url; ?></link>
				<guid><?php echo $event_url; ?></guid>
				<pubDate><?php echo date('D, d M Y H:i:s T', $EM_Event->start); ?></pubDate>
				<description><![CDATA[<?php echo $description; ?>]]></description>
			</item>
			<?php
		}
		?>
		
	</channel>
</rss>