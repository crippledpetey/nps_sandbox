
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

            //$(".header-container > .header-bottom > .nav-container").css({'height':height});
        }
    }

    //standardize menu on page load and resize if screen is large
    standardizeMainNav();
    $(window).resize(function(){
        console.log( "resized window to: " + $(window).width() );
        standardizeMainNav()
    });

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
});