<?php 
/*
	Plugin Name: Room Options
    Plugin URI: 
    Description: Booking System plugin 
    Author: Mina Ragaie
    Version: 1.0
    Author URI: 
*/

define("HOTEL_BOOKING_ACTIVE", true);

include_once 'db-modifications.php';
include_once 'utility-functions.php';
include_once 'import-export.php';
include_once 'calendar-shortcode.php';
include_once 'widget/hotel-booking-widget.php';


load_plugin_textdomain('hotel', false, basename( dirname( __FILE__ ) ) . '/languages' );


function hotel_booking_install() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . "hotel_booking_calendars";
	$table_name2 = $wpdb->prefix . "hotel_booking_availability";
	$table_name3 = $wpdb->prefix . "hotel_booking_reservation";
	$table_name4 = $wpdb->prefix . "hotel_booking_settings";
	
	$hotel_calendars = "CREATE TABLE $table_name (
		id INT NOT NULL AUTO_INCREMENT,
		cal_name VARCHAR(128) DEFAULT '".__("New Calendar", "hotel")."' COLLATE utf8_unicode_ci NOT NULL,
		min_price FLOAT DEFAULT 0 NOT NULL,
        UNIQUE KEY id (id)
    );";
	
	$hotel_availability = "CREATE TABLE $table_name2 (
		id INT NOT NULL AUTO_INCREMENT,
		calendar_id INT DEFAULT '0' NOT NULL,
		day INT(2) DEFAULT '0' NOT NULL,
		month INT(2) DEFAULT '0' NOT NULL,
		year INT(4) DEFAULT '0' NOT NULL,
		availability INT DEFAULT '0' NOT NULL,
		price FLOAT DEFAULT '0' NOT NULL,
		UNIQUE KEY id (id)
    );";
	
	$hotel_reservation = "CREATE TABLE $table_name3 (
		id INT NOT NULL AUTO_INCREMENT,
		calendar_id INT DEFAULT 0 NOT NULL,
        check_in VARCHAR(16) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
        check_out VARCHAR(16) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
        no_items INT DEFAULT '1' NOT NULL,
        price FLOAT DEFAULT '0' NOT NULL,
        email VARCHAR(128) DEFAULT 'none' COLLATE utf8_unicode_ci NOT NULL,
        phone VARCHAR(128) DEFAULT 'none' COLLATE utf8_unicode_ci NOT NULL,
        confirmed TEXT COLLATE utf8_unicode_ci NOT NULL,
        expected TEXT COLLATE utf8_unicode_ci NOT NULL,
        rooms_type TEXT COLLATE utf8_unicode_ci NOT NULL,
        buffet TEXT COLLATE utf8_unicode_ci NOT NULL,
        beverage TEXT COLLATE utf8_unicode_ci NOT NULL,
        status VARCHAR(16) DEFAULT 'pending' COLLATE utf8_unicode_ci NOT NULL,
		name TEXT COLLATE utf8_unicode_ci NOT NULL,
		surname TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_payment TINYINT(1) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
		paypal_payer_id TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_transaction_id TEXT COLLATE utf8_unicode_ci NOT NULL,
		cardholder_name TEXT COLLATE utf8_unicode_ci NOT NULL,
		card_type TEXT COLLATE utf8_unicode_ci NOT NULL,
		card_number TEXT COLLATE utf8_unicode_ci NOT NULL,
		expiration_year INT DEFAULT 0 NOT NULL,
		expiration_month INT DEFAULT 0 NOT NULL,
		comments TEXT COLLATE utf8_unicode_ci NOT NULL,
		date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        UNIQUE KEY id (id)
    );";
	
	$hotel_settings = "CREATE TABLE $table_name4 (
		id INT NOT NULL AUTO_INCREMENT,
		email VARCHAR(128) DEFAULT 'none' COLLATE utf8_unicode_ci NOT NULL,
		currency_symbol TEXT(10) COLLATE utf8_unicode_ci NOT NULL,
		date_format TEXT(10) COLLATE utf8_unicode_ci NOT NULL,
		hide_tax TINYINT(1) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
		add_tax TINYINT(1) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
		tax TEXT(10) COLLATE utf8_unicode_ci NOT NULL,
		confirmation_email_header TEXT COLLATE utf8_unicode_ci NOT NULL,
		confirmation_email_content TEXT COLLATE utf8_unicode_ci NOT NULL,
		cancelation_email_header TEXT COLLATE utf8_unicode_ci NOT NULL,
		cancelation_email_content TEXT COLLATE utf8_unicode_ci NOT NULL,
		without_confirmation_email_header TEXT COLLATE utf8_unicode_ci NOT NULL,
		without_confirmation_email_content TEXT COLLATE utf8_unicode_ci NOT NULL,
		rejected_email_header TEXT COLLATE utf8_unicode_ci NOT NULL,
		rejected_email_content TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_enabled TINYINT(1) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
		paypal_api_username TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_api_password TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_api_signature TEXT COLLATE utf8_unicode_ci NOT NULL,
		paypal_currency_code TEXT COLLATE utf8_unicode_ci NOT NULL,
		sandbox_enabled TINYINT(1) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
		UNIQUE KEY id (id)
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	dbDelta( $hotel_calendars );
	dbDelta( $hotel_availability );
	dbDelta( $hotel_reservation );
	dbDelta( $hotel_settings );

	dbDelta( $wpdb->insert( $table_name4, array(
		'id' => 1,
        'email' => 'test@email.com',
        'currency_symbol' => '$',
		'paypal_currency_code' => 'USD',
		'date_format' => 'american',
		'tax' => '0',		
	)));
}


register_activation_hook( __FILE__, 'hotel_booking_install' );


function hotel_booking_uninstall() {
	global $wpdb;



	$table_name = $wpdb->prefix . "hotel_booking_calendars";
	$table_name2 = $wpdb->prefix . "hotel_booking_availability";
	$table_name3 = $wpdb->prefix . "hotel_booking_reservation";
	$table_name4 = $wpdb->prefix . "hotel_booking_settings";

	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	$wpdb->query("DROP TABLE IF EXISTS $table_name2");
	$wpdb->query("DROP TABLE IF EXISTS $table_name3");
	$wpdb->query("DROP TABLE IF EXISTS $table_name4");
}
register_uninstall_hook(__FILE__, 'hotel_booking_uninstall');


