<?php

/* Create new booking status "reserved" */
function register_own_booking_status(){
   eo_register_booking_status( 'reserved', array(
       'label'                => __('Reserved', 'event-organiser-extended-admin-interface'),
       'label_count'          => _n_noop( 'Reservations <span class="count">(%s)</span>', 'Reservations <span class="count">(%s)</span>', 'event-organiser-extended-admin-interface'),
       'reserve_spaces'       => true,
       'include_in_confirmed' => false,
       'public'               => true,
   ));
}
add_action( 'init', 'register_own_booking_status' );


/* 
 * Create new metadata and column "eoeai-class-no (Class-No.)" to event overview table
 * Create column "eoeai-class-no"
 */
function own_eventorganiser_event_add_columns( $columns ) {
	$columns['eoeai-class-no'] = __('Class Number', 'event-organiser-extended-admin-interface');
	return $columns;
}
add_filter( 'manage_edit-event_columns', 'own_eventorganiser_event_add_columns', 15, 2 );

/* Create value display for new column "eoeai-class-no" */
function own_eventorganiser_event_fill_columns( $column_name, $id ) {
	if($column_name == 'eoeai-class-no') {
		echo get_post_meta($id, 'eoeai-class-no', true);
	}
}
add_action( 'manage_event_posts_custom_column', 'own_eventorganiser_event_fill_columns', 15, 2 );

/* Make new column "eoeai-class-no" sortable */
function own_eventorganiser_event_sortable_columns( $columns ) {
	$columns['eoeai-class-no'] = 'eoeai-class-no';
	return $columns;
}
add_filter( 'manage_edit-event_sortable_columns', 'own_eventorganiser_event_sortable_columns' );


/* 
 * Create new metadata and column "eoeai-show-event-bookings (Show event booking)" to event overview table
 * Create column "eoeai-class-no"  http://neu.verkehrswacht-siegerland.de/wp-admin/edit.php?post_type=event&page=bookings&event_id=262
 */
function own_eventorganiser_event_add_columns( $columns ) {
	$columns['eoeai-show-event-bookings'] = __('Event Bookings', 'event-organiser-extended-admin-interface');
	return $columns;
}
add_filter( 'manage_edit-event_columns', 'own_eventorganiser_event_add_columns', 15, 2 );

/* Create value display for new column "eoeai-show-event-bookings" */
function own_eventorganiser_event_fill_columns( $column_name, $id ) {
	if($column_name == 'eoeai-show-event-bookings') {
		echo '<a href="edit.php?post_type=event&page=bookings&event_id=' . $id . '">' .  __('Show Event Bookings', 'event-organiser-extended-admin-interface') . '</a>';
	}
}
add_action( 'manage_event_posts_custom_column', 'own_eventorganiser_event_fill_columns', 15, 2 );


/* Add new column "eoeai-class-no" and "edit-booking-meta" column to booking overview table
 * Create columns "eoeai-class-no" and "edit-booking-meta"
 */
function own_eventorganiser_bookings_add_columns( $columns ) {
	$columns['eoeai-class-no'] = __('Class Number', 'event-organiser-extended-admin-interface');
	$columns['edit-booking-meta'] = __('Action', 'event-organiser-extended-admin-interface');
	return $columns;
}
add_filter( 'manage_event_page_bookings_columns', 'own_eventorganiser_bookings_add_columns', 15, 2 );

/* Create value display for new columns "eoeai-class-no" and "edit-booking-meta" */
function own_eventorganiser_bookings_fill_columns( $column_name, $item ) {
	if($column_name == 'eoeai-class-no') {
		echo get_post_meta(eo_get_booking_meta( $item->ID, 'event_id' ), 'eoeai-class-no', true);
	}
	if($column_name == 'edit-booking-meta') {
		echo '<a href="edit.php?post_type=event&page=editbooking&booking_id=' . $item->ID . '">' . __('Edit Booking Details', 'event-organiser-extended-admin-interface') . '</a>';
	}
}
add_action( 'eventorganiser_booking_table_column', 'own_eventorganiser_bookings_fill_columns', 15, 2 );

/* Make new columns "eoeai-class-no" and "edit-booking-meta" sortable */
function own_eventorganiser_bookings_sortable_columns( $columns ) {
	$columns['eoeai-class-no'] = 'eoeai-class-no';
	return $columns;
}
add_filter( 'manage_event_page_bookings_sortable_columns', 'own_eventorganiser_bookings_sortable_columns' );


/* Create new admin pages "Reservate Tickets" and "Edit Booking Details */
function register_own_menu_page() {
	add_submenu_page('edit.php?post_type=event', __('Reservate Tickets', 'event-organiser-extended-admin-interface'), __('Reservate Tickets', 'event-organiser-extended-admin-interface'), 'manage_eo_booking', 'reservations', 'reservations');
	add_submenu_page('edit.php?post_type=event', __('Edit Booking Details', 'event-organiser-extended-admin-interface'), __('Edit Booking Details', 'event-organiser-extended-admin-interface'), 'manage_eo_booking', 'editbooking', 'editbooking');
}
add_action('admin_menu', 'register_own_menu_page');





