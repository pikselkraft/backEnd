<?php
	require('includes/header.php');
?>


	<div class="row">
		<div class="small-11 small-centered columns">
			<form action="modifGite.php?etat=M" method="POST">
				<input type="text" name="capacite" value="<?= $_POST["capacite"]; ?>"/>';
				<input type="text" name="url" value="<?= $_POST["url"]; ?>"/>';
				<input type="text" name="montant_caution" value="<?= $_POST["montant_caution"]; ?>"/>';
				<input type="text" name="titre" value="<?= $_POST["titre"]; ?>"/>';
				<input type="text" name="description" value="<?= $_POST["description"]; ?>"/>';
				<input type="text" name="surface" value="<?= $_POST["surface"]; ?>"/>';
			</form>
		</div>
	</div>

<?php

while ($rowReqTarif = $resultreqTarif->fetch_assoc())
			{
				// echo '<tbody>';
				// echo '<tr>';
				// echo '<th colspan ="5">
				//si on modifie les tarifs on affiche un formulaire
				if ($_GET["tarif"]=='M')
				{
				echo '<form action="affichGite.php?etat=M&tarif=S&idtarif='.$rowReqTarif["idtarif"].'&idgite='.$_GET["idgite"].'" method="post">
					<table border="2" width="300px" rules="groups" class="rechClient">
						<tr>
							<td width="50px"><input style="width:50px;" type="text" name="prix" value="'.$rowReqTarif["prix"].'" />
								
							</td>
							<td width="75PX"><input  style="width:75px;" type="text" name="statut_client" value="'.$rowReqTarif["statut_client"].'" />
							</td>
							<td width="50PX"><input  style="width:50px;" type="text" name="saison" value="'.$rowReqTarif["saison"].'" />
							</td>
							
							<td width="25%">action
							</td>
							<td><INPUT src="images/save.gif" title="Editer" type="image" name="envoi" Value="submit"></td>
							<td><a href="affichGite.php?etat=M&idgite='.$idgite.'&tarif=S&idtarif='.$rowReqTarif["idtarif"].'""><img src="images/delete.gif" title="Supprimer"></a></td>
						</tr>
					</table>
				</form>';
				
				}
				else // sinon on affiche tout simplement un tableau
				{
				echo '<form action="affichGite.php?etat=M&tarif=M&idgite='.$rowReqTarif["idtarif"].'" method="post">
					<table border="1" width="300px" rules="groups" class="rechClient">
						<tr>
							<td width="50px"><input type="hidden" name="prix" value="'.$rowReqTarif["prix"].'" />'
								.$rowReqTarif["prix"].'
							</td>
							<td width="75px"><input type="hidden" name="statut_client" value="'.$rowReqTarif["statut_client"].'" />'
								.$rowReqTarif["statut_client"].'
							</td>
							<td width="25%"><input type="hidden" name="saison" value="'.$rowReqTarif["saison"].'" />'
								.$rowReqTarif["saison"].'
							</td>
							
							<td width="25%">action
							</td>
							<td><INPUT src="images/edit.gif" title="Editer" type="image" name="envoi" Value="submit"></td>
							
						</tr>
					</table>
				</form>';
				
					// </th>';
				// echo '</tr>';
				// echo '</tbody>';
				}
			}
			
			
	require('includes/footer.php');
?>