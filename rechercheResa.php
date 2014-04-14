<?php

require('includes/header.php');

/*************************************
Affiche les infos d'une r�servation
**************************************/


$actionResa = $_GET['actionResa'];
$idCommande = $_GET['idcommande'];

$reqResa="	SELECT idreservation 
			FROM COMMANDERESERVER 
			WHERE idcommande='".$idCommande."'";

$sqlResa=$mysqli->query($reqResa);

while ($resultResa = $sqlResa->fetch_assoc()) {

	$idResa = $resultResa['idreservation'];

}


/**
 *  Upadte resa + insert + commande (fichier externe: modifCommande.php)
 */

if ($actionResa==='U') {

	$idreservation    = $_POST["idreservation"];

	/**
	 * recuperation idclient pour calcul tarif
	 */
	$reqcommanderesa="SELECT idcommande, idreservation, idclient FROM COMMANDERESERVER WHERE idreservation=".$idreservation ;
	$resultcommanderesa=$mysqli->query($reqcommanderesa);
	while ($row = $resultcommanderesa->fetch_assoc())
	{
		$idCommande=$row["idcommande"];
		$idClient=$row["idclient"];
	}

	/**
	 * [$reqClient description]
	 * recuperation du statut du tarif
	 */
	$reqClient="SELECT cheminot, region FROM CLIENTS WHERE idclient=".$idClient;
	testVar($reqClient);
	$resultClient=$mysqli->query($reqClient);
	while ($rowClient = $resultcommanderesa->fetch_assoc())
	{
		$cheminot=$rowClient["cheminot"];
		$region=$rowClient["region"];
	}

	/**
	 * [$cheminot description]
	 * @var statutTarif -> statut du client
	 */
	if ($cheminot==1) {
		$statutTarif='CE';
		
		if ($region==1) {
			$statutTarif='CR';
		}
	}
	else {
		$statutTarif='EX';
	}

	/**
	 * [$infoResaOld description]
	 * recuperation des anciennes informations pour calculer la différence
	 */
	$infoResaOld="SELECT idgite, nb_adulte, nb_enfant, date_debut, date_fin FROM RESERVATION WHERE idreservation=".$idreservation;
	$resultResaOld=$mysqli->query($infoResaOld);
	while ($rowResaOld = $resultResaOld->fetch_assoc())
	{
		$nbAdulteOld =$rowResaOld["nb_adulte"];
		$nbEnfantOld =$rowResaOld["nb_enfant"];
		$dateDebOld  =$rowResaOld["date_debut"];
		$dateFinOld  =$rowResaOld["date_fin"];
		$idGiteOld   =$rowResaOld["idgite"];
	}

	$tarifOld         = calculTarif($dateDebOld,$dateFinOld,$idGiteOld,$statutTarif); // calcul ancien tarif

	$facteurEnfantOld = $nbEnfantOld;
	$facteurAdulteOld = $nbAdulteOld;
	
	$taxeAdulteOld    = calculTaxe ("adulte",$facteurAdulteOld,$idreservation,"I"); // calcul ancinnes taxes
	$taxeEnfantOld    = calculTaxe ("enfant",$facteurEnfantOld ,$idreservation,"I");
	$taxeOld          = $taxeAdulteOld + $taxeEnfantOld;
	
	/**
	 * [Post description]
	 * @var recuperation des modifications
	 */
	$nbAdulteModif    = $_POST['nb_adulte'];
	$nbEnfantModif    = $_POST['nb_enfant'];
	$dateDebutModif   = $_POST['date_debut'];
	$dateFinModif     = $_POST['date_fin'];
	$idGiteModif      = $_POST['idgite'];
	$commentaireModif = $_POST['commentaire'];

	/**
	 * [$reqUpdateResa description]
	 * Update des informations
	 */
	$reqUpdateResa = "	UPDATE RESERVATION 
						SET 	
								nb_adulte ='".$nbAdulteModif."', 
								nb_enfant ='".$nbEnfantModif."', 
								date_debut ='".$dateDebutModif."', 
								date_fin ='".$dateFinModif."', 
								idgite ='".$idGiteModif."', 
								commentaire ='".$commentaireModif."' 
						WHERE idreservation=".$idreservation."";
	$resultUpdateResa=$mysqli->query($reqUpdateResa);

	/**
	 *  MAj de la commande
	 */
	
	$reqInfoCo="SELECT total, total_paye, taxe FROM COMMANDE WHERE idcommande=".$idCommande;
	$resultInfoCo=$mysqli->query($reqInfoCo);
	while ($rowInfoCo = $resultInfoCo->fetch_assoc())
	{
		$total      = $rowInfoCo["total"];
		$taxeOld    = $rowInfoCo["taxe"];
	}

	/**
	 * [$tarifNew description]
	 * calcul des nouveaux tarifs
	 */
	$tarifNew      = calculTarif($dateDebutModif,$dateFinModif,$idGiteModif,$statutTarif); 
	
	$facteurEnfant = $nbEnfantModif;
	$facteurAdulte = $nbAdulteModif;
	
	$taxeAdulte    = calculTaxe ("adulte",$facteurAdulte,$idreservation,"I");
	$taxeEnfant    = calculTaxe ("enfant",$facteurEnfant ,$idreservation,"I");

	
	$taxe          = $taxeAdulte + $taxeEnfant;
	$diffTaxe      = $taxeOld - $taxe;
	$taxeMaj       = $taxeOld + $diffTaxe;
	$diffTarif     = $tarifOld - $tarifNew;

	/**
	 * [$taxeMaj description]
	 * 		Mise à jour de la taxe avec la différence
	 * [$totalMaj description]
	 * 		Mise à jour du total avec la différence
	 */
	$taxeMaj       = $taxeOld - ($diffTaxe);
	$totalMaj      = $tarifOld - ($diffTarif);

	$modifCommande="UPDATE COMMANDE SET total= '".$totalMaj."', taxe= '".$taxeMaj."' WHERE idcommande='".$idCommande."'";
	$mysqli->query($modifCommande);


	$actionResa='V'; // affichage des resa de la commande avec maj
}


	if (!empty($idResa))
	{
		$reqcommanderesa="SELECT idcommande, idreservation, idclient FROM COMMANDERESERVER WHERE idreservation=".$idResa ;
		testVar($reqcommanderesa);
		$resultcommanderesa=$mysqli->query($reqcommanderesa);
		while ($row = $resultcommanderesa->fetch_assoc())
		{
			$id_commande=$row["idcommande"];
			$id_client=$row["idclient"];
		}


		$reqlisteCommande="SELECT r.idreservation, r.idgite,r.nb_adulte, r.nb_enfant,r.date_debut,r.date_fin, r.statut, r.date_creation, r.commentaire
							FROM RESERVATION r, COMMANDERESERVER c 
							WHERE c.idcommande=".$id_commande." AND c.idreservation=r.idreservation";

		$resultreqlisteCommande=$mysqli->query($reqlisteCommande);
		
		$i=0;
			while ($rowlisteCommande = $resultreqlisteCommande->fetch_assoc())		
			{
					/**
					 * recuperation des informations des resas
					 */
					$info['idresa'][$i]      = $rowlisteCommande["idreservation"];
					$info['idgite'][$i]      = $rowlisteCommande["idgite"];
					$info['adulte'][$i]      = $rowlisteCommande["nb_adulte"];
					$info['enfant'][$i]      = $rowlisteCommande["nb_enfant"];
					$info['datedeb'][$i]     = $rowlisteCommande["date_debut"];
					$info['datefin'][$i]     = $rowlisteCommande["date_fin"];
					$info['statut'][$i]      = $rowlisteCommande["statut"];
					$info['datecrea'][$i]    = $rowlisteCommande["date_creation"];
					$info['commentaire'][$i] = $rowlisteCommande["commentaire"];

				if ($actionResa=='V') {

					/**
					 * tableau d'afficahge
					 */
					$affichage_resa_ligne.='<table><thead>
							<tr width="50px"><th>Num&eacute;ro de la reservation</th>
							<th width="50">Numéro de Gîte</th>
							<th width="50">Nombre d\'adultes</th>
							<th width="50">Nombre d\'enfants</th>
							<th width="50">Date d\'arrivée</th>
							<th width="50">Date de départ</th>
							<th width="50">Statut (Attente ou Payé)</th>
							<th width="50">Date de création</th>
							<th width="50">Commentaire</th>
							<th width="150" colspan="5">Action</th></tr>
							</thead>';

					/**
					 	* Déclaration variable des resa(s)
					 */


					$affichage_resa_ligne.= '<tr >
									<td>' . $info['idresa'][$i] . '</td>
									<td>' . $info['idgite'][$i] . '</td>
									<td>' . $info['adulte'][$i] . '</td>
									<td>' . $info['enfant'][$i] . '</td>
									<td>' . dateFR( $info['datedeb'][$i] ) .'</td>
									<td>' . dateFR( $info['datefin'][$i] ) .'</td>
									<td>' . $info['statut'][$i] .'</td>
									<td>' . dateFR( $info['datecrea'][$i] ) .'</td>
									<td>' . $info['commentaire'][$i] .'</td>';

					$affichage_resa_ligne.= '<td><a href="rechercheResa.php?actionResa=M&idcommande=' . $idCommande . '&idResa=' . $info['idresa'][$i] . '" title="Editer le statut"><i class="foundicon-edit"></i></a></td>
										<td><a href="rechercheResa.php?actionResa=S&editionCommande=D&idcommande=' . $idCommande . '&idResa=' . $info['idresa'][$i] . '" title="Annuler la commande" onclick="return confirm(\'Etes vous sure de la suppression de cette commande?\');"><i class="foundicon-remove"></i></a></td>
										<td><a href="rechercheResa.php?actionResa=R&idcommande=' . $idCommande . '" title="Voir le compte du client"><i class="foundicon-address-book"></i></a></td>
										</tr>';
					
					$affichage_resa_ligne.='</table>';

					$affichage_edition_ligne="";

					$i++;
				} 
			} // fin while
				if ($actionResa=='M') { // modification d'une resa

					$idResa = $_GET['idResa']; // selection de la resa en cours de modif

					$reqlisteCommandeModif="	SELECT r.idreservation, r.idgite,r.nb_adulte, r.nb_enfant,r.date_debut,r.date_fin, r.statut, r.date_creation, r.commentaire
										FROM RESERVATION r, COMMANDERESERVER c 
										WHERE c.idreservation=".$idResa." AND c.idreservation=r.idreservation";
					$resultreqlisteCommandeModif=$mysqli->query($reqlisteCommandeModif);
					
					$i=0;
					while ($rowlisteCommandeModif = $resultreqlisteCommandeModif->fetch_assoc())		
					{
						// affichage resa en cours de modification
						$info['idresa']      = $rowlisteCommandeModif["idreservation"];
						$info['idgite']      = $rowlisteCommandeModif["idgite"];
						$info['adulte']      = $rowlisteCommandeModif["nb_adulte"];
						$info['enfant']      = $rowlisteCommandeModif["nb_enfant"];
						$info['datedeb']     = $rowlisteCommandeModif["date_debut"];
						$info['datefin']     = $rowlisteCommandeModif["date_fin"];
						$info['statut']      = $rowlisteCommandeModif["statut"];
						$info['datecrea']    = $rowlisteCommandeModif["date_creation"];
						$info['commentaire'] = $rowlisteCommandeModif["commentaire"];

					$affichage_resa_ligne="";

					/**
					 * [$affichage_edition_ligne description]
					 * @var formulaire de modification
					 */
					$affichage_edition_ligne='
						<form action="rechercheResa.php?actionResa=U&idcommande='.$idCommande.'" method="POST">
							<table>
								<tr>
									<td>
										<label>Numéro de la r&eacute;servation
											<input name="idreservation" type="number" size="5" readonly value="' . $info['idresa'] . '">		
										</label>
									</td>
									<td>
										<label>Numéro du Gîte
											<input name="idgite" type="number" size="5" readonly value="' . $info['idgite'] . '">		
										</label>
									</td>
									<td>
										<label>Nombre d\'adultes
											<input name="nb_adulte" type="number" size="25" value="' . $info['adulte'] . '">													
										</label>
									</td>
									<td>
										<label>Nombre d\'enfants
											<input name="nb_enfant" type="number" size="5"  value="' . $info['enfant'] . '">		
										</label>
									</td>
									<td>
										<label>Date d\'ariv&eacute;e
											<input name="date_debut" type="date" size="5"  value="' . $info['datedeb'] . '">													
										</label>
									</td>
									<td>
										<label>Date d&eacute;part
												<input name="date_fin" type="date" size="5"  value="' . $info['datefin'] . '">		
										</label>
									</td>
									<td>
										<label>Statut
											<input name="statut" type="text" size="5" readonly value="' . $info['statut'] . '">	
										</label>
									</td>
									<td>
										<label>Date de cr&eacute;ation
											' . dateFR( $info['datecrea'] ) . '		
										</label>
									</td>
									<td>
										<label>Commentaire
											<input name="commentaire" type="textarea"  value="' . $info['commentaire'] . '">	
										</label>
									</td>
									<td>
										<label>Enregistrer les modifications
											<input src="images/save.gif" title="Enregistrer" type="image" name="envoi" value="submit">
										</label>
									</td>
								</tr>
							</table>
						</form>';

					$i++;
					} // fin while

				}

				$messageSupp = "";

				if ($actionResa=='S') {

					$idResa = $_GET['idResa']; // selection de la resa en cours de modif
					
					$affichage_resa_ligne .=""; // plus d'affichage de tableau

					/**
					 *  DELETE reservation + maj commande
					 */

					$suppCommandeReserver="DELETE FROM COMMANDERESERVER WHERE idcommande='" . $idCommande . "' AND idreservation='" . $idResa . "'";
					$mysqli->query($suppCommandeReserver);

					$suppFixerTaxe="DELETE FROM FIXERTAXE WHERE idreservation='" . $idResa . "'";
					$mysqli->query($suppFixerTaxe);

					$suppChoixOption="DELETE FROM CHOIXOPTION WHERE idreservation='" . $idResa . "'";
					$mysqli->query($suppChoixOption);

					$suppReservation="DELETE FROM RESERVATION WHERE idreservation='" . $idResa . "'";
					$mysqli->query($suppReservation);
					
					/**
					 * Maj de la commande
					 */
					
							/**
							 *  si seul resa supp
							 *  sinon maj comme update
							 */




					$messageSupp = "La r&eacute;servation a &eacute;t&eacute; supprim&eacute;e.";

				}
}			
?>
	
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<h1> R&eacute;servation</h1>
	
			</div>
		</div>		
	</div>
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<?= $messageSupp ; ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
				<h3> R&eacute;sultat</h3>
				<?= $affichage_resa_ligne; ?>
				<?= $affichage_edition_ligne; ?>
		</div>		
	</div>

<?php		
	require('includes/footer.php');
?>