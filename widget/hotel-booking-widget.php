<?php

//Calculating total booking price
function dateRange($first, $last, $format = 'm/d/Y' ) { 
	$dates = array();
	$current = strtotime($first);
	$last = strtotime($last);

	while( $current <= $last ) { 
		$dates[] = date($format, $current);
		$current = strtotime('+1 day', $current);
	}
	
	return $dates;
}


// Creating the widget 
add_shortcode('SH_TEST','SH_TEST_handler');
function SH_TEST_handler(){

	wp_register_style( 'sh_bh_classic', plugins_url( 'bower_components/pickadate/lib/themes/classic.css', __FILE__ ), array(), '1.0.0', 'all' );
	wp_register_style( 'sh_bh_style', plugins_url("css/style.css", __FILE__ ), array(), '1.0.0', 'all' );
	wp_register_style( 'sh_bh_classic_date', plugins_url("bower_components/pickadate/lib/themes/classic.date.css", __FILE__ ), array(), '1.0.0', 'all' );

	wp_enqueue_style( 'sh_bh_classic' );
	wp_enqueue_style( 'sh_bh_style' );
	wp_enqueue_style( 'sh_bh_classic_date' );
	wp_enqueue_script( 'picker', plugins_url( "bower_components/pickadate/lib/compressed/picker.js", __FILE__ ) );
	wp_enqueue_script( 'legacy', plugins_url( "bower_components/pickadate/lib/compressed/legacy.js", __FILE__ ) );
	wp_enqueue_script( 'picker.date', plugins_url( "bower_components/pickadate/lib/compressed/picker.date.js", __FILE__ ) );
	wp_enqueue_script( 'picker.time', plugins_url( "bower_components/pickadate/lib/compressed/picker.time.js", __FILE__ ) );
	wp_enqueue_script( 'datepicker-sample', plugins_url( "js/datepicker-sample.js", __FILE__ ) );
	wp_enqueue_script( 'index', plugins_url( "js/index.js", __FILE__ ) );


	?>
	<div class="cont">
		<div class="demo">
			<div class="login">
			<div class="hotel-widget-app__logo">
				<div class="hotel-widget-app__menu-btn">
					<span></span>
				</div>
				<svg class="hotel-widget-app__icon search svg-icon" viewBox="0 0 20 20">
					<!-- yeap, its purely hardcoded numbers straight from the head :D (same for svg above) -->
					<path d="M20,20 15.36,15.36 a9,9 0 0,1 -12.72,-12.72 a 9,9 0 0,1 12.72,12.72" />
				</svg>
				<p class="hotel-widget-app__hello">Good Morning!</p>
				<div class="hotel-widget-app__user">
				<?php //echo '<img src="'.get_template_directory_uri().'/images/logo-retina.png" data-width="0" alt="" id="prk_logo_image" />'; ?>
					<img src="//s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-512_5.jpg" alt="" class="hotel-widget-app__user-photo" />
					<span class="hotel-widget-app__user-notif"><?php echo count($rooms_list); ?></span>
				</div>
				<div class="hotel-widget-app__month">
					<span class="hotel-widget-app__month-btn left"></span>
					<p class="hotel-widget-app__month-name">March</p>
					<span class="hotel-widget-app__month-btn right"></span>
				</div>
			</div>

			<div>
				    <div align="center" id="checkin" name="checkin" style="background-color: #e9ad4d" class="hotel-widget-options">Check In</div>
				    <div align="center" style="background-color: #4abc66" class="hotel-widget-options">Night</div>
				    <div align="center" id="checkout" name="checkout" style="background-color: #4daed4" class="hotel-widget-options">Check Out</div>
				</div>

				<div class="login__form">

					<!-- <div class="login__row">
						<svg class="login__icon name svg-icon" viewBox="0 0 20 20">
							<path d="M16.254,3.399h-0.695V3.052c0-0.576-0.467-1.042-1.041-1.042c-0.576,0-1.043,0.467-1.043,1.042v0.347H6.526V3.052c0-0.576-0.467-1.042-1.042-1.042S4.441,2.476,4.441,3.052v0.347H3.747c-0.768,0-1.39,0.622-1.39,1.39v11.813c0,0.768,0.622,1.39,1.39,1.39h12.507c0.768,0,1.391-0.622,1.391-1.39V4.789C17.645,4.021,17.021,3.399,16.254,3.399z M14.17,3.052c0-0.192,0.154-0.348,0.348-0.348c0.191,0,0.348,0.156,0.348,0.348v0.347H14.17V3.052z M5.136,3.052c0-0.192,0.156-0.348,0.348-0.348S5.831,2.86,5.831,3.052v0.347H5.136V3.052z M16.949,16.602c0,0.384-0.311,0.694-0.695,0.694H3.747c-0.384,0-0.695-0.311-0.695-0.694V7.568h13.897V16.602z M16.949,6.874H3.052V4.789c0-0.383,0.311-0.695,0.695-0.695h12.507c0.385,0,0.695,0.312,0.695,0.695V6.874z M5.484,11.737c0.576,0,1.042-0.467,1.042-1.042c0-0.576-0.467-1.043-1.042-1.043s-1.042,0.467-1.042,1.043C4.441,11.271,4.908,11.737,5.484,11.737z M5.484,10.348c0.192,0,0.347,0.155,0.347,0.348c0,0.191-0.155,0.348-0.347,0.348s-0.348-0.156-0.348-0.348C5.136,10.503,5.292,10.348,5.484,10.348z M14.518,11.737c0.574,0,1.041-0.467,1.041-1.042c0-0.576-0.467-1.043-1.041-1.043c-0.576,0-1.043,0.467-1.043,1.043C13.475,11.271,13.941,11.737,14.518,11.737z M14.518,10.348c0.191,0,0.348,0.155,0.348,0.348c0,0.191-0.156,0.348-0.348,0.348c-0.193,0-0.348-0.156-0.348-0.348C14.17,10.503,14.324,10.348,14.518,10.348z M14.518,15.212c0.574,0,1.041-0.467,1.041-1.043c0-0.575-0.467-1.042-1.041-1.042c-0.576,0-1.043,0.467-1.043,1.042C13.475,14.745,13.941,15.212,14.518,15.212z M14.518,13.822c0.191,0,0.348,0.155,0.348,0.347c0,0.192-0.156,0.348-0.348,0.348c-0.193,0-0.348-0.155-0.348-0.348C14.17,13.978,14.324,13.822,14.518,13.822z M10,15.212c0.575,0,1.042-0.467,1.042-1.043c0-0.575-0.467-1.042-1.042-1.042c-0.576,0-1.042,0.467-1.042,1.042C8.958,14.745,9.425,15.212,10,15.212z M10,13.822c0.192,0,0.348,0.155,0.348,0.347c0,0.192-0.156,0.348-0.348,0.348s-0.348-0.155-0.348-0.348C9.653,13.978,9.809,13.822,10,13.822z M5.484,15.212c0.576,0,1.042-0.467,1.042-1.043c0-0.575-0.467-1.042-1.042-1.042s-1.042,0.467-1.042,1.042C4.441,14.745,4.908,15.212,5.484,15.212z M5.484,13.822c0.192,0,0.347,0.155,0.347,0.347c0,0.192-0.155,0.348-0.347,0.348s-0.348-0.155-0.348-0.348C5.136,13.978,5.292,13.822,5.484,13.822z M10,11.737c0.575,0,1.042-0.467,1.042-1.042c0-0.576-0.467-1.043-1.042-1.043c-0.576,0-1.042,0.467-1.042,1.043C8.958,11.271,9.425,11.737,10,11.737z M10,10.348c0.192,0,0.348,0.155,0.348,0.348c0,0.191-0.156,0.348-0.348,0.348s-0.348-0.156-0.348-0.348C9.653,10.503,9.809,10.348,10,10.348z" />
						</svg>
						<input type="text" id="checkin" name="checkin" class="login__input name" placeholder="Checkin"/>
					</div> -->
					<!-- <div class="login__row">
						<svg class="login__icon pass svg-icon" viewBox="0 0 20 20">
							<path d="M16.254,3.399h-0.695V3.052c0-0.576-0.467-1.042-1.041-1.042c-0.576,0-1.043,0.467-1.043,1.042v0.347H6.526V3.052c0-0.576-0.467-1.042-1.042-1.042S4.441,2.476,4.441,3.052v0.347H3.747c-0.768,0-1.39,0.622-1.39,1.39v11.813c0,0.768,0.622,1.39,1.39,1.39h12.507c0.768,0,1.391-0.622,1.391-1.39V4.789C17.645,4.021,17.021,3.399,16.254,3.399z M14.17,3.052c0-0.192,0.154-0.348,0.348-0.348c0.191,0,0.348,0.156,0.348,0.348v0.347H14.17V3.052z M5.136,3.052c0-0.192,0.156-0.348,0.348-0.348S5.831,2.86,5.831,3.052v0.347H5.136V3.052z M16.949,16.602c0,0.384-0.311,0.694-0.695,0.694H3.747c-0.384,0-0.695-0.311-0.695-0.694V7.568h13.897V16.602z M16.949,6.874H3.052V4.789c0-0.383,0.311-0.695,0.695-0.695h12.507c0.385,0,0.695,0.312,0.695,0.695V6.874z M5.484,11.737c0.576,0,1.042-0.467,1.042-1.042c0-0.576-0.467-1.043-1.042-1.043s-1.042,0.467-1.042,1.043C4.441,11.271,4.908,11.737,5.484,11.737z M5.484,10.348c0.192,0,0.347,0.155,0.347,0.348c0,0.191-0.155,0.348-0.347,0.348s-0.348-0.156-0.348-0.348C5.136,10.503,5.292,10.348,5.484,10.348z M14.518,11.737c0.574,0,1.041-0.467,1.041-1.042c0-0.576-0.467-1.043-1.041-1.043c-0.576,0-1.043,0.467-1.043,1.043C13.475,11.271,13.941,11.737,14.518,11.737z M14.518,10.348c0.191,0,0.348,0.155,0.348,0.348c0,0.191-0.156,0.348-0.348,0.348c-0.193,0-0.348-0.156-0.348-0.348C14.17,10.503,14.324,10.348,14.518,10.348z M14.518,15.212c0.574,0,1.041-0.467,1.041-1.043c0-0.575-0.467-1.042-1.041-1.042c-0.576,0-1.043,0.467-1.043,1.042C13.475,14.745,13.941,15.212,14.518,15.212z M14.518,13.822c0.191,0,0.348,0.155,0.348,0.347c0,0.192-0.156,0.348-0.348,0.348c-0.193,0-0.348-0.155-0.348-0.348C14.17,13.978,14.324,13.822,14.518,13.822z M10,15.212c0.575,0,1.042-0.467,1.042-1.043c0-0.575-0.467-1.042-1.042-1.042c-0.576,0-1.042,0.467-1.042,1.042C8.958,14.745,9.425,15.212,10,15.212z M10,13.822c0.192,0,0.348,0.155,0.348,0.347c0,0.192-0.156,0.348-0.348,0.348s-0.348-0.155-0.348-0.348C9.653,13.978,9.809,13.822,10,13.822z M5.484,15.212c0.576,0,1.042-0.467,1.042-1.043c0-0.575-0.467-1.042-1.042-1.042s-1.042,0.467-1.042,1.042C4.441,14.745,4.908,15.212,5.484,15.212z M5.484,13.822c0.192,0,0.347,0.155,0.347,0.347c0,0.192-0.155,0.348-0.347,0.348s-0.348-0.155-0.348-0.348C5.136,13.978,5.292,13.822,5.484,13.822z M10,11.737c0.575,0,1.042-0.467,1.042-1.042c0-0.576-0.467-1.043-1.042-1.043c-0.576,0-1.042,0.467-1.042,1.043C8.958,11.271,9.425,11.737,10,11.737z M10,10.348c0.192,0,0.348,0.155,0.348,0.348c0,0.191-0.156,0.348-0.348,0.348s-0.348-0.156-0.348-0.348C9.653,10.503,9.809,10.348,10,10.348z" />
						</svg>
						<input type="text" id="checkout" name="checkout" class="login__input pass" placeholder="Checkout"/>
					</div> -->
					<div class="login__row room-options">
						<select type="text" id="adult" class="login__input name">
							<option selected disabled>Adult</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
						</select>
					</div>
					<div class="login__row room-options">
						<select type="text" id="children" class="login__input name">
							<option selected disabled>Children</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
						</select>
					</div>
					<div class="login__row">
						<select type="text" id="room-number" class="login__input name">
							<option selected disabled>Rooms</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
						</select>
					</div>
					<button type="button" class="login__submit">Book Now</button>
					<p class="login__signup">View All &nbsp;<a>Rooms</a></p>
				</div>
			</div>
			<div id="hotel-widget-app-room-results" class="hotel-widget-app">
				<!-- here's the second form -->

			</div>
		</div>
	</div>
		<?php
}
function render_rooms($rooms_list){
	
		?>
			<div class="hotel-widget-app__top">
				<div class="hotel-widget-app__menu-btn">
					<span></span>
				</div>
				<svg class="hotel-widget-app__icon search svg-icon" viewBox="0 0 20 20">
					<!-- yeap, its purely hardcoded numbers straight from the head :D (same for svg above) -->
					<path d="M20,20 15.36,15.36 a9,9 0 0,1 -12.72,-12.72 a 9,9 0 0,1 12.72,12.72" />
				</svg>
				<p class="hotel-widget-app__hello">Good Morning!</p>
				<div class="hotel-widget-app__user">
				<?php //echo '<img src="'.get_template_directory_uri().'/images/logo-retina.png" data-width="0" alt="" id="prk_logo_image" />'; ?>
					<img src="//s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-512_5.jpg" alt="" class="hotel-widget-app__user-photo" />
					<span class="hotel-widget-app__user-notif"><?php echo count($rooms_list); ?></span>
				</div>
				<div class="hotel-widget-app__month">
					<span class="hotel-widget-app__month-btn left"></span>
					<p class="hotel-widget-app__month-name">March</p>
					<span class="hotel-widget-app__month-btn right"></span>
				</div>
			</div>
			<div class="hotel-widget-app__bot">
				<div class="hotel-widget-app__days">
					<div class="hotel-widget-app__day weekday">Sun</div>
					<div class="hotel-widget-app__day weekday">Mon</div>
					<div class="hotel-widget-app__day weekday">Tue</div>
					<div class="hotel-widget-app__day weekday">Wed</div>
					<div class="hotel-widget-app__day weekday">Thu</div>
					<div class="hotel-widget-app__day weekday">Fri</div>
					<div class="hotel-widget-app__day weekday">Sad</div>
					<div class="hotel-widget-app__day date">8</div>
					<div class="hotel-widget-app__day date">9</div>
					<div class="hotel-widget-app__day date">10</div>
					<div class="hotel-widget-app__day date">11</div>
					<div class="hotel-widget-app__day date">12</div>
					<div class="hotel-widget-app__day date">13</div>
					<div class="hotel-widget-app__day date">14</div>
				</div>
				<div class="hotel-widget-app__meetings">
				<?php 
				foreach ($rooms_list as $key => $room) {
					?>
					<div class="hotel-widget-app__meeting">
						<img src="http://s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-80_5.jpg" alt="" class="hotel-widget-app__meeting-photo" />
						<p class="hotel-widget-app__meeting-name"><?php echo $room['the_title']; ?></p>
						<p class="hotel-widget-app__meeting-info">
							<span class="hotel-widget-app__meeting-time">8 - 10am</span>
							<span class="hotel-widget-app__meeting-place">Real-life</span>
						</p>
					</div>
					<?php
					
				}
				?>
				
					<!-- <div class="hotel-widget-app__meeting">
						<img src="http://s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-80_5.jpg" alt="" class="hotel-widget-app__meeting-photo" />
						<p class="hotel-widget-app__meeting-name">Feed the cat</p>
						<p class="hotel-widget-app__meeting-info">
							<span class="hotel-widget-app__meeting-time">8 - 10am</span>
							<span class="hotel-widget-app__meeting-place">Real-life</span>
						</p>
					</div> 
					<div class="hotel-widget-app__meeting">
						<img src="//s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-512_5.jpg" alt="" class="hotel-widget-app__meeting-photo" />
						<p class="hotel-widget-app__meeting-name">Feed the cat!</p>
						<p class="hotel-widget-app__meeting-info">
							<span class="hotel-widget-app__meeting-time">1 - 3pm</span>
							<span class="hotel-widget-app__meeting-place">Real-life</span>
						</p>
					</div>
					<div class="hotel-widget-app__meeting">
						<img src="//s3-us-west-2.amazonaws.com/s.cdpn.io/142996/profile/profile-512_5.jpg" alt="" class="hotel-widget-app__meeting-photo" />
						<p class="hotel-widget-app__meeting-name">FEED THIS CAT ALREADY!!!</p>
						<p class="hotel-widget-app__meeting-info">
							<span class="hotel-widget-app__meeting-time">This button is just for demo </span>
						</p>
					</div> -->
				</div>
			</div>
			<div class="hotel-widget-app__logout back">
				<svg class="hotel-widget-app__logout-icon svg-icon" viewBox="0 0 20 20">
					<path d="M6,3 a8,8 0 1,0 8,0 M10,0 10,12"/>
				</svg>
			</div>
		<?php
	
}


