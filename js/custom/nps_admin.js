jQuery(document).ready(function($){
	var searchUndefinedAttributeInput = 'Find Attribute: <input type="text" id="unassigned-attr-search"><span style="margin-left: 25px;"><button id="clear-search-undefiined-attributes" title="Clear Search Input" type="button" class="scalable delete" style=""><span><span><span>Clear Search Input</span></span></span></button>';
	$("#tree-div2").prepend( searchUndefinedAttributeInput );

	
	$("#unassigned-attr-search").keyup(function(){
		var inputVal = $("#unassigned-attr-search").val();

		$("#tree-div2 > ul > div > li").each(function(){
			var str = $(this).find("a > span").text();
			if( str.toLowerCase().search( inputVal.toLowerCase() ) < 0 ){
				$(this).closest("li").addClass("hidden");
			} else {
				$(this).closest("li").removeClass("hidden");
			}
		});
	});

	$("#clear-search-undefiined-attributes").click(function(){
		$("#unassigned-attr-search").val("");
		$("#tree-div2 > ul > div > li").each(function(){
			$(this).removeClass("hidden");
		});
	});
});
