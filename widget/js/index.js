jQuery(document).ready(function() {
  console.log("index.js");
  var animating = false,
      submitPhase1 = 1100,
      submitPhase2 = 400,
      logoutPhase1 = 800,
      $login = jQuery(".login"),
      $app = jQuery(".hotel-widget-app");
  
  function ripple(elem, e) {
    jQuery(".ripple").remove();
    var elTop = elem.offset().top,
        elLeft = elem.offset().left,
        x = e.pageX - elLeft,
        y = e.pageY - elTop;
    var $ripple = jQuery("<div class='ripple'></div>");
    $ripple.css({top: y, left: x});
    elem.append($ripple);
  };
  
  jQuery(document).on("click", ".login__submit", function(e) {
    console.log("submit");
    jQuery.ajax({
      url: '/wp-admin/admin-ajax.php',
      data: {
                action : 'my_ajax_handler',
                xyz: '777',
                'check-in': jQuery("[name='checkin']").val(),
                'check-out': jQuery("[name='checkout']").val(),
                adult: jQuery('#adult').val(),
                children: jQuery('#children').val(),
                'room-number': jQuery('#room-number').val(),
            },
      type: 'Post',
      
     
      
      
      beforeSend: function() {
        if (animating) return;
        animating = true;
        var that = jQuery('.login__submit');
        console.log(that);
        ripple(jQuery(that), e);
        jQuery(that).addClass("processing");
   
        
      },
      success: function(data) {
        console.log("success");
        console.log(data);
        jQuery('#hotel-widget-app-room-results').html(data);
        // var cars = ["Saab", "Volvo", "BMW"];
        setTimeout(function() {
          jQuery('.login__submit').addClass("success");
          // jQuery('.hotel-widget-app__hello').text(data);
          // hotel-widget-app__hello
          setTimeout(function() {
            // console.log($app);
            jQuery('.hotel-widget-app').show();
            jQuery('.hotel-widget-app').css("top");
            jQuery('.hotel-widget-app').addClass("active");
          }, submitPhase2 - 70);
          setTimeout(function() {
            jQuery('.login').hide();
            jQuery('.login').addClass("inactive");
            animating = false;
            jQuery('.login__submit').removeClass("success processing");
          }, submitPhase2);
        }, submitPhase1);


      },
      error: function(xhr) { // if error occured
         var that = jQuery('.login__submit');
        console.log(that);
        ripple(jQuery(that), e);
        jQuery(that).addClass("processing");
      },
      complete: function(e) {
         // console.log(e.responseText);
      }
      
    });
    
  });
  
  jQuery(document).on("click", ".back", function(e) {
    if (animating) return;
    jQuery(".ripple").remove();
    animating = true;
    var that = this;
    jQuery(that).addClass("clicked");
    jQuery(that).removeClass("back");
    // console.log("that");
    // console.log(that);

    setTimeout(function() {
      jQuery('.hotel-widget-app').removeClass("active");
      jQuery('.login').show();
      jQuery('.login').css("top");
      jQuery('.login').removeClass("inactive");
    }, logoutPhase1 - 120);
    setTimeout(function() {
      jQuery('.hotel-widget-app').hide();
      animating = false;
      console.log('that');
      console.log(that);
      jQuery(that).removeClass("clicked");
    }, logoutPhase1);
  });

// second step
  jQuery(document).on("click", ".hotel-widget-app__meeting", function(e) {
    console.log("submit");
    jQuery.ajax({
      url: '/wp-admin/admin-jajax.php',
      data: {
                action : 'my_ajax_handler',
                xyz: '777',
                'check-in': jQuery("[name='checkin']").val(),
                'check-out': jQuery("[name='checkout']").val(),
                adult: jQuery('#adult').val(),
                children: jQuery('#children').val(),
                'room-number': jQuery('#room-number').val(),
            },
      type: 'Post',
      
     
      
      
      beforeSend: function() {
        if (animating) return;
        animating = true;
        var that = jQuery('.hotel-widget-app__logout');
        console.log(that);
        ripple(jQuery(that), e);
        jQuery('.hotel-widget-app__logout svg').remove();
        jQuery(that).addClass("processing");
   
        
      },
      success: function(data) {
        console.log("success");
        console.log(data);
        jQuery('#hotel-widget-app-room-results').html(data);
        // var cars = ["Saab", "Volvo", "BMW"];
        setTimeout(function() {
          jQuery('.login__submit').addClass("success");
          // jQuery('.hotel-widget-app__hello').text(data);
          // hotel-widget-app__hello
          setTimeout(function() {
            // console.log($app);
            jQuery('.hotel-widget-app').show();
            jQuery('.hotel-widget-app').css("top");
            jQuery('.hotel-widget-app').addClass("active");
          }, submitPhase2 - 70);
          setTimeout(function() {
            jQuery('.login').hide();
            jQuery('.login').addClass("inactive");
            animating = false;
            jQuery('.login__submit').removeClass("success processing");
          }, submitPhase2);
        }, submitPhase1);


      },
      error: function(xhr) { // if error occured
         var that = jQuery('.login__submit');
        console.log(that);
        ripple(jQuery(that), e);
        jQuery(that).addClass("processing");
      },
      complete: function(e) {
         // console.log(e.responseText);
      }
      
    });
    
  });

  
});