function hotel_booking_show_admin() {
	global $wpdb;
		
	echo "<h2>".__("Current Calendars:","hotel")."</h2>";
	
	$calendars = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'hotel_booking_calendars ORDER BY id ASC');
	
	if ($wpdb->num_rows != 0){
		if ($calendars){
			
			echo "<table class='calendar-table'><tr><th>".__("Id","hotel")."</th><th>".__("Calendar Name","hotel")."</th><th>".__("Min Price","hotel")."</th></tr>";
			
			foreach( $calendars as $calendar ) {
				echo "<tr class='row-wrap header-row'><td class='content-row'>$calendar->id</td><td class='content-row'>$calendar->cal_name</td><td class='content-row'>$calendar->min_price</td><td class='content-row button-wrap'><div class='cal-edit button button-primary'>".__("Edit","hotel")."</div>
				<form method='POST' action='admin-post.php' id='delete-form'>";
				wp_nonce_field( 'hotel_delete_calendar_verify' ); 
				echo "<input type='submit' value=".__("Delete","hotel")." class='button button-secondary'>
					<input type='hidden' name='action' value='hotel_booking_delete_calendar' />
					<input type='hidden' name='cal_id' value='$calendar->id' />
				</form>";
				echo "<td class='edit-row'>$calendar->id</td><td class='edit-row' colspan='3'>
					<form method='POST' action='admin-post.php' id='cal_edit_form'>";
					wp_nonce_field( 'hotel_edit_calendar_verify' ); 
				echo "	<input type='text' name='cal_name' value='$calendar->cal_name'>
						<input type='text' name='cal_min_price' value='$calendar->min_price'>
						<input type='hidden' name='action' value='hotel_booking_edit_calendar' />
						<input type='hidden' name='cal_id' value='$calendar->id' />
						<input type='submit' value=".__("Submit","hotel")." class='button button-primary'>
						<input type='button' value=".__("Cancel","hotel")." class='cal_edit_cancel button button-secondary'>
					</form>
				</td></tr>";
			}
		}
	} else {
		_e("There's no calendars found. You can create new calendars using form below.","hotel");
		echo "<br>";
	}
	
	echo "</table>";
	echo "<br><hr><br><h2>".__("Create New Calendar:","hotel")."</h2>";
	
	echo "<div class='wrap'><form method='POST' action='admin-post.php' id='add-calendar-form'>";
	wp_nonce_field( 'hotel_add_calendar_verify' ); 
	echo "<table class='cal-table'><tr><td class='label'><b>".__("Calendar Name")."</b></td><td><input type='text' name='cal_name' id='cal-name'></td></tr>
	<tr><td class='label'><b>".__("Min Price")."</b></td><td><input type='text' name='cal_min_price' id='cal-min-price'></td></tr></table>
	<input type='hidden' name='action' value='hotel_booking_add_calendar' />
	<br><input type='submit' value=".__("Submit","hotel")." class='button button-primary'>
	</form></div>";
}


function hotel_booking_show_reservations() {
	global $wpdb;
	
	$calendars = $wpdb->get_results('SELECT * FROM ' .$wpdb->prefix. 'hotel_booking_calendars');
		
	echo "<h2>".__("Select Calendar:","hotel")."</h2>";
	echo "<select id='select-calendar-reservation'><option>Select Calendar</option>";
		
	if ($wpdb->num_rows != 0) {
		if ($calendars){
			foreach( $calendars as $calendar ) {
				echo "<option value='$calendar->id'>$calendar->cal_name (ID: $calendar->id)</option>";
			}
		}
	}
			
	echo "</select>";
	echo "<div id='show-reservation-table'></div><br><hr><br>";
}


function hotel_booking_show_calendar_ajax() {
	global $wpdb;
	
	if ( isset( $_REQUEST['cal_id'] ) ) {
		$calID = $_REQUEST['cal_id'];
		
		if ( isset( $_REQUEST['calendar_month'] ) ) {
			$month = $_REQUEST['calendar_month'];
		} else {
			$month = date("n");
		}
	
		if ( isset( $_REQUEST['calendar_year'] ) ) {
			$year = $_REQUEST['calendar_year'];
		} else {
			$year = date("Y");
		}
		
		$cal_year = $year;
		
		switch($month){ 
			case "1": $title = __("January","hotel"); break; 
			case "2": $title = __("February","hotel"); break; 
			case "3": $title = __("March","hotel"); break; 
			case "4": $title = __("April","hotel"); break; 
			case "5": $title = __("May","hotel"); break; 
			case "6": $title = __("June","hotel"); break; 
			case "7": $title = __("July","hotel"); break; 
			case "8": $title = __("August","hotel"); break; 
			case "9": $title = __("September","hotel"); break; 
			case "10": $title = __("October","hotel"); break; 
			case "11": $title = __("November","hotel"); break; 
			case "12": $title = __("December","hotel"); break; 
		}
	
	?> <select id='calendar-month'>
		<option><?php _e("Select Month","hotel") ?></option>
		<option <?php if ( $month == 1 ) { echo 'selected="selected"'; } ?> value='1'><?php _e("January","hotel") ?></option>
		<option <?php if ( $month == 2 ) { echo 'selected="selected"'; } ?> value='2'><?php _e("February","hotel") ?></option>
		<option <?php if ( $month == 3 ) { echo 'selected="selected"'; } ?> value='3'><?php _e("March","hotel") ?></option>
		<option <?php if ( $month == 4 ) { echo 'selected="selected"'; } ?> value='4'><?php _e("April","hotel") ?></option>
		<option <?php if ( $month == 5 ) { echo 'selected="selected"'; } ?> value='5'><?php _e("May","hotel") ?></option>
		<option <?php if ( $month == 6 ) { echo 'selected="selected"'; } ?> value='6'><?php _e("June","hotel") ?></option>
		<option <?php if ( $month == 7 ) { echo 'selected="selected"'; } ?> value='7'><?php _e("July","hotel") ?></option>
		<option <?php if ( $month == 8 ) { echo 'selected="selected"'; } ?> value='8'><?php _e("August","hotel") ?></option>
		<option <?php if ( $month == 9 ) { echo 'selected="selected"'; } ?> value='9'><?php _e("September","hotel") ?></option>
		<option <?php if ( $month == 10 ) { echo 'selected="selected"'; } ?> value='10'><?php _e("October","hotel") ?></option>
		<option <?php if ( $month == 11 ) { echo 'selected="selected"'; } ?> value='11'><?php _e("November","hotel") ?></option>
		<option <?php if ( $month == 12 ) { echo 'selected="selected"'; } ?> value='12'><?php _e("December","hotel") ?></option>
	</select><select id='calendar-year'>
		<option>Select Year</option>
		<option <?php if ( $year == 2014 ) { echo 'selected="selected"'; } ?> value='2014'>2014</option>
		<option <?php if ( $year == 2015 ) { echo 'selected="selected"'; } ?> value='2015'>2015</option>
		<option <?php if ( $year == 2016 ) { echo 'selected="selected"'; } ?> value='2016'>2016</option>
		<option <?php if ( $year == 2017 ) { echo 'selected="selected"'; } ?> value='2017'>2017</option>
		<option <?php if ( $year == 2018 ) { echo 'selected="selected"'; } ?> value='2018'>2018</option>
	</select>
	<?php 
		$availabilities = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'hotel_booking_availability WHERE calendar_id='.$calID.' AND month='.$month.' AND year='.$year);
		$currencySymbol = $wpdb->get_var( "SELECT currency_symbol FROM ".$wpdb->prefix."hotel_booking_settings");
		
		//This gets today's date
		$date =time () ;

		//This puts the day, month, and year in seperate variables
		$day = date('d', $date) ;


		//Here we generate the first day of the month
		$first_day = mktime(0,0,0,$month, 1, $year) ;
	
		//Here we find out what day of the week the first day of the month falls on 
		$day_of_week = date('D', $first_day) ; 

		//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
		switch($day_of_week){ 
			case "Sun": $blank = 0; break; 
			case "Mon": $blank = 1; break; 
			case "Tue": $blank = 2; break; 
			case "Wed": $blank = 3; break; 
			case "Thu": $blank = 4; break; 
			case "Fri": $blank = 5; break; 
			case "Sat": $blank = 6; break; 
		}
		
		//We then determine how many days are in the current month
		$days_in_month = cal_days_in_month(0, $month, $year) ; 
		
		if ($wpdb->num_rows != 0) {
			if ($availabilities){				
		
					//Here we start building the table heads 
					echo "<table id='booking-calendar'>";

					echo "<tr class='month-year'><th colspan='7' class='datayear'> $title $cal_year </th></tr>";
					echo "<tr class='header'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td></tr>";
	
					//This counts the days in the week, up to 7
					$day_count = 1;
					echo "<tr class='day-rows'>";

					//first we take care of those blank days
					while ( $blank > 0 ) { 
						echo "<td></td>"; 
						$blank = $blank-1; 
						$day_count++;
					}

					//sets the first day of the month to 1 
					$day_num = 1;

					//count up the days, untill we've done all of them in the month
					while ( $day_num <= $days_in_month ) { 
						$price_availty_set = false;
						echo "<td ";
						for ($i=0;$i<$days_in_month;$i++) {
							if ( isset( $availabilities[$i]) ) {
								if ( $availabilities[$i]->day == $day_num ) {
									$price_availty_set = true;
									if ( isset($availabilities[$i]->availability) ) {
										if ( $availabilities[$i]->availability > 0 ) { 
											echo "class='cell-available'> <div class='available'>".$day_num." "."</div>";
											echo "<div class='avail-price'>".$currencySymbol.$availabilities[$i]->price."</div>";
											echo "<div class='status'>".$availabilities[$i]->availability." ".__('available','hotel')."</div></td>";
										} else if ( $availabilities[$i]->availability == 0 ) {
											echo "class='cell-booked'><div class='booked'>".$day_num."</div>";
											echo "<div>&nbsp;</div>";
											echo "<div class='status'>".__('booked','hotel')."</div></td>";
										}
									}
								}
							}		
							if ( $price_availty_set ) {
								break;
							}
						}
		
						if ( !$price_availty_set ) {
							echo "class='cell-notset'> <div class='notset'>$day_num</div></td>";
						}
						$day_num++; 
						$day_count++;

						//Make sure we start a new row every week

						if ($day_count > 7) {
							echo "</tr><tr class='day-rows'>";	
							$day_count = 1;
						}
					}
	
					//Finaly we finish out the table with some blank details if needed
					while ( $day_count >1 && $day_count <=7 ) { 
						echo "<td> </td>"; 
						$day_count++; 
					}	 

					echo "</tr></table><br><br><hr><br>"; 
						
				} else {
					
					//Here we start building the table heads 
					echo "<table id='booking-calendar'>";

					echo "<tr class='month-year'><th colspan='7' class='datayear'> $title $cal_year </th></tr>";
					echo "<tr class='header'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td></tr>";
	
					//This counts the days in the week, up to 7
					$day_count = 1;
					echo "<tr class='day-rows'>";
	
					//first we take care of those blank days
					while ( $blank > 0 ) { 
						echo "<td></td>"; 
						$blank = $blank-1; 
						$day_count++;
					}

					//sets the first day of the month to 1 
					$day_num = 1;

					//count up the days, untill we've done all of them in the month
					while ( $day_num <= $days_in_month ){ 
						echo "<td class='cell-notset'> <div class='notset'>$day_num</div> </td>"; 
						$day_num++; 
						$day_count++;

						//Make sure we start a new row every week
						if ($day_count > 7) {
							echo "</tr><tr class='day-rows'>";
							$day_count = 1;
						}

					}
					
					//Finaly we finish out the table with some blank details if needed
					while ( $day_count >1 && $day_count <=7 ) { 
						echo "<td> </td>"; 
						$day_count++; 
					} 
					echo "</tr></table><br><br><hr><br>"; 
										
				}
			}
			
			echo "<h2>".__("Set/Edit Calendar Data:","hotel")."</h2>";
			echo "<form method='POST' action='admin-post.php'>
			<table class='set-data'><tr><td class='label'><strong>".__("Start Data","hotel")."</strong></td><td><input type='text' name='check-in' class='check-in-date date-picker-field'></td></tr>
			<tr><td class='label'><strong>".__("End Data","hotel")."</strong></td><td><input type='text' name='check-out' class='check-out-date date-picker-field'></td></tr>
			<tr><td class='label'><strong>".__("Availability","hotel")."</strong></td><td><input type='text' name='availability' class='regular-text'></td></tr>";
			wp_nonce_field( 'hotel_date_modification_verify' ); 
			echo "<tr><td class='label'><strong>".__("Price","hotel")."</strong></td><td><input type='text' name='price' class='regular-text'></td></tr></table>
			<input type='hidden' name='action' value='hotel_booking_date_modification' />
			<input type='hidden' name='cal_id' value='{$_REQUEST['cal_id']}' />
			<br><input type='submit' value=".__("Submit","hotel")." class='button button-primary'>
			</form>";			
		}

		exit;
}


