jQuery(document).ready(function($){

	function checkNameLength(){
		var manuLen = $("#manufacturer option:selected").text().length;
		var skuLen = $("#sku").val().length;
		var nameLen = $("input#name").val().length;
		
		if( ( manuLen + skuLen + nameLen ) >= 134 ){
			$("input#name").css({"box-shadow":"0 0 10px red"});
		} else {
			$("input#name").css({"box-shadow":"none"});
		}
	}
	$("td.value select.multiselect option:selected").each(function(){
		$(this).addClass("helloClassname");
		$(this).prependTo($(this).parent("select"));
	});

	//make sure product name doesn't run into the price on product drop pages
	checkNameLength();
	$("input#name").keyup(function(){
		checkNameLength();
	});
});