add_action("wp_ajax_my_ajax_handler", "my_ajax_handler");
add_action("wp_ajax_nopriv_my_ajax_handler", "my_ajax_handler");

function my_ajax_handler() {
	if(isset($_POST['check-in']) && isset($_POST['check-out']) && isset($_POST['adult']) && isset($_POST['children']) && isset($_POST['room-number'])){
		$rooms_list = get_rooms($_POST['check-in'], $_POST['check-out'], $_POST['adult'], $_POST['children'], $_POST['room-number']);
		if(is_array($rooms_list)){
			// print_r($rooms_list);


			echo render_rooms($rooms_list);
		}else{
			echo $rooms_list;
		}
		die();
	}
}



function get_rooms($checkIn, $checkOut, $adult, $children, $roomNumber){
 	global $wpdb;
	$bookingSettings = $wpdb->get_row( "SELECT paypal_enabled, date_format, currency_symbol, tax, add_tax, hide_tax FROM {$wpdb->prefix}hotel_booking_settings" );
	$edit_mp = $adult;
	$dateFormat = $bookingSettings->date_format;
	$currencySymbol = $bookingSettings->currency_symbol;
	$tax = $bookingSettings->tax;
	$addTax = $bookingSettings->add_tax;
	$hideTax = $bookingSettings->hide_tax;
	$paypalEnabled = $bookingSettings->paypal_enabled;

	//Get and convert date that user selected to booking pro system format
	if ( $dateFormat == "european" ) {
		$rangeFormat = "d-m-Y";
	} else if ( $dateFormat == "american" ) {
		$rangeFormat = "m/d/Y";
	}
	$dateRange = dateRange( $checkIn, $checkOut, $rangeFormat );
	array_pop($dateRange);
	$roomNumber = 0;

	$the_query = new WP_Query(
		array(
			'post_type' => 'rooms',
			'showposts' => -1,
			'meta_key' => 'max_person',
			'meta_value' => $edit_mp,
			'meta_compare' => '='
			)
		);

	if ( $the_query->have_posts() ) {
		$c=0;
		while ( $the_query->have_posts() ) {
     		$the_query->the_post();
     		
     		$roomAvailable = false;
     		$calID = get_post_meta(get_the_ID(),'calendar',true);
     									
     		if ( isset($_POST['selected-room']) && $_POST['selected-room'] != $calID ) {
     			continue;
     		}
     		
     		$price = $wpdb->get_row( $wpdb->prepare( 
     			"SELECT min_price FROM {$wpdb->prefix}hotel_booking_calendars WHERE id='%d'",
     			$calID
     			) );

     		$minPrice = $price->min_price;
     		for ($ra=0;$ra<count($dateRange);$ra++) {
     			$dayCurrent = date("d",strtotime($dateRange[$ra]));							
     			$monthCurrent = date("m",strtotime($dateRange[$ra]));
     			$yearCurrent = date("Y",strtotime($dateRange[$ra]));
     			$roomAvailty[$ra] = $wpdb->get_row( $wpdb->prepare( 
     				"SELECT * FROM {$wpdb->prefix}hotel_booking_availability WHERE calendar_id='%d' 
     				AND day = $dayCurrent AND month = $monthCurrent AND year = $yearCurrent AND availability >= %d ",
     				$calID,$roomNumber
     			) );
     			
     			if (!$roomAvailty[$ra]) {
     				break;
     			}		
     		}
     		
     		if ( $ra == count( $dateRange ) ) {
     			$roomAvailable = true;
     		} 
         		
     		// Display rooms that available on selected date
     		if ($roomAvailable) {
     			$roomNumber++;
     			if ( has_post_thumbnail() ) {
     				$rooms_list[$c]['the_post_thumbnail'] = the_post_thumbnail('room-normal',array("class"=>"reservation-list-image"));
     			} 

     			$rooms_list[$c]['the_title'] = get_the_title(); 
     			$rooms_list[$c]['calendar'] =  get_post_meta(get_the_ID(),'calendar',true); 
     			if ($minPrice != 0) {
     				$rooms_list[$c]['minPrice'] = $minPrice.$currencySymbol;
     			}

     			for ($i=0;$i<count($roomAvailty);$i++) { 
					$fullDate = $roomAvailty[$i]->day."-".$roomAvailty[$i]->month."-".$roomAvailty[$i]->year;
					$multiple = ($roomNumber > 1) ? " (x".$roomNumber.")" : "";
					$rooms_list[$c]['prices_breakdown'][date("l, F d, Y", strtotime($fullDate)).$multiple] = $roomAvailty[$i]->price*$roomNumber.$currencySymbol;
					

					$total += $roomAvailty[$i]->price;
					$breakdown[$i] = $roomAvailty[$i]->id;
				} 
				
				$incTax = 0;
				$incSrv = 0;
				
				if ( isset($addTax) && $addTax == 1 && isset($hideTax) && $hideTax == 0 ) {
					$incSrv = $total * $roomNumber * (12/100);
					$rooms_list[$c]['prices_breakdown']['Service (12%%)'] = $incSrv.$currencySymbol;
				} 
				$incTax = (($total * $roomNumber) + $incSrv) * ($tax/100);
				$rooms_list[$c]['prices_breakdown']['Tax ('.$tax.'%):'] = $incTax.$currencySymbol; 
				$rooms_list[$c]['prices_breakdown']['Total'] = $total * $roomNumber + $incTax + $incSrv.$currencySymbol; 

     			$dayID = array();
     			
     			for ( $i=0; $i<count($roomAvailty); $i++ ) {
     				$dayID[$i] = $roomAvailty[$i]->id;
     			}
     			
     			$dayID = implode( ",", $dayID );
     			$rooms_list[$c]['day-ids'] = $dayID;
     		}
     		$c++;
     	}
    } else {
    	$rooms_list = "There's no rooms to show!";
    }
    return $rooms_list;
}

	   
?>