function hotel_booking_get_calendar_data_ajax() {
	global $wpdb;
	
	if ( isset( $_REQUEST['cal_id'] ) && isset( $_REQUEST['year'] ) && isset( $_REQUEST['month'] ) ) {
		$availabilities = $wpdb->get_results('SELECT day,availability,price FROM '.$wpdb->prefix.'hotel_booking_availability WHERE calendar_id='.$_REQUEST['cal_id'].' AND month='.$_REQUEST['month'].' AND year='.$_REQUEST['year']);
	}
	
	echo json_encode($availabilities);
	
	exit;
}
// Ajax Actions Defined
add_action('wp_ajax_hotel_booking_get_calendar_data', 'hotel_booking_get_calendar_data_ajax' );
add_action( 'wp_ajax_nopriv_hotel_booking_get_calendar_data', 'hotel_booking_get_calendar_data_ajax' );


// Ajax Actions Defined
add_action('wp_ajax_hotel_show_calendar', 'hotel_booking_show_calendar_ajax' );


function hotel_booking_add_edit_price() {
	global $wpdb;
	
	$calendars = $wpdb->get_results('SELECT * FROM ' .$wpdb->prefix. 'hotel_booking_calendars');
		
	echo "<h2>".__("Select Calendar:","hotel")."</h2>";
	echo "<select id='select-calendar-price'><option>Select Calendar</option>";
		
	if ($wpdb->num_rows != 0) {
		if ($calendars){
			foreach( $calendars as $calendar ) {
				echo "<option value='$calendar->id'>$calendar->cal_name (ID: $calendar->id)</option>";
			}
		}
	}
		
	echo "</select><br><br><hr><br>";
	echo "<div id='show-price-calendar'></div>";
} 


