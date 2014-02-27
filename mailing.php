<?php

	require('includes/header.php');
?>
	
	<div class="row">
		<div class="small-11 small-centered columns">
		
				<?php 

					envoiMail2('sdk@cesncf-stra.org', 'test','test');
				
				?>
				
		</div>
	</div>
<?php

	require('includes/footer.php'); 

?>