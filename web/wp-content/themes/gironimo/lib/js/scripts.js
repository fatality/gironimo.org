/*
 * file: scripts.js
 * This file should contain any js scripts you want to add to the site. Instead 
 * of calling it in the header or throwing it inside wp-head() this file will be 
 * called automatically in the footer so as not to slow the page load.
 *
 * @package WordPress
 * @author Marc Rochow
 * @version 1.0
 */

var gironimo = {};

// Modernizr.load loading the right scripts only if you need them
Modernizr.load([
    {
        test : Modernizr.borderradius,
        nope : ['libs/selectivizr-min.js']
    }
]);

// as the page loads, call these scripts
jQuery(document).ready(function($) {
    gironimo.responsive_navigation.init();
    jQuery("nav select").change(function() {
        window.location = jQuery(this).find("option:selected").val();
    });
});

// HTML5 Fallbacks for older browsers
jQuery(function($) {
    if (!Modernizr.input.placeholder)
    {
        $(this).find('[placeholder]').each(function() {
            $(this).val( $(this).attr('placeholder') );
        });
        
        // focus and blur of placeholders
        $('[placeholder]').focus(function() {
            if ($(this).val() == $(this).attr('placeholder'))
            {
                $(this).val('');
                $(this).removeClass('placeholder');
            }
        }).blur(function() {
            if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))
            {
                $(this).val($(this).attr('placeholder'));
                $(this).addClass('placeholder');
            }
        });
        
        // remove placeholders on submit
        $('[placeholder]').closest('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                if ($(this).val() == $(this).attr('placeholder'))
                {
                    $(this).val('');
                }
            });
        });
    }
});

gironimo.responsive_navigation = (function($) {
    function init()
    {
        $("<select />").appendTo("nav#main-nav");
        
        $("<option />", {
            "selected": "selected",
            "value": "",
            "text": "Navigation"
        }).appendTo("nav select");
        
        $("nav#main-nav a").each(function() {
            var el = $(this);
            $("<option />", {
                "value": el.attr("href"),
                "text": el.text()
            }).appendTo("nav select");
        });
    }
    return {
        init: init
    }
})(jQuery);

// iOS orientation zoom bug fix
(function(w) {
	// This fix addresses an iOS bug, so return early if the UA claims it's something else.
	if (!(/iPhone|iPad|iPod/.test( navigator.platform ) && navigator.userAgent.indexOf("AppleWebKit") > -1 )) return;
	
	var doc = w.document;
	
	if(!doc.querySelector) return;
	
	var meta = doc.querySelector("meta[name=viewport]"),
	    initialContent = meta && meta.getAttribute("content"),
	    disabledZoom = initialContent + ",maximum-scale=1",
	    enabledZoom = initialContent + ",maximum-scale=10",
	    enabled = true,
	    x, y, z, aig;
    
    if (!meta) return;
    
    function restoreZoom()
    {
        meta.setAttribute("content", enabledZoom);
        enabled = true;
    }
    
    function disableZoom()
    {
        meta.setAttribute("content", disabledZoom);
        enabled = false;
    }
    
    function checkTilt(e)
    {
		aig = e.accelerationIncludingGravity;
		x = Math.abs(aig.x);
		y = Math.abs(aig.y);
		z = Math.abs(aig.z);

		// If portrait orientation and in one of the danger zones
        if (!w.orientation && (x > 7 || ( ( z > 6 && y < 8 || z < 8 && y > 6 ) && x > 5)))
        {
			if (enabled) disableZoom();    	
        }
		else if (!enabled)
		{
			restoreZoom();
        }
    }
    
	w.addEventListener("orientationchange", restoreZoom, false);
	w.addEventListener("devicemotion", checkTilt, false);

})(this);