function hotel_booking_settings() {
	global $wpdb;
		
	$settings = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'hotel_booking_settings');
	echo "<h2>".__("Booking System Settings","hotel")."</h2>";
	
	if ($settings) {
		echo "<form method='POST' action='admin-post.php'>";
		wp_nonce_field( 'hotel_date_settings_edit' ); 
		echo "<table class='option-table'><tr><td class='label'><strong>".__("Email:","hotel")." </strong></td><td><input type='type' name='email' value='$settings->email' class='regular-text' /></td></tr>
		<tr><td class='label'><strong>".__("Currency symbol:","hotel")." </strong></td><td><input type='type' name='currency_symbol' value='$settings->currency_symbol' class='regular-text' /></td></tr>
		<tr><td class='label'><strong>".__("Date format:","hotel")." </strong></td><td>
			<select name='date_format'>
				<option ";
				
		if ( $settings->date_format == "american" ) { echo "selected='selected'"; }
		echo " value='american'>American (MM/DD/YYYY)</option>
				<option ";
		if ( $settings->date_format == "european" ) { echo "selected='selected'"; }
		echo " value='european'>European (DD/MM/YYYY)</option>
			</select>
		</td></tr>
		<tr><td class='label'><strong>".__("Hide Tax Info:","hotel")." </strong></td><td><fieldset><label for='hide_tax'><input type='checkbox' id='hide_tax' name='hide_tax' ";
		if ( $settings->hide_tax == 1 ) {
			echo "checked ";
		}
		echo "value='1'>Yes </label></fieldset></td></tr>
		<tr><td class='label'><strong>".__("Add Tax to Price Calculation:","hotel")." </strong></td><td><fieldset><label for='add_tax'><input type='checkbox' id='add_tax' name='add_tax' ";
		if ( $settings->add_tax == 1 ) {
			echo "checked ";
		}
		echo "value='1'>Yes </label></fieldset></td></tr>
		<tr><td class='label'><strong>".__("Tax:","hotel")." </strong></td><td><input type='type' name='tax' value='$settings->tax' class='regular-text' /><br><span style='color:#999'>(".__("please enter without the percent sign","hotel").")</span></td></tr><br>
		<tr><td></td><td></td></tr>
		<tr><td class='label'><strong>".__("Not confirmed email header:","hotel")." </strong></td><td><input type='type' name='without_confirmation_email_header' value='$settings->without_confirmation_email_header' class='regular-text' /></td></tr>
		<tr class='extra-height'><td class='label'><strong>".__("Not confirmed email content:","hotel")." </strong></td><td><textarea name='without_confirmation_email_content' class='large-text'>$settings->without_confirmation_email_content</textarea></td></tr>
		<tr><td class='label'><strong>".__("Confirmed email header:","hotel")." </strong></td><td><input type='type' name='confirmation_email_header' value='$settings->confirmation_email_header' class='regular-text' /></td></tr>
		<tr class='extra-height'><td class='label'><strong>".__("Confirmed email content:","hotel")." </strong></td><td><textarea name='confirmation_email_content' class='large-text'>$settings->confirmation_email_content</textarea></td></tr>
		<tr><td class='label'><strong>".__("Canceled email header:","hotel")." </strong></td><td><input type='type' name='cancelation_email_header' value='$settings->cancelation_email_header' class='regular-text' /></td></tr>
		<tr class='extra-height'><td class='label'><strong>".__("Canceled email content:","hotel")." </strong></td><td><textarea name='cancelation_email_content' class='large-text'>$settings->cancelation_email_content</textarea></td></tr>
		<tr><td class='label'><strong>".__("Rejected email header:","hotel")." </strong></td><td><input type='type' name='rejected_email_header' value='$settings->rejected_email_header' class='regular-text' /></td></tr>
		<tr class='extra-height'><td class='label'><strong>".__("Rejected email content:","hotel")." </strong></td><td><textarea name='rejected_email_content' class='large-text'>$settings->rejected_email_content</textarea></td></tr>		
		<input type='hidden' name='action' value='hotel_booking_settings_edit' /></table>
		<br><input type='submit' value='".__("Save changes","hotel")."' class='button button-primary' class='button button-primary'>
		</form>";
	}
}


function hotel_booking_paypal_settings() {
	global $wpdb;
		
	$settings = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'hotel_booking_settings');
	echo "<h2>".__("PayPal Settings","hotel")."</h2>";
	if ($settings) {
		echo "<form method='POST' action='admin-post.php'>";
		wp_nonce_field( 'hotel_date_paypal_settings_edit' ); 
		echo "<table class='option-table'><tr><td class='label'><strong>".__("Enable PayPal payment:","hotel")." </strong></td><td><input type='checkbox' name='paypal_enabled' ";
		if ( isset($settings->paypal_enabled) && $settings->paypal_enabled ) { echo "checked "; } 
		echo "value='1'> Yes</td></tr>
		<tr><td class='label'><strong>".__("Enable Sandbox mode","hotel")."</strong></td><td><input type='checkbox' name='paypal_sandbox_enabled' ";
		if ( isset($settings->sandbox_enabled) && $settings->sandbox_enabled ) { echo "checked "; } 
		echo "value='1'> Yes</td></tr>
		<tr><td class='label'><strong>".__("API username:","hotel")." </strong></td><td><input type='type' name='api_username' value='$settings->paypal_api_username' class='regular-text' /></td></tr>
		<tr><td class='label'><strong>".__("API password:","hotel")." </strong></td><td><input type='type' name='api_password' value='$settings->paypal_api_password' class='regular-text' /></td></tr>
		<tr><td class='label'><strong>".__("API signature:","hotel")." </strong></td><td><input type='type' name='api_signature' value='$settings->paypal_api_signature' class='regular-text' /></td></tr><br>
		<tr><td class='label'><strong>".__("PayPal currency code:","hotel")." </strong></td><td><input type='type' name='paypal_currency_code' value='$settings->paypal_currency_code' class='regular-text' /><br>".__("(currency code list: https://developer.paypal.com/docs/classic/api/currency_codes/)","hotel")."</td></tr><br>
		
		<input type='hidden' name='action' value='hotel_booking_paypal_settings_edit' /></table>
		<br><input type='submit' value='".__("Save changes","hotel")."' class='button button-primary' class='button button-primary'>
		</form>";
	}
}


