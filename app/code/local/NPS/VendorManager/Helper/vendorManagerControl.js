jQuery(document).ready(function($) {
    $("#nps_vendor_options_update input").change(function() {
    	$("#nps_vendor_options_update").find("input[name='nps_value_updated']").val("true");
    });
    $("#nps_vendor_options_update textarea").change(function() {
    	$("#nps_vendor_options_update").find("input[name='nps_value_updated']").val("true");
    });
    $("#nps_vendor_options_update select").change(function() {
    	$("#nps_vendor_options_update").find("input[name='nps_value_updated']").val("true");
    });
});