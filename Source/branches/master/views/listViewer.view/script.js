jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listener to switch to contact details
	jq(document).on("listviewer.switchto.details", function() {
		jq(".customersListViewContainer .customersListView").addClass("details");
	});
	
	// Switch to list
	jq(document).on("click", ".customersListViewContainer .detailsContainer .wbutton.back", function() {
		jq(".customersListViewContainer .customersListView").removeClass("details");
	});
	
	
	// Search for contacts
	jq(document).on("keyup", ".customersListViewContainer .listContainer .searchContainer .searchInput", function() {
		var search = jq(this).val();
		if (search == "")
			return jq(".customersListViewContainer .listContainer .listItem").show();
			
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Select all project boxes, hide and filter by the regex then show
		jq(".customersListViewContainer .listContainer .listItem").hide().find(".name").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).closest(".listItem").show();
		});
	});
	
	// Stop bubbling when adding person to customers
	jq(document).on("click", "#btn_add_customer", function(ev) {
		ev.stopPropagation();
	});
});