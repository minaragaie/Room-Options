<?php

 // takes two dates formatted as YYYY-MM-DD and creates an
 // inclusive array of the dates between the from and to dates.

function createDateRangeArray( $strDateFrom,$strDateTo,$format ) {
   
    $aryRange=array();
    if ( $format == "european" ) {
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,3,2),substr($strDateFrom,0,2),substr($strDateFrom,6,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,3,2),substr($strDateTo,0,2),substr($strDateTo,6,4));
	} else if ( $format == "american" ) {
		$iDateFrom=mktime(1,0,0,substr($strDateFrom,0,2),substr($strDateFrom,3,2),substr($strDateFrom,6,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,0,2),substr($strDateTo,3,2),substr($strDateTo,6,4));
	}

	
    if ($iDateTo>=$iDateFrom) {
        if ( $format == "european" ) {
			array_push($aryRange, date('d-m-Y',$iDateFrom)); 
		} else if ( $format == "american" ) {
			array_push($aryRange,date('m/d/Y',$iDateFrom)); 
		}
        while ($iDateFrom<$iDateTo) {
            $iDateFrom+=86400; // add 24 hours
            if ( $format == "european" ) {
				array_push($aryRange,date('d-m-Y',$iDateFrom)); 
			} else if ( $format == "american" ) {
				array_push($aryRange,date('m/d/Y',$iDateFrom)); 
			}
        }
    }
	
    return $aryRange;
}


function sendEmail ( $type, $cal_id, $reservation_id, $checkin, $checkout, $total, $items, $confirmed, 
	$expected, $buffet, $beverage, $email, $name, $surname, $comments, $cardtype, $cardholder, $cardnumber, $expmonth, $expyear ) {
		
	global $wpdb;
	
	$bookingSettings = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hotel_booking_settings" );
	$calendar = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hotel_booking_calendars WHERE id='".$cal_id."'" );
	
	if ( $type == 'without_approval' ) {
		$subject = $bookingSettings->without_confirmation_email_header;
		$content = 	$bookingSettings->without_confirmation_email_content;
	} else if ( $type == 'approval' ) {
		$subject = $bookingSettings->confirmation_email_header;
		$content = $bookingSettings->confirmation_email_content;
	} else if ( $type == 'rejected' ) {
		$subject = $bookingSettings->rejected_email_header;
		$content = $bookingSettings->rejected_email_content;
	} else if ( $type == 'canceled' ) {
		$subject = $bookingSettings->cancelation_email_header;
		$content = $bookingSettings->cancelation_email_content;
	}
	
	$adult = str_replace(array("[","]","\""),array("","",""),$adult);
	$adult = explode(",",$adult);
		
	$child = str_replace(array("[","]","\""),array("","",""),$child);
	$child = explode(",",$child);

	$email_content = $content."<br><br>";
	
	//Booking Info
	$email_content .= "<strong>".__("Reservation ID","hotel").":</strong> ".$reservation_id."<br>";
	$email_content .= "<strong>".__("Calendar ID","hotel").":</strong> ".$cal_id."<br>";
	$email_content .= "<strong>".__("Room Type","hotel").":</strong> ".$calendar->cal_name."<br>";
	$email_content .= "<strong>".__("Number of booked items","hotel").":</strong> ".$items."<br>";
	
	$email_content .= "<strong>".__("confirmed","hotel").":</strong> ".$confirmed."<br>";
	$email_content .= "<strong>".__("expected","hotel").":</strong> ".$expected."<br>";
	if($beverage =="on"){
		$beverage ="Beverage Included";
	}else{
		$beverage ="Not Included";
	}
	$email_content .= "<strong>".__("Beverage","hotel").":</strong> ".$beverage."<br>";
	$email_content .= "<strong>".__("Buffet","hotel").":</strong> ".$buffet."<br>";

	
	
	$email_content .= "<br>";
	
	//Check in and check out date
	$email_content .= "<strong>".__("Check In","hotel").":</strong> ".$checkin."<br>";
	$email_content .= "<strong>".__("Check Out","hotel").":</strong> ".$checkout."<br>";
	$email_content .= "<br>";
	
	//Personal Info
	$email_content .= "<strong>".__("Name","hotel").":</strong> ".$name."<br>";
	$email_content .= "<strong>".__("Surname","hotel").":</strong> ".$surname."<br>";
	$email_content .= "<strong>".__("Email","hotel").":</strong> ".$email."<br>";
	$email_content .= "<strong>".__("Card type","hotel").":</strong> ".$cardtype."<br>";
	$email_content .= "<strong>".__("Cardholder name","hotel").":</strong> ".$cardholder."<br>";
	$email_content .= "<strong>".__("Card number","hotel").":</strong> ".$cardnumber."<br>";
	$email_content .= "<strong>".__("Expiration date","hotel").":</strong> ".$expmonth."/".$expyear."<br>";
	if ( isset($comments) && $comments != "" ) { $email_content .= "<strong>".__("Comments","hotel").":</strong> ".$comments."<br>"; }
	$email_content .= "<br>";
	
	//Total Info
	$email_content .= "<strong>".__("Total","hotel").":</strong> ".$total.$bookingSettings->currency_symbol;
	
	//Send email with reservation info to user
	hotel_send($email, $bookingSettings->email, $subject, $email_content);
	
	//Send email with reservation info to admin
	hotel_send($bookingSettings->email, $email , $subject, $email_content);
	
}


function hotel_send($email, $emailfrom, $subject, $content){
    $header = "Content-type: text/html; charset=utf-8"."\r\n". "From: ".get_bloginfo('name')." <".$emailfrom.">\r\n". "Reply-To:".$emailfrom;
    wp_mail($email, $subject, $content, $header);
}

?>