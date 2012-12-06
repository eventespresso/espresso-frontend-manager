<?php
//ATTENTION!
//Before editing this file, please move it to the 'wp-content/uploads/espresso/templates' directory.

function ee_fem_form_output() {
	global $org_options, $use_themeroller, $use_venues;
	//Build the form
	ob_start();
	?>

<div class="event_espresso_form_wrapper">
	<form id="ee_fem_form" name="ee_fem_form" method="post" action="<?php echo $_SERVER["REQUEST_URI"] ?>">
		<div id="event_content" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Event Information', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<div class="event_form_field ee_fem_form_field title">
					<label class="" for="event_title_field">
						<?php _e('Event Title', 'event_espresso'); ?><em>*</em></label>
					<br />
					<input class="required" name="event" tabindex="1" value="" id="event_title_field" type="text">
									</div>
				<div class="event_form_field ee_fem_form_field reg_limit">
					<label class="ee_fem-reg-limit-label" for="reg_limit">
						<?php _e('Registration Limit', 'event_espresso'); ?></label>
					<br />
					<input class="ee_fem-reg-limit-input" name="reg_limit" tabindex="1" value="" id="reg_limit" type="text">
				</div>
				<div class="clear"></div>
				<p class="event_form_field ee_fem_form_field title">
					<label class="" for="event_desc">
						<?php _e('Description', 'event_espresso'); ?>
					</label>
					<br />
					<?php $args = array("textarea_rows" => 5, "textarea_name" => "event_desc", "editor_class" => "my_editor_custom");
				wp_editor("", "event_desc", $args);?>
				</p>
			</div>
		</div>
		<div id="add-reg-dates" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Registration Dates', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<p class="event_form_field ee_fem_form_field">
					<label class="date_time" for="registration_start"><?php echo __('Start Date', 'event_espresso') ?><em>*</em> </label>
					<input type="text" id="registration_start" class="datepicker required ee_fem_date_value" name="registration_start" value="<?php echo date('Y-m-d'); ?>" />
					<label class="date_time time" for="add-reg-start">
						<?php _e('Time', 'event_espresso'); ?>
						<em>*</em> </label>
					<input class="required ee_fem_time_value" type="text" id="add-reg-start" name="registration_startT" value="12:01 AM" />
				</p>
				<p class="event_form_field ee_fem_form_field">
					<label class="date_time" for="registration_end"> <?php echo __('End Date', 'event_espresso') ?><em>*</em></label>
					<input type="text"  id="registration_end" class="datepicker required ee_fem_date_value" name="registration_end" value="<?php echo  date('Y-m-d',time() + (60 * 60 * 24 * 29)); ?>" />
					<label class="date_time time" for="registration_endT">
						<?php _e('Time', 'event_espresso'); ?>
						<em>*</em> </label>
					<input class="required ee_fem_time_value" type="text" id="registration_endT" name="registration_endT" value="11:59 PM" />
					<br />
					<span class="description">
					<?php _e('All events <strong>require</strong> registration start/end dates and start/end times in order to display properly on your pages.', 'event_espresso'); ?>
					</span></p>
				<?php
			//Doesn't hide the display for some reason.
			/*
			if ((isset($org_options['use_event_timezones']) && $org_options['use_event_timezones'] = 'Y') && $espresso_premium == true) { ?>
		<p><span class="run-in">
			<strong><?php _e('Current Server Time:', 'event_espresso'); ?></strong>
			</span><br /><span class="current-date"> <?php echo date(get_option('date_format')) . ' ' . date(get_option('time_format')); ?></span></p>
		<?php
			}*/
		?>
			</div>
		</div>
		<div id="add-event-dates" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Dates & Times', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<p class="event_form_field ee_fem_form_field">
					<label class="date_time" for="start_date">
						<?php  _e('Start Date', 'event_espresso') ?><em>*</em></label>
					<input type="text"  id="start_date" class="datepicker required ee_fem_date_value" name="start_date" value="<?php echo date('Y-m-d',time() + (60 * 60 * 24 * 30)); ?>" />
					<label class="date_time time" for="add-start-time">
						<?php _e('Time', 'event_espresso'); ?>
						<em>*</em></label>
					<input class="required ee_fem_time_value"  type="text" id="add-start-time" name="start_time[]" value="8:00 AM" />
				</p>
				<p class="event_form_field ee_fem_form_field">
					<label class="date_time" for="end_date">
						<?php  _e('End Date', 'event_espresso') ?>
						<em>*</em></label>
					<input type="text"  id="end_date" class="datepicker required ee_fem_date_value" name="end_date" value="<?php echo date('Y-m-d',time() + (60 * 60 * 24 * 30)); ?>" />
					<label class="date_time time" for="add-end-time">
						<?php _e('Time', 'event_espresso'); ?>
						<em>*</em></label>
					<input class="required ee_fem_time_value"   type="text" id="add-end-time" name="end_time[]" value="5:00 PM" />
					<br />
					<span class="description">
					<?php _e('All events <strong>require</strong> a start and end date in order to display properly on your pages.', 'event_espresso'); ?>
					</span></p>
			</div>
		</div>
		
		<?php if (isset($org_options['fem_settings']['enable_fem_pricing_section']) && $org_options['fem_settings']['enable_fem_pricing_section'] == 'Y'): ?>
		<!-- Pricing -->
		<div id="add-pricing" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<?php (defined('EVENTS_MEMBER_REL_TABLE'))? $members_active = 'class="members-active"' : $members_active = ''; ?>
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Event Pricing', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<table class="fem-pricing-table" <?php echo $members_active ?>width="100%" border="0" cellpadding="5">
					<tr valign="top">
						<td id="standard-pricing" class="fem-pricing-td"><?php event_espresso_multi_price_update($event_id); //Standard pricing ?></td>
						<?php
							//If the members addon is installed, define member only event settings
							if  (defined('EVENTS_MEMBER_REL_TABLE') && $espresso_premium == true) { ?>
								<td id="member-pricing" class="b"><?php echo event_espresso_member_only_pricing(); //Show the the member only pricing options. ?></td>
						<?php
							}
						?>
					</tr>
				</table>
			</div>
		</div>
		
		
		<?php endif; ?>
		
		<?php if (isset($org_options['fem_settings']['enable_fem_category_select']) && $org_options['fem_settings']['enable_fem_category_select'] == 'Y'): ?>
		<!-- Categories -->
		<div id="add-categories" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Event Category', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<?php //echo event_espresso_get_categories(0, true); //Shows a list of checkboxes of categories ?>
				<p class="event_form_field ee_fem_form_field"><?php echo event_espresso_categories_dd(0, true); //Shows a dropdown of categories ?></p>
			</div>
		</div>
		<?php endif; ?>
		<?php if ($use_venues == TRUE):?>
		<div id="add-venue" class="event_data event-data-display event-display-boxes <?php echo $use_themeroller == TRUE ? 'ui-widget':''; ?>">
			<h3 class="event_title <?php echo $use_themeroller == TRUE ? 'ui-widget-header ui-corner-top':''; ?>">
				<?php _e('Event Venue', 'event_espresso'); ?>
			</h3>
			<div class="event-data-display <?php echo $use_themeroller == TRUE ? 'ui-widget-content ui-corner-bottom':''; ?>">
				<label class="width3 first" for="venue_name">
					<?php _e('Search for a Venue', 'event_espresso'); ?>
				</label>
				<input id="ee_fem_autocomplete" />
				<input id="venue_id" name="venue_id[]" type="hidden" value="0">
				<input id="add_new_venue_dynamic" name="add_new_venue_dynamic" type="hidden" value="true">
				<p><a href="" id="toggle_venue">
					<?php _e('+ Add a new venue', 'event_espresso'); ?>
					</a></p>
				<div id="new_venue_info" style="display:none;"> <br />
					<p class="event_form_field ee_fem_form_field">
						<label class="width3 first" for="venue_name">
							<?php _e('Venue Name', 'event_espresso'); ?>
						</label>
						<input id="venue_name" name="venue_name" type="text" class="text" value="" />
					</p>
					<p class="event_form_field ee_fem_form_field">
						<label class="width3 first" for="venue_address">
							<?php _e('Address', 'event_espresso'); ?>
						</label>
						<input id="venue_address" name="venue_address" type="text" class="text" value="" />
					</p>
					<p class="event_form_field ee_fem_form_field">
						<label class="width3 first" for="venue_address2">
							<?php _e('Address Line 2', 'event_espresso'); ?>
						</label>
						<input id="venue_address2" name="venue_address2" type="text" class="text" value="" />
					</p>
					<p class="event_form_field ee_fem_form_field">
						<label class="width2 first" for="venue_city">
							<?php _e('City', 'event_espresso'); ?>
						</label>
						<input id="venue_city" name="venue_city" type="text" class="text" value="" />
					</p>
					<p class="event_form_field ee_fem_form_field">
						<label class="width2 first" for="venue_state">
							<?php _e('State', 'event_espresso'); ?>
						</label>
						<input id="venue_state" name="venue_state" type="text" class="text" value="" />
					</p>
					<p class="event_form_field ee_fem_form_field">
						<label class="width1 first" for="venue_zip">
							<?php _e('Postal Code', 'event_espresso'); ?>
						</label>
						<input id="venue_zip" name="venue_zip" class="text" value="" type="text">
					</p>
				</div>
			</div>
		</div>
		<?php endif;?>
		<?php /* DO NOT REMOVE  */?>
		<?php wp_nonce_field('espresso_form_check', 'ee_fem_nonce'); //Security check using nonce ?>
		<input type="hidden" name="ee_fem_action" value="ee_fem_add" />
		<?php /* End DO NOT REMOVE */?>
		<p class="register-link-footer">
			<input class="<?php echo $use_themeroller == TRUE ? 'ui-button ui-button-big ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all':''; ?>" type="submit" name="Submit" value="<?php _e('Submit Event', 'event_espresso'); ?>" id="add_new_event" />
		</p>
	</form>
</div>
<script type="text/javascript" charset="utf-8">
			//<![CDATA[
			jQuery(document).ready(function() {
				
				//Date selector
				jQuery(".datepicker" ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: "yy-mm-dd",
					showButtonPanel: true
				});
				jQuery("#start_date").change(function(){
					jQuery("#end_date").val(jQuery(this).val());
				});
				//End Date selector
				
				<?php if ($use_venues == TRUE):?>
				//Venue selector
				//Found this solution: http://www.codingforums.com/showthread.php?t=198167
				jQuery("#toggle_venue").click(function(j){
					
					j.preventDefault();
					jQuery("#new_venue_info").fadeToggle(500);
					jQuery("#ee_fem_autocomplete").val('');
					jQuery("#venue_id").val(0);
				
				});
				//End Venue selector
				
				//Auto complete
				jQuery("input#ee_fem_autocomplete").autocomplete({
					source: [
						//"c++", "java", "php", "coldfusion", "javascript", "asp", "ruby"
						//{ label: "Choice1", value: "value1" }
						<?php 
								global $wpdb, $espresso_manager, $current_user;
		
								$WHERE = " WHERE ";
								$sql = "SELECT ev.*, el.name AS locale FROM " . EVENTS_VENUE_TABLE . " ev ";
								$sql .= " LEFT JOIN " . EVENTS_LOCALE_REL_TABLE . " lr ON lr.venue_id = ev.id ";
								$sql .= " LEFT JOIN " . EVENTS_LOCALE_TABLE . " el ON el.id = lr.locale_id ";
						
								if(  function_exists('espresso_member_data') && ( espresso_member_data('role')=='espresso_group_admin' ) ){
									if(	$espresso_manager['event_manager_venue'] == "Y" ){
										//show only venues inside their assigned locales.
										$group = get_user_meta(espresso_member_data('id'), "espresso_group", true);
										$group = unserialize($group);
										$sql .= " $WHERE lr.locale_id IN (" . implode(",", $group) . ")";
										$sql .= " OR ev.wp_user = ".$current_user->ID ;
										$WHERE = " AND ";
									}
								}
								$sql .= " GROUP BY ev.id ORDER by name";
								
								$venues = $wpdb->get_results($sql);
								$num_rows = $wpdb->num_rows;
								
								if ($num_rows > 0) {
									foreach ($venues as $venue){
										//An Array of Objects with label and value properties:
										echo '{ value: "'.stripslashes_deep($venue->name) . ' (' . stripslashes_deep($venue->city) . ', ' . stripslashes_deep($venue->state) . ')", id: "'.$venue->id.'"},';
									}
								}
						?>
					],
					select: function(event,ui){
						jQuery('#venue_id').val(ui.item.id);
						
						//jQuery("#new_venue_info").fadeToggle(500);
						jQuery("#venue_name").val('');
						jQuery("#venue_address").val('');
						jQuery("#venue_address_2").val('');
						jQuery("#venue_city").val('');
						jQuery("#venue_state").val('');
						jQuery("#venue_zip").val('');
					}
				});
				//End auto complete
				<?php endif;?>
				//Form validation
				jQuery(function(){
					jQuery('#ee_fem_form').validate();
				});
				
			 });
		
			//]]>
		</script>
<?php /* End Autocomplete Script */?>
<?php
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}


