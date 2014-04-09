<?php

require('includes/header.php');
	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
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
// fin de la boucle pour les statuts


/*************************************************************
* Traitement des diff�rentes actions possible sur cette page *
* avec le parame�tre actionCommande							 *
**************************************************************/

    // differentes valeurs de la variable actionCommande passee en argument 
	// vide : on affiche un formulaire de recherche de commandes
	// R : on affiche les r�sultat
	// Z : action par défaut via le lien du menu
	// S : édition du statut de la commande + maj somme
		// US update statut et sommes
	// E : édition de la commande + maj
		// UE update base + sommes
	// M : mail de rappel
	// D : annulation	

	if (!empty($_GET["actionCommande"]))
	{
		$actionCommande=$_GET["actionCommande"];
	
		//on regarde si on a un idcommande en parametre
		if (!empty($_GET["idcommande"])) { // var de l'url
			
			$idcommande=$_GET["idcommande"];
		}
		else if (!empty($_POST["idcommande"])) { // sinon on utilise la method post (form)
			
			$idcommande=$_POST["idcommande"];
		}
		else { // sinon on utilise pas l'idcommande
			$idcommande="";
		}
		
		
		if($_GET["email"]) { // var de l'url (rechercheClient)
		
			$email=$_GET["email"];
		}
		else {
		
			$email=$_POST["email"];
		}

					$editionCommande=$_GET["editionCommande"]; // action sur la commande (statut/edition/mail/delete)
				
					if(!empty($_GET["editionCommande"])){
					
						switch ($editionCommande) 
						{
							case "US": //Update après édition statut
									
									
									$modifStatut="UPDATE COMMANDE SET statut_facture='".$_POST['statutModif']."' WHERE idcommande='".$idcommande."'";
									$mysqli->query($modifStatut);
									
									/**
										* modification des prix selon statut
									*/
									
									switch($_POST['statutModif']) {
										case 0: // annulee
										
											$modifPrix="UPDATE COMMANDE SET total_paye=0, accompte_paye=0 WHERE idcommande='".$idcommande."'";
											$mysqli->query($modifPrix);
										
										break; 
										
										case 1: // Attente accompte
										
											$modifPrix="UPDATE COMMANDE SET caution_paye='A', total_paye=0,accompte_paye=0 WHERE idcommande='".$idcommande."'";
											$mysqli->query($modifPrix);
										
										break;
										
										case 2: // accompte paye
										
											$modifPrix="UPDATE COMMANDE SET caution_paye='P', accompte_paye=1, total_paye=accompte WHERE idcommande='".$idcommande."'";				
											$mysqli->query($modifPrix);
										
										break;
										
										case 3: // total paye
										
											$modifPrix="UPDATE COMMANDE SET caution_paye='P', accompte_paye=1, total_paye=total WHERE idcommande='".$idcommande."'";				
											$mysqli->query($modifPrix);
										
										break;
										
										case 4: // caution paye
										
											$modifPrix="UPDATE COMMANDE SET caution_paye='P' WHERE idcommande='".$idcommande."'";
											$mysqli->query($modifPrix);
										
										break;
										
										case 5: // caution rendu
										
											$modifPrix="UPDATE COMMANDE SET caution_paye='R' WHERE idcommande='".$idcommande."'";
											$mysqli->query($modifPrix);
										
										break;

									}
									
								/**
	
									!!* EVM envoi mail + template!!
								*/		

							break;
							
							case "UE": //Update après édition commande
									
									/**
										* édition des prix et statuts
									*/
									
									$modifCommande="UPDATE COMMANDE SET caution= '".$_POST['cautionCommande']."',caution_paye= '".$_POST['cautionPayeCommande']."', accompte= '".$_POST['accompteCommande']."', accompte_paye= '".$_POST['accomptePayeCommande']."', total= '".$_POST['totalCommande']."', total_paye= '".$_POST['totalPayeCommande']."', statut_facture= '".$_POST['statutModif']."' WHERE idcommande='".$idcommande."'";

									$mysqli->query($modifCommande);
											
									/**
	
										!!* EVM envoi mail + template!!
									*/	
											
							break;
							
							case "M": // mail client pour rappel
									
									$recupMailClient="SELECT cr.idclient, c.email FROM COMMANDERESERVER cr, CLIENTS c WHERE  idcommande='".$idcommande."' AND cr.idclient=c.idclient";
									$result_recupMailClient=$mysqli->query($recupMailClient);

									while ($row_recupMailClient = $result_recupMailClient->fetch_assoc())
									{					
										$mailClient=$row_recupMailClient["email"]; // recuperation du mail client
									}
									
									/**
	
										!!* EVM envoi mail + template!!
									*/	
								
									//envoiMail2('sdk@cesncf-stra.org', 'test','test');
							break;
							
							case "D": //suppression de la commande
								
									$recupResa="SELECT idreservation FROM COMMANDERESERVER WHERE idcommande='".$idcommande."'";
									$result_recupResa=$mysqli->query($recupResa);
									$i=0;
									while ($row_recupResa = $result_recupResa->fetch_assoc())
									{					
										$idresa[$i]=$row_recupResa["idreservation"]; // recuperation des id réservation

									/**
										* Suppression des données de la commande et des reservation (selon l'ordre des clés étrangères)
									*/
										$suppCommandeReserver="DELETE FROM COMMANDERESERVER WHERE idcommande='".$idcommande."' AND idreservation='".$idresa[$i]."'";
										$mysqli->query($suppCommandeReserver);

										$suppFixerTaxe="DELETE FROM FIXERTAXE WHERE idreservation='".$idresa[$i]."'";
										$mysqli->query($suppFixerTaxe);

										$suppChoixOption="DELETE FROM CHOIXOPTION WHERE idreservation='".$idresa[$i]."'";
										$mysqli->query($suppChoixOption);

										$suppReservation="DELETE FROM RESERVATION WHERE idreservation='".$idresa[$i]."'";
										$mysqli->query($suppReservation);


										$i++; // incrementation en cas de multi resa
									}
									
									/**
										* Suppression de la commande
									*/
									$suppCommande="DELETE FROM COMMANDE WHERE idcommande='".$idcommande."'";
									$mysqli->query($suppCommande);
									
									if($mysqli) { // message succès
										$MessageEdition = "La commande a &eacute;t&eacute; supprim&eacute;e";
										$messageTableauSupp = "La commande n'existe plus"; // remplace l'affichage du tableau de la commande 
										
										/**
	
											!!* EVM envoi mail + template!!
										*/	
									} 
									else {$MessageEdition = "Erreur lors de la suppression";}
							
							break;

							case "R": // remise
									
									$recupComm="SELECT remise_taux FROM COMMANDE WHERE  idcommande='".$idcommande."'";
									$result_recupComm=$mysqli->query($recupComm);
									$remise = $_GET["remise"];
									$modifCommande="UPDATE COMMANDE SET remise_taux=".$remise." WHERE idcommande='".$idcommande."'";
									$result_modifCommande=$mysqli->query($modifCommande);
							break;
						}
					} 
		
		switch ($actionCommande) 
		{

			case "R": //rechercher de commandes
			
			$reqCommandeResa="SELECT distinct CO.idcommande,  CM.idclient, C.nom, C.prenom, CO.taxe, CO.caution, CO.caution_paye, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, CO.total,CO.total_paye  
					FROM COMMANDE CO, COMMANDERESERVER CM, CLIENTS C
					WHERE CM.idclient=C.idclient and CO.idcommande=CM.idcommande";
			
					 if ((isset($_POST["statut_facture"])) and (($_POST["statut_facture"])<10) )
					 {
						$reqCommandeResa.=" and CO.statut_facture=".$_POST["statut_facture"];
						$affichage_commande_ligne='<p> Affichage des commandes avec le statut ' . $statut[(int)$_POST["statut_facture"]]["designation"].'</p>';
					 }

					if (!empty($email) and (!empty($_POST["nom"])))
					{
						$reqCommandeResa.=" and C.email like '".$email."' and C.nom like '%".$_POST["nom"]."%'";
					}
					 else
					{
						if (!empty($_POST["nom"]) ) 					 
						{
							$reqCommandeResa.=" and C.nom like '%".$_POST["nom"]."%'";
						}
						else
						{
							if (!empty($email))
							{
								$reqCommandeResa.=" and C.email like '".$email."'";
							}
							else
							{
								if (!empty($idcommande))  {$reqCommandeResa.=" and CO.idcommande like '".$idcommande."'";}
							}
						}
					}
			$reqCommandeResa.=" order by CO.statut_facture ";
			
				$result_reqCommandeResa=$mysqli->query($reqCommandeResa); /* execution req recherche commande*/
				/**
					* Dev si requete est nul -> message
				*/
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r&eacute;sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="R&eacute;sultat de la recherche : ";
				}			
				//Boucle qui parcourt les clients dans la base de donn�es
					
					
				break;
				
			case "Z": //raffcihe les 20 derni�res commandes
			// $reqCommandeResa="SELECT distinct CO.idcommande,  CM.idclient, C.nom, CO.taxe, CO.caution, CO.caution_paye, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, CO.total,CO.total_paye  
			// 		FROM COMMANDE CO, COMMANDERESERVER CM, CLIENTS C
			// 		WHERE CM.idclient=C.idclient and CO.idcommande=CM.idcommande and CO.idcommande > '((SELECT max(idcommande) FROM COMMANDE)-20)'";
			// ancienne requête
			$reqCommandeResa="SELECT distinct CO.idcommande,  CM.idclient, C.nom, C.prenom, CO.taxe, CO.caution, CO.caution_paye, 
				CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, 
				CO.total, CO.total_paye, G.nom as nom_gite, G.idgite, R.date_debut, R.date_fin, CO.remise_taux
				FROM COMMANDE CO, COMMANDERESERVER CM, CLIENTS C, GITE G, RESERVATION R
				WHERE CM.idclient=C.idclient 
				AND CO.idcommande=CM.idcommande
				AND R.idreservation=CM.idreservation
				AND G.idgite=R.idgite
				AND CO.idcommande > '((SELECT max(idcommande) FROM COMMANDE)-20)'
				ORDER BY CO.date_creation, CO.statut_facture";
			$result_reqCommandeResa=$mysqli->query($reqCommandeResa);
			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Pas de r&eacute;sultat pour cette recherche" ;  
			} 
			else
			{
				$MessageAction="Affichage des 20 derni&egrave;res commandes en cours : ";
			}
			break;		
				
		} // fin switch


		if ((strcmp($actionCommande,'R')==0) or (strcmp($actionCommande,'Z')==0) or (strcmp($editionCommande,'S')==0) or (strcmp($editionCommande,'E')==0) or (strcmp($editionCommande,'M')==0) or (strcmp($editionCommande,'US')==0) or (strcmp($editionCommande,'UE')==0))
		{
					
		/**		
			* Developpement de l'affichage des réservations d'une commande
		*/
			
		// Creation du tableau pour afficher les clients
				$affichage_commande_ligne.='<table><thead>
								<th width="100px">Numéro de la Commande</th>
								<th width="50px">Nom et numéro du gîte</th>
								<th width="50px">Periode de reservation</th>
								<th width="50px">Date de Commande</th>
								<th width="50px">Nom</th>
								<th width="50px">Statut</th>
								<th width="50px" data-tooltip class="has-tip" title="A:attente/P:Payé/R:Rendu">Caution</th>
								<th width="50px">Accompte</th>
								<th width="50px">Remise</th>
								<th width="50px">Total</th>
								<th width="50px">Total payé</th>
								<th width="150px" colspan="6">Action</th></tr>
								</thead>';

			//boucle qui parcourt le résultats des requetes demandées dans la BD

			while ($row = $result_reqCommandeResa->fetch_assoc())
			{

				if ($editionCommande=='UE' || $editionCommande=='US') { // mise à jour couleur pour update
					
					switch ((int)$_POST['statutModif'])
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
				}
				else {
					
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
				}
				
				$couleurCommande='style=" border:2px solid '.$couleurStatut.';"';
				if ($row["accompte_paye"] == 0)
					$accompte_paye_symbole = '<i data-tooltip class="foundicon-error has-tip" title="Acompte non payé" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';
				else
					$accompte_paye_symbole = '<i data-tooltip class="foundicon-checkmark has-tip" title="Acompte payé" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';

				if($editionCommande=='E') { // changement des informations de la commande (utilisation d'input)
					
						/** 
							* stockages des sélecteurs pour les booleans  
						*/

						$result=count($statut);
						$a=0;
						
						$majStatut='<select name ="statutModif"><option selected="'.$statut[(int)$row["statut_facture"]]["designation"].'" value="'.$statut[(int)$row["statut_facture"]]["designation"].'">'.$statut[(int)$row["statut_facture"]]["designation"].'</option>';
						while ($a<$result)
						{
							$majStatut.='<option value="'.(int)$a.'">'.$statut[(int)$a]["designation"].'</option>';
							$a++;
						}
							
						$cautionSelect='<select name ="cautionPayeCommande"><option selected="'.$row["caution_paye"].'" value="'.$row["caution_paye"].'">'.$row["caution_paye"].'</option>';

						$cautionSelect.='<option value="A">A (attente)</option>
										<option value="P">P (paye)</option>
										<option value="R">R (rendu)</option>';
										
						$accompteSelect='<select name ="accomptePayeCommande"><option selected="'.$row["accompte_paye"].'" value="'.$row["accompte_paye"].'">Pay&eacute; ou non</option>';

						$accompteSelect.='<option value="1">oui</option>
										<option value="0">non</option>';

						/**
							* formulaire de modification
						*/
						
						$affichage_edition_ligne='
							<form action="rechercheCommande.php?actionCommande=R&editionCommande=UE&idcommande='.$row["idcommande"].'" method="POST">
								<table>
									<tr>
										<td '.$couleurCommande.'>
											<label>Numéro de la commande
												<input name="idtaxe" type="text" size="5" readonly value="'.$row["idcommande"].'">													</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Date
												<input name="dateCommande" type="date" size="25"  readonly value="'.dateFr($row["date_creation"]).'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Nom et pr&eacute;nom du client
												<input name="infoClient" type="text" size="5"  readonly value="'.$row["nom"].' '.$row["prenom"].'">													
												</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Statut de la facture
												'.$majStatut.'												
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Caution
												<input name="cautionCommande" type="text" size="5"  value="'.$row["caution"].'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Caution pay&eacute;e
													'.$cautionSelect.'
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Accompte
												<input name="accompteCommande" type="number" size="5"  value="'.$row["accompte"].'">												</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Accompte payé
												'.$accompteSelect.'		
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Total (montant du total)
												<input name="totalCommande" type="number" size="5"  value="'.$row["total"].'">														</label>
										</td>
										<td '.$couleurCommande.'>
											<label>Total pay&eacute; (somme payée)
												<input name="totalPayeCommande" type="number" size="5"  value="'.$row["total_paye"].'">													</label>
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
				else if($editionCommande=='S') { // changement de statut
										
										
									$result=count($statut);
									$a=0;

									/* formulaire de modification du statut*/
									$majStatut='<form action="rechercheCommande.php?actionCommande=R&editionCommande=US&idcommande='.$row["idcommande"].'" method="POST"><table><tr><td>';
									$majStatut.='<select name ="statutModif"><option selected="'.$statut[(int)$row["statut_facture"]]["designation"].'" value="'.$statut[(int)$row["statut_facture"]]["designation"].'">'.$statut[(int)$row["statut_facture"]]["designation"].'</option>';
									while ($a<$result)
									{
										$majStatut.='<option value="'.(int)$a.'">'.$statut[(int)$a]["designation"].'</option>';
										$a++;
									}
									$majStatut.='</select><input type="submit" value="Modifier"></td></tr></table></form>';

					$affichage_commande_ligne.= '<tr >
									<td '.$couleurCommande.'>'.$row["idcommande"].'</td>
									<td '.$couleurCommande.'>'.$row["nom_gite"].'('.$row["idgite"].')</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_debut"]).' - '.dateFr($row["date_fin"]).'</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_creation"]).'</td>
									<td '.$couleurCommande.'>'.$row["nom"].' '.$row["prenom"].'</td>
									<td '.$couleurCommande.'>'.$majStatut.'</td>
									<td data-tooltip class="has-tip" title="A:attente/P:Payé/R:Rendu" '.$couleurCommande.'>('.$row["caution_paye"].') '.$row["caution"].' &euro;</td>									
									<td '.$couleurCommande.'>'.$accompte_paye_symbole.'</td>
									<td '.$couleurCommande.'>'.$row["remise_taux"].' %</td>
									<td '.$couleurCommande.'>'.$row["total"].' &euro;</td>
									<td '.$couleurCommande.'>'.$row["total_paye"].' &euro;</td>';

											
				}
				else if ($editionCommande=='UE' || $editionCommande=='US') { // 	affichage après update

					$affichage_commande_ligne.= '<tr >
									<td '.$couleurCommande.'>'.$row["idcommande"].'</td>
									<td '.$couleurCommande.'>'.$row["nom_gite"].'('.$row["idgite"].')</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_debut"]).' - '.dateFr($row["date_fin"]).'</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_creation"]).'</td>
									<td '.$couleurCommande.'>'.$row["nom"].' '.$row["prenom"].'</td>
									<td '.$couleurCommande.'>'.$statut[(int)$row["statut_facture"]]["designation"].'</td>
									<td data-tooltip class="has-tip" title="A:attente/P:Payé/R:Rendu" '.$couleurCommande.'>('.$row["caution_paye"].') '.$row["caution"].' &euro;</td>
									<td '.$couleurCommande.'>'.$accompte_paye_symbole.'</td>
									<td '.$couleurCommande.'>'.$row["remise_taux"].' %</td>
									<td '.$couleurCommande.'>'.$row["total"].' &euro;</td>
									<td '.$couleurCommande.'>'.$row["total_paye"].' &euro;</td>';

				}
				else {
					
					$affichage_commande_ligne.= '<tr >
									<td '.$couleurCommande.'><a href="rechercheResa.php?idcommande='.$row["idcommande"].'&actionResa=V">'.$row["idcommande"].'</a></td>
									<td '.$couleurCommande.'>'.$row["nom_gite"].'('.$row["idgite"].')</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_debut"]).' - '.dateFr($row["date_fin"]).'</td>
									<td '.$couleurCommande.'>'.dateFr($row["date_creation"]).'</td>
									<td '.$couleurCommande.'>'.$row["nom"]." ".$row["prenom"].'</td>
									<td '.$couleurCommande.'>'.$statut[(int)$row["statut_facture"]]["designation"].'</td>
									<td data-tooltip class="has-tip" title="A:attente/P:Payé/R:Rendu" '.$couleurCommande.'>('.$row["caution_paye"].') '.$row["caution"].' &euro;</td>
									<td '.$couleurCommande.'>'.$accompte_paye_symbole.'</td>
									<td '.$couleurCommande.'>'.$row["remise_taux"].' %</td>
									<td '.$couleurCommande.'>'.$row["total"].' &euro;</td>
									<td '.$couleurCommande.'>'.$row["total_paye"].' &euro;</td>';
				}										
			$affichage_commande_ligne.= '<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=S&idcommande='.$row["idcommande"].'" title="Editer le statut"><i class="foundicon-edit"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=E&idcommande='.$row["idcommande"].'" title="Modification de la commande" ><i class="foundicon-add-doc"></i></a></td>
										<td '.$couleurCommande.'><a  title="Editer les remises" onclick="remise_taux('.((double)$row["total"]-$row["taxe"]).','.$row["idcommande"].')" ><i class="foundicon-heart"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=M&idcommande='.$row["idcommande"].'" title="Rappel mail de la commande" ><i class="foundicon-mail"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=D&idcommande='.$row["idcommande"].'" title="Annuler la commande" onclick="return confirm(\'Etes vous sure de la suppression de cette commande?\');"><i class="foundicon-remove"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheClient.php?actionClient=R&idcommande='.$row["idcommande"].'" title="Voir le compte du client"><i class="foundicon-address-book"></i></a></td>
										</tr>';
			}		
			
			$affichage_commande_ligne.='</table>';
		
			if($editionCommande=='D') { // la commande n'existe plus car supp
					$affichage_commande_ligne=$messageTableauSupp;
			}
			else if($editionCommande=='E') {
					$affichage_commande_ligne=$affichage_edition_ligne;
			}
			else if ($editionCommande=='UE'){
					$messageAvertissement= '<span class="label [radius round]">Ces données doivent être saisis avec précisions et être cohérentes</span>';
			}	
		}
	}
