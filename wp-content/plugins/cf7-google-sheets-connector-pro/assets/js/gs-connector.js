jQuery(document).ready(function () {
     
     /**
    * verify the api code
    * @since 1.0
    */
    jQuery(document).on('click', '#save-gs-code', function () {
        jQuery(this).parent().children(".loading-sign").addClass( "loading" );
        var data = {
        action: 'verify_gs_integation',
        code: jQuery('#gs-code').val(),
        security: jQuery('#gs-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function (response ) {
         if ( response == -1 ) {
            return false; // Invalid nonce
         }
         
         if( ! response.success ) { 
           jQuery( ".loading-sign" ).removeClass( "loading" );
           jQuery( "#gs-validation-message" ).empty();
           jQuery("<span class='error-message'>Invalid Access code entered.</span>").appendTo('#gs-validation-message');
         } else {
           jQuery( ".loading-sign" ).removeClass( "loading" );
           jQuery( "#gs-validation-message" ).empty();
           jQuery("<span class='gs-valid-message'>Your Google Access Code is Authorized and Saved.</span> <br/><br/><span class='wp-valid-notice'> Note: If you are getting any errors or not showing sheet in dropdown, then make sure to check the debug log. To contact us for any issues do send us your debug log.</span>").appendTo('#gs-validation-message');
		   setTimeout(function () { location.reload(); }, 7000);
         }
      });
      
    }); 
    
	 /**
    * deactivate the api code
    * @since 1.0
    */
    jQuery(document).on('click', '#deactivate-log', function () {
        jQuery(".loading-sign-deactive").addClass( "loading" );
		var txt;
		var r = confirm("Are You sure you want to deactivate Google Integration ?");
		if (r == true) {
			var data = {
				action: 'deactivate_gs_integation',
				security: jQuery('#gs-ajax-nonce').val()
			};
			jQuery.post(ajaxurl, data, function (response ) {
				if ( response == -1 ) {
					return false; // Invalid nonce
				}
			 
				if( ! response.success ) {
					alert('Error while deactivation');
					jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
					jQuery( "#deactivate-message" ).empty();
					
				} else {
					jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
					jQuery( "#deactivate-message" ).empty();
					jQuery("<span class='gs-valid-message'>Your account is removed. Reauthenticate again to integrate Contact Form with Google Sheet.</span>").appendTo('#deactivate-message');
		   		    setTimeout(function () { location.reload(); }, 5000);
				}
			});
		} else {
			jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
		}
        
      
      
    }); 
	
   /**
    * Clear debug
    */
   jQuery(document).on('click', '.debug-clear', function () {
      jQuery(".clear-loading-sign").addClass("loading");
      var data = {
         action: 'gs_clear_log',
         security: jQuery('#gs-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         
         if (response.success) {
            jQuery(".clear-loading-sign").removeClass("loading");
            jQuery("#gs-validation-message").empty();
            jQuery("<span class='gs-valid-message'>Logs are cleared.</span>").appendTo('#gs-validation-message');
         }
      });
   });
   
   /**
    * Sync with google account to fetch latest sheet and tab name list.
    */
   jQuery(document).on('click', '#gs-sync', function () {
      jQuery(this).parent().children(".loading-sign").addClass( "loading" );
      var integration = jQuery(this).data("init");
      var data = {
         action: 'sync_google_account',
         isajax: 'yes',
         isinit : integration,
         security: jQuery('#gs-ajax-nonce').val()
      };
      
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         
         if ( response.data.success === "yes" ) {
            jQuery(".loading-sign").removeClass( "loading" );
            jQuery( "#gs-validation-message" ).empty();
            jQuery("<span class='gs-valid-message'>Fetched all sheet details.</span>").appendTo('#gs-validation-message'); 
         } else {
            jQuery(this).parent().children(".loading-sign").removeClass( "loading" );
            location.reload(); // simply reload the page
         }
      });
   });
    
   /** 
    * Get tab name list 
    */
   jQuery(document).on("change", "#gs-sheet-name", function () {
      var sheetname = jQuery(this).val();
      jQuery(".loading-sign").addClass( "loading" );
      var data = {
         action: 'get_tab_list',
         sheetname: sheetname,
         security: jQuery('#gs-ajax-nonce').val()
      };
      
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         if ( response.success ) {
            jQuery('#gs-sheet-tab-name').html( html_decode(response.data) );
            jQuery( ".loading-sign" ).removeClass( "loading" );
         }
      });      
   });
   
   // TODO : Combine into one
   jQuery(document).on("change", "#gs-sheet-tab-name", function () {
      var tabname = jQuery(this).val();
      var sheetname = jQuery("#gs-sheet-name").val();
      jQuery(this).parent().children(".loading-sign").addClass( "loading" );
      var data = {
         action: 'get_sheet_id',
         tabname: tabname,
         sheetname: sheetname,
         security: jQuery('#gs-ajax-nonce').val()
      };
      
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         
         if ( response.success ) {
            jQuery('#sheet-url').html( html_decode(response.data) );
            jQuery( ".loading-sign" ).removeClass( "loading" );
         }
      });      
   });
      
   function html_decode(input){
		var doc = new DOMParser().parseFromString(input, "text/html");
		return doc.documentElement.textContent;
	}
	
	// single checkbox with checked value get
	var count = jQuery("#drag").find("#total-count").val();
	jQuery("input[id='gs-cstm-chk']:checkbox").on("click", function(){
    if(jQuery(this).is(":checked")) {
        jQuery(this).closest("tr").find("td:eq(2)").each(function(){
			var txt = jQuery(this).closest("tr").find('td').find("input[type='text']").val();
			if(txt == ""){
				var txt = jQuery(this).closest("tr").find('td').find("#custom-value").val();
			}
			jQuery("#drag").append("<div class='drag-item'><li class='draggable-item'>" +txt+ "<input type='hidden' value='"+txt+"' id='gs-drag-drop' name='gs-drag-index["+count+"]' > </li></div>");
		});
	} else {
		var getData = jQuery(this).closest("tr").find('td').find("#gs-custom-header").val();
		if(getData == ""){
			var getData = jQuery(this).closest("tr").find('td').find("#custom-value").val();
		}
		jQuery(".draggable-item").find("input[type='hidden']").each(function(){
			var getVal = jQuery(this).closest(".draggable-item").find('#gs-drag-drop').val();
			if(getData == getVal){
				var get = jQuery(this).closest(".drag-item").empty();
			}      
		});
    }
	count++;
	});
	
	jQuery( init );
	function init() {
		jQuery( ".droppable-area1" ).sortable({
		  connectWith: ".connected-sortable",
		  stack: '.connected-sortable ul',
		  update: function() {
			var count = 0;
			 jQuery.each(jQuery(".draggable-item input[id='gs-drag-drop']"), function(){ 
				  jQuery(this).attr("name", "gs-drag-index"  +'['+count+']'); 
				  count++;
			});
			}
		}).disableSelection();
	}
	
	// add input field for custom name
	jQuery(document).on("click", "#manual-name", function () {
		var sheetname = jQuery(this).val();
		jQuery(this).parent().children(".loading-sign").addClass( "loading" );
		 if(jQuery(this).is(":checked")) {
				jQuery(".sheet-details").addClass('hide');
				jQuery(".manual-fields").removeClass('hide');
		 } else {
				jQuery(".sheet-details").removeClass('hide');
				jQuery(".manual-fields").addClass('hide');
		 }
   });


});




