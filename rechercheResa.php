<?php

require('includes/header.php');

/*************************************
Affiche les infos d'une r�servation
**************************************/


$actionResa=$_GET['actionResa'];

$idCommande=$_GET['idcommande'];

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
	$nbAdulteModif    = $_POST['nb_adulte'];
	$nbEnfantModif    = $_POST['nb_enfant'];
	$dateDebutModif   = $_POST['date_debut'];
	$dateFinModif     = $_POST['date_fin'];
	$idGiteModif      = $_POST['idgite'];
	$commentaireModif = $_POST['commentaire'];


	$reqUpdateResa = "	UPDATE RESERVATION 
						SET 	
								nb_adulte ='".$nbAdulteModif."', 
								nb_enfant ='".$nbEnfantModif."', 
								date_debut ='".$dateDebutModif."', 
								date_fin ='".$dateFinModif."', 
								idgite ='".$idGiteModif."', 
								commentaire ='".$commentaireModif."' 
						WHERE idreservation=".$idreservation."";
	testVar($reqUpdateResa);
	$resultUpdateResa=$mysqli->query($reqUpdateResa);
}



?>

	<div class="row">
		<div class="small-11 small-centered columns">	
			<?php
			if (!empty($idResa))
			{
				$reqcommanderesa="SELECT idcommande, idreservation, idclient FROM COMMANDERESERVER WHERE idreservation=".$idResa ;
				$resultcommanderesa=$mysqli->query($reqcommanderesa);
				while ($row = $resultcommanderesa->fetch_assoc())
				{
					$id_commande=$row["idcommande"];
					$id_client=$row["idclient"];
				}


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

				$reqlisteCommande="SELECT r.idreservation, r.idgite,r.nb_adulte, r.nb_enfant,r.date_debut,r.date_fin, r.statut, r.date_creation, r.commentaire
									FROM RESERVATION r, COMMANDERESERVER c 
									WHERE c.idcommande=".$id_commande." AND c.idreservation=r.idreservation";
				$resultreqlisteCommande=$mysqli->query($reqlisteCommande);
				
				while ($rowlisteCommande = $resultreqlisteCommande->fetch_assoc())		
				{
					
					if ($actionResa==='V') {
						
						$affichage_resa_ligne.= '<tr >
										<td>'.$rowlisteCommande["idreservation"].'</td>
										<td>'.$rowlisteCommande["idgite"].'</td>
										<td>'.$rowlisteCommande["nb_adulte"].'</td>
										<td>'.$rowlisteCommande["nb_enfant"].'</td>
										<td>'.dateFR($rowlisteCommande["date_debut"]).'</td>
										<td>'.dateFR($rowlisteCommande["date_fin"]).'</td>
										<td>'.$rowlisteCommande["statut"].'</td>
										<td>'.dateFR($rowlisteCommande["date_creation"]).'</td>
										<td>'.$rowlisteCommande["commentaire"].' </td>';

						$affichage_resa_ligne.= '<td '.$couleurCommande.'><a href="rechercheResa.php?actionResa=M&idcommande='.$idCommande.'" title="Editer le statut"><i class="foundicon-edit"></i></a></td>
											<td><a href="rechercheResa.php?actionResa=R&editionCommande=E&idcommande='.$row["idcommande"].'" title="Modification de la reservation" ><i class="foundicon-add-doc"></i></a></td>
											<td><a href="rechercheResa.php?actionResa=R&editionCommande=D&idcommande='.$row["idcommande"].'" title="Annuler la commande" onclick="return confirm(\'Etes vous sure de la suppression de cette commande?\');"><i class="foundicon-remove"></i></a></td>
											<td><a href="rechercheResa.php?actionResa=R&idcommande='.$idCommande.'" title="Voir le compte du client"><i class="foundicon-address-book"></i></a></td>
											</tr>';
						
						$affichage_resa_ligne.='</table>';

						$affichage_edition_ligne="";
					}
					else if ($actionResa=='M') {

						$affichage_resa_ligne="";

						$affichage_edition_ligne='
							<form action="rechercheResa.php?actionResa=U&idcommande='.$idCommande.'" method="POST">
								<table>
									<tr>
										<td>
											<label>Numéro de la r&eacute;servation
												<input name="idreservation" type="number" size="5" readonly value="'.$rowlisteCommande["idreservation"].'">		
											</label>
										</td>
										<td>
											<label>Numéro du Gîte
												<input name="idgite" type="number" size="5" readonly value="'.$rowlisteCommande["idgite"].'">		
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Nombre d\'adultes
												<input name="nb_adulte" type="number" size="25" value="'.$rowlisteCommande["nb_adulte"].'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Nombre d\'enfants
												<input name="nb_enfant" type="number" size="5"  value="'.$rowlisteCommande["nb_enfant"].'">		
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Date d\'ariv&eacute;e
												<input name="date_debut" type="date" size="5"  value="'.$rowlisteCommande["date_debut"].'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Date d&eacute;part
													<input name="date_fin" type="date" size="5"  value="'.$rowlisteCommande["date_fin"].'">		
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Statut
												<input name="statut" type="text" size="5" readonly value="'.$rowlisteCommande["statut"].'">	
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Date de cr&eacute;ation
												'.$rowlisteCommande["date_creation"].'		
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Commentaire
												<input name="commentaire" type="textarea"  value="'.$rowlisteCommande["commentaire"].'">	
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Enregistrer les modifications
												<input src="images/save.gif" title="Enregistrer" type="image" name="envoi" value="submit">
											</label>
										</td>
									</tr>
								</table>
							</form>';

					}

					$messageSupp = "";

					if (actionResa=='S') {
						
						$affichage_resa_ligne .="";

						/**
						 *  DELETE reservation + maj commande
						 */


						$messageSupp          = "La r&eacute;servation a &eacute;t&eacute; supprim&eacute;e.";

					}
				} // fin while		

		}			
		?>
	

	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<?= $messageSupp ; ?>

			
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<h1> Recherche des r&eacute;servation</h1>
	
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