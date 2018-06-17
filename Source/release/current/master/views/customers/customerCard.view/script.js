jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload contact info
	jq(document).on("customerinfo.reload", function() {
		jq("#customerInfoViewContainer").trigger("reload");
		jq("#personInfoViewContainer").trigger("reload");
	});
	
	// Reload financial info
	jq(document).on("customerfinances.reload", function() {
		jq("#financesInfoViewContainer").trigger("reload");
	});
});