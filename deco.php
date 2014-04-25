<?php 
session_start();
require('/var/www/resa/dev/config.php'); 
require('includes/header.php');

	if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])) {
	    
	    unset($_SESSION['LoggedIn']);
	    unset($_SESSION['Username']);
	    unset($_SESSION['mp']); 
		session_destroy();

	    header('Location:index.php');
	}
	else {
	 	header('Location:index.php');
	}
?>