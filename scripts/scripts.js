$(document).ready(function() { 

	$("#choixTaxe").hide();

	$("#taxeAdulte").on("click", function(e) {
			$("#choixTaxe").show();
		});

		$("#taxeEnfant").on("click", function(e) {
			$("#choixTaxe").show();
		});
});