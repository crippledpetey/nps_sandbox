jQuery(document).ready(function($){

	//OUTPUT THE PAGE AND DOCUMENT INFORMATION TO THE CONSOLE
	//console.log( $( window ) );
	//console.log( $(document) );

	//check for preselected finish
	$(".preselected-finish .inventory-controller select option").each(function(){
		if ($(this).attr('value') == $(".preselected-finish").data('finishId') ) {
	        $(this).attr("selected",true);
	    } else {
	        $(this).removeAttr("selected");
	    }
	});

	/* ================== FIX PRODUCT PAGE CONTENT BOXES TO NORMALIZE HEIGHT AND / OR WIDTH ================== */
	function fixPrdContentBoxDimensions(windowWidth) {
		
		//VERIFY THAT THE WINDOW WIDTH IS NOT A PORTRAIT MOBILE DEVICE
		if( windowWidth > 479 ){

			//COLLECT THE PRODUCT ADD TO CART BOX HEIGHT
			boxHeight = $(".product-options-bottom").outerHeight()+31;
			//boxHeight = boxHeight + parseInt($(".product-options-bottom").css('padding-top')) + parseInt($(".product-options-bottom").css('padding-bottom')) + 3;
			console.log( boxHeight);

			//MAKE SURE THAT IT IS LARGER THAN THE CURRENT SIZE OF THE OPTIONS WRAPPER
			if( boxHeight > $("#product-options-wrapper").height() ){
				$("#product-options-wrapper").css({
					"height":(boxHeight-1)+"px",
				});	
			}else{ //IF THE ADD TO CART CONTAINER IS NOT LARGER THAN TGE PRODUCT OPTIONS
				$("#product-options-wrapper").css({
					"min-height":boxHeight+"px",
					"height":"auto",
				});	
			} 
			
			//EXTEND THE "MORE VIEWS" CONTAINER TO THE END OF THE PAGE
			//$("#product-more-views > ul").css("width",windowWidth-100);
		}
	}
	function triggerPopUp(popup){
		$("#page-overlay-dark").removeClass("hidden");
		if( $(popup).data("origSrc") !== "" && $(popup).data("origSrc") !== undefined ){
			var origSrc = $(popup).data("origSrc");
			$(popup).children("iframe").attr("src",origSrc);	
		} else {
			var origSrc = $(popup).find('iframe').prop("src");
			$(popup).attr("data-orig-src",origSrc);
		}
		
		
		$("#page-overlay-dark").animate(400,function(){
			$(this).css({
				'height' 	: $(window).height(),
				'width' 	: '100%',
				'display'	: 'block', 
			});
		},function(){
			$(popup).removeClass("hidden");			
			$(popup).css({
				'position':'fixed',
				'z-index' : 10000,
				'top':'10%',
				'left' : ($(window).width() - $(popup).outerWidth())/2,
			});
		});
	}

	$(".video-pop").each(function(){
		var height = $(window).height()*.8;
		var width = $(window).width()*.8;
		$(this).children("iframe").attr({
			"height":height,
			"width":width,
		});
	});
	$("#page-overlay-dark").click(function(){
		$(".video-pop").each(function(){
			$(this).addClass("hidden").attr("style","");
			$(this).children("iframe").attr("src","");
			$(".trigger-video-pop").each(function(){
				$(this).removeClass("hidden");
			})
		});
	});

	//GET PAGE WIDTH AND HEIGHT FOR USE
	var wWidth = $( window ).width();
	var wHeight = $( window ).height();

	//CONTENT BOX DIMENSION FIX AND NORMALIZE HEIGHT
	//fixPrdContentBoxDimensions(wWidth);

	//CHANGE PRODUCT QUANTITY TO 1 IF CURRENT QUANTITY IS 0
	if( $("#qty").val() == 0 ){
		$("#qty").val("1");
	}

	//ON CHANGE OF INVENTORY CONTROLLER OPTION THIS WILL TRIGGER THE AVAILABILITY NOTICES AND THE IMAGE FLASH THAT OCCURS VIA CSS CLASSES
	$(".inventory-controller select").change(function(){
		var str = $(this).find(":selected").text();
		var current = $("#prd-page-availability").html();
		$(".include-loader-bkg").addClass("overridden");
		if( str.search("OUT OF STOCK") > 0 || str.search("Out of stock") > 0 ){
			//remove the out of stock flash class
			$(".product-img-box .product-image span").removeClass("scream-out-in-stock");
			$(".product-img-box .product-image span").removeClass("scream-out-of-stock");
			
			//add the out of stock flash class
			$(".product-img-box .product-image span").addClass("scream-out-of-stock");
			
			//change the text value to out of stock if it reads in stock
			if( current.toLowerCase() == "in stock"){
				$("#prd-page-availability").parent("p.availability").removeClass("in-stock");
				$("#prd-page-availability").parent("p.availability").addClass("out-of-stock");
				$("#prd-page-availability").empty();
				$("#prd-page-availability").append("OUT OF STOCK");
			}
		} else {
			//remove out of stock flash class
			$(".product-img-box .product-image span").removeClass("scream-out-in-stock");
			$(".product-img-box .product-image span").removeClass("scream-out-of-stock");

			//add in stock flash class
			$(".product-img-box .product-image span").addClass("scream-in-stock");

			//change the text value to in stock if it reads out of stock
			if( current.toLowerCase() == "out of stock"){
				$("#prd-page-availability").parent("p.availability").addClass("in-stock");
				$("#prd-page-availability").parent("p.availability").removeClass("out-of-stock");
				$("#prd-page-availability").empty();
				$("#prd-page-availability").append("IN STOCK");
			}
		}
	});

	//ON A WINDOW RESOLUTION CHANGE
	$(window).resize(function(){

		//RECOLLECT THE WINDOW DIMENSIONS
		var wWidth = $( window ).width();
		var wHeight = $( window ).height();
		
		//RE-NORMALIZE PRODUCT PAGE CONTENT BLOCKS
		//fixPrdContentBoxDimensions(wWidth);
	});

	$(".product-image-zoom > img").width("278");
	$(".product-image-zoom > img").height("278");

	$(".attachment-icon a").each(function(){
		$(this).mouseover(function() { 
			var src = $(this).children("img").attr("src").match(/[^\.]+/) + "_hover.png";
            $(this).children("img").attr("src", src);
        })
        $(this).mouseout(function() {
            $(this).children("img").animate(100,function(){
            	var src = $(this).attr("src").replace("_hover.png", ".png");
            	$(this).attr("src", src);
            });
        });
	});
	$(".trigger-video-pop").click(function(){
		$(this).addClass("hidden");
		var popUpID = $(this).data("triggerIdKey")+"-video-pop";
		triggerPopUp( $("#"+popUpID) );
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27) { 
			console.log( $(".video-stream") );
			$(".video-stream").stopVideo();
		}
	})

});

