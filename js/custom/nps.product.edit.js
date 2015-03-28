jQuery(document).ready(function($){
	$("td.value select.multiselect option:selected").each(function(){
		$(this).addClass("helloClassname");
		$(this).prependTo($(this).parent("select"));
	});
	console.log("ghello there");

	$("input#name").keyup(function(){
		console.log( $(this).val().length );
		if($(this).val().length >= 134 ){
			$(this).css({"box-shadow":"0 0 10px red"});
		} else {
			$(this).css({"box-shadow":"none"});
		}
	});
});

