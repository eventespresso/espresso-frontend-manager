<?php
/*
  Plugin Name: Event Espresso - Front-end Event Manager
  Plugin URI: http://eventespresso.com/
  Description: Tool for creating events from the front-end of your WordPress website. Add [ESPRESSO_CREATE_EVENT_FORM] to a page.

  Version: 1.0.1.b

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
	return '1.0.1.b';
}


//Update notifications  (i'm just going to hook this in with espresso_core_update_api rather than create another action hook in core) /de
add_action('action_hook_espresso_core_update_api', 'ee_fem_load_pue_update');
function ee_fem_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = array(
			// remove following line when releasing this version to stable
			'premium' => array('b' => 'espresso-frontend-event-manager-pr'),
			// uncomment following line when releasing this version to stable
    		// 'premium' => array('p' => 'eespresso-frontend-event-manager'),
   			'prerelease' => array('b' => 'espresso-frontend-event-manager-pr')
		);
		$options = array(
			'apikey' => $api_key,
			'lang_domain' => 'event_espresso',
			'checkPeriod' => '24',
			'option_key' => 'site_license_key',
			'options_page_slug' => 'event_espresso',
			'plugin_basename' => plugin_basename(__FILE__),
			'use_wp_update' => FALSE, //if TRUE then you want FREE versions of the plugin to be updated from WP
		);
		$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options); //initiate the class and start the plugin update engine!
	}
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

//Installation
if (!function_exists('espresso_fem_install')) {
    function espresso_fem_install() {
        update_option('espresso_fem_version', espresso_fem_version());
        update_option('espresso_fem_active', 1);
    }
}

//Deactivation
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

function ee_fem_save_event(){
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/insert_event.php');
}


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
	wp_register_script('jquery.validate.js', (EVENT_ESPRESSO_PLUGINFULLURL . "scripts/jquery.validate.min.js"), array('jquery'), '1.8.1');
	wp_enqueue_script('jquery.validate.js');
	
	//Check if using venues
	$use_venues = FALSE;
	if (isset($org_options['use_venue_manager']) && $org_options['use_venue_manager'] == 'Y'){
		$use_venues = TRUE;
	}
	
	//Make sure user is logged in
	if ( !is_user_logged_in() ) {
		return '<div class="ee_fem_error">'.sprintf(__('You must be <a href="%s">logged-in</a> to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';;
	}
	
	//If using R&P Pro
	if ( function_exists('espresso_permissions_pro_run') ){
		global $espresso_manager;
		if ( $espresso_manager['minimum_fem_level'] > espresso_check_user_level() ){
			return '<div class="ee_fem_error">'.sprintf(__('Sorry, you do not have access to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';;
		}
	}
	
	//Save the event
	if ( isset($_REQUEST['ee_fem_action']) && $_REQUEST['ee_fem_action'] == 'ee_fem_add') {
		ee_fem_save_event();
		return add_event_to_db();
	}
	
	//Load scripts
	add_action('wp_footer', 'ee_load_jquery_autocomplete_scripts');
	
	//Requires the event management functions
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/event_functions.php');
	
	//Check if the FEM template is in the espresso/templates directory
	if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR . "fem_form_output.php")) {
		require_once(EVENT_ESPRESSO_TEMPLATE_DIR . "fem_form_output.php");
	} else {
		require_once(ESPRESSO_FEM_FULL_PATH . 'templates/fem_form_output.php');
	}
	return ee_fem_form_output();
}

//Templates settings
//Adds a meta box to the Event Espresso > Template Settings page
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
												</label>
											</th>
											<td><?php echo select_input('enable_fem_category_select', $values, $org_options['fem_settings']['enable_fem_category_select'], 'id="enable_fem_category_select"'); ?> <br />
												<span class="description">
													<?php _e('Enables category selection (make sure you have categories).', 'event_espresso'); ?>
												</span></td>
										</tr>
										<tr>
											<th> <label>
													<?php _e('Show Pricing Section?', 'event_espresso'); ?>
												</label>
											</th>
											<td><?php echo select_input('enable_fem_pricing_section', $values, $org_options['fem_settings']['enable_fem_pricing_section'], 'id="enable_fem_pricing_section"'); ?> <br />
												<span class="description">
													<?php _e('Allows users to add prices to events.', 'event_espresso'); ?>
												</span></td>
										</tr>
										<tr>
											<th> <label>
													<?php _e('Show Venue Section?', 'event_espresso'); ?>
												</label>
											</th>
											<td><?php echo select_input('enable_fem_venue_section', $values, $org_options['fem_settings']['enable_fem_venue_section'], 'id="enable_fem_venue_section"'); ?> <br />
												<span class="description">
													<?php _e('Allows users to assign venues to events.', 'event_espresso'); ?>
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
