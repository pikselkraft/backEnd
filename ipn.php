<?php
require('includes/header.php');
?>

<?php 
//IPN paypal v&eacute;rification
//
//VERSION: 1.0
//
//TEMPLATE NAME: ipn

/**
	*	Landing page ipn
*/

//CREATION: 06/01/2014
?>

<?php
		

//permet de traiter le retour ipn de paypal
$email_account = "baseK@cesncf-stra.org";
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Host: www.sandbox.paypal.com\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$invoice = $_POST['invoice'];
$custom = $_POST['custom'];

		/* recherche de l'id client du client pour v&eacute;rification */
		$reqIdClient =	"SELECT civilite, nom, prenom,idclient FROM CLIENTS 
								WHERE email='".$receiver_email."'";
																
		$sqlIdClient = $mysqli->query($reqIdClient);

		while ($resqlIdClient = $sqlIdClient->fetch_assoc())
		{
			$civilite = $resqlIdClient['civilite'];
			$nom = $resqlIdClient['nom'];
			$prenom = $resqlIdClient['prenom'];
			$idClient = $resqlIdClient['idclient'];
		}

		/* recup&eacute;ration information du client */
		$sqlTarifCommande = "SELECT total,accompte,taxe, caution FROM COMMANDE WHERE idcommande='".$invoice."'";
		$reqTarifCommande = $mysqli->query($sqlTarifCommande);
								
		while ($resqlTarifCommande = $reqTarifCommande->fetch_assoc())
		{
			$tarifCommande = $resqlTarifCommande['total'];
			$caution = $resqlTarifCommande['caution'];
			$tarifAccompte = $resqlTarifCommande['accompte'];
			$tarifTaxe = $resqlTarifCommande['taxe'];
		}
			$testTarifCommande = $tarifCommande - $caution;
			$testTarifAccompte = $tarifAccompte;				
		
		/* recup&eacute;ration de l'id de la reservation -> pour mise à jour */
		$recupIdResa = "SELECT idreservation FROM COMMANDERESERVER WHERE idcommande='".$invoice."'";

		$resultIdResa = $mysqli->query($recupIdResa);
		
		$i=0;
		while ($ressqlIdResa = $resultIdResa->fetch_assoc()) 
		{
			$idResa[$i] = $ressqlIdResa['idreservation'];
			$i++;
		}

		$to = 'sdk@cesncf-stra.org';
		$subject = 'Dev - Test variables';
		$msg = "Dev - fp false, verified good | testrequteResa: ".$recupIdResa." count ".$countIdResa." | custom: ".$custom."| paye: ".$payment_amount." | tarifco: ".$tarifCommande." | tariftaxe: ". $tarifAccompte." |tariftaxe: ".$tarifTaxe."sql : ".$reqIdClient." | ".$sqlTarifCommande." | ".$idResa[0]." | ".$idResa[1]." | ".$idResa[2]." | testTarifCommande ".$testTarifCommande."| testTarifAccompte ".$testTarifAccompte."| ".$tarifTaxe."";
		$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
		$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
		$headers .= "\r\n";
		mail($to, $subject, $msg, $headers);

