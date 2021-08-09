jQuery(document).ready(function($){
	
	var originalButton = $('a.page-title-action');
	
	var cloneButton = originalButton.clone();

	cloneButton.text('Add New Reusable Part').attr('href', ct_template_add_reusable_link);

	cloneButton.insertAfter(originalButton);

});

	