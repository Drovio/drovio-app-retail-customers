jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("click", ".editCustomerFinancialInfo .close_ico", function() {
		// Trigger to cancel edit
		jq(document).trigger("customerfinances.cancel_edit");
	});
});