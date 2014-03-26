<?php

	require('includes/header.php');

	//$idCommande = $_GET['idcommande'];
	$idCommande=314;
	$reqInfoCo="SELECT idcommande, taxe, caution, caution_paye, montant_option, remise, code_promo, date_creation, statut_facture, accompte, accompte_paye, total, total_paye FROM COMMANDE WHERE idcommande=".$idCommande;
	$resultInfoCo=$mysqli->query($reqInfoCo);
	while ($rowInfoCo = $resultInfoCo->fetch_assoc())
	{
		$tab[] = $rowInfoCo["idcommande"];
		$tab[] = $rowInfoCo["taxe"];
		$tab[] = $rowInfoCo["caution"];
		$tab[] = $rowInfoCo["caution_paye"];
		$tab[] = $rowInfoCo["montant_option"];
		$tab[] = $rowInfoCo["remise"];
		$tab[] = $rowInfoCo["code_promo"];
		$tab[] = $rowInfoCo["date_creation"];
		$tab[] = $rowInfoCo["statut_facture"];
		$tab[] = $rowInfoCo["accompte"];
		$tab[] = $rowInfoCo["accompte_paye"];
		$tab[] = $rowInfoCo["total"];
		$tab[] = $rowInfoCo["total_paye"];
	}

	$information    = serialize($tab);
	$informationUrl = urlencode($information);

	$reqInfoResa="SELECT c.idreservation, r.date_debut, r.date_fin, c.idclient FROM COMMANDERESERVER c, RESERVATION r WHERE idcommande='".$idCommande."' LIMIT 0,1";
	$resultInfoResa=$mysqli->query($reqInfoResa);
	while ($rowInfoResa = $resultInfoResa->fetch_assoc())
	{
		$tabResa[] = $rowInfoResa["idclient"];
		$tabResa[] = $rowInfoResa["date_debut"];
		$tabResa[] = $rowInfoResa["date_fin"];
		$tabResa[] = $rowInfoResa["idreservation"];
	}
	//testVar($tabResa);

	$informationResa    = serialize($tabResa);
	$informationResaUrl = urlencode($informationResa);

	$reqInfoClient="SELECT nom, prenom, civilite FROM CLIENTS WHERE idclient=".$tabResa[0];
	//testVar($reqInfoClient);
	$resultInfoClient=$mysqli->query($reqInfoClient);
	while ($rowInfoClient = $resultInfoClient->fetch_assoc())
	{
		$tabClient[] = $rowInfoClient["nom"];
		$tabClient[] = $rowInfoClient["civilite"];
	}

	
	$tabFusion           = array_merge((array)$tab, (array)$tabResa);
	$tabFinal            = array_merge((array)$tabFusion, (array)$tabClient);
	testVar($tabFinal);
	
	$informationEncodage = serialize($tabFinal);
	$informationUrl      = urlencode($informationEncodage);

?>
	
	<div class="row">
		<div class="small-11 small-centered columns">
		
			<?= '<a href="includes/pdf/example/facture-GiteLemetzval.php?information='.$informationUrl.' title="PDF [new window]" target="_blank">PDF</a>' ?>

		</div>
	</div>
<?php

	require('includes/footer.php'); 

?>