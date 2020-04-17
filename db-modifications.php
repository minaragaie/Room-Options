<?php

// Calendar database modifications


function hotel_booking_add_calendar_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	
	// Check that nonce field
	check_admin_referer( 'hotel_add_calendar_verify' );
	
	if ( isset($_POST["cal_name"]) ) {
		if ( isset( $_POST["cal_min_price"] ) ) { 
			$min_price = $_POST["cal_min_price"];
		} else {
			$min_price = '';
		}		
		$wpdb->insert($wpdb->prefix.'hotel_booking_calendars', array('cal_name' => $_POST["cal_name"], 'min_price' => $min_price ));
	}
	
	wp_redirect(  admin_url( "admin.php?page=hotel-booking" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_add_calendar', 'hotel_booking_add_calendar_func' );


function hotel_booking_edit_calendar_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	
	// Check that nonce field
	check_admin_referer( 'hotel_edit_calendar_verify' );
	
	if ( isset($_POST["cal_name"]) ) {
		if ( isset( $_POST["cal_min_price"] ) ) { 
			$min_price = $_POST["cal_min_price"];
		} else {
			$min_price = '';
		}		
		$wpdb->update( $wpdb->prefix.'hotel_booking_calendars', array('cal_name' => $_POST["cal_name"], 'min_price' => $min_price ), array('id'=>$_POST["cal_id"]) );
	}
	
	wp_redirect(  admin_url( "admin.php?page=hotel-booking" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_edit_calendar', 'hotel_booking_edit_calendar_func' );


function hotel_booking_delete_calendar_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	
	// Check that nonce field
	check_admin_referer( 'hotel_delete_calendar_verify' );
	
	if ( isset($_POST["cal_id"]) ) {		
		$wpdb->delete( $wpdb->prefix.'hotel_booking_calendars', array( 'ID' => $_POST["cal_id"] ), array( '%d' ) );
	}
	
	wp_redirect(  admin_url( "admin.php?page=hotel-booking" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_delete_calendar', 'hotel_booking_delete_calendar_func' );


// Reservation Database modifications 
function hotel_booking_add_reservation_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( 'You are not allowed to be on this page.' );
	}
	// Check nonce field
	check_admin_referer( 'hotel_add_reservation_verify' );
	
	$dateformat = $wpdb->get_var( "SELECT date_format FROM ".$wpdb->prefix."hotel_booking_settings");
	
	//Calculate price of reservation
	$datesArray = createDateRangeArray( $_POST["check-in"], $_POST["check-out"], $dateformat );
	
	$errorText = "";
	$error = 0;
	$price = 0;
	
	if ( $datesArray ) { 
		for ( $i=0;$i<count($datesArray)-1;$i++ ) {
			//Process dates
			$day = date("d", strtotime($datesArray[$i]));
			$month = date("m", strtotime($datesArray[$i]));
			$year = date("Y", strtotime($datesArray[$i]));
			
			$getPriceAvailability = $wpdb->get_row( $wpdb->prepare(
				"SELECT price,availability FROM ".$wpdb->prefix."hotel_booking_availability WHERE day=%d AND month=%d AND year=%d AND calendar_id=%d", 
				$day, $month, $year, $_POST["cal_id"]
			));
		
			$dayNow = date("d");
			$monthNow = date("m");
			$yearNow = date("Y");
			
			if ( $day < $dayNow || $month < $monthNow || $year < $yearNow ) { 
				$error = 1;
				$errorText = __("You can't made reservation on the date that already has passed.","hotel");
				break;
			} else if ( !$getPriceAvailability ) { 
				$error = 1;
				$errorText = __("You can't create this reservation as room don't available on selected dates. To made this room available please visit Dashboard > Room Options > Availability/Price.", "hotel");
				break;
			} else if ( isset($getPriceAvailability->availability) && $getPriceAvailability->availability == 0 ) {
				$error = 1;
				$errorText = __("You can't create this reservation as the room was already booked on the selected dates.","hotel");
				break;
			} else if ( isset($getPriceAvailability->availability) && $_POST["room-number"] > $getPriceAvailability->availability ) {
				$error = 1;
				$errorText = __("You can't create this reservation as the number of rooms available on this date less than requested number.","hotel");
				break;
			} else {
				$price += $getPriceAvailability->price;
			}
		}
	} else {
		$error = 1;
		$errorText = __("Check out date should be larger than Check in date","hotel");
	}
	
	if ( $error == 0 ) {
		 $wpdb->insert($wpdb->prefix.'hotel_booking_reservation', array(
	       	'calendar_id' => $_POST["room-id"], 
	       	'check_in' => date('d/m/Y', strtotime($_POST["check-in"])),
	       	'check_out' => date('d/m/Y', strtotime($_POST["check-out"])),
	       	'no_items' =>1 ,// $_POST["room-number"], 
	       	'email' => $_POST["resform-email"], 
	       	'phone' => $_POST["phone"],// 
	       	'confirmed' => $_POST["confirmed"],// '', 
	       	'expected' => $_POST["expected"],
	       	'rooms_type' => $_POST["rooms-type"],
	       	'buffet' => $_POST["food-type"],
	       	'beverage' => $_POST["beverage"], // '', 
	       	'status' => 'pending',// 'pending', 
	       	'price' => $_POST["price"],// $_POST["price"], 
	       	'name' => $_POST["resform-firstname"], // $_POST["resform-firstname"], 
	       	'surname' => $_POST["resform-lastname"], // $_POST["resform-lastname"], 
	       	'paypal_payment' => 0 ,// 0, 
	       	'paypal_payer_id' => 0,// 0, 
	       	'paypal_transaction_id' =>0,// 0,
	       	'card_type' => $_POST["resform-cardtype"],// 0, 
	       	'cardholder_name' => $_POST["resform-cardholdername"],// 0, 
	       	'card_number' => $_POST["resform-cardnumber"],// 0, resform-cardnumber
	       	'expiration_year' => $_POST["resform-expirationmonth"],// 0, 
	       	'expiration_month' => $_POST["resform-expirationyear"],// 0, 
	       	'comments' => $_POST["resform-comments"],// $_POST["resform-comments"], 
	       	'date_created' =>  current_time('mysql', 1) ,// current_time('mysql', 1) 
	       )); 
		
		//Send email to admin and user
		sendEmail ( 'without_approval', $reservation->calendar_id, $_POST["reservation_id"], $reservation->check_in, $reservation->check_out, $reservation->price, $reservation->no_items, $reservation->confirmed, $reservation->expected, $reservation->buffet, $reservation->beverage, $reservation->email, $reservation->name, $reservation->surname, $reservation->comments, $reservation->card_type, $reservation->cardholder_name, $reservation->card_number, $reservation->expiration_month, $reservation->expiration_year );

	// 	( $type, $cal_id, $reservation_id, $checkin, $checkout, $total, $items, $confirmed, 
	// $expected, $buffet, $beverage, $email, $name, $surname, $comments, $cardtype, $cardholder, $cardnumber, $expmonth, $expyear )
		
		wp_redirect(  admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
		exit;
	} else {
		echo $errorText."<br>";
		printf(__("<a href='%s'>Return on previous page</a>","hotel"), admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
	}
	
}
add_action( 'admin_post_hotel_booking_add_reservation', 'hotel_booking_add_reservation_func' );


// Reservation database modifications
function hotel_booking_approve_reservation_func() {
	global $wpdb;
	
	$error=0;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_approve_reservation_verify' );
	
	$dateformat = $wpdb->get_var( "SELECT date_format FROM ".$wpdb->prefix."hotel_booking_settings");
	
	if ( isset($_POST["reservation_id"]) ) {
		
		//Retrieve our reservation date from the table
		$reservation = $wpdb->get_row( $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'hotel_booking_reservation WHERE id=%d',$_POST["reservation_id"]
			)
		);
		
		//Create date range
		$datesArray = createDateRangeArray( $reservation->check_in, $reservation->check_out, $dateformat );
		
		//Automatically made reservation on selected date in availability table
		for ( $i=0;$i<count($datesArray)-1;$i++ ) {
			//Process dates
			$day = date("d", strtotime($datesArray[$i]));
			$month = date("m", strtotime($datesArray[$i]));
			$year = date("Y", strtotime($datesArray[$i]));
			
			$numberAvailable = $wpdb->get_var( $wpdb->prepare(
				"SELECT availability FROM ".$wpdb->prefix."hotel_booking_availability WHERE day=%d AND month=%d AND year=%d AND calendar_id=%d", 
				$day, $month, $year, $reservation->calendar_id
			));
			
			$totalNumberAvailable = $numberAvailable - $reservation->no_items;
			
			$status = $wpdb->update( $wpdb->prefix.'hotel_booking_availability', array( 'availability' => $totalNumberAvailable ), array( 'day' => $day, 'month' => $month, 'year' => $year, 'calendar_id' => $reservation->calendar_id ) );
		}
		
		$wpdb->update( $wpdb->prefix.'hotel_booking_reservation', array( 'status' => "approved" ), array( 'id' => $_POST["reservation_id"] ) );
		
		//Send email to admin and user
		sendEmail ( 'approval', $reservation->calendar_id, $_POST["reservation_id"], $reservation->check_in, $reservation->check_out, $reservation->price, $reservation->no_items, $reservation->confirmed, $reservation->expected, $reservation->buffet, $reservation->beverage, $reservation->email, $reservation->name, $reservation->surname, $reservation->comments, $reservation->card_type, $reservation->cardholder_name, $reservation->card_number, $reservation->expiration_month, $reservation->expiration_year );
		
		wp_redirect(  admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
		exit;
	}
}
add_action( 'admin_post_hotel_booking_approve_reservation', 'hotel_booking_approve_reservation_func' );


function hotel_booking_cancel_reservation_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_cancel_reservation_verify' );
	
	$dateformat = $wpdb->get_var( "SELECT date_format FROM ".$wpdb->prefix."hotel_booking_settings");
	
	//Retrieve our reservation date from the table
	$reservation = $wpdb->get_row( $wpdb->prepare(
		'SELECT * FROM '.$wpdb->prefix.'hotel_booking_reservation WHERE id=%d',$_POST["reservation_id"]
	));
		
	//Create date range
	$datesArray = createDateRangeArray( $reservation->check_in, $reservation->check_out, $dateformat );
		
	for ( $i=0;$i<count($datesArray)-1;$i++ ) {
		//Process dates
		$day = date("d", strtotime($datesArray[$i]));
		$month = date("m", strtotime($datesArray[$i]));
		$year = date("Y", strtotime($datesArray[$i]));
			
		$numberAvailable = $wpdb->get_var( $wpdb->prepare(
			"SELECT availability FROM ".$wpdb->prefix."hotel_booking_availability WHERE day=%d AND month=%d AND year=%d AND calendar_id=%d", 
			$day, $month, $year, $reservation->calendar_id
		));
			
		$totalNumberAvailable = $numberAvailable + $reservation->no_items;
		$status = $wpdb->update( $wpdb->prefix.'hotel_booking_availability', array( 'availability' => $totalNumberAvailable ), array( 'day' => $day, 'month' => $month, 'year' => $year, 'calendar_id' => $reservation->calendar_id ) );
	}
		
	$wpdb->update( $wpdb->prefix.'hotel_booking_reservation', array( 'status' => "canceled" ), array( 'id' => $_POST["reservation_id"] ) );
	
	//Send email to admin and user
	sendEmail ( 'canceled', $reservation->calendar_id, $_POST["reservation_id"], $reservation->check_in, $reservation->check_out, $reservation->price, $reservation->no_items, $reservation->confirmed, $reservation->expected, $reservation->buffet, $reservation->beverage, $reservation->email, $reservation->name, $reservation->surname, $reservation->comments, $reservation->card_type, $reservation->cardholder_name, $reservation->card_number, $reservation->expiration_month, $reservation->expiration_year );
		
	wp_redirect(  admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
	exit;

}
add_action( 'admin_post_hotel_booking_cancel_reservation', 'hotel_booking_cancel_reservation_func' );


function hotel_booking_delete_reservation_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( 'You are not allowed to be on this page.' );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_delete_reservation_verify' );
	
	if ( isset($_POST["reservation_id"]) ) {
		$wpdb->delete( $wpdb->prefix.'hotel_booking_reservation', array( 'id' => $_POST["reservation_id"] ) );
	}
	wp_redirect( admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_delete_reservation', 'hotel_booking_delete_reservation_func' );


function hotel_booking_reject_reservation_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( 'You are not allowed to be on this page.' );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_reject_reservation_verify' );
	
	if ( isset($_POST["reservation_id"]) ) {
		$wpdb->update( $wpdb->prefix.'hotel_booking_reservation', array( 'status' => "rejected" ), array( 'id' => $_POST["reservation_id"] ) );
	
		//Retrieve our reservation date from the table
		$reservation = $wpdb->get_row( $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'hotel_booking_reservation WHERE id=%d',$_POST["reservation_id"]
		));
	
		//Send email to admin and user
		sendEmail ( 'rejected', $reservation->calendar_id, $_POST["reservation_id"], $reservation->check_in, $reservation->check_out, $reservation->price, $reservation->no_items, $reservation->confirmed, $reservation->expected, $reservation->buffet, $reservation->beverage, $reservation->email, $reservation->name, $reservation->surname, $reservation->comments, $reservation->card_type, $reservation->cardholder_name, $reservation->card_number, $reservation->expiration_month, $reservation->expiration_year );
	
	}
	
	wp_redirect(  admin_url( "admin.php?page=hotel-booking-reservation-show" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_reject_reservation', 'hotel_booking_reject_reservation_func' );


// Dates database modifications
function hotel_booking_date_modification_func() {
	global $wpdb;
	
	$dateformat = $wpdb->get_var( "SELECT date_format FROM ".$wpdb->prefix."hotel_booking_settings");
		
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( 'You are not allowed to be on this page.' );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_date_modification_verify' );
	
	if ( isset($_POST["check-in"]) && isset($_POST["check-out"]) ) {
	
		$datesArray = createDateRangeArray( $_POST["check-in"], $_POST["check-out"], $dateformat );
		
		array_pop($datesArray);
					
		for ( $i=0;$i<count($datesArray);$i++ ) {
			//Process dates
			$day = date("d", strtotime($datesArray[$i]));
			$month = date("m", strtotime($datesArray[$i]));
			$year = date("Y", strtotime($datesArray[$i]));
			
			$status = $wpdb->update( $wpdb->prefix.'hotel_booking_availability', array( 'availability' => $_POST["availability"], 'price' => $_POST["price"] ), array( 'day' => $day, 'month' => $month, 'year' => $year, 'calendar_id' => $_POST["cal_id"] ) );
		
			if ( !$status ) {
				$wpdb->insert($wpdb->prefix.'hotel_booking_availability', array('calendar_id' => $_POST["cal_id"], 'day' => $day, 'month' => $month, 'year' => $year, 'availability' => $_POST["availability"], 'price' => $_POST["price"] ));
			}
		}
	}
	
	$day = date("d"); $month = date("m"); $year = date("Y");

	$availabilities = $wpdb->get_results( $wpdb->prepare( 
		"SELECT price FROM ".$wpdb->prefix."hotel_booking_availability WHERE calendar_id = %d AND day >= $day AND month >= $month AND year >= $year", 
		$_POST["cal_id"]
	));
	
	// Find min price
	if ($wpdb->num_rows != 0){
		if ($availabilities){
			foreach( $availabilities as $availability ) {
				if ( !isset($priceArray) ) {
					$priceArray[0]=$availability->price;
				} else {
					array_push($priceArray,$availability->price);
				}
			}
		}
	}
	
	$minPrice = min($priceArray);
	$status = $wpdb->update( $wpdb->prefix.'hotel_booking_calendars', array( 'min_price' => $minPrice ), array( 'id' => $_POST["cal_id"] ) );
	
	wp_redirect(  admin_url( "admin.php?page=availability-price-calendar" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_date_modification', 'hotel_booking_date_modification_func' );


// Settings Database modifications
function hotel_booking_settings_edit_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_date_settings_edit' );
		
	$check = $wpdb->update( $wpdb->prefix.'hotel_booking_settings', array( 
		'email' => $_POST['email'],
		'currency_symbol' => $_POST['currency_symbol'], 
		'date_format' => $_POST['date_format'], 
		'hide_tax' => $_POST['hide_tax'],
		'add_tax' => $_POST['add_tax'],
		'tax' => $_POST['tax'], 
		'confirmation_email_header' => $_POST['confirmation_email_header'], 
		'confirmation_email_content' => $_POST['confirmation_email_content'], 
		'cancelation_email_header' => $_POST['cancelation_email_header'], 
		'cancelation_email_content' => $_POST['cancelation_email_content'], 
		'without_confirmation_email_header' => $_POST['without_confirmation_email_header'], 
		'without_confirmation_email_content' => $_POST['without_confirmation_email_content'], 
		'rejected_email_header' => $_POST['rejected_email_header'], 
		'rejected_email_content' => $_POST['rejected_email_content'] ), 
		array( 'id' => 1 ) );
		
	
	wp_redirect(  admin_url( "admin.php?page=settings" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_settings_edit', 'hotel_booking_settings_edit_func' );


// Paypal Settings Database modifications
function hotel_booking_paypal_settings_edit_func() {
	global $wpdb;
	
	if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You are not allowed to be on this page.','hotel') );
	}
	// Check that nonce field
	check_admin_referer( 'hotel_date_paypal_settings_edit' );
	
	$wpdb->update( $wpdb->prefix.'hotel_booking_settings', array( 
		'paypal_enabled' => $_POST['paypal_enabled'],
		'sandbox_enabled' => $_POST['paypal_sandbox_enabled'],
		'paypal_api_username' => $_POST['api_username'], 
		'paypal_api_password' => $_POST['api_password'], 
		'paypal_api_signature' => $_POST['api_signature'],
		'paypal_currency_code' => $_POST['paypal_currency_code']
	), array( 'id' => 1 ) );
	
	wp_redirect(  admin_url( "admin.php?page=paypal-settings" ) );
	exit;
}
add_action( 'admin_post_hotel_booking_paypal_settings_edit', 'hotel_booking_paypal_settings_edit_func' );


?>