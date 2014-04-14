<?php
//require('../../header.php');
/**
 * Creates an example PDF TEST document using TCPDF
 * @package www.gite-lemetzval.fr
 * @abstract Facture - La facture de votre reservation
 * @author Gite Le Metzval
 * @since 19/03/2014
 */



/**
 *  Récupération des informations de la commande
 */

$information = urldecode($_GET['information']);
$informationPdf = unserialize(urldecode($_GET['information']));


// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Gite Le Metzval');
$pdf->SetTitle('Facture - La facture de votre reservation');
$pdf->SetSubject('Facture - La facture de votre reservation');
$pdf->SetKeywords('Facture, PDF, Le Metzval');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = <<<EOD
<h1>Bonjour $informationPdf[18].$informationPdf[17]</h1>
<i>Ci-dessous le détail de votre r&eacute;servation du $informationPdf[14] au $informationPdf[15]</i>
<br /><br />
<table summary="Tarif du g&icirc;te le Metzval">
							<thead>
								<tr>
									<th>Informations</th>
									<th>Montant</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Votre num&eacute;ro de commande</td>
									<td>$informationPdf[0]</td>
								</tr>
								<br />
								<tr>
									<td>Montant des taxes</td>
									<td>$informationPdf[1] euros</td>
								</tr>
								<br />
								<tr>
									<td>Montant de la caution</td>
									<td>$informationPdf[2] euros</td>
								</tr>
								<br />
								<tr>
									<td>Montant des options</td>
									<td>$informationPdf[4] euros</td>
								</tr>
								<br />
								<tr>
									<td>Remise</td>
									<td>$informationPdf[5] euros</td>
								</tr>
								<br />
								<tr>
									<td>Code Promotion</td>
									<td>$informationPdf[6]</td>
								</tr>
								<br />
								<br />
								<tr>
									<td>Accompte</td>
									<td>$informationPdf[9] euros</td>
								</tr>
								<br />
								<tr>
									<td>Total</td>
									<td>$informationPdf[11] euros</td>
								</tr>
								<br />
								<tr>
									<td>Total payé</td>
									<td>$informationPdf[12] euros</td>
								</tr>
								<br />
							</tbody>
						</table>
<br /><br /><br />
						<i>Dans l'attente de vos nouvelles, à bientôt !</i><br />
						<i>Le gîte le Metzval</i>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('facture-GiteLemetzval.pdf', 'I');

/**
 * piece jointe mail à faire
 */

//============================================================+
// END OF FILE
//============================================================+