function hotel_booking_ajax_show_reservations() {
	global $wpdb;
		
	if ( isset ($_REQUEST['cal_id']) ) {	
	
		$currencySymbol = $wpdb->get_var( "SELECT currency_symbol FROM ".$wpdb->prefix."hotel_booking_settings");
		$reservations = $wpdb->get_results( $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'hotel_booking_reservation WHERE calendar_id=%d ORDER BY id ASC',$_REQUEST['cal_id']
			)
		);
		
		echo "<br><h2>".__("Active Reservations:","hotel")."</h2>";
		
		if ($wpdb->num_rows != 0) {
			if ($reservations){
				
				echo "<table class='reservation-table'>
				<tr>
					<th>".__("Id","hotel")."</th>
					<th>".__("Status","hotel")."</th>
					<th>".__("Check In","hotel")."</th>
					<th>".__("Check Out","hotel")."</th>
					<th>".__("Visitors","hotel")."</th>
					<th>".__("Buffet","hotel")."</th>
					<th>".__("Beverage","hotel")."</th>
					<th>".__("Email","hotel")."</th>
					<th>".__("Info","hotel")."</th>
					<th>".__("Price","hotel")."</th>
				</tr>";
			
				foreach( $reservations as $reservation ) {
					echo "<tr class='row-wrap ";
					
					if ( $reservation->status == "approved" ) {
						echo "approved";
					} else if ( $reservation->status == "pending" ) {
						echo "pending";
					} else if ( $reservation->status == "rejected" || $reservation->status == "canceled" ) {
						echo "rejected";
					}
					
					echo "'>
						<td class='content-row'>$reservation->id</td>
						<td class='content-row'>$reservation->status</td>
						<td class='content-row'>$reservation->check_in</td>
						<td class='content-row'>$reservation->check_out</td>
						<td class='content-row'>";
					
					
						echo "Confirmed: ".$reservation->confirmed."<br>Expected: ".$reservation->expected;

					
					echo "</td>
						<td class='content-row'>$reservation->email</td>";
					echo "<td class='content-row'>".$reservation->buffet."</td>";

					if($reservation->beverage == "on"){
						echo "<td class='content-row'>Beverage Included</td>";
					}else{
						echo "<td class='content-row'>Not Included</td>";
					}
					

						
					if ( $reservation->paypal_payment == 0 ) {
						echo "<td class='content-row'>
							".__("name:","hotel")." &nbsp;<b>$reservation->name $reservation->surname</b> <br>
							".__("card type:","hotel")." &nbsp;<b>$reservation->card_type</b> <br>
							".__("cardholder name:","hotel")." &nbsp;<b>$reservation->cardholder_name</b> <br>	
							".__("card number:","hotel")." &nbsp;<b>$reservation->card_number</b> <br>	
							".__("expiration date","hotel")." &nbsp;<b>{$reservation->expiration_month}/{$reservation->expiration_year}</b> <br>
						</td>";
					} else if ( $reservation->paypal_payment == 1 ) {
						echo "<td class='content-row'>
							".__("Reservation paid through PayPal.","hotel")."<br>
							".__("PayPal Transaction ID:","hotel")." &nbsp;<b>$reservation->paypal_transaction_id</b> <br>
							".__("PayPal Payer ID:","hotel")." &nbsp;<b>$reservation->paypal_payer_id</b> <br>
							".__("name:","hotel")." &nbsp;<b>$reservation->name $reservation->surname</b> <br>
						</td>"; 
					}	
					echo "<td class='content-row'><div class='reservation-price-wrap'>$reservation->price{$currencySymbol}</div></td>";
						
						if ( $reservation->status == "pending" ) {
							echo "<td><form method='POST' action='admin-post.php' id='approve-form'>";
							wp_nonce_field( 'hotel_approve_reservation_verify' );
						
							echo "<input id='approve-button' type='submit' value=".__("Approve","hotel")." class='button button-primary'>&nbsp;
							<input type='hidden' name='action' value='hotel_booking_approve_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>
						
							<td><form method='POST' action='admin-post.php' id='reject-form'>";
							wp_nonce_field( 'hotel_reject_reservation_verify' );
						
							echo "<input id='reject-button' type='submit' value=".__("Reject","hotel")." class='button button-secondary'>
							<input type='hidden' name='action' value='hotel_booking_reject_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>";
						} else if ( $reservation->status == "approved" ) {
							echo "<td><form method='POST' action='admin-post.php' id='cancel-form'>";
							wp_nonce_field( 'hotel_cancel_reservation_verify' );
						
							echo "<input id='cancel-button' type='submit' value=".__("Cancel","hotel")." class='button button-secondary'>
							<input type='hidden' name='action' value='hotel_booking_cancel_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>";
						} else if ( $reservation->status == "canceled" ) {
							echo "<td><form method='POST' action='admin-post.php' id='approve-form'>";
							wp_nonce_field( 'hotel_approve_reservation_verify' );
						
							echo "<input id='approve-button' type='submit' value=".__("Approve","hotel")." class='button button-primary'>
							<input type='hidden' name='action' value='hotel_booking_approve_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>
						
							<td><form method='POST' action='admin-post.php'>";
							wp_nonce_field( 'hotel_delete_reservation_verify' );
						
							echo "<input type='submit' value=".__("Delete","hotel")." class='button button-secondary'>
							<input type='hidden' name='action' value='hotel_booking_delete_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>";
						} else if ( $reservation->status == "rejected" ) {
							echo "<td><form method='POST' action='admin-post.php' id='approve-form'>";
							wp_nonce_field( 'hotel_approve_reservation_verify' );
						
							echo "<input id='approve-button' type='submit' value=".__("Approve","hotel")." class='button button-primary'>
							<input type='hidden' name='action' value='hotel_booking_approve_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>
						
							<td><form method='POST' action='admin-post.php'>";
							wp_nonce_field( 'hotel_delete_reservation_verify' );
							
							echo "<input type='submit' value=".__("Delete","hotel")." class='button button-secondary'>
							<input type='hidden' name='action' value='hotel_booking_delete_reservation' />
							<input type='hidden' name='reservation_id' value='$reservation->id' />
							</form></td>";
						}
					
					echo "</tr>";
				}
	
				echo "</table>"; 
		
			}
		} else {
			_e("There's no reservation found for this calendar.","hotel");
			echo "<br>";
		}
		
		echo "<br><hr><br><h2>".__("Create New Reservation:","hotel")."</h2>";
	
		echo "<div class='wrap'><form method='POST' action='admin-post.php' id='reservation-add'>";
		wp_nonce_field( 'hotel_add_reservation_verify' ); 
		echo "<table id='create-reservation-table'><tr><td class='label'><strong>".__("Check-in date","hotel")."</strong></td><td><input type='text' name='check-in' class='check-in-date date-picker-field'></td></tr>
		<tr><td class='label'><strong>".__("Check-out date","hotel")."</strong></td><td><input type='text' name='check-out' class='check-out-date date-picker-field'></td></tr>
		<tr><td class='label'><strong>".__("Number of room","hotel")."</strong></td><td><input type='text' name='room-number' id='room-number' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Email","hotel")."</strong></td><td><input type='text' name='email' id='email' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Adults","hotel")."</strong></td><td><input type='text' name='adults' id='adults' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Children","hotel")."</strong></td><td><input type='text' name='children' id='children' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Name","hotel")."</strong></td><td><input type='text' name='name' id='name' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Surname","hotel")."</strong></td><td><input type='text' name='surname' id='surname' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Card type","hotel")."</strong></td><td>
			<select name='cardtype'>
				<option value='americanexpress'>American Express</option>
				<option value='mastercard'>Master Card</option>
				<option value='visa'>Visa</option>
			</select>
		</td></tr>		
		<tr><td class='label'><strong>".__("Cardholder name","hotel")."</strong></td><td><input type='text' name='cardholder' id='cardholder' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Card number","hotel")."</strong></td><td><input type='text' name='cardnumber' id='cardnumber' class='regular-text'></td></tr>
		<tr><td class='label'><strong>".__("Expiration month","hotel")."</strong></td><td>
			<select name='expmonth'>
				<option value='01'>01</option>
				<option value='02'>02</option>
				<option value='03'>03</option>
				<option value='04'>04</option>
				<option value='05'>05</option>
				<option value='06'>06</option>
				<option value='07'>07</option>
				<option value='08'>08</option>
				<option value='09'>09</option>
				<option value='10'>10</option>
				<option value='11'>11</option>
				<option value='12'>12</option>
			</select>
		</td></tr>
		<tr><td class='label'><strong>".__("Expiration year","hotel")."</strong></td><td>
			<select name='expyear'>
				<option value='2014'>2014</option>
				<option value='2015'>2015</option>
				<option value='2016'>2016</option>
				<option value='2017'>2017</option>
				<option value='2018'>2018</option>
				<option value='2019'>2019</option>
				<option value='2020'>2020</option>
				<option value='2021'>2021</option>
				<option value='2022'>2022</option>
				<option value='2023'>2023</option>
				<option value='2024'>2024</option>
				<option value='2025'>2025</option>
			</select>
		</td></tr>
		<tr><td class='label'><strong>".__("Comments","hotel")."</strong></td><td><textarea name='comments' class='large-text'></textarea></td></tr>
		</table>
		<input type='hidden' name='action' value='hotel_booking_add_reservation' />
		<input type='hidden' name='cal_id' value='{$_REQUEST['cal_id']}' />
		<br><input type='submit' value=".__("Submit","hotel")." class='button button-primary'>
		</form></div>";
	}
	exit;	
}
add_action('wp_ajax_hotel_show_reservations', 'hotel_booking_ajax_show_reservations' );


