<?php

require_once 'phpmailer/class.phpmailer.php';
require('/var/www/resa/dev/config.php'); 
require_once 'baseMailHTML.php';

/************************************************************/
/* constante 
/***********************************************************/
define("MAIL_METZVAL", "contact@gite-lemetzval.fr");
define("MAIL_SDK", "sdk@cesncf-stra.org");
define("MAIL_OCT", "oct@cesncf-stra.org");


if(isset($_POST["sujet"]) && isset($_POST["message"]) ) {
	$sujet = $_POST["sujet"];
	$message = $_POST["message"];

	$message_html=$messageHeader.$messageCSS.$messageBodyBefore.
	'<tr>
		<td>
			'.nl2br('<p class="lead">'.htmlspecialchars($message).'</p><br/><br/>').'
			<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
			<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
		</td>
		<td class="expander"></td>
	</tr>'.$messageBodyAfter;

	$mail = new PHPMailer();
	$mail->AddReplyTo(MAIL_METZVAL, 'G&icirc;te le metzval');
	$mail->SetFrom(MAIL_METZVAL, 'G&icirc;te le metzval');
	$mail->Subject = htmlspecialchars($sujet);
	$mail->MsgHTML($message_html);
	//uniquement les inscrits a la newsletter
	$sqlVerifExistant 	= "SELECT nom, prenom, email from CLIENTS WHERE newsletter=1";
	$result=$mysqli->query($sqlVerifExistant);
	//on ajoute les abonnes un a un
	while ($row=$result->fetch_Assoc())
		$mail->AddAddress($row["email"], $row["nom"].' '.$row["prenom"]);
	
	try{
		$mail->Send();
		echo "ok";
	}catch(Exception $e){
		echo "blabla ".$e->getMessage();
	}
}else
	echo "Champs incomplets...";
?>