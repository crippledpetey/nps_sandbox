jQuery(document).ready(function($){

	$("#unassigned-attr-search").change(function(){
		console.log( $(this).val() );
	});

	$("#unassigned-attr-search").keyup(function(){
		var inputVal = $("#unassigned-attr-search").val();

		$("#tree-div2 > ul > div > li").each(function(){
			var str = $(this).find("a > span").text();
			if( str.toLowerCase().search( inputVal.toLowerCase() ) < 0 ){
				$(this).closest("li").addClass("hidden");
			} else {
				console.log(  str );
				$(this).closest("li").removeClass("hidden");
			}
		});
	});
});

