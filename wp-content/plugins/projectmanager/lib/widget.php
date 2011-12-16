<?php
/** Widget class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2008-2009
*/

class ProjectManagerWidget extends WP_Widget
{
	/**
	 * prefix of widget
	 * 
	 * @var string
	 */
	var $prefix = 'projectmanager-widget';
	
	
	/**
	 * initialize
	 *
	 * @param none
	 * @return void
	 */
	function __construct( $template = false )
	{
		if ( !$template ) {
			$widget_ops = array('classname' => 'widget_projectmanager', 'description' => __('Display datasets from ProjectManager', 'projectmanager') );
			parent::__construct('projectmanager-widget', __( 'Project', 'projectmanager' ), $widget_ops);
		}
	}
	function ProjectManagerWidget( $template = false )
	{
		$this->__construct( $template );
	}
	
		
	/**
	 * displays widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 */
	function widget( $args, $instance )
	{
		global $wpdb, $projectmanager;

		$project_id = $instance['project'];
		$projectmanager->initialize($project_id);
		
		$project = $projectmanager->getCurrentProject();
		
		$defaults = array(
			'before_widget' => '<li id="projectmanager-'.$this->number.'" class="widget '.get_class($this).'_'.__FUNCTION__.'">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'number' => $this->number,
			'widget_title' => $project->title,
		);
		$args = array_merge( $defaults, $args );
		extract( $args, EXTR_SKIP );
		
		$limit = ( 0 != $instance['limit'] ) ? "LIMIT 0,".$instance['limit'] : '';
		$datasets = $wpdb->get_results( "SELECT `id`, `name`, `image` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} ORDER BY `id` DESC ".$limit." " ); 

		$slideshow = ( 1 == $instance['slideshow']['show'] ) ? true : false;

		if ( $slideshow ) {
		?>
		<script type='text/javascript'>
		//<![CDATA[
		jQuery(document).ready(function(){
		   jQuery('#projectmanager_slideshow_<?php echo $number ?>').cycle({
			   fx: '<?php echo $instance['slideshow']['fade'] ?>',
			   timeout: <?php echo $instance['slideshow']['time']*1000; ?>,
			   random: <?php echo $instance['slideshow']['order'] ?>,
			   pause: 1
		   });
		});
		//]]>
		</script>
		<style type="text/css">
			div#projectmanager_slideshow_<?php echo $number ?> div {
				width: <?php echo $instance['slideshow']['width'] ?>px;
				height: <?php echo $instance['slideshow']['height'] ?>px;
			}
		</style>
		<?php
		}
		
		echo $before_widget;
		
		if ( !empty($widget_title) ) echo $before_title . $widget_title . $after_title;

		if ( $slideshow )
			echo '<div id="projectmanager_slideshow_'.$number.'" class="projectmanager_slideshow">';
		else
			echo "<ul class='projectmanager_widget'>";
				
		if ( $datasets ) {
			$url = get_permalink($instance['page_id']);
			foreach ( $datasets AS $dataset ) {
				$url = add_query_arg('show', $dataset->id, $url);
				$name = ($projectmanager->hasDetails()) ? '<a href="'.$url.'"><img src="'.$projectmanager->getFileURL($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" /></a>' : '<img src="'.$projectmanager->getFileURL($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" />';
				
				if ( $slideshow ) {
					if ( $dataset->image != '' )
						echo "<div>".$name."</div>";
				} else
					echo "<li>".$name."</li>";
			}
		}
		if ( $slideshow )
			echo "</div>";
		else
			echo "</ul>";
		echo $after_widget;
	}
	

