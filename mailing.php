<?php

	require('includes/header.php');

	testVar(unserialize(urldecode($_GET['information'])));
	$information = urldecode($_GET['information']);
	$informationPdf = unserialize($information);
	print_r($informationPdf);
?>
	
	<div class="row">
		<div class="small-11 small-centered columns">
		
				<?php 

					//envoiMail2('sdk@cesncf-stra.org', 'test','test');
				
				?>
				
		</div>
	</div>
<?php

	require('includes/footer.php'); 

?>