if (!empty($MessageAction))
{
	$MessageAction='<span class="label [radius round]">'.$MessageAction.'</span>';
}
/**
		*	affichages des Commandes stockees dans la base	
*/

$result=count($statut);

$affichage_recherche.='<form action="rechercheCommande.php?actionCommande=R" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="port">Num�ro de commande: </label><input id="idcommande" name="idcommande" type="int">
			<label for="statut_facture">Statut de la facture: </label><select name="statut_facture">';
$a=0; //compteur pour le parcourt du tableau
	$affichage_recherche.='<option selected="selected" value="10">Tout statut</option>';
while ($a<$result)
{
	$affichage_recherche.='<option value="'.(int)$a.'">'.$statut[(int)$a]["designation"].'</option>';
	$a++;
}
$affichage_recherche.='</select><input type="submit" value="Rechercher"></form>';

/**
 * RECHERCHE DES TRANSACTIONS
 */

$recupTransaction="SELECT idtransaction, type_transaction, txn_id, date_transaction, motif, reference FROM TRANSACTION WHERE  idcommande='".$idcommande."'";
$resultTransaction=$mysqli->query($recupTransaction);

while ($rowTransaction = $resultTransaction->fetch_assoc())
{					
	 // stockage des transaction des la commande
	 $affichage_transaction_ligne = '<form action="rechercheCommande.php?actionCommande=R&editionCommande=UE&idcommande='.$rowTransaction["idtransaction"].'" method="POST">
								<table>
									<thead>
										<tr width="50px"><th>Num&eacute;ro de la transaction</th>
										<th width="50">Identifiant Paypal</th>
										<th width="50">Date</th>
										<th width="50">Motif</th>
										<th width="50">R&eacute;f&eacute;rence</th>
										<th width="150" colspan="5">Action</th></tr>
									</thead>
									<tr>
										<td '.$couleurCommande.'>
											<label>
												<input name="type_transaction" type="text" size="5" readonly value="'.$rowTransaction["type_transaction"].'">													</label>
										</td>
										<td '.$couleurCommande.'>
											<label>
												<input name="txn_id" type="date" size="25"  readonly value="'.$rowTransaction["txn_id"].'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>
												<input name="date_transaction" type="text" size="5"  readonly value="'.dateFr($rowTransaction["date_transaction"]).'">													
												</label>
										</td>
										<td '.$couleurCommande.'>
											<label>
												<input name="motif" type="text" size="5"  readonly value="'.dateFr($rowTransaction["motif"]).'">													
											</label>
										</td>
										<td '.$couleurCommande.'>
											<label>
												<input name="reference" type="text" size="5"  value="'.$rowTransaction["reference"].'">													
											</label>
										</td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=S&idcommande='.$row["idcommande"].'" title="Editer le statut"><i class="foundicon-edit"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=E&idcommande='.$row["idcommande"].'" title="Modification de la commande" ><i class="foundicon-add-doc"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=M&idcommande='.$row["idcommande"].'" title="Rappel mail de la commande" ><i class="foundicon-mail"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=R&editionCommande=D&idcommande='.$row["idcommande"].'" title="Annuler la commande" onclick="return confirm(\'Etes vous sure de la suppression de cette commande?\');"><i class="foundicon-remove"></i></a></td>
										<td '.$couleurCommande.'><a href="rechercheClient.php?actionClient=R&idcommande='.$row["idcommande"].'" title="Voir le compte du client"><i class="foundicon-address-book"></i></a></td>
									</tr>
								</table>
							</form>';
}


?>

	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
			
				<?= $MessageAction; ?>
				<?= $MessageEdition; ?>
				<?= $messageAvertissement; ?>
			
			</div>
		</div>
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
 -->				<h3> R&eacute;sultat</h3>
				<?= $affichage_commande_ligne; ?>
		<!-- </div> -->		
<!-- 	</div> -->
	<div class="row">
		<div class="large-12 columns">
				<h3> Transaction</h3>
				<?= $affichage_transaction_ligne; ?>
		</div>		
	</div>


<?php
	require('includes/footer.php');
?>
<script>
function remise_taux(somme_ht,id){
	var saisie = prompt("Le total hors taxes s'élève à "+somme_ht+"€, quelle remise (en %) voulez-vous appliquer à cette commande ?");
	document.location = 'rechercheCommande.php?actionCommande=Z&editionCommande=R&idcommande='+id+'&remise='+saisie;
}	
</script>