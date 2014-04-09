<?php

require_once 'tcpdf.php';

function generationPdf($idcom){
	global $mysqli; 
	$reqInfoCo="SELECT idcommande, taxe, caution, montant_option, remise, code_promo, accompte, total, total_paye, remise_taux FROM COMMANDE WHERE idcommande=".$idcom;
	$resultInfoCo=$mysqli->query($reqInfoCo);
	while ($rowInfoCo = $resultInfoCo->fetch_assoc())
	{
		$tab[] = $rowInfoCo["idcommande"];
		$tab[] = $rowInfoCo["taxe"];
		$tab[] = $rowInfoCo["caution"];
		$tab[] = $rowInfoCo["montant_option"];
		$tab[] = $rowInfoCo["remise"];
		$tab[] = $rowInfoCo["code_promo"];
		$tab[] = $rowInfoCo["accompte"];
		$tab[] = $rowInfoCo["total"];
		$tab[] = $rowInfoCo["total_paye"];
		$tab[] = $rowInfoCo["remise_taux"];
	}

	$reqInfoResa="SELECT c.idreservation, r.date_debut, r.date_fin, c.idclient FROM COMMANDERESERVER c, RESERVATION r WHERE idcommande='".$idcom."' LIMIT 0,1";
	$resultInfoResa=$mysqli->query($reqInfoResa);
	while ($rowInfoResa = $resultInfoResa->fetch_assoc())
	{
		$idcli = $rowInfoResa["idclient"];// pour la requete suivante
		$tab[] = dateFr($rowInfoResa["date_debut"]);
		$tab[] = dateFr($rowInfoResa["date_fin"]);
		//$tab[] = $rowInfoResa["idreservation"];
	}

	$reqInfoClient="SELECT nom, prenom, civilite FROM CLIENTS WHERE idclient=".$idcli;
	$resultInfoClient=$mysqli->query($reqInfoClient);
	while ($rowInfoClient = $resultInfoClient->fetch_assoc())
	{
		$tab[] = $rowInfoClient["civilite"];
		$tab[] = $rowInfoClient["prenom"];
		$tab[] = $rowInfoClient["nom"];
	}

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetAuthor('Gite Le Metzval');
	$pdf->SetTitle('Facture - La facture de votre reservation');
	$pdf->SetSubject('Facture - La facture de votre reservation');
	$pdf->SetKeywords('Facture, PDF, Le Metzval');
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
	$pdf->setFooterData(array(0,64,0), array(0,64,128));
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}
	//si la remise est nulle on affiche rien
	if ($tab[4] == 0)
		$texteRemise = "";
	else
		$texteRemise = "<tr>
			<td>Remise</td>
			<td>$tab[4] euros</td>
		</tr>
		<br />
		<tr>
			<td>Code Promotion</td>
			<td>$tab[5]</td>
		</tr>
		<br />";
	if ($tab[9] == 0)
		$texteRemiseTaux = "";
	else{
		$texteRemiseTaux = "<tr>
			<td>Remise </td>
			<td>$tab[9] %</td>
		</tr>
		<br />";
		$tab[7] = $tab[7]*(1-($tab[9]/100));
	}
	$pdf->setFontSubsetting(true);
	$pdf->SetFont('dejavusans', '', 14, '', true);
	$pdf->AddPage();
	$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
/*
0	=> idcommande
1	=> taxe
2 	=> caution
3 	=> options
4 	=> remise (avec code promo)
5 	=> code promo
6 	=> accompte
7 	=> total
8 	=> total paye
9 	=> remise (par taux)
10 	=> date de debut de location
11	=> date de fin de location
12	=> civilite client
13	=> prenom client
14	=> nom client
*/
	$html = <<<EOD
	<h2>Bonjour $tab[12]. $tab[13] $tab[14]</h2>
	<i>Ci-dessous le détail de votre r&eacute;servation du $tab[10] au $tab[11]</i>
	<br /><br />
	<table summary="Tarif du g&icirc;te le Metzval">
								<thead>
									<tr>
										<th>Informations</th>
										<th>Vos donn&eacute;es</th>
									</tr>
								</thead>
								</br>
								<tbody>
									<tr>
										<td>Votre num&eacute;ro de commande</td>
										<td>$tab[0]</td>
									</tr>
									<br />
									<tr>
										<td>Montant des taxes</td>
										<td>$tab[1] &euro;</td>
									</tr>
									<br />
									<tr>
										<td>Montant de la caution</td>
										<td>$tab[2] &euro;</td>
									</tr>
									<br />
									<tr>
										<td>Montant des options</td>
										<td>$tab[3] &euro;</td>
									</tr>
									<br />
									<tr>
										<td>Accompte</td>
										<td>$tab[6] &euro;</td>
									</tr>
									<br />
									$texteRemise
									$texteRemiseTaux
									<br />
									<tr>
										<td>Total</td>
										<td>$tab[7] &euro;</td>
									</tr>
									<br />
									<tr>
										<td>Total payé</td>
										<td>$tab[8] &euro;</td>
									</tr>
									<br />
								</tbody>
							</table>
	<br /><br /><br />
							<i>Dans l'attente de vos nouvelles, à bientôt !</i><br />
							<i>Le gîte le Metzval</i>
EOD;

	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	return $pdf->Output('facture-GiteLemetzval.pdf', 'S');
}
?>