// Import and export page
function hotel_booking_import_export() {
	echo '<div class="wrap">';
    screen_icon();
    echo '<h2>' . __( 'Export/Import Booking System Data','hotel') . '</h2>';
    ?>
 
    <form id="export-log-form" method="post" action='admin-post.php'>
        <p><label><?php _e( 'Click to export all booking system data','hotel'); ?></label>
		<input type="hidden" name="action" value="hotel_booking_export" /></p>
        <?php wp_nonce_field('hotel_booking_export_verify') ;?>
        <?php submit_button( __('Download Booking System Data','hotel'), 'button button-secondary' ); ?>
    </form>
	<hr><br>
	<?php // Was an import attempted and are we on the correct admin page?
	if ( isset( $_GET['imported'] ) ) {
		$imported = intval( $_GET['imported'] );
		if ( 1 == $imported ) {
			printf( '<div class="updated"><p>%s</p></div>', __( 'Data was successfully imported', 'hotel' ) );
		} else {
			printf( '<div class="error"><p>%s</p></div>', __( ' No data were imported', 'hotel' ) );
		}
	}
	?>
	<form method="post" action="admin-post.php" enctype="multipart/form-data">
		<p><label for="import_logs"><?php _e( 'Import an .xml file.','hotel' ); ?></label>
		<input type="file" id="import_logs" name="booking_import" /></p>
		<input type="hidden" name="action" value="hotel_booking_import" />
		<?php wp_nonce_field( 'hotel_booking_import_verify' ); ?>
		<?php submit_button( __( 'Import Booking System Data','hotel' ), 'button button-secondary' ); ?>
	</form><?php
}


function hotel_booking_add_style() {
	wp_register_style('hotel-admin-style', plugins_url('includes/css/admin-style.css', __FILE__));
	wp_register_style('datepicker-style', plugins_url('includes/css/datepicker-style.css', __FILE__));
	wp_enqueue_style('hotel-admin-style');
	wp_enqueue_style('datepicker-style');
}
add_action('admin_enqueue_scripts', 'hotel_booking_add_style');


function hotel_booking_add_script() {
	global $wpdb; 
	
	wp_register_script('hotel-admin-script', plugins_url('includes/js/admin-script.js', __FILE__), array('jquery'), false, true);
	wp_register_script('cookie', plugins_url('includes/js/jquery.cookie.js', __FILE__), array('jquery', 'hotel-admin-script'), false, true);
	$dateformat = $wpdb->get_var( "SELECT date_format FROM ".$wpdb->prefix."hotel_booking_settings");
	wp_enqueue_script('jquery');
	wp_enqueue_script('cookie');
	wp_enqueue_script('jquery-ui-datepicker');
	
	//Pass some variables to allscript files 
	$passVar = array( 'ajaxurl' => admin_url('admin-ajax.php'), 'dateformat' => $dateformat );
    wp_localize_script( 'hotel-admin-script', 'bookingOption', $passVar );
	
	wp_enqueue_script('hotel-admin-script');
}
add_action('admin_enqueue_scripts', 'hotel_booking_add_script');


function hotel_booking_admin_actions() {
	add_menu_page(__('Room Options','hotel'), __('Room Options','hotel'), 'manage_options', 'hotel-booking', 'hotel_booking_show_admin','dashicons-store' );
	add_submenu_page('hotel-booking', __('Reservations','hotel'), __('Reservations','hotel'), 'manage_options', 'hotel-booking-reservation-show', 'hotel_booking_show_reservations');
	add_submenu_page('hotel-booking', __('Availability/Price','hotel'), __('Availability/Price','hotel'), 'manage_options', 'availability-price-calendar', 'hotel_booking_add_edit_price');
	add_submenu_page('hotel-booking', __('Settings','hotel'), __('Settings','hotel'), 'manage_options', 'settings', 'hotel_booking_settings');
	add_submenu_page('hotel-booking', __('PayPal Settings','hotel'), __('PayPal Settings','hotel'), 'manage_options', 'paypal-settings', 'hotel_booking_paypal_settings');
	add_submenu_page('hotel-booking', __('Import/Export','hotel'), __('Import/Export','hotel'), 'manage_options', 'import-export', 'hotel_booking_import_export');
}
add_action('admin_menu', 'hotel_booking_admin_actions');






function hotel_create_my_post_types() {

	register_post_type( 'rooms', 
		array(
			'labels' => array(
				'name' => 'rooms' ,
				'singular_name' => 'Venue',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Venue',
				'edit' => 'Edit Venue',
				'edit_item' => 'Edit Venue',
			),
			'public' => true,
			'supports' => array( 'excerpt', 'title', 'editor', 'thumbnail', 'comments'),
			'menu_icon' => 'dashicons-dashicons-location-alt' 
		)
	);



	// Define additional "post thumbnails" for rooms and posts
	if (class_exists('MultiPostThumbnails')) {
	    new MultiPostThumbnails(array(
	        'label' => '1st venue Image',
	        'id' => 'slider-image-1',
	        'post_type' => 'rooms'
	        )
	    );
	    new MultiPostThumbnails(array(
	        'label' => '2nd venue Image',
	        'id' => 'slider-image-2',
	        'post_type' => 'rooms'
	        )
	    );
	    new MultiPostThumbnails(array(
	        'label' => '3rd venue Image',
	        'id' => 'slider-image-3',
	        'post_type' => 'rooms'
	        )
	    );
		new MultiPostThumbnails(array(
	        'label' => '4th venue Image',
	        'id' => 'slider-image-4',
	        'post_type' => 'rooms'
	        )
	    );
		new MultiPostThumbnails(array(
	        'label' => 'Post Thumbnail',
	        'id' => 'post-thumbnail',
	        'post_type' => 'post'
	        )
	    );
	};



}
add_action( 'init', 'hotel_create_my_post_types' );



// Add metaboxes
add_action( 'add_meta_boxes', 'hotel_meta_box' );  
add_action( 'save_post', 'metabox1_save' ); 
if ( defined('HOTEL_BOOKING_ACTIVE') ) { 
	add_action( 'save_post', 'metabox2_save' ); 
}
add_action( 'save_post', 'metabox3_save' ); 
add_action( 'save_post', 'metabox4_save' ); 
add_action( 'save_post', 'metabox5_save' ); 


function hotel_meta_box() {  
	if ( defined('HOTEL_BOOKING_ACTIVE') ) {
		add_meta_box( 'metabox2', 'Select Calendar for this Venue', 'metabox2_rendering', 'rooms', 'normal', 'core' ); 
	}
	add_meta_box( 'metabox1', 'Description', 'metabox1_rendering', 'rooms', 'normal', 'core' );  
	add_meta_box( 'metabox5', 'Single Blog Template', 'metabox5_rendering', 'post', 'normal', 'core' );
	add_meta_box( 'metabox3', 'Venue Title', 'metabox3_rendering', 'rooms', 'normal', 'core' ); 
	add_meta_box( 'metabox3', 'Page Title', 'metabox3_rendering', 'page', 'normal', 'core' ); 
	add_meta_box( 'metabox3', 'Page Title', 'metabox3_rendering', 'post', 'normal', 'core' ); 
	add_meta_box( 'metabox4', 'Author Info', 'metabox4_rendering', 'testimonials', 'normal', 'core' ); 
}  