	/**
	 * save settings
	 *
	 * @param array $new_instance
	 * @param $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		return $new_instance;
	}


	/**
	 * widget control panel
	 *
	 * @param array $instance
	 */
	function form( $instance )
	{
		global $wp_registered_widgets;
		
		echo '<div class="projectmanager_widget_control">';
		echo '<p><label for="'.$this->get_field_id('project').'">'.__('Project', 'projectmanager').'</label>'.$this->getProjectsDropdown($instance['project']).'</p>';
		echo '<p><label for="'.$this->get_field_id('limit').'">'.__('Display', 'projectmanager').'</label><select style="margin-top: 0;" size="1" name="'.$this->get_field_name('limit').'" id="'.$this->get_field_id('limit').'">';
		$selected['show_all'] = ( $instance['limit'] == 0 ) ? " selected='selected'" : '';
		echo '<option value="0"'.$selected['show_all'].'>'.__('All','projectmanager').'</option>';
		for ( $i = 1; $i <= 10; $i++ ) {
		        $selected = ( $instance['limit'] == $i ) ? " selected='selected'" : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		echo '</select></p>';
		echo '<p><label for="'.$this->get_field_id('page_id').'">'.__('Page','projectmanager').'</label>'.wp_dropdown_pages(array('name' => $this->get_field_name('page_id'), 'selected' => $instance['page_id'], 'echo' => 0)).'</p>';
		echo '<fieldset class="slideshow_control"><legend>'.__('Slideshow','projectmanager').'</legend>';
		$checked = ($instance['slideshow']['show'] == 1) ? ' checked="checked"' : '';
		echo '<p><input type="checkbox" name="'.$this->get_field_name('slideshow][show').'" id="'.$this->get_field_id('slideshow_show').'" value="1"'.$checked.' style="margin-left: 0.5em;" />&#160;<label for="'.$this->get_field_id('slideshow_show').'" class="right">'.__( 'Use Slideshow', 'projectmanager' ).'</label></p>';
		echo '<p><label for="'.$this->get_field_id('slideshow_width').'">'.__( 'Width', 'projectmanager' ).'</label><input type="text" size="3" name="'.$this->get_field_name('slideshow][width').'" id="'.$this->get_field_id('slideshow_width').'" value="'.$instance['slideshow']['width'].'" /> px</p>';
		echo '<p><label for="'.$this->get_field_id('slideshow_height').'">'.__( 'Height', 'projectmanager' ).'</label><input type="text" size="3" name="'.$this->get_field_name('slideshow][height').'" id="'.$this->get_field_id('slideshow_height').'" value="'.$instance['slideshow']['height'].'" /> px</p>';
		echo '<p><label for="'.$this->get_field_id('slideshow_time').'">'.__( 'Time', 'projectmanager' ).'</label><input type="text" name="'.$this->get_field_name('slideshow][time').'" id="'.$this->get_field_id('slideshow_time').'" size="1" value="'.$instance['slideshow']['time'].'" /> '.__( 'seconds','projectmanager').'</p>';
		echo '<p><label for="'.$this->get_field_id('slideshow_fade').'">'.__( 'Fade Effect', 'projectmanager' ).'</label>'.$this->getSlideshowFadeEffects($instance['slideshow']['fade']).'</p>';
		echo '<p><label for="'.$this->get_field_id('slideshow_order').'">'.__('Order','projectmanager').'</label>'.$this->getSlideshowOrder($instance['slideshow']['order']).'</p>';
		echo '</fieldset>';
		echo '</div>';
	}
	
	
	/**
	* dropdown list of available fade effects
	*
	* @param string $selected
	* @return string
	*/
	function getSlideshowFadeEffects( $selected )
	{
		
		$effects = array(__('Blind X','projectmanager') => 'blindX', __('Blind Y','projectmanager') => 'blindY', __('Blind Z','projectmanager') => 'blindZ', __('Cover','projectmanager') => 'cover', __('Curtain X','projectmanager') => 'curtainX', __('Curtain Y','projectmanager') => 'curtain>', __('Fade','projectmanager') => 'fade', __('Fade Zoom','projectmanager') => 'fadeZoom', __('Scroll Up','projectmanager') => 'scrollUp', __('Scroll Left','projectmanager') => 'scrollLeft', __('Scroll Right','projectmanager') => 'scrollRight', __('Scroll Down','projectmanager') => 'scrollDown', __('Scroll Horizontal', 'projectmanager') => 'scrollHorz', __('Scroll Vertical', 'projectmanager') => 'scrotllVert', __('Shuffle','projectmanager') => 'shuffle', __('Slide X','projectmanager') => 'slideX', __('Slide Y','projectmanager') => 'slideY', __('Toss','projectmanager') => 'toss', __('Turn Up','projectmanager') => 'turnUp', __('Turn Down','projectmanager') => 'turnDown', __('Turn Left','projectmanager') => 'turnLeft', __('Turn Right','projectmanager') => 'turnRight', __('Uncover','projectmanager') => 'uncover', __('Wipe','projectmanager') => 'wipe', __( 'Zoom','projectmanager') => 'zoom', __('Grow X','projectmanager') => 'growX', __('Grow Y','projectmanager') => 'growY', __('Random','projectmanager') => 'all');

		$out = '<select size="1" name="'.$this->get_field_name('slideshow][fade').'" id="'.$this->get_field_id('slideshow_fade').'">';
		foreach ( $effects AS $name => $effect ) {
			$checked =  ( $selected == $effect ) ? " selected='selected'" : '';
			$out .= '<option value="'.$effect.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * dropdown list of Order possibilites
	 *
	 * @param string $selected
	 * @return string
	 */
	function getSlideshowOrder( $selected )
	{
		$order = array(__('Ordered','projectmanager') => '0', __('Random','projectmanager') => '1');
		$out = '<select size="1" name="'.$this->get_field_name('slideshow][order').'" id="'.$this->get_field_id('slideshow_order').'">';
		foreach ( $order AS $name => $value ) {
			$checked =  ( $selected == $value ) ? " selected='selected'" : '';
			$out .= '<option value="'.$value.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * gets all projects as dropdown menu
	 *
	 * @param int $current
	 * @param int $number
	 * @return array
	 */
	function getProjectsDropdown($current)
	{
		global $projectmanager;
		$projects = $projectmanager->getProjects();
		
		$out = "<select size='1' name='".$this->get_field_name('project')."' id='".$this->get_field_id('project')."'>";
		foreach ( $projects AS $project ) {
			$selected = ( $current == $project->id ) ? " selected='selected'" : '';
			$out .= "<option value='".$project->id."'".$selected.">".$project->title."</option>";
		}
		$out .= "</select>";
		return $out;
	}
}
?>
