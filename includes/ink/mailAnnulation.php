<?php

// gestion des mails d'annulation

require('/var/www/resa/dev/config.php');
require_once 'phpmailer/class.phpmailer.php';

/************************************************************/
/* constante 
/***********************************************************/
define("MAIL_METZVAL", "contact@gite-lemetzval.fr");
define("MAIL_SDK", "sdk@cesncf-stra.org");
define("MAIL_OCT", "oct@cesncf-stra.org");
//echo " \n-".$_GET["email"]."-";

require_once 'baseMailHTML.php';

if(isset($_GET["email"]) && $_GET["email"]!="aucun" && isset($_GET["fonction"])) {
	$email = $_GET["email"];
	$sqlVerifExistant 	= "SELECT civilite, nom, prenom, email,mp FROM CLIENTS WHERE email = '".$email."';" ; //verif mail unique
	$result=$mysqli->query($sqlVerifExistant);
	if ($row=$result->fetch_Assoc()) {	
		$civilite = $row['civilite'];
		$nom = $row['nom'];
		$prenom = $row['prenom'];
		$message1 = '<tr>
						<td>
						<h1>Bonjour '.$civilite.' '.$nom.' '.$prenom.'</h1>
							<p class="lead">Nous vous informons que l\'annulation de votre commande a bien &eacute;t&eacute; prise en compte.</p>
							<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
							<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
						</td>
						<td class="expander"></td>
					</tr>';
		$message2 = '<tr>
						<td>
						<h1>Bonjour '.$civilite.' '.$nom.' '.$prenom.'</h1>
							<p class="lead">Nous avons le regret de vous annoncer que votre commande n\'a pas pu aboutir pour des raisons de disponibilit&eacute;s.</p>
							<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
							<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
						</td>
						<td class="expander"></td>
					</tr>';
		//  ======================  differentes fonctions
		$fonction = $_GET["fonction"];
		switch ($fonction) {
			case 'apercu':
				echo '<h2>Apercu du mail d\'anulation</h2><p>Quel message voulez-vous envoyer ?</p>
				<a class="button block" onclick="envoiAnnulation(\'1\',\''.$email.'\');">Annulation par le client</a>
				<a class="button block" onclick="envoiAnnulation(\'2\',\''.$email.'\');">Annulation indisponibilit&eacute;</a>';
				break;//href="includes/ink/mailAnnulation.php?fonction=envoyer1&email='.$email.'"
			case 'envoyer1':
				echo "envoyer1 -".$email."-";
				$mail = new PHPMailer(); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
				try {
					$mail->AddReplyTo(MAIL_METZVAL, 'G&icirc;te le metzval');
					$mail->AddAddress($email, $nom.' '.$prenom);
					$mail->SetFrom(MAIL_METZVAL, 'G&icirc;te le metzval');
					$mail->Subject = 'Votre commande a bien &eacute;t&eacute; annul&eacute;e';
					//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
					$mail->MsgHTML($messageHeader.$messageCSS.$messageBodyBefore.$message1.$messageBodyAfter);
					$mail->Send();
				} catch (phpmailerException $e) {
					//echo $e->getMessage();
				} catch (Exception $e) {
					//echo $e->getMessage();
				}
				//header(string)
				break;
			case 'envoyer2':
				$mail = new PHPMailer(); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
				try {
					$mail->AddReplyTo(MAIL_METZVAL, 'G&icirc;te le metzval');
					$mail->AddAddress($email, $nom.' '.$prenom);
					$mail->SetFrom(MAIL_METZVAL, 'G&icirc;te le metzval');
					$mail->Subject = 'Votre commande ne peut aboutir';
					//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
					$mail->MsgHTML($messageHeader.$messageCSS.$messageBodyBefore.$message2.$messageBodyAfter);
					$mail->Send();
				} catch (phpmailerException $e) {
				} catch (Exception $e) {}
				break;
			default:
				echo "defaut";
				break;
		}
	}else
		echo "Erreur, aucun client ne correspond a cet email";	
}else
	echo "Email invalide";
?>