function metabox1_rendering($page) {
	$values = get_post_custom( $page->ID ); 
	$max_person = isset( $values['max_person'] ) ? esc_attr( $values['max_person'][0] ) : '';  
	$room_bed = isset( $values['room_bed'] ) ? esc_attr( $values['room_bed'][0] ) : ''; 
	$room_size = isset( $values['room_size'] ) ? esc_attr( $values['room_size'][0] ) : ''; 
	$features = isset( $values['room_features'] ) ? esc_attr( $values['room_features'][0] ) : ''; 
	$policies = isset( $values['room_policies'] ) ? esc_attr( $values['room_policies'][0] ) : '';

	$buffet_menu = isset( $values['buffet_menu'] ) ? esc_attr( $values['buffet_menu'][0] ) : ''; 
	$alacarte_menu = isset( $values['alacarte_menu'] ) ? esc_attr( $values['alacarte_menu'][0] ) : ''; 
	
    wp_nonce_field( 'metabox1_nonce', 'metabox1_nonce' ); 
?>
	
	<div style="display:inline-block;margin:10px;margin-left:20px;">
		<label for="person_per_room"><?php _e('Max Person per venue','hotel'); ?></label><br />
		<p><input name="max_person" id="person_per_room" value="<?php echo $max_person ?>"></p>
	</div>
	
<!-- 	<div style="display:inline-block;margin:10px;margin-left:20px;">
		<label for="room_bed"><?php //_e('Room bed(s)','hotel'); ?></label><br />
		<p><input name="room_bed" id="room_bed" value="<?php// echo $room_bed ?>"></p>
	</div> -->
	
	<div style="display:inline-block;margin:10px;margin-left:20px;">
		<label for="room_size"><?php _e('Venue size','hotel'); ?></label><br />
		<p><input name="room_size" id="room_size" value="<?php echo $room_size ?>"></p>
	</div>

	<p><label for="room_features" style="margin-left:10px"><?php _e("Features (leave this field blank if you don't want to show features tab for this venue):",'hotel'); ?></label><br />
    <p><textarea style="margin-left:10px" rows="5" cols="90" name="room_features" id="room_features"><?php echo $features ?></textarea></p></p>

	<p><label for="room_policies" style="margin-left:10px"><?php _e("Policies (leave this field blank if you don't want to show policies tab for this venue):",'hotel'); ?></label><br />
    <p><textarea style="margin-left:10px" rows="5" cols="90" name="room_policies" id="room_policies"><?php echo $policies ?></textarea></p></p>


    	<p><label for="buffet_menu" style="margin-left:10px"><?php _e("Buffet (leave this field blank if you don't want to show buffet tab for this venue):",'hotel'); ?></label><br />
        <p><textarea style="margin-left:10px" rows="5" cols="90" name="buffet_menu" id="buffet_menu"><?php echo $buffet_menu ?></textarea></p></p>

    	<p><label for="alacarte_menu" style="margin-left:10px"><?php _e("Alacarte (leave this field blank if you don't want to show alacarte tab for this venue):",'hotel'); ?></label><br />
        <p><textarea style="margin-left:10px" rows="5" cols="90" name="alacarte_menu" id="alacarte_menu"><?php echo $alacarte_menu ?></textarea></p></p>
	
<?php }

function metabox1_save($page) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;  
    if( !isset( $_POST['metabox1_nonce'] ) || !wp_verify_nonce( $_POST['metabox1_nonce'], 'metabox1_nonce' ) ) return; 
    if( !current_user_can( 'edit_posts' ) ) return;  
        
	if( isset( $_POST['max_person'] ) ) 
		update_post_meta( $page, 'max_person', esc_attr( $_POST['max_person']) ); 
	
	// if( isset( $_POST['room_bed'] ) ) 
	// 	update_post_meta( $page, 'room_bed', esc_attr( $_POST['room_bed']) ); 
	
	if( isset( $_POST['room_size'] ) ) 
		update_post_meta( $page, 'room_size', esc_attr( $_POST['room_size']) ); 
		
	if( isset( $_POST['room_policies'] ) ) 
		update_post_meta( $page, 'room_policies', esc_attr( $_POST['room_policies']) ); 
		
	if( isset( $_POST['room_features'] ) ) 
		update_post_meta( $page, 'room_features', esc_attr( $_POST['room_features']) ); 
}

if ( defined('HOTEL_BOOKING_ACTIVE') ) {

	function metabox2_rendering($page) {
		$values = get_post_custom( $page->ID );
		$calendarG = isset( $values['calendar'] ) ? esc_attr( $values['calendar'][0] ) : '';  
		wp_nonce_field( 'metabox2_nonce', 'metabox2_nonce' ); 
	?>
	
	<div style="display:inline-block;margin:10px;margin-left:10px;">
		<div><?php _e('<b>Note: </b> you have to select different calendar for every venue.','hotel'); ?></div><br />
		<?php 
			global $wpdb;
			$calendars = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "hotel_booking_calendars");
			echo "<select name='calendar' style='width:250px !important'>";
			foreach($calendars as $calendar){
			?>
				<option <?php if ( $calendarG == $calendar->id ) echo 'selected="selected"'; ?> value="<?php echo $calendar->id ?>"> <?php echo $calendar->cal_name ?> </option>
			<?php
			}
			echo "</select>";
		?>
		<br><br><div><?php _e('(you can create new calendar by clicking on "Room Options" link in the left column)','hotel'); ?></div>
	</div>
	
	<?php }

	function metabox2_save($page) {
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;  
		if( !isset( $_POST['metabox2_nonce'] ) || !wp_verify_nonce( $_POST['metabox2_nonce'], 'metabox2_nonce' ) ) return; 
		if( !current_user_can( 'edit_posts' ) ) return;  
        
		if( isset( $_POST['calendar'] ) ) 
			update_post_meta( $page, 'calendar', esc_attr( $_POST['calendar']) );          
	}

}

function metabox3_rendering($page) {
	$values = get_post_custom( $page->ID );
	$pageDescription = isset( $values['page_description'] ) ? esc_attr( $values['page_description'][0] ) : '';  
	$pageTitle = isset( $values['page_title'] ) ? esc_attr( $values['page_title'][0] ) : "Yes"; 
	$pageClass = isset( $values['page_class'] ) ? esc_attr( $values['page_class'][0] ) : '';
	$breadcrumb = isset( $values['breadcrumb'] ) ? esc_attr( $values['breadcrumb'][0] ) : 'No'; 
	$pageIcon = isset( $values['page_icon'] ) ? esc_attr( $values['page_icon'][0] ) : '';
	$pageTitleAlign = isset( $values['page_align'] ) ? esc_attr( $values['page_align'][0] ) : 'Left';
    wp_nonce_field( 'metabox3_nonce', 'metabox3_nonce' ); 
?>
	<p><div><?php _e('Does page title should shows?','hotel'); ?></div>
	<p><input type="radio" id="title_yes" name="page_title" value="Yes" <?php checked( $pageTitle, 'Yes' ); ?> />
	<label for="title_yes"><?php _e('Yes','hotel'); ?></label>&nbsp;&nbsp;&nbsp;
	<input type="radio" id="title_no" name="page_title" value="No" <?php checked( $pageTitle, 'No' ); ?> />
	<label for="title_no"><?php _e('No','hotel'); ?></label></p></p>
	
	<?php if (get_post_type() == 'page') $cPage='page'; if (get_post_type() == 'rooms') $cPage='room'; if (get_post_type() == 'post') $cPage='post'; ?>
	<p><label for="page_description"><?php printf(__("Description field (leave this field blank if you don't want to show description for that %s):",'hotel'),$cPage); ?></label><br />
    <p><textarea rows="5" cols="90" name="page_description" id="page_description"><?php echo $pageDescription ?></textarea></p></p>
	
	<p><div>Does breadcrumb should shows?</div>
	<p><input type="radio" id="breadcrumb_yes" name="breadcrumb" value="Yes" <?php checked( $breadcrumb, 'Yes' ); ?> />
	<label for="breadcrumb_yes"><?php _e('Yes','hotel'); ?></label>&nbsp;&nbsp;&nbsp;
	<input type="radio" id="breadcrumb_no" name="breadcrumb" value="No" <?php checked( $breadcrumb, 'No' ); ?> />
	<label for="breadcrumb_no"><?php _e('No','hotel'); ?></label></p></p>
	
	<p><div>Choose page title alignment:</div>
	<p><input type="radio" id="title-align-left" name="page_align" value="Left" <?php checked( $pageTitleAlign, 'Left' ); ?> />
	<label for="title-align-left"><?php _e('Left','hotel'); ?></label>&nbsp;&nbsp;&nbsp;
	<input type="radio" id="title-align-center" name="page_align" value="Center" <?php checked( $pageTitleAlign, 'Center' ); ?> />
	<label for="title-align-center"><?php _e('Center','hotel'); ?></label></p></p>
	
	<p><div><?php _e('Add icon to page title:','hotel') ?></div></p>
	<input name="page_icon" id="page_icon" value="<?php echo $pageIcon; ?>">
	
	<p><div><?php _e('Add class to page wrap:','hotel') ?></div></p>
	<input name="page_class" id="page_class" value="<?php echo $pageClass; ?>">
<?php }

