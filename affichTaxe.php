<?php

//Gestion des taxes des gites 

	require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		Traitement des taxes  des gites     *
**********************************************/
		
    // differentes valeurs de la variable actionTaxe passee en argument 
	// vide : on affiche toutes les taxes
	// M : on affiche un formulaire pour modifier les taxes
	// S : on enregistre les modifications sur l taxe modifiee
	// D : on efface l taxe
	// AD: on ajoute une nouvelle taxe dans la base
	if (!empty($_GET["actionTaxe"]))
	{
		$actionTaxe=$_GET["actionTaxe"];
	}
	else
	{ $actionTaxe=""; }
	
	
	//on regarde si on a un idption en parametre
	if (!empty($_GET["idtaxe"]))
	{
		$idtaxe=$_GET["idtaxe"];
	}

	switch ($actionTaxe) 
	{
		case "S": //enregistrement des modifs de l taxe
			$reqUpdate="update TAXE	
					 SET tarif ='".$_POST["tarif"]."',
					 denomination='".$_POST["denomination"]."'
					 where idtaxe='".$_GET["idtaxe"]."'" ;
				$mysqli->query($reqUpdate);

			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Mise a jour de la taxe impossible." ;  
			} 
			else
			{
				$MessageAction="Enregistrement des modifications r&eacute;ussies.";
			}
					
		
			$actionTaxe='';
			break;
		case "D": //suppression d une taxe
				//on verifie d abord si cette taxe est encore utilisee par un gite, si oui on refuse la suppression
				
					$reqSuppression="DELETE FROM TAXE
					WHERE idtaxe='".$_GET["idtaxe"]."'";
						$mysqli->query($reqSuppression);
				
					if(!$mysqli)
					{
						$MessageAction="ERREUR : La taxe n'est pas effac&eacute;e.";  
					}
					else
					{ 
						$MessageAction="La taxe est correctement effac&eacute;e.";
					}	
				 
			break;
		case "AD": // ajout d une nouvelle taxe dans la BD
			$reqInsert="INSERT into TAXE (tarif,denomination) VALUES ('".$_POST["tarif"]."','".$_POST["denomination"]."')";
				$mysqli->query($reqInsert);
			if(!$mysqli)
			{
				$MessageAction= "ERREUR : insertion d'un nouveau tarif dans la base de donn&eacute;es impossible." ;  
			}
			else 
			{
				$MessageAction= "Insertion d'une nouvelle taxe r&eacute;ussie.";
			}
			break;
	}

if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo alert-box">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des taxes stock�es dans la base *
*												 *	
**************************************************/
$reqTaxes="select idtaxe, tarif, denomination from TAXE";
$result_reqTaxes=$mysqli->query($reqTaxes);

// Creation du tableau pour afficher les taxes
$affichage_taxe_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="400px"><thead>
				<tr><td >idtaxe</td><td>Tarif</td><td>D�nomination</td><th colspan="2">Action</th></tr>
				</thead>';
//Boucle qui parcourt les taxes dans la base de donn�es
while ($row = $result_reqTaxes->fetch_assoc())
{

	if (($actionTaxe=='M') and ($idtaxe==$row["idtaxe"])) // on affiche un formulaire pour modifier l taxe demandee
	{
		$affichage_taxe_ligne.='<tr ><th colspan="4"><form action="affichTaxe.php?actionTaxe=S&idtaxe='.$row["idtaxe"].'" method="post">
							<table><tr><td><input name="idtaxe" type="text" size="5" readonly value="'.$row["idtaxe"].'"></td><td><input name="tarif" size="6" type="text" value="'.$row["tarif"].'"></td><td><input name="denomination" type="text" value="'.$row["denomination"].'"></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							<td><a href="affichTaxe.php">
							<img src="images/cancel.gif" title="Annuler"></a></td>
							</tr></table>
							</form></th></tr>';
	
	
	}
	else // on affiche l taxe normalement
	{
	$affichage_taxe_ligne.='<tr height="28px"><td>'.$row["idtaxe"].'</td><td>'.$row["tarif"].'</td><td>'.$row["denomination"].'</td>
							<td><a href="affichTaxe.php?idtaxe='.$row["idtaxe"].'&actionTaxe=M"><img src="images/edit.gif" title="Modifier"></a></td>
							<td><a href="affichTaxe.php?idtaxe='.$row["idtaxe"].'&actionTaxe=D" onclick="return confirm(\'Etes vous s?re de vouloir supprimer cette taxe ?\');">
							<img src="images/delete.gif" title="Supprimer"></a></td>
							</tr>';
	}
	
}
//permet de creer un formulaire pour ajouter une nouvelle taxe dans la bd
	$affichage_taxe_ligne.='<tr ><th colspan="8"><form action="affichTaxe.php?actionTaxe=AD" method="post">
							<table><tr><td><input name="idtaxe" type="text" size="5" readonly></td><td><input name="tarif" size="6" type="text"></td><td><input name="denomination" type="text"></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							
							</tr></table>
							</form></th></tr>';
$affichage_taxe_ligne.='</table>';
?>

	<div class="row">
		<div class="small-6 large-centered columns">
			<h3>Gestion des taxes</h3>
		<?php
			echo '<p>'.$MessageAction.'</p>';
			echo $affichage_taxe_ligne;
		?>
		</div>
	</div>

</body>

<?php
	require('includes/footer.php');
?>