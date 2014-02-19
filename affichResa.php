<?php

require('includes/header.php');

/*************************************

Affiche les infos d'une r�servation


**************************************/	
	
$id_resa=$_GET["idresa"];
extract($_POST);
$date_debut=date('Y-m-d',strtotime($date_debut));
var_dump($date_debut);
if(empty($id_resa)) 
{$id_resa=$idreservation;

}
$idgite=$_GET["idgite"];
$etatR=$_GET["etatR"];

if ($etatR=='S')
{
 if (verifReservation ($date_debut,$date_fin,$idgite,$idreservation)) echo "modif possible";
	

}

	?>
	
<body>
	<div class="row">
		<div class="small-11 small-centered columns">	
			<?php
			if (!empty($id_resa))
			{
			
				$reqcommanderesa="select idcommande, idreservation, idclient from COMMANDERESERVER where idreservation=".$id_resa ;
				
				$resultcommanderesa=$mysqli->query($reqcommanderesa);
				while ($row = $resultcommanderesa->fetch_assoc())
				{
					$id_commande=$row["idcommande"];
					$id_client=$row["idclient"];
				}
				
				$req_commande_client="SELECT distinct c.idcommande, c.taxe,c.caution, c.montant_option, c.remise, c.code_promo, c.date_creation, c.statut_facture, c.accompte, c.accompte_paye, c.total, c.total_paye, cl.idclient, cl.nom, cl.prenom 
				from COMMANDE c, CLIENTS cl, COMMANDERESERVER cr, GITE g 
				where cr.idreservation=".$id_resa." and  cr.idcommande=c.idcommande and cr.idclient=cl.idclient";
				
				
				$result_commande_client=$mysqli->query($req_commande_client);
				while ($row_result_commande = $result_commande_client->fetch_assoc())
				{
				echo '
					<form action="" method="post">
						<fieldset>
							<legend>Commande</legend>
							<li>
								<label for=nom >Nom</label>
								<input id=nom name=nom type=text readonly="true" value="'.$row_result_commande["nom"].'">
							</li>
							<li>
								<label for=prenom >prenom</label>
								<input id=prenom name=prenom type=text readonly="true" value="'.$row_result_commande["prenom"].'">
							</li>
							<li>
								<label for=idcommande >idCommande</label>
								<input id=idcommande name=idcommande type=text readonly="true" value="'.$row_result_commande["idcommande"].'">
							</li>
							<li>
								<label for=taxe >taxe</label>
								<input id=taxe name=taxe type=text readonly="true" value="'.$row_result_commande["taxe"].'">
							</li>
							<li>
								<label for=caution >caution</label>
								<input id=caution name=caution type=text readonly="true" value="'.$row_result_commande["caution"].'">
							</li>
							<li>
								<label for=options >Montant de l\'option</label>
								<input id=options name=options type=text value="'.$row_result_commande["option"].'">
							</li>
							<li>
								<label for=remise >Remise</label>
								<input id=remise name=remise type=text  value="'.$row_result_commande["remise"].'">
							</li>
							<li>
								<label for=code_promo >Code promo</label>
								<input id=code_promo name=code_promo type=text  value="'.$row_result_commande["code_promo"].'">
							</li>
							<li>
								<label for=date_creation >Date Commande</label>
								<input id=date_creation name=date_creation type=text readonly="true" value="'.$row_result_commande["date_creation"].'">
							</li>
							<li>
								<label for=statut_facture >Statut facture</label>
								<input id=statut_facture name=statut_facture type=text  value="'.$row_result_commande["statut_facture"].'">
							</li>
							<li>
								<label for=accompte >Accompte</label>
								<input id=accompte name=accompte type=text  value="'.$row_result_commande["accompte"].'">
							</li>
							<li>
								<label for=accompte_paye >Accompte pay� ?</label>
								<input id=accompte_paye name=accompte_paye type=text  value="'.$row_result_commande["accompte_paye"].'">
							</li>
							<li>
								<label for=total >Total</label>
								<input id=total name=total type=text  value="'.$row_result_commande["total"].'">
							</li>
								<li>
								<label for=total_paye >Total pay�</label>
								<input id=total_paye name=total_paye type=text  value="'.$row_result_commande["total_paye"].'">
							</li>
							<button type=submit>enregistrer</button>	
						</fieldset>
					</form>';
					
					// echo '<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="800px">'; 
					// echo '<thead >';
					// echo '<tr><td width="90px">idCommande</td><td width="60px">taxe</td><td width="60px">Caution</td><td width="30px">remise</td><td>code_promo</td><td>Date</td><td>Statut</td><td >action</td></tr>';
					// echo '</thead>';
				
							// while ($row = $resultcommande->fetch_assoc())
							// {
								// echo '<tr align="left"><th colspan="10">
								// <form action="affichGite.php?etatG=S&idgite='.$row["idgite"].'" method="post">';
								// echo '<table width="800px" >';
								// echo '<tbody>';
								// echo '<tr >';
								// echo '<td width="90px"><input type="text" name="nom" value="'.$row["nom"].'" /></td>';		
								// echo '</tr>';
								// echo '</tbody>';
								// echo '</table></form></th></tr>';
							// }
				}
			}			
		?>
		</div>
	</div> <!-- fin div Commande-->
	
	<div class="row">
		<div class="small-11 small-centered columns"> 
		<?php
		// affichage de la liste des r�servation pour une commande
			$reqlisteCommande="select r.idreservation, r.idgite,r.nb_adulte, r.nb_enfant,r.date_debut,r.date_fin, r.statut 
			from RESERVATION r, COMMANDERESERVER c where
			 c.idcommande=".$id_commande." and c.idreservation=r.idreservation";
			
			$resultreqlisteCommande=$mysqli->query($reqlisteCommande);
			
			
		echo '<table border="2"  rules="groups" id="tableauClient" class="rechClient"  >'; 
				echo '<thead >';
				echo '<tr><td width="50px">idResa</td><td width="64px">gite</td><td width="60px">Nb Adulte</td><td width="30px">Nb Enfant</td><td>Date D�but</td><td>Date fin </td><td>Statut</td><th>action</th></tr>';
				echo '</thead>';
			
			
			
			while ($rowlisteCommande = $resultreqlisteCommande->fetch_assoc())		
			{
				echo '<tr align="left">
				<th colspan="8">
				<form action="affichResa.php?etatR=S&idgite='.$rowlisteCommande["idgite"].'" method="post">';
				echo '<table>';
				echo '<tbody>';
				echo '<tr >';
				echo '<td width="20px" ><input readonly="true" type="text" name="idreservation" size="1" value="'.$rowlisteCommande["idreservation"].'" /></td>';	
				echo '<td width="64px"><input type="text" name="nom" size="6" value="'.$rowlisteCommande["idgite"].'" /></td>';	
				echo '<td ><input type="text" name="nb_adulte" size="1" value="'.$rowlisteCommande["nb_adulte"].'" /></td>';	
				echo '<td ><input type="text" name="nb_enfant" size="1" value="'.$rowlisteCommande["nb_enfant"].'" /></td>';
				$date_debut=date('d/m/Y',strtotime($rowlisteCommande["date_debut"]));
				$date_fin=date('d/m/Y',strtotime($rowlisteCommande["date_fin"]));
				echo '<td width="64px"><input type="text" id="datepicker" name="date_debut" size="6" value="'.$date_debut.'" /></td>';
				echo '<td width="64px"><input type="text" name="date_fin" id="datepicker2" size="6" value="'.$date_fin.'" /></td>';
				echo '<td ><input type="text" name="statut" id="datepicker2" size="6" value="'.$rowlisteCommande["statut"].'" /></td>';
				echo '<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" value="submit"></td>';
				echo '</tr >';
				echo '</tbody>';
				echo '</table>';
				echo '</form></th>';
				
			}
		echo '</table>';	
			?>
		</div><!-- fin div R�servation-->
	</div>
<?php

			
	require('includes/footer.php');
?>