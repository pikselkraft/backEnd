<?php
require('tcpdf_include.php');
require('/var/www/resa/dev/config.php'); 
require('../../fonctions.php'); 

if (isset($_GET["idcommande"]))
	$idcom = $_GET["idcommande"];
else
	$idcom = 312;

// --------- Recuperation desz donnees
$tab = Array();
$reqInfoCo="SELECT idcommande, taxe, caution, montant_option, remise, code_promo, accompte, total, total_paye, remise_taux, date_creation FROM COMMANDE WHERE idcommande=".$idcom;
$resultInfoCo=$mysqli->query($reqInfoCo);
while ($rowInfoCo = $resultInfoCo->fetch_assoc())
{
	$tab[] = $rowInfoCo["idcommande"];
	$tab[] = dateFr($rowInfoCo["date_creation"]);
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

$reqInfoClient="SELECT nom, prenom, civilite, idclient, adresse, codepostal, ville  FROM CLIENTS WHERE idclient=".$idcli;
$resultInfoClient=$mysqli->query($reqInfoClient);
while ($rowInfoClient = $resultInfoClient->fetch_assoc())
{
	$tab[] = $rowInfoClient["civilite"];
	$tab[] = $rowInfoClient["prenom"];
	$tab[] = $rowInfoClient["nom"];
	$tab[] = $rowInfoClient["idclient"];
	$tab[] = $rowInfoClient["adresse"];
	$tab[] = $rowInfoClient["codepostal"];
	$tab[] = $rowInfoClient["ville"];

}

//generation du ficher pdf

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
if ($tab[5] == 0)
	$texteRemise = "";
else
	$texteRemise = "<tr>
		<td>Remise</td>
		<td>$tab[5] euros</td>
	</tr>
	<br />
	<tr>
		<td>Code Promotion</td>
		<td>$tab[6]</td>
	</tr>
	<br />";
if ($tab[10] == 0)
	$texteRemiseTaux = "";
else{
	$texteRemiseTaux = "<tr>
		<td>Remise </td>
		<td>$tab[10] %</td>
	</tr>
	<br />";
	$tab[8] = $tab[8]*(1-($tab[10]/100));
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 13, '', true);
$pdf->AddPage();
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));


/*
----------------------Foramt du tableau de valeurs-----------------
0	=> idcommande
1	=> date de creation de la commande
2 	=> taxe
3	=> caution
4 	=> options
5 	=> remise (avec code promo)
6 	=> code promo
7 	=> accompte
8 	=> total
9  	=> total paye
10	=> remise (par taux)
11 	=> date de debut de location
12	=> date de fin de location
13	=> civilite client
14	=> prenom client
15	=> nom client
16	=> id client
17	=> adresse client
18	=> code postal client
19	=> ville client
-------------------------------------------------------------------
*/

$html = <<<EOD
<table>
	<tr>
		<td>
			<dl>
				<dt><small>Le gîte le Metzval	</small></dt>
				<dt><small>7 Rue de la Gare,	</small></dt>
				<dt><small>68380 Metzeral		</small></dt>
				<dt><small>Tel: 06 25 14 37 06	</small></dt>
				<dt><small>www.gite-lemtezval.fr</small></dt>
			</dl>
			<dl>
				<dt><small>N° commande :  $tab[0]</small></dt>
				<dt><small>Date :  $tab[1]</small></dt>
				<dt><small>N° client :  $tab[16]</small></dt>
			</dl>
		</td>
		<td>
			<dl>
				<dd></dd><dd></dd><dd></dd>
				<dd><b>$tab[13]. $tab[14] $tab[15]</b></dd>
				<dd>$tab[17]</dd>
				<dd>$tab[18] $tab[19]</dd>
			</dl>
		</td>
	</tr>
</table>
</br></br></br></br></br>
<h3>Bonjour $tab[13]. $tab[14] $tab[15],</h3>
<i><small>Ci-dessous le détail de votre r&eacute;servation du $tab[11] au $tab[12]</small></i>
<br /><br /><br />
<table summary="Tarif du g&icirc;te le Metzval">
	<thead>
		<tr>
			<th>Informations</th>
			<th>Vos donn&eacute;es</th>
		</tr>
	</thead>
	<tbody>
		<tr><br /></tr>
		<tr>
			<td>Montant des taxes</td>
			<td>$tab[2] &euro;</td>
		</tr>
		<br />
		<tr>
			<td>Montant de la caution</td>
			<td>$tab[3] &euro;</td>
		</tr>
		<br />
		<tr>
			<td>Montant des options</td>
			<td>$tab[4] &euro;</td>
		</tr>
		<br />
		<tr>
			<td>Accompte</td>
			<td>$tab[7] &euro;</td>
		</tr>
		<br />
		$texteRemise
		$texteRemiseTaux
		<br />
		<tr>
			<td>Total</td>
			<td>$tab[8] &euro;</td>
		</tr>
		<br />
		<tr>
			<td>Total payé</td>
			<td>$tab[9] &euro;</td>
		</tr>
		
		<br />
	</tbody>
</table><br /><br /><br /><br /><br />
<i>Dans l'attente de vos nouvelles, à bientôt !</i>
EOD;

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$pdf->Output('facture-GiteLemetzval.pdf', 'I');


?>