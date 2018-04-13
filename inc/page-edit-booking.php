<?php

/* Generate admin page "Edit booking details" and all functions */
function editbooking() {
	
	/* If no bookingID is given, redirect to booking overview page */
	if(!isset($_GET["booking_id"]) or !is_numeric($_GET["booking_id"]) or $_GET["booking_id"] == "") {
		echo '<script type="text/javascript">window.location = "edit.php?post_type=event&page=bookings";</script>';
		exit();
	}
	
	/* Prepare bookingID */
	$_booking_id = intval($_GET["booking_id"]);
	
	/* Print the headline */
	echo '<div class="wrap">';
	echo '  <h2 class="wp-heading-inline">' . __('Edit booking details', 'event-organiser-extended-admin-interface') . '</h2>';
	echo '</div>';
	
	/* Check if bookingID is valid */
	if(get_post_type($_booking_id) == "eo_booking") {
		
		/* get meta data to given bookingID */
		$meta = get_post_meta( $_booking_id );
		
	} else {
		
		/* Print error message if bookingID is invalid */
		echo '<div class="notice notice-error">';
		echo '<p>' . __('The transferred ID is not a valid booking ID!', 'event-organiser-extended-admin-interface') . '</p>';
		echo '<p><a href="edit.php?post_type=event&page=bookings">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
		echo '</div>';
		
		/* Exit */
		exit();
		
	}
	
	/* If form isn't send yet */
	if(!$_POST["action"] == "save") {
		
		/* If there are meta datas */
		if(count($meta) > 0) {
			
			/* Check if a valid bookingFormID is given */
			if($meta["_eo_booking_form"] == -1 or $meta["_eo_booking_form"] == '') {
				
				/* Print error message if bookingFormID is invalid */
				echo ' <p>';
				printf( __('The booking with booking reference <b>#%1$s</b> cannot be modified because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), $_booking_id, $meta["_eo_booking_form"][0] );
				echo ' </p>';
				
			} else {
				
				/* Print form */
				echo '<form action="edit.php?post_type=event&page=editbooking&booking_id=' . $_booking_id . '" method="post">';
				echo ' <input type="hidden" name="booking_id" value="' . $_booking_id . '" />';
				echo ' <input type="hidden" name="action" value="save" />';
				echo ' <table class="form-table">';
				echo '  <tbody>';
				
				/* Get meta data of the given form */
				$_form_meta	= get_post_meta( $meta["_eo_booking_form"][0] );
				$_form_data	= unserialize( $_form_meta["_eo_booking_form_fields"][0] );
				
				/* Check if it is a valid form */
				if(get_post_type( $meta["_eo_booking_form"][0] ) == "eo_booking_form" &&  count( $_form_data ) > 0) {
					
					/* Generate the form */
					foreach($_form_data as $_form_data_id => $_form_data_values) {
						
						/* reset the fieldname variable on each turn */
						$_fieldname = '';
						
						/* switch cases for type attribute */
						switch ($_form_data_values["type"]) {
							case 'radio':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate radio inputs */
								foreach($_form_data_values["options"] as $_option_val => $_option_name) {
									echo '     <input type="radio" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $_option_name . '"'; echo ($meta[$_fieldname][0] == $_option_name) ? ' checked="checked"' : ''; echo '>' . $_option_name . '</input><br />';
								}
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'name':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '_fname">' . __('First Name', 'event-organiser-extended-admin-interface') . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate first name input */
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '[]" id="' . $_fieldname . '_fname" value="' . $meta[$_fieldname][0] . '" placeholder="Max" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '_lname">' . __('Last Name', 'event-organiser-extended-admin-interface') . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate last name input */
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '[]" id="' . $_fieldname . '_lname" value="' . $meta[$_fieldname][1] . '" placeholder="Mustermann" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'date':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate date input */
								echo '     <input class="regular-text" type="date" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . date("Y-m-d", strtotime($meta[$_fieldname][0])) . '" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'address':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate address inputs */
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_street-address" name="' . $_fieldname . '_street-address" value="' . $meta[$_fieldname . '_street-address'][0] . '" placeholder="Musterstraße 123" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_2nd-line" name="' . $_fieldname . '_2nd-line" value="' . $meta[$_fieldname . '_2nd-line'][0] . '" placeholder="c/o Firma Mustermann GmbH&Co.KG" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_postcode" name="' . $_fieldname . '_postcode" value="' . $meta[$_fieldname . '_postcode'][0] . '" placeholder="12345" />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_city" name="' . $_fieldname . '_city" value="' . $meta[$_fieldname . '_city'][0] . '" placeholder="Musterstadt" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_state" name="' . $_fieldname . '_state" value="' . $meta[$_fieldname . '_state'][0] . '" placeholder="NRW" />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_country" name="' . $_fieldname . '_country" value="' . $meta[$_fieldname . '_country'][0] . '" placeholder="Deutschland" /><br />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'email':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate email input */
								echo '     <input class="regular-text" type="email" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $meta[$_fieldname][0] . '" placeholder="max@mustermann.de" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'input':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate email input */
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $meta[$_fieldname][0] . '" placeholder="Text" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'phone':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate telephone input */
								echo '     <input class="regular-text" type="tel" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $meta[$_fieldname][0] . '" placeholder="00490123456789" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'textarea':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate textarea input */
								echo '     <textarea class="widefat" name="' . $_fieldname . '" id="' . $_fieldname . '">' . $meta[$_fieldname][0] . '</textarea>';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'url':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate url input */
								echo '     <input class="regular-text" type="url" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $meta[$_fieldname][0] . '" placeholder="http://www.musterseite.de/" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'number':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate number input */
								echo '     <input class="regular-text" type="number" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $meta[$_fieldname][0] . '" min="' . $_form_data_values["min"] . '" max="' . $_form_data_values["min"] . '" placeholder="1" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'select':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate select tag */
								echo '     <select class="regular-text" name="' . $_fieldname . '" id="' . $_fieldname . '">';
								
								/* generate options */
								foreach($_form_data_values["options"] as $_option_val => $_option_name) {
									echo '      <option value="' . $_option_name . '"'; echo ($meta[$_fieldname][0] == $_option_name) ? ' selected="selected"' : ''; echo '>' . $_option_name . '</option>';
								}
								
								/* close select tag */
								echo '     </select>';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'multiselect':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* create empty array, if there is no array to avoid error messages */
								if(!is_array($meta[$_fieldname])) {
									$meta[$_fieldname] = array();
								}
								
								/* generate select tag */
								echo '     <select multiple class="regular-text" name="' . $_fieldname . '[]" id="' . $_fieldname . '">';
								
								/* generate options */
								foreach($_form_data_values["options"] as $_option_val => $_option_name) {
									echo '      <option value="' . $_option_name . '"'; echo (in_array($_option_name, $meta[$_fieldname]) == true) ? ' selected="selected"' : ''; echo '>' . $_option_name . '</option>';
								}
								
								/* close select tag */
								echo '     </select>';
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							case 'checkbox':
								/* set fieldname */
								$_fieldname = '_eo_booking_meta_' . $_form_data_id;
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '">' . $_form_data_values["label"] . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* create empty array, if there is no array to avoid error messages */
								if(!is_array($meta[$_fieldname])) {
									$meta[$_fieldname] = array();
								}
								
								/* generate checkbox inputs */
								foreach($_form_data_values["options"] as $_option_val => $_option_name) {
									echo '       <input type="checkbox" class="regular-text" name="' . $_fieldname . '[]" id="' . $_fieldname . '" value="' . $_option_name . '"'; echo (in_array($_option_name, $meta[$_fieldname]) == true) ? ' checked="checked"' : ''; echo '>' . $_option_name . '</input><br />';
								}
								
								echo '    </td>';
								echo '   </tr>';
								
								break;
								
							default:
								/* print out nothing */
								break;
						}
					}
					
					/* close tbody and table tags */
					echo '  </tbody>';
					echo ' </table>';
					
					/* print the submit button */
					echo ' <input type="submit" id="submitBttn" value="' . __('Save', 'event-organiser-extended-admin-interface') . '" class="button-primary" />';
				
				} else {
					
					/* if the bookingFormID is invalid */
					echo '   <tr>';
					echo '    <td>';
					printf( __('The booking with booking reference <b>#%1$s</b> cannot be modified because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), $_booking_id, $meta["_eo_booking_form"][0] );
					echo '    </td>';
					echo '   </tr>';
					echo '  </tbody>';
					echo ' </table>';
					
				}
				
				/* close the form tag */
				echo '</form>';
				
				/* Exit */
				exit();
			}
			
		} else {
			
			/* Print out error message for invalid booking form */
			echo ' <p>';
			printf( __('The booking with booking reference <b>#%1$s</b> cannot be modified because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), $_booking_id, $meta["_eo_booking_form"][0] );
			echo ' </p>';
			
		}
		
	} else {
		
		/* prepare form data */
		$_form_meta	= get_post_meta( $meta["_eo_booking_form"][0] );
		$_form_data	= unserialize( $_form_meta["_eo_booking_form_fields"][0] );
		
		/* check if booking form is available */
		if(get_post_type( $meta["_eo_booking_form"][0] ) == "eo_booking_form" &&  count( $_form_data ) > 0) {
			
			/* save all booking form fields to meta data of the booking */
			foreach($_form_data as $_form_data_id => $_form_data_values) {
				
				/* reset fieldname on each turn */
				$_fieldname = '';
				
				/* switch case for type attribute */
				switch ($_form_data_values["type"]) {
					case 'radio':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'name':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* delete old meta data */
						delete_post_meta($_booking_id, $_fieldname);
						
						/* Save meta data */
						add_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname][0]);
						add_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname][1]);
						update_post_meta($_booking_id, '_eo_booking_anon_first_name', $_POST[$_fieldname][0]);
						update_post_meta($_booking_id, '_eo_booking_anon_last_name', $_POST[$_fieldname][1]);
						update_post_meta($_booking_id, '_eo_booking_anon_display_name', $_POST[$_fieldname][0] . ' ' . $_POST[$_fieldname][1]);
						
						break;
						
					case 'date':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'address':
						/* set fieldnames */
						$_fieldnames = array();
						foreach($_form_data_values["components"] as $_fieldname_component) {
							$_fieldnames[] = '_eo_booking_meta_' . $_form_data_id . '_' . $_fieldname_component;
						}
						
						/* Save meta data */
						foreach($_fieldnames as $_fieldname) {
							update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						}
						
						break;
						
					case 'email':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						update_post_meta($_booking_id, '_eo_booking_anon_email', $_POST[$_fieldname]);
						
						break;
						
					case 'input':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'phone':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'textarea':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'url':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'number':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'select':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						update_post_meta($_booking_id, $_fieldname, $_POST[$_fieldname]);
						
						break;
						
					case 'multiselect':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						delete_post_meta($_booking_id, $_fieldname);
						
						/* create empty array, if there is no array to avoid error messages */
						if(!is_array($_POST[$_fieldname])) {
							$_POST[$_fieldname] = array($_POST[$_fieldname]);
						}
						
						/* Save each meta data */
						foreach($_POST[$_fieldname] as $_index => $_new_value) {
							add_post_meta($_booking_id, $_fieldname, $_new_value, false);
						}
						
						break;
						
					case 'checkbox':
						/* set fieldname */
						$_fieldname = '_eo_booking_meta_' . $_form_data_id;
						
						/* Save meta data */
						delete_post_meta($_booking_id, $_fieldname);
						
						/* create empty array, if there is no array to avoid error messages */
						if(!is_array($_POST[$_fieldname])) {
							$_POST[$_fieldname] = array($_POST[$_fieldname]);
						}
						
						/* Save each meta data */
						foreach($_POST[$_fieldname] as $_index => $_new_value) {
							add_post_meta($_booking_id, $_fieldname, $_new_value, false);
						}
						
						break;
						
					default:
						/* In all other cases: do nothing */
						break;
				}
			}
			
		} else {
			
			/* if the bookingFormID is invalid */
			echo '<div class="notice notice-error">';
			echo '<p>';
			printf( __('The booking with booking reference <b>#%1$s</b> cannot be modified because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), $_booking_id, $meta["_eo_booking_form"][0] );
			echo '</p>';
			echo '<p><a href="edit.php?post_type=event&page=bookings">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
			echo '</div>';
			
			/* Exit */
			exit();
			
		}
		
		/* Print out success message an back link */
		echo '<div class="notice notice-success">';
		echo '<p>';
		printf( __('The booking with the booking reference <a href="edit.php?post_type=event&page=bookings&action=edit&booking_id=%1$s"><b>#%1$s</b></a> has been successfully processed.', 'event-organiser-extended-admin-interface'), $_booking_id );
		echo '</p>';
		echo '<p><a href="edit.php?post_type=event&page=bookings">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
		echo '</div>';
		
		/* Exit */
		exit();
		
	}
	
}

?>