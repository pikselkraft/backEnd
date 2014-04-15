<?php
require_once 'includes/header.php';


/*********************************************
*		REcherche des commande  des gites     *
**********************************************/
// requete qui permet de charge la liste des status des commandes dans un tableau
$reqStatutCommande="SELECT idstatut, designation 
					FROM STATUTCOMMANDE";
	
$result_reqStatutCommande=$mysqli->query($reqStatutCommande);
while ($row = $result_reqStatutCommande->fetch_assoc())
{					
		// $statut[(int)$row["idstatut"]]["designation"]=$row["designation"];
		$statut[(int)$row["idstatut"]]["designation"]=$row["designation"];
		
}
if (isset($_POST["nom"]) || isset($_POST["email"]) || isset($_POST["idcommande"])){
	$reqCommandeResa="SELECT distinct CO.idcommande,  CM.idclient, C.nom, C.prenom, C.email, CO.taxe, CO.caution, 
	CO.caution_paye, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, 
	CO.accompte_paye, CO.total, CO.total_paye, G.nom as nom_gite, G.idgite, CO.remise_taux
	FROM COMMANDE CO, COMMANDERESERVER CM, CLIENTS C, RESERVATION R, GITE G
	WHERE CM.idclient=C.idclient AND CM.idreservation =R.idreservation AND CO.idcommande=CM.idcommande AND G.idgite=R.idgite";

	if ((isset($_POST["statut_facture"])) and (($_POST["statut_facture"])<10) ){
		$reqCommandeResa.=" and CO.statut_facture=".$_POST["statut_facture"];
		$affichage_commande_ligne='<p> Affichage des commandes avec le statut ' . $statut[(int)$_POST["statut_facture"]]["designation"].'</p>';
	}
	if (!empty($email) and (!empty($_POST["nom"]))){
		$reqCommandeResa.=" and C.email like '".$email."' and C.nom like '%".$_POST["nom"]."%'";
	}else{
		if (!empty($_POST["nom"]) ){
			$reqCommandeResa.=" and C.nom like '%".$_POST["nom"]."%'";
		}else{
			if (!empty($email)){
				$reqCommandeResa.=" and C.email like '".$email."'";
			}else{
				if (!empty($idcommande))  {$reqCommandeResa.=" and CO.idcommande like '".$idcommande."'";}
			}
		}
	}
	$reqCommandeResa.=" order by CO.statut_facture ";
	$result_reqCommandeResa=$mysqli->query($reqCommandeResa); /* execution req recherche commande*/
	/**
	* Dev si requete est nul -> message
	*/
	$affichage_commande_ligne.='<table><thead>
		<th width="100px">Num&eacute;ro de la Commande</th>
		<th width="50px">Nom et num&eacute;ro du g&icirc;te</th>
		<th width="50px">Periode de r&eacute;servation</th>
		<th width="50px">Date de Commande</th>
		<th width="50px">Nom</th>
		<th width="50px">Statut</th>
		<th width="50px" data-tooltip class="has-tip" title="A:attente/P:Pay&eacute;/R:Rendu">Caution</th>
		<th width="50px">Accompte</th>
		<th width="50px">Remise</th>
		<th width="50px">Total</th>
		<th width="50px">Total pay&eacute;</th>
		<th>Action</th></tr>
	</thead>';
	//Boucle qui parcourt les clients dans la base de donn�es
	while ($row = $result_reqCommandeResa->fetch_assoc()){
		switch ((int)$row["statut_facture"])
		{
			case 0 : $couleurStatut ='#000000';
			break;
			case 1 : $couleurStatut ='#d9534f';
			break;
			case 2 : $couleurStatut ='#f0ad4e';
			break;
			case 3 : $couleurStatut ='#5bc0de';
			break;
			case 4 : $couleurStatut ='#5cb85c';
			break;
		}

		$couleurCommande='style=" border:2px solid '.$couleurStatut.';"';
		if ($row["accompte_paye"] == 0)
			$accompte_paye_symbole = '<i data-tooltip class="foundicon-error has-tip" title="Acompte non pay&eacute;" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';
		else
			$accompte_paye_symbole = '<i data-tooltip class="foundicon-checkmark has-tip" title="Acompte pay&eacute;" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';

		$affichage_commande_ligne.= '<tr>
			<td '.$couleurCommande.'>'.$row["idcommande"].'</td>
			<td '.$couleurCommande.'>'.$row["nom_gite"].'('.$row["idgite"].')</td>
			<td '.$couleurCommande.'>'.dateFr($row["date_debut"]).' - '.dateFr($row["date_fin"]).'</td>
			<td '.$couleurCommande.'>'.dateFr($row["date_creation"]).'</td>
			<td '.$couleurCommande.'>'.$row["nom"]." ".$row["prenom"].'</td>
			<td '.$couleurCommande.'>'.$statut[(int)$row["statut_facture"]]["designation"].'</td>
			<td data-tooltip class="has-tip" title="A:attente/P:Pay&eacute;/R:Rendu" '.$couleurCommande.'>('.$row["caution_paye"].') '.$row["caution"].' &euro;</td>
			<td '.$couleurCommande.'>'.$accompte_paye_symbole.'</td>
			<td '.$couleurCommande.'>'.$row["remise_taux"].' %</td>
			<td '.$couleurCommande.'>'.$row["total"].' &euro;</td>
			<td '.$couleurCommande.'>'.$row["total_paye"].' &euro;</td>
			<td '.$couleurCommande.'><a href="includes/pdf/factures/telechargementPDF.php?idcommande='.$row["idcommande"].'" title="Imprimer la facture" target="_blank"><i class="foundicon-page"></i></a></td>
			</tr>';	
	}
	$affichage_commande_ligne .= '</table>';
}

$result=count($statut);
$affichage_recherche ='<form action="facturation.php" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="idcommande">Num&eacute;ro de commande: </label><input id="idcommande" name="idcommande" type="int">';
$a=0; //compteur pour le parcourt du tableau
	$affichage_recherche.='<option selected="selected" value="10">Tout statut</option>';
while ($a<$result)
{
	$affichage_recherche.='<option value="'.(int)$a.'">'.$statut[(int)$a]["designation"].'</option>';
	$a++;
}
$affichage_recherche.='</select><input type="submit" value="Rechercher"></form>';

?>
</div>

<div class="row">
	<div class="large-12 columns">
		<div class="panel">
			<h1> Recherche des commandes</h1>
			<?= $affichage_recherche; ?>
		</div>
	</div>		
</div>
<!-- <div class="row"> -->
<!-- 		<div class="large-12 columns">
-->				
	<!-- </div> -->		
<!-- 	</div> -->

<?php
if (isset($affichage_commande_ligne) )
	echo  '<h3> R&eacute;sultat</h3>'.$affichage_commande_ligne;

require('includes/footer.php'); 
?>