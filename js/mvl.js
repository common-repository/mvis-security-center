
jQuery(document).ready(function() {
	jQuery('.password').pstrength();
});

  
jQuery(document).ready(function() {
	jQuery( "#tabs" ).tabs();
	jQuery("table.tablesorter").tablesorter();
});
  
  // modular content: iframe (external site) & ajax (internal site)
  // documentation: http://www.jacklmoore.com/colorbox

jQuery(document).ready(function(){
	jQuery(".iframex").colorbox({iframe:true, width:"700px", height:"80%",fastIframe:false});
	jQuery(".ajax").colorbox();
	jQuery(".alert").colorbox({iframe:false,width:"80%", height:"80%"});
  });
    
// show all in tables

jQuery(document).ready(function(e) {
	jQuery(".show-more").hide();
     
	jQuery(".more").click(function () {
        var t = jQuery(this);
        // toggle hidden div
        t.parent().find('.show-more').toggle('150');
    });
    
	jQuery('.more').click(function() {
        if (jQuery(this).val() === "show all")
        {
        	jQuery(this).val("hide");
        	jQuery(this).attr("title", "hide");
        }
        else
        {
        	jQuery(this).val("show all");
        	jQuery(this).attr("title", "show all");
        }
    });

 });    
