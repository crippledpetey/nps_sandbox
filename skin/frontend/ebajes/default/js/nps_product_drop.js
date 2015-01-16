jQuery(document).ready(function($){

	function clearProductGrid(){
		//clear existing clearer elements
		remvClearer();

		//set the size variables
		var windowSize = $(window).width();
		var smlMax = 479;
		var smlMin = 1;
		var medMax = 959;
		var medMin = 480;
		var lrgMax = 1400;
		var lrgMin = 960;
		var xlrgMax = 5000;
		var xlrgMin = 1400;
		
		//set start for counting iterations
		var i = 1;

		//add clear blocks to make sure there is no odd wrapping
		if( windowSize < xlrgMin && windowSize > medMax ){//if page is large
			//cycle through each product item
			$("ul.products-grid").children("li").each(function(){
				//check if element is third
				if( i == 3 ){
					//add clearing block if so
					addClearer( $(this) );
					i=1;
				} else {
					//if not increase count and continue
					i++;
				}
			});
		}else if( windowSize < lrgMin && windowSize > medMin ){
			//cycle through each product item
			$("ul.products-grid").children("li").each(function(){
				//check if element is third
				if( i == 2 ){
					//add clearing block if so
					addClearer( $(this) );
					i=1;
				} else {
					//if not increase count and continue
					i++;
				}
			});
		}
	}

	function addClearer(elem){
		$(elem).after("<div class='clearer'></div>");
	}
	function remvClearer(){
		//test if next element is clearer
		$("ul.products-grid").children("div.clearer").remove();
	}


	//clear the grid on page load
	clearProductGrid();

	///clear the grisd on resize
	$( window ).resize(function() {
	  clearProductGrid();
	});
	
});