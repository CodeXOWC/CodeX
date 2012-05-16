
$(function(){

    var config = {    
         sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)    
         interval: 0,  // number = milliseconds for onMouseOver polling interval    
         over: doOpen,   // function = onMouseOver callback (REQUIRED)    
         timeout: 100,   // number = milliseconds delay before onMouseOut    
         out: doClose    // function = onMouseOut callback (REQUIRED)    
    };
    
    function doOpen() {
        $(this).addClass("hover");
        $('ul:first',this).css('display','none');
    }
 
    function doClose() {
        $(this).removeClass("hover");
        $('ul:first',this).css('display','none');
    }

    $("ul.main_nav li").hoverIntent(config);
    
    //$("ul.main_nav li ul li:has(ul)").find("a:first").append(" &raquo; ");
		
	$('ul.main_nav a:first').css('padding-left','0');	
		
});