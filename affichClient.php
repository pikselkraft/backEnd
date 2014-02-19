<?php

//Gestion d un client dans la BD


	require('includes/header.php');

	
$MessageAction=''; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
$modif='  '; // par defaut les donnes ne sont pas modifiables
/*********************************************
*		Traitement des clients  des gites     *
**********************************************/
if(!empty($_GET["idclient"]))
{
	$idclient=(int)$_GET["idclient"];
}
else
{
	$MessageAction="Erreur Aucun client pass� en param�tre";
}

if (!empty($_GET["actionClient"]))
{
	$actionClient=$_GET["actionClient"];
}
else
{ $actionClient=""; }
	


/*********************************************
*		Action possible sur la fiche client    *
**********************************************/
		
    // differentes valeurs de la variable actionClient passee en argument 
	// vide : on affiche un formulaire de recherche de clients
	// M : on modifie les valeurs renseign�

switch ($actionClient) 
	{
		case "MS": //on modifie les valeurs du clients dans la BD
				$reqUpdate="update CLIENTS	
					 SET nom='".$_POST["nom"]."', prenom='".$_POST["prenom"]."', entreprise='".$_POST["entreprise"]."', 
					 adresse='".$_POST["adresse"]."', codepostal='".$_POST["codepostal"]."', ville='".$_POST["ville"]."', 
					 pays='".$_POST["ville"]."', tel='".$_POST["tel"]."', port='".$_POST["port"]."', 
					 email='".$_POST["email"]."',date_naissance='".$_POST["date_naissance"]."', 
					 cheminot='".$_POST["cheminot"]."', code_cheminot='".$_POST["code_cheminot"]."', region='".$_POST["region"]."', 
					 newsletter='".$_POST["newsletter"]."', 
					 commentaire='".$_POST["commentaire"]."'
					 where idclient= ".$idclient ;
				$mysqli->query($reqUpdate);
				
		
				
		break;
		
	}
// construction de la requete
$reqClient="select idclient, nom, prenom, entreprise, adresse, codepostal, ville, pays, tel, port, email,date_naissance, creation, cheminot, code_cheminot, region, newsletter, commentaire 
					from CLIENTS
					where idclient=".$idclient;
$result_reqClient=$mysqli->query($reqClient);
			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Pas de r�sultat " ;  
			} 
			else
			{
				$MessageAction="Client ok : ";
			}
