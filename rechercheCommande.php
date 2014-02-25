<?php

//recherche des commandes

//version: 1.0
//
//creation: 27/01/2014


/* NOTE ////

	****	
	
		* Faire le lien sur le main
		* édition complete
		* css tableau et form ?
	
	****
	
*/


	require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		REcherche des commande  des gites     *
**********************************************/
// requete qui permet de charge la liste des status des commandes dans un tableau
$reqStatutCommande="select idstatut, designation 
					from STATUTCOMMANDE";
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
	
	
	if (!empty($_GET["actionCommande"]))
	{
			$actionCommande=$_GET["actionCommande"];

		
		//on regarde si on a un idcommande en parametre
		if (!empty($_GET["idcommande"]))
		{
			$idcommande=$_GET["idcommande"];
		}

		switch ($actionCommande) 
		{
			case "R": //rechercher de commande
			
			$reqCommandeResa="select distinct CO.idcommande,  CM.idclient, C.nom, CO.taxe, CO.caution, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, CO.total,CO.total_paye  
					from COMMANDE CO, COMMANDERESERVER CM, CLIENTS C
					where CM.idclient=C.idclient and CO.idcommande=CM.idcommande ";
	
						 if ((isset($_POST["statut_facture"])) and (($_POST["statut_facture"])<10) )
						 {
							$reqCommandeResa.=" and CO.statut_facture=".$_POST["statut_facture"];
							$affichage_commande_ligne='<p> Affichage des commandes avec le statut ' . $statut[(int)$_POST["statut_facture"]]["designation"].'</p>';
						 }
						 
						if (!empty($_POST["email"]) and (!empty($_POST["nom"])))
						{
							$reqCommandeResa.=" and C.email like '".$_POST["email"]."' and C.nom like '%".$_POST["nom"]."%'";
						}
						 else
						{
							if (!empty($_POST["nom"]) ) 					 
							{
								$reqCommandeResa.=" and C.nom like '%".$_POST["nom"]."%'";
							}
							else
							{
								if (!empty($_POST["email"]))
								{
									$reqCommandeResa.=" and C.email like '".$_POST["email"]."'";
								}
								else
								{
									if (!empty($_POST["idcommande"]))  $reqCommandeResa.=" and CO.idcommande like '".$_POST["idcommande"]."'";
								}
							}
						}
			$reqCommandeResa.=" order by CO.statut_facture ";
		
				$result_reqCommandeResa=$mysqli->query($reqCommandeResa);
				/**
					* Dev si requete est nul -> message
				*/
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r�sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="Resultat de la recherche : ";
				}
							
				//Boucle qui parcourt les clients dans la base de donn�es
				
			
				break;
			case "Z": //raffcihe les 20 derni�res commandes
			$reqCommandeResa="select distinct CO.idcommande,  CM.idclient, C.nom, CO.taxe, CO.caution, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, CO.total,CO.total_paye  
					from COMMANDE CO, COMMANDERESERVER CM, CLIENTS C
					where CM.idclient=C.idclient and CO.idcommande=CM.idcommande and CO.idcommande > ((select max(idcommande) from COMMANDE)-20)";
				$result_reqCommandeResa=$mysqli->query($reqCommandeResa);
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r�sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="Affichage des 20 derni�res commandes en cours : ";
				}
			break;			
					
		}
		
		if ((strcmp($actionCommande,'R')==0) or (strcmp($actionCommande,'Z')==0) or (strcmp($actionCommande,'TN')==0) or (strcmp($actionCommande,'TP')==0) or (strcmp($actionCommande,'TT')==0) or (strcmp($actionCommande,'MDP')==0))
		{
					
		// Creation du tableau pour afficher les clients
				$affichage_commande_ligne.='<table><thead>
								<tr><th>IdCommande</a></th>
								<th>Date de Commande</th>
								<th>Nom</th>
								<th>Statut</th>
								<th>Accompte</th>
								<th>Accompte pay�</th>
								<th>Total � payer</th>
								<th colspan="4">Action</th></tr>
								</thead>';
								
			//boucle qui parcourt le r�sultats des requetes demand�es dans la BD
			while ($row = $result_reqCommandeResa->fetch_assoc())
			{
				switch ((int)$row["statut_facture"])
				{
					case 0 : $couleurStatut='#000000';
					break;
					case 1 : $couleurStatut='#fe0202';
					break;
					case 2 : $couleurStatut='#feb402';
					break;
					case 3 : $couleurStatut='#02fe1a';
					break;
					case 4 : $couleurStatut='#02fe1a';
					break;
				}
				$couleurCommande='style=" border:2px solid '.$couleurStatut.';"';
				$affichage_commande_ligne.= '<tr >
										<td '.$couleurCommande.'>'.$row["idcommande"].'</td>
										<td '.$couleurCommande.'>'.date('d/m/Y �  H:i:s ',strtotime($row["date_creation"])).'</td>
										<td '.$couleurCommande.'>'.$row["nom"].'</td>
										<td '.$couleurCommande.'>'.$statut[(int)$row["statut_facture"]]["designation"].'</td>
										<td '.$couleurCommande.'>'.$row["accompte"].' �</td>
										<td '.$couleurCommande.'>'.$row["accompte_paye"].' �</td>
										<td '.$couleurCommande.'>'.$row["total"].' �</td>
										<td '.$couleurCommande.'>'.$row["total_paye"].' �</td>
									
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=E&idcommande='.$row["idcommande"].'" title="Editer la commande"><img src="images/edit.gif" ></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=Z&idcommande='.$row["idcommande"].'" title="Annuler la commande" ><img src="images/delete.gif" ></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=Z&idcommande='.$row["idcommande"].'" title="Annuler la commande" ><img src="images/delete.gif" ></a></td>
										<td '.$couleurCommande.'><a href="rechercheCommande.php?actionCommande=Z&idcommande='.$row["idcommande"].'" title="Annuler la commande" ><img src="images/delete.gif" ></a></td>
										</tr>';
			}		
			
		$affichage_commande_ligne.='</table>';	
		}
	}
if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des Commandes stock�es dans la base *
*												 *	
**************************************************/

$result=count($statut);


$affichage_recherche.='<form action="rechercheCommande.php?actionCommande=R" method="POST">';
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

?>
<body>

	<div class="row">
		<div class="small-11 small-centered columns">
			<div class="panel">
			
				<?= $MessageAction; ?>
			
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="small-11 small-centered columns">
		<div class="panel">
		<h1> Recherche des commandes</h1>
		<?php
			echo $affichage_recherche;
			echo $affichage_commande_ligne;
		?>
		</div>
		</div>		
	</div>

</body>

<?php
	require('includes/footer.php');
?>