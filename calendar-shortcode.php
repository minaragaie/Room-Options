<?php 

function hotel_booking_add_booking_script() {
	global $wpdb; 
	
	$currency = $wpdb->get_var( "SELECT currency_symbol FROM ".$wpdb->prefix."hotel_booking_settings");
	
	wp_register_script('custom-calendar', plugins_url('includes/js/calendar-script.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('custom-calendar');
	
	//Pass some variables to custom-calendar 
	$passVar = array( 'ajaxurl' => admin_url('admin-ajax.php'), 'currency' => $currency, 'available' => __('available','hotel'), 'booked' => __('booked','hotel') );
    wp_localize_script( 'custom-calendar', 'bookingOption', $passVar );

	wp_register_style('hotel-calendar-style', plugins_url('includes/css/calendar-style.css', __FILE__));
	wp_enqueue_style('hotel-calendar-style');
	
}
add_action('wp_enqueue_scripts', 'hotel_booking_add_booking_script');


function shortcodeInit( $atts, $content = null ) {
	global $wp_locale;
		
	$dayNames =	__("['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']","hotel");
	$dayShort = __("['Sun','Mon','Tue','Wed','Thu','Fri','Sat']","hotel");
	$dayMin = __("['Su','Mo','Tu','We','Th','Fr','Sa']","hotel");
	$monthShort = __("['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']","hotel");
	$monthLong = __("['January','February','March','April','May','June','July','August','September','October','November','December']","hotel");	
	
	extract(shortcode_atts(array(
		'id' => '',
	), $atts, 'error'));

	$content = "
	
	<script>
	(function($) {
		$(document).ready(function() { 
			$('#hotel-calendar-view-$id').hotelcalendar({
				
				dayNames:".$dayNames.",
				dayNamesShort:".$dayShort.",
				dayNamesMin:".$dayMin.",
				monthNames:".$monthLong.",
				monthNamesShort:".$monthShort.",
				
				calID:$id,		
				beforeShowDay: function (date) {return [false, ''];}
			});
		})
	})(jQuery);
	</script>	
	<div id='hotel-calendar-view-$id' class='hotel-datepicker-initialize'><span class='calendar-loading'>&nbsp;</span></div>";
	
	return $content;
	
}
add_shortcode('hotel_calendar', 'shortcodeInit');

?>