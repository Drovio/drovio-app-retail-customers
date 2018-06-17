jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("customerfinances.edit", function() {
		jq(".customerFinancesContainer .customerFinances").addClass("edit");
	});
	
	// Cancel edit action
	jq(document).on("customerfinances.cancel_edit", function() {
		// Remove class
		jq(".customerFinancesContainer .customerFinances").removeClass("edit");
		
		// Clear edit form container contents
		jq(".customerFinancesContainer .editFormContainer").html("");
	});
});