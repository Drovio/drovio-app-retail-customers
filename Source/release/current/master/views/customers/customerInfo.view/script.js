jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("customerinfo.edit", function() {
		jq(".customerInfoContainer .customerInfo").addClass("edit");
	});
	
	// Cancel edit action
	jq(document).on("customerinfo.cancel_edit", function() {
		// Remove class
		jq(".customerInfoContainer .customerInfo").removeClass("edit");
		
		// Clear edit form container contents
		jq(".editFormContainer").html("");
	});
});