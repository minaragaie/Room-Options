(function($) {
	$(document).ready(function() {
		
		
		$(".cal-edit").click(function(e) {
			$(this).parents(".row-wrap").find(".content-row").css("display","none");
			$(this).parents(".row-wrap").find(".edit-row").css("display","table-cell");
		});
		$(".cal_edit_cancel").click(function(e) {
			$(this).parents(".row-wrap").find(".content-row").css("display","table-cell");
			$(this).parents(".row-wrap").find(".edit-row").css("display","none");
		});
		
		
		var ajaxurl = bookingOption.ajaxurl; 
		$("#select-calendar-reservation").change(function(){
			$.post(ajaxurl, {action: 'hotel_show_reservations', cal_id: $(this).val()}, function(data){
				$("#show-reservation-table").html(data);
			});
			$.cookie("calendar",$(this).val());
		});
		
		if ( $.cookie("calendar") != undefined && $("#select-calendar-reservation").length ) {
			$("#select-calendar-reservation").val($.cookie("calendar"));
			$.post(ajaxurl, {action: 'hotel_show_reservations', cal_id: $.cookie("calendar")}, function(data){
				$("#show-reservation-table").html(data);
			});
		}
		
		$("#select-calendar-price").change(function(){
			$.post(ajaxurl, {action: 'hotel_show_calendar', cal_id: $(this).val()}, function(data){
				$("#show-price-calendar").html(data);
			});
			$.cookie("calendar",$(this).val());
		});
				
		if ( $.cookie("calendar") != undefined && $("#select-calendar-price").length ) {
			$("#select-calendar-price").val($.cookie("calendar"));
			
			if ( $.cookie("calendar-month") != undefined && $.cookie("calendar-year") != undefined ) {
				$("#calendar-month").val($.cookie("calendar-month"));
				$("#calendar-year").val($.cookie("calendar-year"));
			
				$.post(ajaxurl, {action: 'hotel_show_calendar', cal_id: $.cookie("calendar"), calendar_month: $.cookie("calendar-month"), calendar_year: $.cookie("calendar-year")}, 
				function(data){
					$("#show-price-calendar").html(data);
				});
			} else if ( $.cookie("calendar-month") != undefined ) {
				$.post(ajaxurl, {action: 'hotel_show_calendar', cal_id: $.cookie("calendar"), calendar_month: $.cookie("calendar-month")}, 
				function(data){
					$("#show-price-calendar").html(data);
				});
			} else if ( $.cookie("calendar-year") != undefined ) {
				$.post(ajaxurl, {action: 'hotel_show_calendar', cal_id: $.cookie("calendar"), calendar_year: $.cookie("calendar-year")}, 
				function(data){
					$("#show-price-calendar").html(data);
				});
			} else { 
				$.post(ajaxurl, {action: 'hotel_show_calendar', cal_id: $.cookie("calendar")}, function(data){
					$("#show-price-calendar").html(data);
				});
						
			}
		}
		
		
		
		$(document).on("change", "#calendar-month", function(){
			$.post(ajaxurl, { action: 'hotel_show_calendar', cal_id: $("#select-calendar-price").val(), calendar_month: $("#calendar-month").val(), calendar_year: $("#calendar-year").val() }, function(data){
				$("#show-price-calendar").html(data);
			});
			$.cookie("calendar-month",$(this).val());
			location.reload();
		});
		
		$(document).on("change", "#calendar-year", function(){
			$.post(ajaxurl, { action: 'hotel_show_calendar', cal_id: $("#select-calendar-price").val(), calendar_month: $("#calendar-month").val(), calendar_year: $("#calendar-year").val() }, function(data){
				$("#show-price-calendar").html(data);
			});
			$.cookie("calendar-year",$(this).val());
			location.reload();
		});
		
		
		
		$(document).on("submit", "#reservation-add", function(){
			$("#reservation-add").append("<input type='hidden' name='cal_id' value='"+$("#select-calendar-reservation").val()+"'>");
		});
		
		var dateFormat = "";
		var defaultDate = "";
		
		if ( bookingOption.dateformat == "european" ) {
			dateFormat = "dd-mm-yy";
			defaultDate = "01-" + $.cookie("calendar-month") + "-" + $.cookie("calendar-year");
		} else if ( bookingOption.dateformat == "american" ) {
			dateFormat = "mm/dd/yy";
			defaultDate = $.cookie("calendar-month") + "/01/" + $.cookie("calendar-year");
		}
		
		$('body').on("focus","#create-reservation-table .check-in-date, #create-reservation-table .check-out-date",function(){
			$(this).datepicker({ dateFormat: dateFormat });
		});		
		

		
		$('body').on("focus",".set-data .check-in-date, .set-data .check-out-date", function() {
			$(this).datepicker({ 
				dateFormat: dateFormat,
				defaultDate: defaultDate
			});
		});
		
		
		//Check calendars form fields
		
		$("#add-calendar-form").submit(function(){
			var calName = $("#add-calendar-form #cal-name");
		
			if ( calName.val() == "" ) {
				calName.addClass('highlight');
				return false;
			} else {
				calName.removeClass('highlight');
			}

		});
		
		//Check reservation form fields
		
		$('body').on("submit", "#reservation-add", function(){
			var checkIn = $("#reservation-add .check-in-date");
			var checkOut = $("#reservation-add .check-out-date");
			var roomNumber = $("#reservation-add #room-number");
			var email = $("#reservation-add #email");
			var adults = $("#reservation-add #adults");
			var children = $("#reservation-add #children");
			var cardholder = $("#reservation-add #cardholder");
			var cardnumber = $("#reservation-add #cardnumber");
			
			var name = $("#reservation-add #name");
			var surname = $("#reservation-add #surname");
			
			if ( checkIn.val() == "" ) {
				checkIn.addClass('highlight');
				return false;
			} else {
				checkIn.removeClass('highlight');
			} if ( checkOut.val() == "" ) {
				checkOut.addClass('highlight');
				return false;
			} else {
				checkOut.removeClass('highlight');
			} if ( roomNumber.val() == "" ) {
				roomNumber.addClass('highlight');
				return false;
			} else {
				roomNumber.removeClass('highlight');
			} if ( email.val() == "" ) {
				email.addClass('highlight');
				return false;
			} else {
				email.removeClass('highlight');
			} if ( adults.val() == "" ) {
				adults.addClass('highlight');
				return false;
			} else {
				adults.removeClass('highlight');
			} if ( children.val() == "" ) {
				children.addClass('highlight');
				return false;
			} else {
				children.removeClass('highlight');
			} if ( name.val() == "" ) {
				name.addClass('highlight');
				return false;
			} else {
				name.removeClass('highlight');
			} if ( surname.val() == "" ) {
				surname.addClass('highlight');
				return false;
			} else {
				surname.removeClass('highlight');
			} if ( cardholder.val() == "" ) {
				cardholder.addClass('highlight');
				return false;
			} else {
				cardholder.removeClass('highlight');
			} if ( cardnumber.val() == "" ) {
				cardnumber.addClass('highlight');
				return false;
			} else {
				cardnumber.removeClass('highlight');
			}
			
		});
		
		$('body').on("submit", "#set-edit-price", function(){
			var checkIn = $("#set-edit-price .check-in-date");
			var checkOut = $("#set-edit-price .check-out-date");
			var roomNumber = $("#set-edit-price #room-number");
			var priceRoom = $("#set-edit-price #price-per-room");
		
			if ( checkIn.val() == "" ) {
				checkIn.addClass('highlight');
				return false;
			} else {
				checkIn.removeClass('highlight');
			}
			if ( checkOut.val() == "" ) {
				checkOut.addClass('highlight');
				return false;
			} else {
				checkOut.removeClass('highlight');
			}
			if ( roomNumber.val() == "" ) {
				roomNumber.addClass('highlight');
				return false;
			} else {
				roomNumber.removeClass('highlight');
			}
			if ( priceRoom.val() == "" ) {
				priceRoom.addClass('highlight');
				return false;
			} else {
				priceRoom.removeClass('highlight');
			}
			
		});
		
		
	});
})(jQuery);