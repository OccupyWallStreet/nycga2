<?php
//WangGuard Con Jobs
function wangguard_cronjobs() {
	global $wpdb,$wangguard_nonce, $wangguard_api_key , $wangguard_is_network_admin;
	global $wangguard_cronjob_run_options , $wangguard_cronjob_actions_options , $wangguard_cronjob_lookup_options;

	$urlFunc = "admin_url";
	if ($wangguard_is_network_admin && function_exists("network_admin_url"))
		$urlFunc = "network_admin_url";
	
	if (wangguard_is_multisite()) {
		$spamFieldName = "spam";
		$sqlSpamWhere = "spam = 1";
		$sqlNoSpamWhere = "spam = 0";
	}
	else {
		$spamFieldName = "user_status";
		$sqlSpamWhere = "user_status = 1";
		$sqlNoSpamWhere = "user_status <> 1";
	}

	
	
	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));
	
	
	?>

<div class="wrap" id="wangguard-wizard-cont">
	<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/settings.png" alt="<?php echo htmlentities(__('WangGuard Cron Jobs', 'wangguard')) ?>" /></div>
	<div class="icon32" id="icon-wangguard"><br></div>
	<h2><?php _e('WangGuard Cron Jobs', 'wangguard'); ?></h2>
	

		<p><?php _e( "Create one or more WangGuard cron jobs. WangGuard to periodically check your registered users for Sploggers and flag or delete them.", 'wangguard') ?></p>
		<p><?php echo sprintf( __( "Note: WangGuard cron jobs will NOT verify the users flagged as %s, these are the users for which you've selected the &quot;Not a Splogger&quot; option from the Users admin or flagged as &quot;Not Spam&quot;." , "wangguard") ,   "<span class='wangguard-status-checked'>".__("Checked (forced)" , "wangguard")."</span>"  ) ?></p>
		
		<?php
		$table_name = $wpdb->base_prefix . "wangguardcronjobs";
		$wgcronRs = $wpdb->get_results("select * from $table_name order by id");

		?><h3><?php _e('Existing WangGuard cron jobs', 'wangguard')?></h3>

		<?php if (empty ($wgcronRs)) { ?>
		<div id="wangguard-cron-nocron"><?php _e('No cron jobs created yet','wangguard')?></div> 
		<?php } ?>

		<?php
		foreach ($wgcronRs as $cronjob) {
			$args = array((int)$cronjob->id);
			$timestamp = wp_next_scheduled( 'wangguard_cronjob_runner' , $args );
			$date = date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
			?>
			<div class="wangguard-cronjob" id="wangguard-cronjob-<?php echo $cronjob->id?>">
			<?php _e("Cron Job Code", 'wangguard')?>: <strong><?php echo $cronjob->id?></strong><br/>
			<?php _e("Run", 'wangguard')?>: <strong><?php echo $wangguard_cronjob_run_options[$cronjob->RunOn]?> @ <?php echo $cronjob->RunAt ?></strong><br/>
			<?php _e("Action", 'wangguard')?>: <strong><?php echo $wangguard_cronjob_actions_options[$cronjob->Action]?></strong><br/>
			<?php _e("Check users registered in the last", 'wangguard')?>: <strong><?php echo $wangguard_cronjob_lookup_options[$cronjob->UsersTF]?></strong><br/>
			<?php _e("Last run", 'wangguard')?>: <strong><?php echo ($cronjob->LastRun ? date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($cronjob->LastRun)) : "-")?></strong><br/>
			<?php _e("Next run", 'wangguard')?>: <strong><?php echo $date ?></strong><br/>
			<a href="javascript:void(0)" rel="<?php echo $cronjob->id?>" class="wangguard-delete-cronjob"><?php _e('delete cron job', 'wangguard')?></a>
			</div>
		<?php } ?>
		<div id="wangguard-new-cronjob-container">
		</div>


		<h3><?php _e('Add a new cron job', 'wangguard')?></h3>
		<table>
			<tr>
				<td style="text-align:right;"><?php _e("Run", 'wangguard')?>&nbsp;</td>
				<td><select name="wangguardnewcronjob" id="wangguardnewcronjob" class="wangguard_select">
			<?php foreach ($wangguard_cronjob_run_options as $v => $o) { ?>
			<option value="<?php echo $v ?>"><?php echo $o ?></option>
			<?php } ?>
		</select> @ <select name="wangguardnewcronjobtimeh" id="wangguardnewcronjobtimeh" class="wangguard_select">
<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
					</select> : <select name="wangguardnewcronjobtimem" id="wangguardnewcronjobtimem" class="wangguard_select">
<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
<option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
					</select></td>
			</tr>
			<tr>
				<td style="text-align:right;"><?php _e("Action", 'wangguard')?>&nbsp;</td>
				<td><select name="wangguardnewcronjobaction" id="wangguardnewcronjobaction" class="wangguard_select">
			<?php foreach ($wangguard_cronjob_actions_options as $v => $o) { ?>
			<option value="<?php echo $v ?>"><?php echo $o ?></option>
			<?php } ?>
		</select></td>
			</tr>
			<tr>
				<td style="text-align:right;"><?php _e("Check users registered in the last", 'wangguard')?>&nbsp;</td>
				<td><select name="wangguardnewcronjoblookup" id="wangguardnewcronjoblookup" class="wangguard_select">
			<?php foreach ($wangguard_cronjob_lookup_options as $v => $o) { ?>
			<option value="<?php echo $v ?>"><?php echo $o ?></option>
			<?php } ?>
					</select></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div id="wangguardnewconjoberror">
						<?php _e('The cron job cannot be created', 'wangguard')?>
					</div>
					<p class="submit"><input type="button" id="wangguardnewcronjobbutton" class="button-primary" name="submit" value="<?php _e('Create con job &raquo;', 'wangguard'); ?>" /></p>
				</td>
			</tr>
		</table>
		<br/>
	

</div>
<?php
}
?>