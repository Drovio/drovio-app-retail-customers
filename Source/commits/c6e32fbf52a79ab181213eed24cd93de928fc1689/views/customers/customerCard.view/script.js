jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload contact info
	jq(document).on("customerinfo.reload", function() {
		jq("#customerInfoViewContainer").trigger("reload");
		jq("#personInfoViewContainer").trigger("reload");
	});
});