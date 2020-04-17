    jQuery(document).ready(function(){
        jQuery("#checkin").click(function(){
            jQuery("#checkin").pickadate({
              
               formatSubmit: 'mm/dd/yyyy',
                hiddenName: true,
               onOpen: function() {
                console.log('set new date');
              },
              onClose:function() {
                console.log('set close');
              },
               onSet: function(context) {
                  console.log('Just set stuff:', context);
                  myDate = new Date(context.select);
                  jQuery("#checkin").text(myDate.toDateString());
                },
                
            });  
        });

        jQuery("#checkout").click(function(){
            jQuery("#checkout").pickadate({
               formatSubmit: 'mm/dd/yyyy',
                hiddenName: true,
               onOpen: function() {
                // console.log('set new date');
              },
              onClose:function() {
                // console.log('set close');
              },

              onSet: function(context) {
                  console.log('Just set stuff:', context);
                  myDate = new Date(context.select);
                  jQuery("#checkout").text(myDate.toDateString());
                },  
            }); 
            
        });
    }); //this was missing