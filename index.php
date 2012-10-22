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

function espresso_event_submission_version() {
	return '0.0.1';
}

//Register the plugin
register_activation_hook(__FILE__, 'espresso_event_submission_install');
register_deactivation_hook(__FILE__, 'espresso_event_submission_deactivate');

//Define the plugin url
$wp_plugin_url = WP_PLUGIN_URL;
//$wp_content_url = WP_CONTENT_URL;

//Check if the site is using ssl
if (is_ssl()) {

    $wp_plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL);
    //$wp_content_url = str_replace('http://', 'https://', WP_CONTENT_URL);
}


define("ESPRESSO_EVENT_SUBMISSION_PATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
define("ESPRESSO_EVENT_SUBMISSION_FULL_PATH", WP_PLUGIN_DIR . ESPRESSO_EVENT_SUBMISSION_PATH);
define("ESPRESSO_EVENT_SUBMISSION_FULL_URL", $wp_plugin_url . ESPRESSO_EVENT_SUBMISSION_PATH);
define("ESPRESSO_EVENT_SUBMISSION_ACTIVE", TRUE);

if (!function_exists('espresso_event_submission_install')) {
    function espresso_event_submission_install() {
        update_option('espresso_event_submission_version', espresso_event_submission_version());
        update_option('espresso_event_submission_active', 1);
       // global $wpdb;
    }
}

if (!function_exists('espresso_event_submission_deactivate')) {
    function espresso_event_submission_deactivate() {
        update_option('espresso_event_submission_active', 0);
    }
}

if (!function_exists('espresso_event_submission_init')) {
    function espresso_event_submission_init() {
		//Load any code here that needs to be intialized with WordPress
    }
}

/*function ee_load_jquery_autocomplete_scripts(){
	wp_enqueue_script('jquery-ui-core');
	wp_register_script('jquery-ui-autocomplete', plugins_url( 'js/jquery.ui.autocomplete.min.js', __FILE__ ), array( 'jquery-ui-widget', 'jquery-ui-position' ), '1.8.2', true );
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-datepicker');
}*/

function ee_fes_print_styles(){
	wp_register_style('jquery-ui-style-datepicker', ESPRESSO_EVENT_SUBMISSION_FULL_URL . 'css/ui-ee-theme/jquery.ui.datepicker.css');
	wp_print_styles( 'jquery-ui-style-datepicker' );
}

function ee_fes_save_event(){
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/insert_event.php');
}

/*function ee_fes_save_venue(){
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'admin-files/venue-management/add_venue_to_db.php');
}*/


//Create the form output shortcode
add_shortcode('ESPRESSO_CREATE_EVENT_FORM', 'espresso_create_event_form');
function espresso_create_event_form(){
	
	if ( !is_user_logged_in() ) {
		echo '<div class="ee_fes_error">'.sprintf(__('You must be <a href="%s">logged-in</a> to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';
		return;
	}
	
	if ( function_exists('espresso_permissions_pro_run') ){
		global $espresso_manager;
		
		if ( $espresso_manager['minimum_fes_level'] > espresso_check_user_level() ){
			echo '<div class="ee_fes_error">'.sprintf(__('Sorry, you do not have access to create events.', 'event_espresso'), wp_login_url( get_permalink() )).'</div>';
			return;
		}
	}
	
	if ( isset($_REQUEST['ee_fes_action']) && $_REQUEST['ee_fes_action'] == 'ee_fes_add') {
		ee_fes_save_event();
		return add_event_to_db();
	}
	
	//Load styles
	ee_fes_print_styles();
	
	//Load scripts
	add_action('wp_footer', 'ee_load_jquery_autocomplete_scripts');
	
	//Requires the event management functions
	require_once(EVENT_ESPRESSO_INCLUDES_DIR.'event-management/event_functions.php');
	
	if (file_exists(EVENT_ESPRESSO_TEMPLATE_DIR . "fes_form_output.php")) {
		require_once(EVENT_ESPRESSO_TEMPLATE_DIR . "fes_form_output.php");
	} else {
		require_once(ESPRESSO_EVENT_SUBMISSION_FULL_PATH . 'templates/fes_form_output.php');
	}
	echo espresso_fes_form_output();
}