/* Create metabox on edit event page for new meta values "eoeai-class-no (Class-No.)" and "eoeai-internal-notes (Internal notes)" */
function register_eoeai_metadata_metabox() {
	add_meta_box( 'edit-eoeai-metadata', 'Buchungsdetails bearbeiten', 'display_eoeai_metadata_metabox', 'event', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'register_eoeai_metadata_metabox' );


/* Create meta box display */
function display_eoeai_metadata_metabox( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'eoeai_post_class_nonce' );
	
	echo '<div>';
    echo ' <label for="eoeai-class-no"><strong>' . __( "Class-No.", 'event-organiser-extended-admin-interface' ) . '</strong></label>';
	echo ' <p class="description">' . __( "Please enter a class number (up to 10 characters)", 'event-organiser-extended-admin-interface' ) . '</p>';
	echo '</div>';
	echo '<div>';
	echo ' <p>';
    echo '  <input class="widefat" type="text" name="eoeai-class-no" id="eoeai-class-no" value="' . esc_attr( get_post_meta( $post->ID, 'eoeai-class-no', true ) ) . '" maxlength="10" required />';
	echo ' </p>';
	echo '</div>';
	echo '<hr>';
	echo '<div>';
    echo ' <label for="eoeai-internal-notes"><strong>' . __( "Internal notes", 'event-organiser-extended-admin-interface' ) . '</strong></label>';
	echo ' <p class="description">' . __( "Here you can save internal notes.", 'event-organiser-extended-admin-interface' ) . '</p>';
	echo '</div>';
	echo '<div>';
	echo ' <p>';
    echo '  <textarea class="widefat" type="text" name="eoeai-internal-notes" id="eoeai-internal-notes">' . esc_attr( get_post_meta( $post->ID, 'eoeai-internal-notes', true ) ) . '</textarea>';
	echo ' </p>';
	echo '</div>';
}
add_action( 'save_post', 'save_eoeai_metadata_metabox', 10, 2 );


/* Create save function for meta box values "eoeai-class-no (Class-No.)" and "eoeai-internal-notes (Internal notes)" */
function save_eoeai_metadata_metabox( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['eoeai_post_class_nonce'] ) || !wp_verify_nonce( $_POST['eoeai_post_class_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );
  
  /* Verify the post type is "event" */
  if ( !$post_type == 'event' )
    return $post_id;

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it. */
  $new_meta_value_eoeai_class_no		= ( isset( $_POST['eoeai-class-no'] ) ? sanitize_text_field( $_POST['eoeai-class-no'] ) : '' );
  $new_meta_value_eoeai_internal_notes	= ( isset( $_POST['eoeai-internal-notes'] ) ? sanitize_text_field( $_POST['eoeai-internal-notes'] ) : '' );

  /* Get the meta key. */
  $meta_key_eoeai_class_no			= 'eoeai-class-no';
  $meta_key_eoeai_internal_notes	= 'eoeai-internal-notes';

  /* Get the meta value of the custom field key. */
  $meta_value_eoeai_class_no		= get_post_meta( $post_id, $meta_key_eoeai_class_no, true );
  $meta_value_eoeai_internal_notes	= get_post_meta( $post_id, $meta_key_eoeai_internal_notes, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value_eoeai_class_no && '' == $meta_value_eoeai_class_no )
    add_post_meta( $post_id, $meta_key_eoeai_class_no, $new_meta_value_eoeai_class_no, true );
  if ( $new_meta_value_eoeai_internal_notes && '' == $meta_value_eoeai_internal_notes )
    add_post_meta( $post_id, $meta_key_eoeai_internal_notes, $new_meta_value_eoeai_internal_notes, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value_eoeai_class_no && $new_meta_value_eoeai_class_no != $meta_value_eoeai_class_no )
    update_post_meta( $post_id, $meta_key_eoeai_class_no, $new_meta_value_eoeai_class_no );
  elseif ( $new_meta_value_eoeai_internal_notes && $new_meta_value_eoeai_internal_notes != $meta_value_eoeai_internal_notes )
    update_post_meta( $post_id, $meta_key_eoeai_internal_notes, $new_meta_value_eoeai_internal_notes );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value_eoeai_class_no && $meta_value_eoeai_class_no )
    delete_post_meta( $post_id, $meta_key_eoeai_class_no, $meta_value_eoeai_class_no );
  elseif ( '' == $new_meta_value_eoeai_internal_notes && $meta_value_eoeai_internal_notes )
    delete_post_meta( $post_id, $meta_key_eoeai_internal_notes, $meta_value_eoeai_internal_notes );
}


?>