(function() {

// Localize jQuery variable
var jQuery;

/******** Load jQuery if not present *********/
if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.4.2') {
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type","text/javascript");
    script_tag.setAttribute("src",
        "http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
    if (script_tag.readyState) {
      script_tag.onreadystatechange = function () { // For old versions of IE
          if (this.readyState == 'complete' || this.readyState == 'loaded') {
              scriptLoadHandler();
          }
      };
    } else {
      script_tag.onload = scriptLoadHandler;
    }
    // Try to find the head, otherwise default to the documentElement
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
} else {
    // The jQuery version on the window is the one we want to use
    jQuery = window.jQuery;
    main();
}

/******** Called once jQuery has loaded ******/
function scriptLoadHandler() {
    // Restore $ and window.jQuery to their previous values and store the
    // new jQuery in our local jQuery variable
    jQuery = window.jQuery.noConflict(true);
    // Call our main function
    main(); 
}



/******** Our main function ********/
function main() { 
    jQuery(document).ready(function($) { 
        /******* Load CSS *******/
           
		$('#kookoo_click2call').html("<input type='text' id='kookoo_number' name='kookoo_number'/><input type='button' id='kookoo_btn_click2call' value='Click to call'/><div id='kookoo_click2call_status'></div>");
		
		$('#kookoo_btn_click2call').click(function() {
  /******* Load HTML *******/
  		$('#kookoo_click2call_status').html('Dialing.....');
		var number=$('#kookoo_number').val();
/*******http://www.yourdomain.com/click2call.php ***/
        var jsonp_url = "click2call.php?number="+number+"&callback=?";
        $.getJSON(jsonp_url, function(data) {
          //$('#kookoo_click2call_status').html("This data comes from another server: " + data.html);
         
		  $('#kookoo_click2call_status').html(data.html);
        });
});
		
		
      });
	  
	  
}

})(); // We call our anonymous function immediately
