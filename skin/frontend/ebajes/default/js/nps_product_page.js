jQuery(document).ready(function($){

	var wWidth = $( window ).width();
	var wHeight = $( window ).height();
	console.log( "Window Width: "+wWidth );
	console.log( "Window Height: "+wHeight );

	//default qty to 1
	$("#qty").val("1");

	//resize product page elements based on large scale viewport
	if( wWidth > 479 ){

		boxHeight = $(".product-options-bottom").height() + parseInt($(".product-options-bottom").css('padding-top')) + parseInt($(".product-options-bottom").css('padding-bottom')) +3;
		
		$("#product-options-wrapper").css("min-height",boxHeight+"px");

		//allow more views box to extend the width of the page
		$("#product-more-views > ul").css("width",wWidth-100);
	}


});
