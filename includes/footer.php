		<footer class="row">
			<div class="large-12 columns">
				<hr />
					<div class="row">
						<div class="large-6 columns">
							<p>Gîte le Metzval</p>
						</div>
					<div class="large-6 columns">
						<a href="#top-page">Aller en haut de la page</a>
					</div>
					</div>
			</div>
		</footer>

 					 
 	<script type="text/javascript">			

	 /*
		/* Gestion input cheminot ou non
	 */ 

	$('#cheminotnon').click(function() 
	{
			$('#ce_cheminot').hide();
			$('#region').hide();
			$('#label-region').hide();
			$('#label-ce_cheminot').hide();
	});
	   
	 $('#cheminotoui').click(function() 
	{
			$('#ce_cheminot').show();
			$('#region').show();
			$('#label-region').show();
			$('#label-ce_cheminot').show();
	});
	
	   
	 /*
		/* Gestion input selection un ou plusieurs gîtes
	 */ 
	 $(document).ready(function() {
	 
	 	$('#ce_cheminot').hide();
		$('#region').hide();
		$('#label-region').hide();
		$('#label-ce_cheminot').hide();
	 
	 
		$('#code-promo').show();
		$('#label-code-promo').show();
	   	
	   	$('#gite-select').hide();
	   	$('#gite-select2').hide();
	   	$('#information-resa').hide();
	   	$('#label-selector-gite').hide();
	   	 
	   	$('#multi-gite').show();
	   	$('#label-multi-gite').show();
	   });

	 $('#un-gite').click(function() 
	{
			$('#code-promo').show();
			$('#label-code-promo').show();
			$('#information-resa').hide();
			$('#gite-select').hide();
			$('#gite-select2').hide();
			$('#label-selector-gite').hide();
			
			$('#payementJ-30').show();
			$('#payementJ+30').show();
			$('#payementcbJ-30').show();
			$('#payementcbJ+30').show();
			$('#payementchequeJ-30').show();
			$('#payementchequeJ+30').show();
			$('.paye-input-payement').show();
			$('.paye-input-payement').show();	
	}); 
	
	$('#multi-gite').click(function() 
	{
			$('#code-promo').hide();
			$('#label-code-promo').hide();
			$('#gite-select').show();
			$('#gite-select2').show();
			$('#information-resa').show();
			$('#label-selector-gite').show();
			
			$('#payementJ-30').hide();
			$('#payementJ+30').hide();
			$('#payementcbJ-30').hide();
			$('#payementcbJ+30').hide();
			$('#payementchequeJ-30').hide();
			$('#payementchequeJ+30').hide();
			$('.paye-input-payement').hide();
			$('.paye-input-payement').hide();	
	});
			
	/*
		/* vérification formulaires
	*/ 
	 $(function() {
	  
		// Setup form validation on the #register-form element
		$("#register-form").validate({
		
			// Specify the validation rules
			rules: {
				login: {
					required: true,
					email: true
				},
				loginConfirm: {
					required: true,
					email: true,
					equalTo: "#login"
				},
				password: {
					required: true,
					minlength: 5
				},
				passwordConfirm: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
			},
			
			// Specify the validation error messages
			messages: {
				password: {
					required: "Mot de passe obligatoire",
					minlength: "Saisir un mot de passe de 5 caractères minimum"
				},
				passwordConfirm: {
					required: "Mot de passe obligatoire",
					minlength: "Saisir un mot de passe de 5 caractères minimum",
					equalTo: "Les mots de passe sont différents"
				},
				login: {
					   required: "Adresse mail obligatoire",
					   email: "Entrez une adresse valide"
				},
				loginConfirm: {
					required:"Adresse amil obligatoire",
					email:"Entrez une adresse valide",
					equalTo: "Les adresses mails sont différentes"
				},
			},
			
			submitHandler: function(form) {
				form.submit();
			}
		});
	
	  });
	  
	   $(function() {
	  
		// Setup form validation on the #register-form element
		$("#register-contact").validate({
		
			// Specify the validation rules
			rules: {
				email: {
					required: true,
					email: true
				},
			},
			
			// Specify the validation error messages
			messages: {
				email: {
					   required: "Adresse mail obligatoire",
					   email: "Entrez une adresse valide"
				},

			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	
	  });
	  
	  $(function() {
	  
		// Setup form validation on the #register-form element
		$("#connect-form").validate({
		
			// Specify the validation rules
			rules: {
				login: {
					required: true,
					email: true
				},
				password: {
					required: true,
				},
			},
			
			// Specify the validation error messages
			messages: {
				password: {
					required: "Mot de passe obligatoire",
				},
				login: "Entrez une adresse valide",
			},
			
			submitHandler: function(form) {
				form.submit();
			}
		});
	
	  });

		 
		$(document).foundation({
			reveal : {
				animation_speed: 500
			},
			tooltip : {
				disable_for_touch: true
			},
			topbar : {
				sticky_class : 'sticky',
				custom_back_text: true, // Set this to false and it will pull the top level link name as the back text
				back_text: 'Back', // Define what you want your custom back text to be if custom_back_text: true
				is_hover: true,
				mobile_show_parent_link: false, // will copy parent links into dropdowns for mobile navigation
				scrolltop : true // jump to top when sticky nav menu toggle is clicked
			}
		});


		$(function() {
			$( "#datepicker" ).datepicker();
		});

		$(function() {
			$( "#datepicker2" ).datepicker();
		});

		function verif_action(){
			return confirm("Etes vous s�r ?");
		}
		
	</script>

 
 <?php
 	deco();
 ?>

</body>
</html>