<?php
/*
  Plugin Name: Event Espresso - Front-end Event Manager
  Plugin URI: http://eventespresso.com/
  Description: Tool for creating events from the front-end of your WordPress website. Add [ESPRESSO_CREATE_EVENT_FORM] to a page.

  Version: 0.0.1

  Author: Event Espresso
  Author URI: http://www.eventespresso.com

  Copyright (c) 2012 Event Espresso  All Rights Reserved.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

 */

function espresso_fem_version() {
	return '0.0.1';
}

//Register the plugin
register_activation_hook(__FILE__, 'espresso_fem_install');
register_deactivation_hook(__FILE__, 'espresso_fem_deactivate');

//Define the plugin url
$wp_plugin_url = WP_PLUGIN_URL;
//$wp_content_url = WP_CONTENT_URL;

//Check if the site is using ssl
if (is_ssl()) {

    $wp_plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL);
    //$wp_content_url = str_replace('http://', 'https://', WP_CONTENT_URL);
}


define("ESPRESSO_FEM_PATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
define("ESPRESSO_FEM_FULL_PATH", WP_PLUGIN_DIR . ESPRESSO_FEM_PATH);
define("ESPRESSO_FEM_FULL_URL", $wp_plugin_url . ESPRESSO_FEM_PATH);
define("ESPRESSO_FEM_ACTIVE", TRUE);

if (!function_exists('espresso_fem_install')) {
    function espresso_fem_install() {
        update_option('espresso_fem_version', espresso_fem_version());
        update_option('espresso_fem_active', 1);
    }
}

if (!function_exists('espresso_fem_deactivate')) {
    function espresso_fem_deactivate() {
        update_option('espresso_fem_active', 0);
    }
}

if (!function_exists('espresso_fem_init')) {
    function espresso_fem_init() {
		//Load any code here that needs to be intialized with WordPress
    }
}

/*function ee_load_jquery_autocomplete_scripts(){
	wp_enqueue_script('jquery-ui-core');
	wp_register_script('jquery-ui-autocomplete', plugins_url( 'js/jquery.ui.autocomplete.min.js', __FILE__ ), array( 'jquery-ui-widget', 'jquery-ui-position' ), '1.8.2', true );
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-datepicker');
}*/

function ee_fem_save_event(){
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/insert_event.php');
}

/*function ee_fem_save_venue(){
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'admin-files/venue-management/add_venue_to_db.php');
}*/


//Create the form output shortcode
add_shortcode('ESPRESSO_CREATE_EVENT_FORM', 'espresso_create_event_form');
function espresso_create_event_form(){
	global $org_options, $use_themeroller, $use_venues;
	
	//Load the datepicker styles
	wp_register_style('jquery-ui-style-datepicker', ESPRESSO_FEM_FULL_URL . 'css/ui-ee-theme/jquery.ui.datepicker.css');
	wp_enqueue_style( 'jquery-ui-style-datepicker' );
		
	//Decide if we are using Themeroller
	if (isset($org_options['style_settings']['enable_default_style']) && $org_options['style_settings']['enable_default_style'] == 'Y'){
		$use_themeroller = TRUE;
	}else{
		$use_themeroller = FALSE;
		//This is so we can show a nice looking date picker if Themeroller is turned off.
		wp_register_style('jquery-ui-style', EVENT_ESPRESSO_PLUGINFULLURL . 'templates/css/themeroller/smoothness/style.css');
		wp_enqueue_style( 'jquery-ui-style' );
	}
	
	//Load the validation scripts
	wp_register_script('jquery.validate.js', (EVENT_ESPRESSO_PLUGINFULLURL . "scripts/jquery.validate.min.js"), false, '1.8.1');
	wp_enqueue_script('jquery.validate.js');
	
	$use_venues = FALSE;
	if (isset($org_options['use_venue_manager']) && $org_options['use_venue_manager'] == 'Y'){
		$use_venues = TRUE;
	}
	
	if ( !is_user_logged_in() ) {
		echo '<div class="ee_fem_error">'.sprintf(__('You must be <a href="%s">logged-in</a> to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';
		return;
	}
	
	if ( function_exists('espresso_permissions_pro_run') ){
		global $espresso_manager;
		
		if ( $espresso_manager['minimum_fem_level'] > espresso_check_user_level() ){
			echo '<div class="ee_fem_error">'.sprintf(__('Sorry, you do not have access to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';
			return;
		}
	}
	
	if ( isset($_REQUEST['ee_fem_action']) && $_REQUEST['ee_fem_action'] == 'ee_fem_add') {
		ee_fem_save_event();
		return add_event_to_db();
	}
	
	//Load scripts
	add_action('wp_footer', 'ee_load_jquery_autocomplete_scripts');
	
	//Requires the event management functions
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/event_functions.php');
	
	if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR . "fem_form_output.php")) {
		require_once(EVENT_ESPRESSO_TEMPLATE_DIR . "fem_form_output.php");
	} else {
		require_once(ESPRESSO_FEM_FULL_PATH . 'templates/fem_form_output.php');
	}
	echo ee_fem_form_output();
}

function ee_fem_template_settings() {
	global $org_options;
	$values = array(
			array('id' => 'Y', 'text' => __('Yes', 'event_espresso')),
			array('id' => 'N', 'text' => __('No', 'event_espresso'))
	);
	?>
	<div class="metabox-holder">
					<div class="postbox">
						<div title="Click to toggle" class="handlediv"><br />
						</div>
						<h3 class="hndle">
							<?php _e('Front-end Event Manager', 'event_espresso'); ?>
						</h3>
						<div class="inside">
							<div class="padding">
								<?php
								/*if (isset($org_options['enable_default_style'])) {
									include('style_settings.php');
								}*/
								?>
								<h2>
									<?php _e('Template Settings', 'event_espresso'); ?>
								</h2>
								<!-- FEM Template Settings -->
								<table class="form-table">
									<tbody>
										<tr>
											<th> <label>
													<?php _e('Show Category Selection?', 'event_espresso'); ?>
													<?php //echo apply_filters('filter_hook_espresso_help', 'enable_styles_info'); ?>
												</label>
											</th>
											<td><?php echo select_input('enable_fem_category_select', $values, $org_options['fem_settings']['enable_fem_category_select'], 'id="enable_fem_category_select"'); ?> <a class="thickbox"  href="#TB_inline?height=400&width=500&inlineId=enable_fem_category_select" target="_blank"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/question-frame.png" width="16" height="16" /></a><br />
												<span class="description">
													<?php _e('Enables category selection (make sure you have categories).', 'event_espresso'); ?>
												</span></td>
										</tr>
										<tr>
											<th> <label>
													<?php _e('Show Pricing Section?', 'event_espresso'); ?>
												</label>
												<?php //echo apply_filters('filter_hook_espresso_help', 'themeroller_info'); ?>
											</th>
											<td><?php echo select_input('enable_fem_pricing_section', $values, $org_options['fem_settings']['enable_fem_pricing_section'], 'id="enable_fem_pricing_section"'); ?> <a class="thickbox"  href="#TB_inline?height=400&width=500&inlineId=enable_fem_pricing_section" target="_blank"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/question-frame.png" width="16" height="16" /></a><br />
												<span class="description">
													<?php _e('Allows users to add a single price to events.', 'event_espresso'); ?>
												</span></td>
										</tr>
									</tbody>
								</table>
								<p>
									<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Options', 'event_espresso'); ?>" id="save_organization_setting_3" />
								</p>
							</div>
							<!-- / .padding --> 
						</div>
						<!-- / .inside --> 
					</div>
					<!-- / .postbox --> 
				</div>
				<!-- / .metabox-holder -->
	<?php

}
add_action( 'action_hook_espresso_fem_template_settings', 'ee_fem_template_settings' );