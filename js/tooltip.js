/*
 * Tooltip script 
 * powered by jQuery (http://www.jquery.com)
 * 
 * written by Alen Grakalic (http://cssglobe.com)
 * 
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */
 
this.tooltip = function(){	
	/* CONFIG */		
	xOffset = -7;
	yOffset = -100;	
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result		
	/* END CONFIG */		
	jQuery("a.mvltooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		jQuery("body").append("<p id='mvltooltip'>"+ this.t +"</p>");
		jQuery("#mvltooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		jQuery("#mvltooltip").remove();
    });	
	jQuery("a.mvltooltip").mousemove(function(e){
		jQuery("#mvltooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};

// starting the script on page load
jQuery(document).ready(function(){
	tooltip();
});