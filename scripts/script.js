$(document).ready(function() { 


	/**
	 * edition reservation -> checkbox taxe
	 */
	$("#choixTaxe").hide();

	$("#taxeAdulte").on("click", function(e) {
		$("#choixTaxe").show();
	});

	$("#taxeEnfant").on("click", function(e) {
		$("#choixTaxe").show();
	});

});