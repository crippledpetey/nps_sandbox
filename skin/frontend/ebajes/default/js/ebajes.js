
function toggleMobileMenu(){
        console.log("triggered");        
        var hidden_style = {'-ms-transform':'rotate(0deg)','-webkit-transform':'rotate(0deg)','transform':'rotate(0deg)'};
        var displayed_style = {'-ms-transform':'rotate(180deg)','-webkit-transform':'rotate(180deg)','transform':'rotate(180deg)'};
        if( jQuery(window).width() < 768 ){
            //check if down
            if( jQuery(".header-container > .header-bottom > .nav-container").hasClass("displayed") ){
                jQuery(".header-container > .header-bottom > .nav-container").slideUp(400,function(){
                    jQuery(".header-container > .header-bottom > .nav-container").removeClass("displayed");
                    //jQuery("#mobile-menu-toggle").css( hidden_style );
                });
            } else {
                jQuery(".header-container > .header-bottom > .nav-container").slideDown(400,function(){
                    jQuery(".header-container > .header-bottom > .nav-container").addClass("displayed");
                    //jQuery("#mobile-menu-toggle").css( displayed_style );
                });
            }
        }
    }


(function($) {	
    function BestsellerSlideshow() {
        $('#slideshow_bestseller').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 196,
            minItems: 4,
            controlNav: false,
            directionNav: true,
            maxItems: 4,
            start: function(slider){
              $('body').removeClass('loading');
            }
      });
    }
	
    $(window).bind('load', function() {
    	BestsellerSlideshow();
    });
})(jQuery);
   

jQuery(document).ready(function($){
   
    function standardizeMainNav(){
        if( $(window).width() > 768 ){
            var navfullWidth = $("#nav").innerWidth();
            var navMenuItems = $("#nav > li").length;
            //console.log( 'width'+navfullWidth );
            //console.log( 'items:'+navMenuItems );
            $("#nav > li").each(function(){
                $(this).css("width", navfullWidth / navMenuItems );
            });
        } else {
            $("#nav > li").each(function(){
                $(this).css("width", "100%" );
            });
        }
    }

    //mobile settings / css
    function setMobile(width){
        if( width < 768 ){

            //get menu height
            var height = 0;
            $(".header-container > .header-bottom > .nav-container > ul").children('li').each(function(){
                height += $(this).height();
            });
        }
    }

    //hide the page overlay and message boxes
    function hideMessageOverlay(){
        //if overlay is not hidden
        if( !$("#page-overlay-dark").hasClass("hidden") ){

            //add the class to fade it out
            $("#page-overlay-dark").addClass("fade-out");
            $("ul.messages").addClass("fade-out");

            //after half a second add the hidden class
            setTimeout(function() {
                $("#page-overlay-dark").addClass("hidden");
                $("ul.messages").addClass("hidden");
            }, 1000);
        }
    }


    //set mobile 
    setMobile( $(window).width() );

    //instantiate fancybox
    $(".fancybox").fancybox(); 

    //clear search
    $("#clear-search").click(function(){
        if( $("#search").val() !== "Search entire store here..." ){
            $("#search").val('');
        }
    });

    //show page overlay if there is a page message
    if ($("ul.messages").length > 0){
      $("#page-overlay-dark").removeClass("hidden");
    }
    //on click of page overlay dark hide the overlay and the messages
    $("#page-overlay-dark").click(function(){
        hideMessageOverlay();   
    });

    //hide the overlay on esc push
    $(document).keyup(function(e) {
        //if escape key is pressed
        if (e.keyCode == 27) { 
            hideMessageOverlay(); 
        }  
    });
});