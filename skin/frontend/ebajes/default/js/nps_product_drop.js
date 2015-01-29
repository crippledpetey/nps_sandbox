jQuery(document).ready(function($){

	function clearProductGrid(){
		//clear existing clearer elements
		remvClearer();

		//get number of items to show
		var availTiles = Math.floor(  $("ul.products-grid").width() / 180 );
		
		//set start for counting iterations
		var i = 1;

		//cycle through each product item
		$("ul.products-grid").children("li").each(function(){
			//check if element is third
			if( i == availTiles ){
				//add clearing block if so
				addClearer( $(this) );
				i=1;
			} else {
				//if not increase count and continue
				i++;
			}
		});
	}

	function addClearer(elem){
		$(elem).after("<div class='clearer'></div>");
	}
	function remvClearer(){
		//test if next element is clearer
		$("ul.products-grid").children("div.clearer").remove();
	}

	function normalizeDropContainers(){
		if( $(window).width() > 740 ){
			var mainWidth = $(window).width() - 430;
			$(".col-wrapper > .col-main").css("width",mainWidth);
			clearProductGrid();
		} else {
			$(".col-wrapper > .col-main").css("width","");
		}
	}


	//clear the grid on page load
	clearProductGrid();

	//normalize the grid container
	normalizeDropContainers();

	///re-run normalize on widow rezise
	$( window ).resize(function() {
	  clearProductGrid();
	  normalizeDropContainers();
	});
	
});