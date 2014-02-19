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

		<script>
		 
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

 </html>
 <?php
 deco();
 ?>