function metabox3_save($page) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;  
    if( !isset( $_POST['metabox3_nonce'] ) || !wp_verify_nonce( $_POST['metabox3_nonce'], 'metabox3_nonce' ) ) return; 
    if( !current_user_can( 'edit_posts' ) ) return;  
        
    if( isset( $_POST['page_description'] ) ) 
        update_post_meta( $page, 'page_description', esc_attr( $_POST['page_description']) );    
    
	if( isset( $_POST['page_title'] ) ) 
        update_post_meta( $page, 'page_title', esc_attr( $_POST['page_title']) ); 	
		
	if( isset( $_POST['page_class'] ) ) 
        update_post_meta( $page, 'page_class', esc_attr( $_POST['page_class']) ); 

	if( isset( $_POST['breadcrumb'] ) ) 
		update_post_meta( $page, 'breadcrumb', esc_attr( $_POST['breadcrumb']) );  
		
	if( isset( $_POST['page_icon'] ) ) 
		update_post_meta( $page, 'page_icon', esc_attr( $_POST['page_icon']) ); 
		
	if( isset( $_POST['page_align'] ) ) 
		update_post_meta( $page, 'page_align', esc_attr( $_POST['page_align']) ); 
}

function metabox4_rendering($page) {
	$values = get_post_custom( $page->ID );
	$authorName = isset( $values['author_name'] ) ? esc_attr( $values['author_name'][0] ) : ''; 
	$authorOccupation = isset( $values['author_occupation'] ) ? esc_attr( $values['author_occupation'][0] ) : ''; 	
    wp_nonce_field( 'metabox4_nonce', 'metabox4_nonce' ); 
	
?>
	
	<p><div><?php _e('Author Name:','hotel') ?></div></p>
	<input name="author_name" id="author_name_field" value="<?php echo $authorName; ?>">
	
	<p><div><?php _e('Author Occupation:','hotel') ?></div></p>
	<input name="author_occupation" id="author_occupation_field" value="<?php echo $authorOccupation; ?>">
	
<?php }

function metabox4_save($page) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;  
    if( !isset( $_POST['metabox4_nonce'] ) || !wp_verify_nonce( $_POST['metabox4_nonce'], 'metabox4_nonce' ) ) return; 
    if( !current_user_can( 'edit_posts' ) ) return;  
        
    if( isset( $_POST['author_name'] ) ) 
        update_post_meta( $page, 'author_name', esc_attr( $_POST['author_name']) );    
	
	if( isset( $_POST['author_occupation'] ) ) 
        update_post_meta( $page, 'author_occupation', esc_attr( $_POST['author_occupation']) );  
          
}

function metabox5_rendering($page) {
	$values = get_post_custom( $page->ID );
	$template = isset( $values['single_template'] ) ? esc_attr( $values['single_template'][0] ) : '';  
    wp_nonce_field( 'metabox5_nonce', 'metabox5_nonce' ); 
	
?>
	
	<div style="display:inline-block;margin:10px;margin-left:10px;">
		<div><?php _e('Select page template for blog post.','hotel'); ?></div><br />
		<select name='single_template' style='width:250px !important'>
			<option value="blog-right"> <?php _e('Blog standard','hotel') ?> </option>
			<option value="blog-fullwidth"> <?php _e('Blog fullwidth','hotel') ?></option>
			<option value="blog-left"> <?php _e('Blog left sidebar','hotel') ?> </option>
		</select>	
	</div>
	
<?php }

function metabox5_save($page) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;  
    if( !isset( $_POST['metabox5_nonce'] ) || !wp_verify_nonce( $_POST['metabox5_nonce'], 'metabox5_nonce' ) ) return; 
    if( !current_user_can( 'edit_posts' ) ) return;  
        
    if( isset( $_POST['single_template'] ) ) 
        update_post_meta( $page, 'single_template', esc_attr( $_POST['single_template']) );    
          
}


/* Filter the single_template with our custom function*/
add_filter('single_template', 'rooms_template');

function rooms_template($single) {
    global $wp_query, $post;

    /* Checks for single template by post type */
    if ($post->post_type == "rooms"){
        if(file_exists(PLUGIN_PATH . '/single-rooms.php'))
            return PLUGIN_PATH . '/single-rooms.php';
    }
    return $single;
}




function rooms_scripts() {
	global $wpdb; 
	
	wp_register_script('single_rooms_script', plugins_url('js/sc.js', __FILE__), array('jquery'), false, true);
	
	
	wp_enqueue_script('single_rooms_script');
}
add_action('wp_enqueue_scripts', 'rooms_scripts');




//-------------------------------------
$get_the_url = 'http://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js';

$cdnIsUp = get_transient( 'cnd_is_up' );

if ( $cdnIsUp ) {

    $load_source = 'load_external_validation';

} else {

    $cdn_response = wp_remote_get( $get_the_url );

    if( is_wp_error( $cdn_response ) || wp_remote_retrieve_response_code($cdn_response) != '200' ) {

        $load_source = 'load_local_validation';

    }
    else {

        $cdnIsUp = set_transient( 'cnd_is_up', true, MINUTE_IN_SECONDS * 20 );
        $load_source = 'load_external_validation';
    }
 }

add_action('wp_enqueue_scripts', $load_source );

function load_external_validation() { 
    wp_register_script( 'validation_plugin', 'http://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js', array('jquery'), 3.3, true); 
    wp_enqueue_script('validation_plugin'); 
}

function load_local_validation() {
    wp_register_script('validation_plugin',  plugins_url('js/validation.js', __FILE__), array('jquery'), 3.3, true);
    wp_enqueue_script('validation_plugin'); 
}

//-------------------------
//
//
add_action("single_rooms_script", function() {

     if (is_single()) {
        if (get_post_type() == 'hostel_room')
        {
          
			
			

			// wp_register_script( 'single_roomsjs', plugins_url('js/ev.js', __FILE__), array('jquery') );
			//   wp_enqueue_script('single_roomsjs',plugins_url('js/ev.js', __FILE__), array( 'jquery' ), '1.0' ,true);
			// wp_localize_script( 'combo_checkout_iRange', 'myAjax', $data_array );
			// 
			function rooms_scripts() {
				global $wpdb; 
				
				wp_register_script('single_rooms_script', plugins_url('js/script.js', __FILE__), array('jquery'), false, true);
				
				
				wp_enqueue_script('single_rooms_script');
			}
			add_action('wp_enqueue_scripts', 'rooms_scripts');
			
			
           
        }
    }
});

