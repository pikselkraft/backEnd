<?php

	require('includes/header.php');
?>
  <!-- Main Page Content and Sidebar -->
 
  <h1>Maaiiil!</h1>
 	<?php
    	
    	require('includes/ink/mailPass.php');
    	require('includes/ink/mailFacture.php');
    	require('includes/ink/mailRappel.php');
    	require('includes/ink/mailBienvenu.php');
    	require('includes/ink/mailIpn.php');
    	
    	envoiPass(MAIL_SDK);
    	envoiBienvenu(MAIL_SDK);
    	envoiFacture(MAIL_SDK);
    	envoiRappel(MAIL_SDK);
    	envoiIpn(MAIL_SDK,1,2,3);
  
    ?>


<?php

	require('includes/footer.php'); 

?>