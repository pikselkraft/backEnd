<?php
session_start();	
require('config.php');
require('fonctions.php');
?>

<?php require('header.php'); ?>

<?php 
//Gestion Mot de Passe
//
//VERSION: 1.0
//
//TEMPLATE NAME: mail_password
//
//********************************************
// TEST MAIL
// MAIL AVEC NOUVEAU MOT DE PASSE
// INPUT MAIL
//********************************************
//
//CREATION: 06/01/2014

		if(isset($_POST['veriefMail']))
		{
			 
			 $mail					= $_POST['veriefMail'];
 
			 $sqlVerifExistant 	= "SELECT email,mp from CLIENTS WHERE email ='".$mail."'" ; //verif mail unique
			 
			 $result=$mysqli->query($sqlVerifExistant);
			 
			 if ($row=$result->fetch_Assoc())
			 {
				echo "Nous vous envoyons un nouveau mot de passe";
				
				$newPass = chaineAleatoire(8);
				
				/**************
				
				Créer un template mail responsiv et personalisé
				
				*************/
				
				//header("Location:connexion.php");
				//exit();
				
			 }
			else{echo "Email invalide";}
		}else{echo"Vous n'avez pas de saisi de mail";}

?>
				<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
					<fieldset>
						<legend>Votre email</legend>
						<li>
							<label for=veriefMail>Votre Email</label>
							<input id=veriefMail name=veriefMail type=email placeholder="exemple@domaine.com" required>
						</li>
						<button type=submit>Se connecter</button>
					</fieldset>
				</form>

<?php	require('footer.php'); ?>