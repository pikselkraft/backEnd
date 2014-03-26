<?php
session_start();
$_SESSION = array();
session_destroy();

header('Location:affichTous.php');
exit();
//logout gîte le metzval
//
//version: 1.0
//
//template name: logout
//creation: 11/12/2013	
?>