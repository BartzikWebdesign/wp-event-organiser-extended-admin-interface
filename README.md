# event-organiser-extended-admin-interface

This WordPress plugin is an extension for WP Event Organizer Pro (http://wp-event-organiser.com).

It extends the backend with the possibility to make reservations and edit booking details (eg contact details of the booker).

Prerequisite is the paid Pro version of WP Event Organizer Pro.

This plugin is not directly related to WP Event Organizer or the authors.


## Installation
* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.


## Screenshots
![Reserve tickets](/img/screenshots/wp-event-organiser-extended-admin-interface_1.png)
![Reserve tickets](/img/screenshots/wp-event-organiser-extended-admin-interface_2.png)
![Edit booking details](/img/screenshots/wp-event-organiser-extended-admin-interface_3.png)
![Edit booking details](/img/screenshots/wp-event-organiser-extended-admin-interface_4.png)


## Features
* Administrators are given the opportunity to reserve tickets for an event
* Administrators are given the option of editing the details of a booking (for example, address data of the booker). For this the booking form deposited for the event will be used.
* For a better overview, a mandatory field "Course No." added to every event. This can also be queried in the template.


## Further Features
* Supports translation, by default: English, German (.po / .mo-files)


## Authors
* Marciel Bartzik, [Bartzik Webdesign](http://www.bartzik.net)


## Template usage
You can use some custom code eg in your template files to interact with the plugin.
Please replace `{{variable}}` with a variable or value.

### Show number of reservated tickets for a event
```php
<?php

$reservations = eo_get_bookings( array(
	'status'	=> 'reserved',
	'event_id'	=> {{eventID}},
	'occurrence_id'	=> {{occurrenceID}}
) );

$num_reservations = 0;

if( $reservations ){
	foreach( $reservations as $reservation ){
		$num_reservations += eo_get_booking_meta( $reservation->ID, 'ticket_quantity' );
	}
}

?>
```


### Show "Course No." in your template
```php
<?php

echo get_post_meta( {{eventID}}, 'eoeai-class-no', true );

?>
```


## Versions
Version | Changes
------- | -------
1.0.0 (stable) | master branch