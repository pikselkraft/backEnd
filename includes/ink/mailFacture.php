<?php

/**
	* Gestion des mails de facture(s)
*/
require_once 'phpmailer/class.phpmailer.php';
//require_once 'codeCSS.php';


/************************************************************/
/* constante 
/***********************************************************/
define("MAIL_METZVAL", "contact@gite-lemetzval.fr");
define("MAIL_SDK", "sdk@cesncf-stra.org");
define("MAIL_OCT", "oct@cesncf-stra.org");



function envoiFacture($email,$chaineFichier,$message="")
{
	global $mysqli;
//	global $codeCSS;
	require_once 'baseMailHTML.php';
	if(isset($email)) {
		$sqlVerifExistant 	= "SELECT civilite, nom, prenom, email from CLIENTS WHERE email ='".$email."'" ; //verif mail unique
		$result=$mysqli->query($sqlVerifExistant);
		 
		if ($row=$result->fetch_Assoc()) {	
			$civilite = $row['civilite'];
			$nom = $row['nom'];
			$prenom = $row['prenom'];
			$email = $row['email'];

			$message_html=$messageHeader.$messageCSS.$messageBodyBefore.
						'<tr>
							<td>
							<h1>Bonjour '.$civilite.'&nbsp; '.$nom.' '.$prenom.'</h1>
										<p class="lead">'.$message.'</p>
										<p>Ci-joint votre facture au format pdf pour votre r&eacute;servation au g&icirc;te Le Metzval.</p>
										<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
										<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
							</td>
							<td class="expander"></td>
						</tr>'.$messageBodyAfter;

			$mail = new PHPMailer(); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
			try {
				$mail->AddReplyTo(MAIL_METZVAL, 'G&icirc;te le metzval');
				$mail->AddAddress($email, $nom.' '.$prenom);
				$mail->SetFrom(MAIL_METZVAL, 'G&icirc;te le metzval');
				$mail->Subject = 'Recapitulatif de votre commande';
				//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
				$mail->MsgHTML($message_html);
				//$mail->AddAttachment('images/phpmailer.gif');      // attachment
				$mail->AddStringAttachment($chaineFichier,"facture-gite-Le-Metzval-$idcommande.pdf"); // attachment
				$mail->Send();
				//echo "Message Sent OK<p></p>\n";
			} catch (phpmailerException $e) {
				echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
				echo $e->getMessage(); //Boring error messages from anything else!
			}

		}
		else
		{
			$mailInvalide=false;
			return $mailInvalide;
		}

	}
	else{echo "Email invalide";}
}

?>