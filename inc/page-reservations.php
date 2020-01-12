<?php

/* Generate admin page "Reservate Tickets" and all functions */
function reservations() {
	
	/* Print the headline */
	echo '<div class="wrap">';
	echo '  <h2 class="wp-heading-inline">' . __('Reservate Tickets', 'event-organiser-extended-admin-interface') . '</h2>';
	echo '</div>';
	
	
	/* If the form isn't send yet */
	if(!$_POST["action"] == "create") {
		
		/* Get all events from today to future */
		$events = eo_get_events(array(
			'numberposts'		=> -1,
			'event_start_after'	=> 'today',
			'showpastevents'	=> false		// Will be deprecated, but set it to true to play it safe.
		 ));
		
		/* If there are one or more events */
		if($events):
			
			/* set dateformat */
			$format = "d.m.Y";
			
			/* Generate js function to switch page on event selection */
			echo '<script type="text/javascript">';
			echo ' jQuery(function($){function selectedEventChange(e) {';
			echo '  var remainingT = $(e).find(":selected").data("remainingtickets");';
			echo '  var eventID 			= $(e).find(":selected").data("eventid");';
			echo '  var eventOccurrenceID	= $(e).find(":selected").data("eventoccurrenceid");';
			echo '  $(e).attr("disabled", "disbaled");';
			echo '  $("#wait_image").removeAttr("hidden");';
			echo '  location.href = "?post_type=event&page=reservations&event_id=" + eventID + "&event_occurence_id=" + eventOccurrenceID';
			echo ' }});';
			echo '</script>';
			
			/* If there is a valid eventID and optional a valid OccurrenceID */
			if(isset($_GET["event_id"]) && intval($_GET["event_id"]) != "" && get_post_type(intval($_GET["event_id"])) == "event" && isset($_GET["event_occurence_id"])) {
				/* get tickets on sale for choosen event */
				$_remaining_tickets = 0;
				$_remaining_tickets = eo_get_remaining_tickets_count(intval($_GET["event_id"]), intval($_GET["event_occurence_id"]));
				/* show message if there are no tickets left for the choosen event */
				if($_remaining_tickets == 0) {
					echo '<div id="warningmsg" class="notice notice-warning">';
					echo ' <p>' . __('All seats are already occupied for this appointment. Reservations can no longer be made.', 'event-organiser-extended-admin-interface') . '</p>';
					echo '</div>';
					
					/* set a echo-var to disable some inputs */
					$disabled = ' disabled="disabled"';
				}
			}
			
			/* Print part one of the reservation form */
			echo ' <h3 class="wp-heading-inline">' . __('Reservation details', 'event-organiser-extended-admin-interface') . '</h3>';
			echo ' <p>' . __('Please select the date for which you would like to make a reservation and then the number of tickets to reserve. Under Comments you can optionally enter a note for the reservation. This can be edited later.', 'event-organiser-extended-admin-interface') . '</p>';
			echo '<form action="edit.php?post_type=event&page=reservations" method="post">';
			echo ' <input type="hidden" name="action" value="create" />';
			echo ' <table class="form-table">';
			echo '  <tbody>';
			echo '   <tr>';
			echo '    <th scope="row">';
			echo '     <label for="selectedEvent">' . __('Event', 'event-organiser-extended-admin-interface') . '</label>';
			echo '    </th>';
			echo '    <td>';
			echo '     <select id="selectedEvent" name="selectedEvent" onchange="selectedEventChange(this);">';
			echo '      <option value=""></option>';
			
			/* print an option for each event */
			foreach ($events as $event):
				echo '      <option data-remainingtickets="' . eo_get_remaining_tickets_count($event->ID, $event->occurrence_id) . '" data-eventid="' . $event->ID . '" data-eventoccurrenceid="' . $event->occurrence_id . '" value="' . $event->ID . '|' . $event->occurrence_id . '"';
				
				/* if the given eventID is equal to the ID of the option make it selected */
				if(intval($_GET["event_id"]) == $event->ID) echo ' selected';
				
				echo '>#' . get_post_meta($event->ID, 'eoeai-class-no', true) . ' | EventID: ' . $event->ID . ' | ' . eo_get_the_start( $format, $event->ID, $event->occurrence_id ) . ' | ' . get_the_title($event->ID) . '</option>\n';
			endforeach;
			
			/* close select tag and print another part of the reservation form */
			echo '     </select> <img id="wait_image" src="' .  plugins_url( '/../img/wait.gif', __FILE__ ) . '" hidden="hidden"><br>';
			echo '     <span class="description">' . __('Select the appointment from the list for which tickets are to be reserved.', 'event-organiser-extended-admin-interface') . '</span>';
			echo '    </td>';
			echo '   </tr>';
			
			/* If there is a valid eventID, print the second part of the reservation form */
			if(isset($_GET["event_id"]) && intval($_GET["event_id"]) != "" && get_post_type(intval($_GET["event_id"])) == "event") {
			
				echo '   <tr>';
				echo '    <th scope="row">';
				echo '     <label for="reservationTicketType">' . __('Ticket Type', 'event-organiser-extended-admin-interface') . '</label>';
				echo '    </th>';
				echo '    <td>';
				
				/* get all tickets on sale for the choosen event */
				$_tickets_on_sale = eo_get_event_tickets(intval($_GET["event_id"]), intval($_GET["event_occurence_id"]));
				
				/* generate a select for the tickets on sale */
				echo '     <select class="reservationTicketType" name="reservationTicketType" id="reservationTicketType"' . $disabled . '>';
				
				/* generate an option for each ticket on sale */
				foreach ($_tickets_on_sale as $_tickets_on_sale_mid => $_tickets_on_sale_details):
					echo '      <option value="' . $_tickets_on_sale_mid . '">' . $_tickets_on_sale_details['name'] . ' (' . __('Price', 'event-organiser-extended-admin-interface') . ': ' . number_format($_tickets_on_sale_details['price'], 2) . ' EUR)</option>';
				endforeach;
				
				/* close select tag */
				echo '     </select>';
				
				/* print another part of the reservation form */
				echo '     <br />';
				echo '     <span class="description">' . __('Select ticket type from the list to be reserved.', 'event-organiser-extended-admin-interface') . '</span>';
				echo '    </td>';
				echo '   </tr>';
				echo '   <tr>';
				echo '    <th scope="row">';
				echo '     <label for="reservationTicketNumber">' . __('Reservations', 'event-organiser-extended-admin-interface') . '</label>';
				echo '    </th>';
				echo '    <td>';
				echo '     <input type="number" id="reservationTicketNumber" name="reservationTicketNumber" min="1" max="' . $_remaining_tickets . '" value="1" required="required"' . $disabled . ' /> ';
				echo '     <span id="remainingTicketsCount">';
				
				/* print the remaining tickets counter */
				printf( _n( '%s Ticket available', '%s Tickets available', $_remaining_tickets, 'event-organiser-extended-admin-interface' ), $_remaining_tickets );
				
				echo '     </span><br>';
				echo '     <span class="description">' . __('Enter the number of tickets to be reserved here.', 'event-organiser-extended-admin-interface') . '</span>';
				echo '    </td>';
				echo '   </tr>';
				echo '   <tr>';
				echo '    <th scope="row">';
				echo '     <label for="reservationNote">' . __('Remarks', 'event-organiser-extended-admin-interface') . '</label>';
				echo '    </th>';
				echo '    <td>';
				echo '     <textarea class="widefat" id="reservationNote" name="reservationNote"' . $disabled . '>' . __('Reservation by administrator', 'event-organiser-extended-admin-interface') . '</textarea><br>';
				echo '     <span class="description">' . __('Enter here a comment to be saved to the reservation.', 'event-organiser-extended-admin-interface') . '</span>';
				echo '    </td>';
				echo '   </tr>';
				echo '  </tbody>';
				echo ' </table>';
				echo ' <p>&nbsp;</p>';
				echo ' <p>&nbsp;</p>';
				echo ' <h3 class="wp-heading-inline">' . __('Optional reservation information', 'event-organiser-extended-admin-interface') . '</h3>';
				echo ' <p>' . __('The following information is optional and can be edited later.', 'event-organiser-extended-admin-interface') . '</p>';
				
				/* get meta data of the choosen event */
				$meta = get_post_meta( intval($_GET["event_id"]) );
				
				/* print the meta form based on the formID of the choosen event */
				echo ' <table class="form-table">';
				echo '  <tbody>';
				
				/* prepare form data */
				$_form_meta	= get_post_meta( $meta["_eventorganiser_booking_form"][0] );
				$_form_data	= unserialize( $_form_meta["_eo_booking_form_fields"][0] );
				
				/* check the booking form */
				if(get_post_type( $meta["_eventorganiser_booking_form"][0] ) == "eo_booking_form" &&  count( $_form_data ) > 0) {
					
					/* render each meta field */
					foreach($_form_data as $_form_data_id => $_form_data_values) {
						
						/* reset fieldname on each turn */
						$_fieldname = '';
						
						/* switch case for type attribute */
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
									echo '     <input type="radio" name="' . $_fieldname . '" id="' . $_fieldname . '" value="' . $_option_name . '"' . $disabled . '>' . $_option_name . '</input><br />';
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
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '[]" id="' . $_fieldname . '_fname" placeholder="Max" />';
								
								echo '    </td>';
								echo '   </tr>';
								
								/* print label tag */
								echo '   <tr>';
								echo '    <th scope="row">';
								echo '     <label for="' . $_fieldname . '_lname">' . __('Last Name', 'event-organiser-extended-admin-interface') . '</label>';
								echo '    </th>';
								echo '    <td>';
								
								/* generate last name input */
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '[]" id="' . $_fieldname . '_lname" placeholder="Mustermann" />';
								
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
								echo '     <input class="regular-text" type="date" name="' . $_fieldname . '" id="' . $_fieldname . '"' . $disabled . ' />';
								
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
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_street-address" name="' . $_fieldname . '_street-address" placeholder="Musterstraße 123" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_2nd-line" name="' . $_fieldname . '_2nd-line" placeholder="c/o Firma Mustermann GmbH&Co.KG" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_postcode" name="' . $_fieldname . '_postcode" placeholder="12345" />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_city" name="' . $_fieldname . '_city" placeholder="Musterstadt" /><br />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_state" name="' . $_fieldname . '_state" placeholder="NRW" />';
								echo '     <input type="text" class="regular-text" id="' . $_fieldname . '_country" name="' . $_fieldname . '_country" placeholder="Deutschland" /><br />';
								
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
								echo '     <input class="regular-text" type="email" name="' . $_fieldname . '" id="' . $_fieldname . '" placeholder="max@mustermann.de" />';
								
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
								
								/* generate telephone input */
								echo '     <input class="regular-text" type="text" name="' . $_fieldname . '" id="' . $_fieldname . '" placeholder="Text" />';
								
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
								echo '     <input class="regular-text" type="tel" name="' . $_fieldname . '" id="' . $_fieldname . '" placeholder="00490123456789" />';
								
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
								echo '     <textarea class="widefat" name="' . $_fieldname . '" id="' . $_fieldname . '"></textarea>';
								
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
								echo '     <input class="regular-text" type="url" name="' . $_fieldname . '" id="' . $_fieldname . '" placeholder="http://www.musterseite.de/" />';
								
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
								echo '     <input class="regular-text" type="number" name="' . $_fieldname . '" id="' . $_fieldname . '" min="' . $_form_data_values["min"] . '" max="' . $_form_data_values["min"] . '" placeholder="1" />';
								
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
									echo '      <option value="' . $_option_name . '">' . $_option_name . '</option>';
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
									echo '      <option value="' . $_option_name . '">' . $_option_name . '</option>';
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
									echo '       <input type="checkbox" class="regular-text" name="' . $_fieldname . '[]" id="' . $_fieldname . '" value="' . $_option_name . '">' . $_option_name . '</input><br />';
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
					printf( __('A reservation for the event with the eventID <b>#%1$s</b> cannot be made because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), intval($_GET["event_id"]), $meta["_eventorganiser_booking_form"][0] );
					echo '    </td>';
					echo '   </tr>';
					echo '  </tbody>';
					echo ' </table>';
					
				}
				
				/* close the form tag */
				echo '</form>';
			
			} else {
				
				/* if the eventID is invalid
				 * do nothing
				 */
				
			}
			
		endif;
	}
	
	/* if form was sent */
	if($_POST["action"] == "create") {
		
		/* if a event is given */
		if(isset($_POST["selectedEvent"]) && $_POST["selectedEvent"] != "") {
			
			/* set some variables for later use */
			$selectedEvent				= explode('|', $_POST["selectedEvent"]);
			$selectedEventID			= intval($selectedEvent[0]);
			$selectedEventOccurrenceID	= intval($selectedEvent[1]);
			$reservationTicketNumber	= intval($_POST["reservationTicketNumber"]);
			$reservationNote			= addslashes($_POST["reservationNote"]);
			$remainingTickets			= eo_get_remaining_tickets_count($selectedEventID, $selectedEventOccurrenceID);
			$ticket_type_id				= intval($_POST["reservationTicketType"]);
			
			/* set the ticket type id in case no ticket type id is given to -1 */
			if(isset($ticket_type_id) && $ticket_type_id != "" && $ticket_type_id != 0) {
				$ticket_type_id = $ticket_type_id;
			} else {
				$ticket_type_id = -1;
			}
			
			/* get meta data of the choosen event */
			$meta = get_post_meta( $selectedEventID );
			
			/* prepare form data */
			$_form_meta	= get_post_meta( $meta["_eventorganiser_booking_form"][0] );
			$_form_data	= unserialize( $_form_meta["_eo_booking_form_fields"][0] );
			
			/* check reservationTicketNumber */
			if(is_numeric($reservationTicketNumber) && $reservationTicketNumber > 0) {
				
				/* check if there are enough remaining tickets left */
				if(intval($reservationTicketNumber) <= intval($remainingTickets)) {
					
					/* generate the ticket instances array */
					$ticketinstances = array();
					
					/* generate each ticket and save it in the ticket instances array */
					for ($i = 0; $i < $reservationTicketNumber; $i++) {
						$ticketinstances[$ticket_type_id][$i] = array( 'eocid' => 'automatic_reservation_'.time().'_'.($i+1), 'type' => $ticket_type_id );
					}
					
					/* generate the booking array */
					$booking = array(
						'booking_user'		=> 0,
						'booking_status'	=> 'reserved',
						'event_id'			=> $selectedEventID,
						'booking_notes'		=> $reservationNote,
						'occurrence_id'		=> $selectedEventOccurrenceID,
						'ticketinstances'	=> $ticketinstances,
				   );
				   
				   /* do the booking and recieve the new bookingID and booking references */
				   $booking_id			= eo_insert_booking( $booking );
				   $booking_references	= json_decode(json_encode(eo_get_booking_tickets($booking_id, false)), true);
				   
				   /* Set booking form id */
				   add_post_meta($booking_id, '_eo_booking_form', $meta["_eventorganiser_booking_form"][0], true);
				   
				   /* check if booking form is available */
				   if(get_post_type( $meta["_eventorganiser_booking_form"][0] ) == "eo_booking_form" &&  count( $_form_data ) > 0) {
					   
					   /* save all booking form fields to meta data of the new booking */
					   foreach($_form_data as $_form_data_id => $_form_data_values) {
							
							/* reset fieldname on each turn */
							$_fieldname = '';
							
							/* switch case for type attribute */
							switch ($_form_data_values["type"]) {
								case 'radio':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'name':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname][0]);
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname][1]);
									add_post_meta($booking_id, '_eo_booking_anon_first_name', $_POST[$_fieldname][0]);
									add_post_meta($booking_id, '_eo_booking_anon_last_name', $_POST[$_fieldname][1]);
									add_post_meta($booking_id, '_eo_booking_anon_display_name', $_POST[$_fieldname][0] . ' ' . $_POST[$_fieldname][1]);
									
									break;
									
								case 'date':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'address':
									/* set labelnames and fieldnames */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									$_fieldnames = array();
									foreach($_form_data_values["components"] as $_fieldname_component) {
										$_fieldnames[] = '_eo_booking_meta_' . $_form_data_id . '_' . $_fieldname_component;
									}
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									foreach($_fieldnames as $__fieldname) {
										add_post_meta($booking_id, $_fieldname, $_POST[$__fieldname], false);
										add_post_meta($booking_id, $__fieldname, $_POST[$__fieldname]);
									}
									
									break;
									
								case 'email':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									add_post_meta($booking_id, '_eo_booking_anon_email', $_POST[$_fieldname]);
									
									break;
									
								case 'input':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'phone':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'textarea':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'url':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'number':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'select':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* Save meta data */
									add_post_meta($booking_id, $_fieldname, $_POST[$_fieldname]);
									
									break;
									
								case 'multiselect':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* create empty array, if there is no array to avoid error messages */
									if(!is_array($_POST[$_fieldname])) {
										$_POST[$_fieldname] = array($_POST[$_fieldname]);
									}
									
									/* Save each meta data */
									foreach($_POST[$_fieldname] as $_index => $_new_value) {
										add_post_meta($booking_id, $_fieldname, $_new_value, false);
									}
									
									break;
									
								case 'checkbox':
									/* set labelname and fieldname */
									$_labelname = '_eo_booking_label_meta_' . $_form_data_id;
									$_fieldname = '_eo_booking_meta_' . $_form_data_id;
									
									/* Add meta label */
									add_post_meta($booking_id, $_labelname, $_form_data_values["label"], true);
									
									/* create empty array, if there is no array to avoid error messages */
									if(!is_array($_POST[$_fieldname])) {
										$_POST[$_fieldname] = array($_POST[$_fieldname]);
									}
									
									/* Save each meta data */
									foreach($_POST[$_fieldname] as $_index => $_new_value) {
										add_post_meta($booking_id, $_fieldname, $_new_value, false);
									}
									
									break;
									
								default:
									/* In all other cases: do nothing */
									break;
							}
						}
				   }  else {
						
						/* if the bookingFormID is invalid */
						echo '<div class="notice notice-error">';
						echo '<p>';
						printf( __('A reservation for the event with the eventID <b>#%1$s</b> cannot be made because the booking form <b>#%2$s</b> is not available (anymore).', 'event-organiser-extended-admin-interface'), intval($_GET["event_id"]), $meta["_eventorganiser_booking_form"][0] );
						echo '</p>';
						echo '<p><a href="edit.php?post_type=event&page=bookings">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
						echo '</div>';
						
						/* Exit */
						exit();
						
					}
					
					/* Print a success notice */
					echo '<div class="notice notice-success">';
					echo '<p>';
					printf( __('The reservation was successfully completed using the booking reference <a href="edit.php?post_type=event&page=bookings&action=edit&booking_id=%1$s"><b>#%1$s</b></a>.', 'event-organiser-extended-admin-interface'), $booking_id );
					echo '</p>';
					
					/* Print out all new ticket references */
					if(is_array($booking_references)) {
						echo '<p>' . __('Generated Tickets', 'event-organiser-extended-admin-interface') . ': <br /><ul>';
						foreach($booking_references as $ref) {
							echo '<li>' . __('Ticket No.', 'event-organiser-extended-admin-interface') . ' <b>' . strtoupper($ref['ticket_reference']) . '</b></li>';
						}
						echo '</ul></p>';
					}
					
					/* Print out backlink */
					echo '<p><a href="edit.php?post_type=event&page=reservations">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
					echo '</div>';
					
				} else {
					
					/* In case there are not enough seats available */
					echo '<div class="notice notice-error">';
					echo '<p>' . __('There are not enough seats available for this event', 'event-organiser-extended-admin-interface') . '.<br />';
					echo __('Desired reservations', 'event-organiser-extended-admin-interface') . ': ' . $reservationTicketNumber . '<br />';
					echo __('Available reservations', 'event-organiser-extended-admin-interface') . ': ' . $remainingTickets . '</p>';
					echo '<p>' . __('No reservation has been made.', 'event-organiser-extended-admin-interface') . '</p>';
					echo '<p><a href="edit.php?post_type=event&page=reservations">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
					echo '</div>';
					
				}
				
			} else {
				
				/* If no valid number of tickets is given */
				echo '<div class="notice notice-error">';
				echo '<p>' . __('You have not indicated a valid number of reserved tickets!', 'event-organiser-extended-admin-interface') . '</p>';
				echo '<p><a href="edit.php?post_type=event&page=reservations">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
				echo '</div>';
				
			}
			
		} else {
			
			/* If data is missing */
			echo '<div class="notice notice-error">';
			echo '<p>' . __('Not all required data was transmitted!', 'event-organiser-extended-admin-interface') . '</p>';
			echo '<p><a href="edit.php?post_type=event&page=reservations">' . __('Back', 'event-organiser-extended-admin-interface') . '</a></p>';
			echo '</div>';
			
		}
		
	}
	
}

?>