if (!$fp) {
	echo "<div class=msg-error>Une erreur est survenue, veuillez contacter le gîte, excusez nous pour le d&eacute;rangement</div>";
} else {
			fputs ($fp, $header . $req);
					
while (!feof($fp)) 
    {
    	$res = fgets ($fp, 1024);
    	if (strcmp ($res, "VERIFIED") == 0) 
    	{
        
        // v&eacute;rifier que payment_status a la valeur Completed
        if ($payment_status == "Completed") 
        {
               
               if ($email_account == $receiver_email) 
               	{
						$to = 'sdk@cesncf-stra.org';
						$subject = 'Dev - Test Mail4';
               			$msg = "Dev - mail good | test MAIL5 : ".$idClient." = ".$custom." | testfunction: ".$test ." | txn ".$txn_id." | status: ".$payment_status." | email: ".$receiver_email." ". $email_account." res: ".$res."";
						$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
						$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
						$headers .= "\r\n";
						mail($to, $subject, $msg, $headers);
						
				$idClient =  $custom;
				// v&eacute;rifier que receiver_email est votre adresse email PayPal principale
				if ($idClient == $custom) 
				{
						$to = 'sdk@cesncf-stra.org';
						$subject = 'Dev - Test Mail5';
               			$msg = "Dev - test | Commande: ".$invoice." | idclient good test MAIL5 : ".$payment_amount." = ".$testTarifCommande." | ".$payment_amount." = ".$testTarifAccompte." | ".$payment_amount." = ".$testTarifCommande." | ".$testTarifAccompte." | idcommande=".$invoice." ";
						$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
						$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
						$headers .= "\r\n";
						mail($to, $subject, $msg, $headers);
						
//						$testTarifCommande = $payment_amount;
//						$testTarifAccompte = $payment_amount;
					// v&eacute;rifier que payment_amount et payment_currency sont corrects
					if(((string)($payment_amount)==(string)($testTarifCommande)) || ((string)($payment_amount)==(string)($testTarifAccompte)))
					{
						/* tester update et table transaction */
						$to = 'sdk@cesncf-stra.org';
						$subject = 'Dev - Test Mail5.22222';
               			$msg = "Dev - test | Commande: ".$invoice." | idclient good test MAIL5 : ".$payment_amount." = ".$testTarifCommande." | ".$payment_amount." = ".$testTarifAccompte." | ".$payment_amount." | ".$testTarifCommande." | ".$testTarifAccompte." | idcommande=".$invoice." ";
						$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
						$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
						$headers .= "\r\n";
						mail($to, $subject, $msg, $headers);
						
						/* empêche multi insertion dans la table txn_id */
						$reqTxn="SELECT COUNT(txn_id) FROM TRANSACTION WHERE txn_id='".$txn_id."' AND idcommande='".$invoice."' AND mail_paypal=0";
						
						$to = 'sdk@cesncf-stra.org';
						$subject = 'Dev - Test Mail5.1';
               			$msg = "Dev - test | Commande: ".$invoice." | idclient good test MAIL5 : ".$payment_amount." = ".$testTarifCommande." | ".$payment_amount." = ".$testTarifAccompte." | ".$payment_amount." | ".$testTarifCommande." | ".$testTarifAccompte." || ".$reqTxn."";
						$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
						$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
						$headers .= "\r\n";
						mail($to, $subject, $msg, $headers);
				
						$resultTxn = $mysqli->query($reqTxn);
						$countTxn=mysqli_fetch_array($resultTxn); 	
						
						$to = 'sdk@cesncf-stra.org';
						$subject = 'Dev - Test Mail5.2';
               			$msg = "Dev - test | countTxn: ".$countTxn[0]." | idclient good test MAIL5.1 : ".$result." ".$row_cnt."";
						$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
						$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
						$headers .= "\r\n";
						mail($to, $subject, $msg, $headers);

						if ($countTxn[0]==0) { /* verifie unicité du txn_id */
								
							/* adapte le motif à + ou - 30 jours*/
							if($payment_amount==$testTarifCommande)
							{
								$motif = "total";
							}

							if($payment_amount==$testTarifAccompte)
							{
								$motif = "accompte";
							}


							$dateCrea = date("Y-m-d H:i:s");
							/* insertion dans la table transaction avec date,numero commande et paypal + motif:accompte */
							$sqlTransaction = "INSERT INTO TRANSACTION (idcommande,type_transaction,txn_id,date_transaction,motif,reference,mail_paypal) VALUES ('".$invoice."','paypal','".$txn_id."','".$dateCrea."','".$motif."','".$txn_id."','1')"; // requête transaction
							$mysqli->query($sqlTransaction); // insertion transaction

							/* mise à jour statut de l'accompte (à pay&eacute;e) */
							$updateCommande = "UPDATE COMMANDE SET accompte_paye ='1', total_paye='".$payment_amount."' WHERE idcommande='".$invoice."'";
							$mysqli->query($updateCommande);

							$to = 'sdk@cesncf-stra.org';
							$subject = 'Dev - Test Mail6';
							$msg = "Dev - transac good, verified good | sql: ".$sqlTransaction." | ".$payer_email." | ".$updateCommande." | ".$idResa." | idresa ".$idResa[0]."";
							$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
							$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
							$headers .= "\r\n";
							mail($to, $subject, $msg, $headers);

								if($mysqli) {
									
									$test=count($idResa);
									$to = 'sdk@cesncf-stra.org';
									$subject = 'Dev - Test Mail6.1';
									$msg = "Dev - transac good, verified good | sql: ".$sqlTransaction." | ".$payer_email." | ".$updateCommande." |count ".$test." | ".$idResa[0]."";
									$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
									$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
									$headers .= "\r\n";
									mail($to, $subject, $msg, $headers);

//									$paye = $payment_amount + $tarifTaxe;
//									$resteApaye = $tarifCommande - $paye + $tarifTaxe;
									
									$paye = $payment_amount;
									$resteApaye = $tarifCommande - $paye;
									
									require('mailIpn.php');
									
									envoiIpn('sdk@cesncf-stra.org',$invoice,$paye,$resteApaye);
								}
								
								/* changement statut de la reservation à reserv&eacute;*/ 
								for($i=0;$i<count($idResa);$i++)
								{
									$to = 'sdk@cesncf-stra.org';
									$subject = 'Dev - Test Mail6.2';
									$msg = "Dev - transac good, verified good | sql: ".$i."";
									$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
									$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
									$headers .= "\r\n";
									mail($to, $subject, $msg, $headers);
									
									$updateResa = "UPDATE RESERVATION SET statut='R' WHERE idreservation='".$idResa[$i]."'";

//									$to = 'sdk@cesncf-stra.org';
//									$subject = 'Dev - Test Mail7';
//									$msg = "Dev - transac good, verified good | sql: ".$updateResa."";
//									$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
//									$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
//									$headers .= "\r\n";
//									mail($to, $subject, $msg, $headers);

									$mysqli->query($updateResa);

									$to = 'sdk@cesncf-stra.org';
									$subject = 'Dev - Test Mail7.1';
									$msg = "Dev - transac good, verified good | sql: ".$updateResa."";
									$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
									$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
									$headers .= "\r\n";
									mail($to, $subject, $msg, $headers);
								}
					
							}
							else {
								echo"insertion déjà effectuée";
								$to = 'sdk@cesncf-stra.org';
								$subject = 'Dev - Test Mail5.1.1';
								$msg = "Dev - test | txn: ".$txn_id." ".$invoice." | idclient good test MAIL5.1 : ".$result." ".$row_cnt."";
								$headers = 'From: oct <oct@cesncf-stra.org>'."\r\n";
								$headers .= 'Bcc: sdk <sdk@cesncf-stra.org>'."\r\n";
								$headers .= "\r\n";
								mail($to, $subject, $msg, $headers);
							}	
					}
					else {echo"Mauvais tarif";}
				}
				else {echo"Mauvaise adresse email paypal";}
		}
		else {echo"statut commande";}
      }
      else {
                // Statut de paiement: Echec
        }
        exit(); 
   }
    else if (strcmp ($res, "INVALID") == 0) {
		// Transaction invalide
    }
}
fclose ($fp);
}	

require('includes/footer.php');
?>