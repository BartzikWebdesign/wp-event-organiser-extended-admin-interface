<?php

/*
 * Plugin Name: Event Organiser Extended Admin Interface
 * Description: Extends the administration interface of the "Event Organizer" plugin with the "Reserve Tickets" entry as well as the appropriate ticket status "Reserved". Furthermore, it expands events with the meta tag "class no." and "internal notes". It also offers the possibility to further process bookings.
 * Plugin URI: https://github.com/BartzikWebdesign/wp-event-organiser-extended-admin-interface
 * Author: Bartzik Webdesign // BARTZIK.NET
 * Author URI: http://www.barzik.net/
 * Version: 1.0.1
 * License: GNU General Public License, version 3 (GPLv3)
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: event-organiser-extended-admin-interface
 * Domain Path: /languages
 */


/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;


/* Load plugin text domain */
function my_plugin_load_plugin_textdomain() {
    load_plugin_textdomain( 'event-organiser-extended-admin-interface', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'my_plugin_load_plugin_textdomain' );


/* Update-Checker */
require plugin_dir_path( __FILE__ ) . 'inc/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/BartzikWebdesign/wp-event-organiser-extended-admin-interface',
	__FILE__,
	'event-organiser-extended-admin-interface'
);
$myUpdateChecker->setBranch('master');


/* Include general functions and plugin parts */
require_once( plugin_dir_path( __FILE__ ) . 'inc/general-functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/page-reservations.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/page-edit-booking.php' );


?>