while ($row = $result_reqClient->fetch_assoc())
{			
$affichage_info_client='<form action="affichClient.php?actionClient=MS&idclient='.$idclient.'" method="post"><ul>';
$affichage_info_client.='<li><label for="nom">Nom : </label><input id="nom" name="nom" type="text" value="'.$row["nom"].'"'  .$modif. '></li>
					<li><label for="prenom">Pr�nom : </label><input id="prenom" name="prenom" type="text" value="'.$row["prenom"].'"'  .$modif. '></li>
					<li><label for="entreprise">Entreprise : </label><input id="entreprise" name="entreprise" type="text" value="'.$row["entreprise"].'"'  .$modif. '></li>
					<li><label for="adresse">Adresse : </label><input id="Adresse" name="Adresse" type="text" value="'.$row["Adresse"].'"'  .$modif. '></li>
					<li><label for="codepostal">Codepostal : </label><input id="codepostal" name="codepostal" type="text" value="'.$row["codepostal"].'"'  .$modif. '></li>
					<li><label for="ville">Ville : </label><input id="ville" name="ville" type="text" value="'.$row["ville"].'"'  .$modif. '></li>
					<li><label for="pays">Pays : </label><input id="pays" name="pays" type="text" value="'.$row["pays"].'"'  .$modif. '></li>
					<li><label for="tel">Tel : </label><input id="tel" name="tel" type="text" value="'.$row["tel"].'"'  .$modif. '></li>
					<li><label for="port">Portable : </label><input id="port" name="port" type="text" value="'.$row["port"].'"'  .$modif. '></li>
					<li><label for="email">Email : </label><input id="email" name="email" type="text" value="'.$row["email"].'"'  .$modif. '></li>
					<li><label for="date_naissance">Date naissance : </label><input id="date_naissance" name="date_naissance" type="date" value="'.$row["date_naissance"].'"'  .$modif. '></li>
					<li><label for="creation">Date cr�ation: </label><input id="creation" name="creation" type="text" value="'.date_format(date_create($row["creation"]),'d-m-Y H:i:s').'"'  .$modif. '></li>
					<li><label for="cheminot">Cheminot : </label><select name="cheminot">';
					
					if (((int)$row["cheminot"])==1) 
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["cheminot"].'">Oui</option>
												<option  value="0">Non</option>';
					}
					else							
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["statut"].'">Non</option>
												<option  value="1">Oui</option>';
					}
					$affichage_info_client.='</select></li>
						
					<li><label for="code_cheminot">N� CP : </label><input id="code_cheminot" name="code_cheminot" type="text" value="'.$row["code_cheminot"].'"'  .$modif. '></li>
					<li><label for="region">R�gion : </label><select name="region">';
					
					if (((int)$row["region"])==1) 
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Oui</option>
												<option  value="0">Non</option>';
					}
					else							
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Non</option>
												<option  value="1">Oui</option>';
					}
					$affichage_info_client.='</select></li>
					
					<li><label for="newsletter">Newsleters </label>';
					
					$affichage_info_client.='<select name="newsletter">';
					if (((int)$row["newsletter"])==1) 
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Oui</option>
												<option  value="0">Non</option>';
					}
					else							
					{
						$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Non</option>
												<option  value="Oui">Oui</option>';
					}
					$affichage_info_client.='</select></li>
					<li><label for="commentaire">Commentaire </label>
					<input id="commentaire" name="commentaire" type="text" value="'.$row["commentaire"].'"'  .$modif. '></li>
					<input type="submit" value="Modifier">
				</form></ul>';	

					
					
}
//requete pour l'affichage des commandes du client
	$reqCommandeResa="select CO.idcommande,  CM.idclient, CO.taxe, CO.caution, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, CO.accompte_paye, CO.total,CO.total_paye  
					from COMMANDE CO, COMMANDERESERVER CM
					where CM.idclient=".$idclient. " and CO.idcommande=CM.idcommande";
	
					$result_reqCommandeResa=$mysqli->query($reqCommandeResa);
	$tableau_commande=' <table>
								   <caption>Listes des commandes du client</caption>
								 
								   <thead> <!-- En-t�te du tableau -->
									   <tr>
										   <th>N� de la commande</th>
										   <th>caution</th>
										   <th>montant_option</th>
										   <th>remise</th>
										   <th>code_promo</th>
										   <th>date_creation</th>
										   <th>statut_facture</th>
										   <th>accompte</th>
										   <th>accompte_paye</th>
										   <th>total</th>
										   <th>total_paye</th>
									   </tr>
								   </thead>
								 
								   <tfoot> <!-- Pied de tableau -->
									   <tr>
										   <th>N� de la commande</th>
										   <th>caution</th>
										   <th>montant_option</th>
										   <th>remise</th>
										   <th>code_promo</th>
										   <th>date_creation</th>
										   <th>statut_facture</th>
										   <th>accompte</th>
										   <th>accompte_paye</th>
										   <th>total</th>
										   <th>total_paye</th>
									     </tr>
								   </tfoot>
								 
								   <tbody> <!-- Corps du tableau -->';
	while ($row = $result_reqCommandeResa->fetch_assoc())
	{
		$tableau_commande.='<tr><td>'.$row["idcommande"].'</td>';
		$tableau_commande.='<td>'.$row["caution"].'</td>';
		$tableau_commande.='<td>'.$row["montant_option"].'</td>';
		$tableau_commande.='<td>'.$row["remise"].'</td>';
		$tableau_commande.='<td>'.$row["code_promo"].'</td>';
		$tableau_commande.='<td>'.$row["date_creation"].'</td>';
		$tableau_commande.='<td>'.$row["statut_facture"].'</td>';
		$tableau_commande.='<td>'.$row["accompte"].'</td>';
		$tableau_commande.='<td>'.$row["accompte_paye"].'</td>';
		$tableau_commande.='<td>'.$row["total"].'</td>';
		$tableau_commande.='<td>'.$row["total_paye"].'</td>';
		$tableau_commande.='</tr>';
		
	}
	
	$tableau_commande.= '
								   </tbody>
								</table>';
?>

<body>
	
		<?php
			echo $affichage_recherche;
			echo $MessageAction;
		?>
		<div class="row">
			<div class="small-11 small-centered columns">
				<dl class="tabs" data-tab>
					<dd class="active"><a href="#panel2-1">Information Clients</a></dd>
					<dd><a href="#panel2-2">Information Commande</a></dd>
					<dd><a href="#panel2-3">Information Réservation</a></dd>
					<dd><a href="#panel2-4">Tab 4</a></dd>
				</dl>
				<div class="tabs-content">
					<div class="content active" id="panel2-1">
						<?= $affichage_info_client;?>
					</div>
					<div class="content" id="panel2-2">
						<?= $tableau_commande;?>
					</div>
					<div class="content" id="panel2-3">
						<p>Third panel content goes here...</p>
					</div>
					<div class="content" id="panel2-4">
						<p>Fourth panel content goes here...</p>
					</div>
				</div>
			</div>
		</div>

</body>

<?php
	require('includes/footer.php');
?>