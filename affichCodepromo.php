<?php

//GEstion des codepromos des codes promo

//version: 1.0
//
//creation: 23/01/2014


	require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		Traitement des codepromo  des commande    *
**********************************************/
		
    // differentes valeurs de la variable actionCodePromo passee en argument 
	// vide : on affiche toutes les codepromos
	// M : on affiche un formulaire pour modifier les codepromos
	// S : on enregistre les modifications sur la codepromo modifiee
	// D : on efface la codepromo
	// AD: on ajoute une nouvelle codepromo dans la base
	// SAM : on ajoute tous les samedis d'une ann�e
	if (!empty($_GET["actionCodePromo"]))
	{
		$actionCodePromo=$_GET["actionCodePromo"];
	}
	else
	{ $actionCodePromo=""; }
	
	
	//on regarde si on a un idption en parametre
	if (!empty($_GET["idcodepromo"]))
	{
		$idcodepromo=$_GET["idcodepromo"];
	}

	switch ($actionCodePromo) 
	{
		case "S": //enregistrement des modifs de l codepromo
			$reqUpdate="update CODEPROMO	
					 SET code ='".$_POST["code"]."',
					 remise='".$_POST["remise"]."',
					 validite='".$_POST["validite"]."',
					 actif='".$_POST["actif"]."',
					 nb='".$_POST["nb"]."'					 
					 where idcodepromo='".$_GET["idcodepromo"]."'" ;
				$mysqli->query($reqUpdate);

			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Mise a jour codepromo impossible" ;  
			} 
			else
			{
				$MessageAction="Enregistrement des modifications OK";
			}
					
		
			$actionCodePromo='';
			break;
		case "D": //suppression d une codepromo
				$reqSuppression="delete from CODEPROMO
				where idcodepromo='".$_GET["idcodepromo"]."'";
				$mysqli->query($reqSuppression);
				if(!$mysqli)
				{
					$MessageAction="ERREUR : Effacement de la codepromo pas effectuee" ;  
				}
				else
				{ 
					$MessageAction="Promo correctement effacee";
				}	
				
			break;
		case "AD": // ajout d une nouvelle codepromo dans la BD
			$reqInsert="insert into CODEPROMO (code,remise,validite,actif,nb) values ('".$_POST["code"]."','".$_POST["remise"]."','".$_POST["validite"]."','".$_POST["actif"]."','".$_POST["nb"]."')";
				$mysqli->query($reqInsert);
				
			if(!$mysqli)
			{
				$MessageAction= "ERREUR : insertion d une nouvelle codepromo dans la BD" ;  
			}
			else 
			{
				$MessageAction= "insertion nouvelle codepromo ok";
			}
			break;
		
	}

if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des codepromos stock�es dans la base *
*												 *	
**************************************************/
$reqPromo="select idcodepromo, code,remise,validite,actif,nb from CODEPROMO";
$result_reqPromo=$mysqli->query($reqPromo);



// Creation du tableau pour afficher les codepromos
$affichage_codepromo_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient">
				<thead>
				<tr><td>idcodepromo</td><td>Code</td><td>remise</td><td>validite</td><td>actif</td><td>Nombre</td><th colspan="2">Action</th></tr>
				</thead>';
//Boucle qui parcourt les codepromos dans la base de donn�es
while ($row = $result_reqPromo->fetch_assoc())
{


	if (($actionCodePromo=='M') and ($idcodepromo==$row["idcodepromo"])) // on affiche un formulaire pour modifier l codepromo demandee
	{
		$affichage_codepromo_ligne.='<tr ><th colspan="8"><form action="affichCodepromo.php?actionCodePromo=S&idcodepromo='.$row["idcodepromo"].'" method="post">
							<table>
							<tr>
							<td><input name="idcodepromo" type="text" size="2" readonly value="'.$row["idcodepromo"].'"></td>
							<td ><input name="code" size="10" type="text" value="'.$row["code"].'"></td>
							<td><input name="remise" size="6" type="text" value="'.$row["remise"].'"></td>
							<td><input name="validite" type="date" value="'.$row["validite"].'"></td>
							<td>
							<select name="actif">
							
							';
							if ($row["actif"]==1) 
							{
								$affichage_codepromo_ligne.='<option selected="selected" value="'.$row["actif"].'">OUI</option>
														<option  value="0">NON</option>';
							}
							else							
							{
								$affichage_codepromo_ligne.='<option selected="selected" value="'.$row["actif"].'">NON</option>
														<option  value="1">OUI</option>';
							}
							
							$affichage_codepromo_ligne.='</select></td>
							<td><input name="nb" type="int" value="'.$row["nb"].'" size="3"></td>
							
							
							
						
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							<td><a href="affichCodepromo.php">
							<img src="images/cancel.gif" title="Annuler"></a></td>
							</tr></table>
							</form></th></tr>';
	
	
	}
	else // on affiche la codepromo normalement
	{
	$affichage_codepromo_ligne.='<tr height="28px"><td>'.$row["idcodepromo"].'</td><td>'.$row["code"].'</td><td>'.$row["remise"].'</td><td>'.$row["validite"].'</td><td>';
	if ($row["actif"]==1) $affichage_codepromo_ligne.='Oui' ;
	else $affichage_codepromo_ligne.='Non';
	$affichage_codepromo_ligne.='</td><td>'.$row["nb"].'</td>
							<td><a href="affichCodepromo.php?idcodepromo='.$row["idcodepromo"].'&actionCodePromo=M"><img src="images/edit.gif" title="Modifier"></a></td>
							<td><a href="affichCodepromo.php?idcodepromo='.$row["idcodepromo"].'&actionCodePromo=D" onclick="return confirm(\'Etes vous s?re de vouloir supprimer cette codepromo ?\');">
							<img src="images/delete.gif" title="Supprimer"></a></td>
							</tr>';
	}
	
}
//permet de creer un formulaire pour ajouter une nouvelle codepromo dans la bd
	$affichage_codepromo_ligne.='<tr ><th colspan="8"><form action="affichCodepromo.php?actionCodePromo=AD" method="post">
							<table>
							<tr>
							<td width="130px"><input name="idcodepromo" type="text" size="5" readonly></td>
							<td><input name="code" size="6" type="text"></td>
							<td><input name="remise" size="6" type="text"></td>
							<td><input name="validite" size="6" type="date"></td>
							<td><select name="actif"><option value="1">Oui</option>
							<option value="2">Non</option></select></td>
							<td><input name="nb" size="6" type="int"></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
														</tr></table>
							</form></th></tr>';
$affichage_codepromo_ligne.='</table>';
?>

	<div class="row">
		<div class="small-11 small-centered columns">
		<?php
			echo $MessageAction;
			echo $affichage_codepromo_ligne;
		?>	
		</div>
	</div>

</body>

<?php
	include('includes/footer.php');
?>