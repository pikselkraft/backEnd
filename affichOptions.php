<?php

//Gestion des options des gites 


	require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		Traitement des options  des gites     *
**********************************************/
		
    // differentes valeurs de la variable actionOption passee en argument 
	// vide : on affiche toutes les options
	// M : on affiche un formulaire pour modifier les options
	// S : on enregistre les modifications sur l option modifiee
	// D : on efface l option
	// AD: on ajoute une nouvelle option dans la base
	if (!empty($_GET["actionOption"]))
	{
		$actionOption=$_GET["actionOption"];
	}
	else
	{ $actionOption=""; }
	
	
	//on regarde si on a un idption en parametre
	if (!empty($_GET["idoption"]))
	{
		$idoption=$_GET["idoption"];
	}

	switch ($actionOption) 
	{
		case "S": //enregistrement des modifs de l option
			$reqUpdate="update OPTIONRESA	
					 SET option_tarif ='".$_POST["option_tarif"]."',
					 denomination='".$_POST["denomination"]."'
					 where idoption='".$_GET["idoption"]."'" ;
				$mysqli->query($reqUpdate);

			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Mise a jour option impossible" ;  
			} 
			else
			{
				$MessageAction="Enregistrement des modifications OK";
			}
					
		
			$actionOption='';
			break;
		case "D": //suppression d une option
				//on verifie d abord si cette option est encore utilisee par un gite, si oui on refuse la suppression
				$reqOptionsGite="select idoption from POSSEDEOPTION where idoption='".$_GET["idoption"]."'";
				$result_reqOptionsGite=$mysqli->query($reqOptionsGite);
				
				if (mysqli_num_rows($result_reqOptionsGite)>0)
				{
					$MessageAction="suppression impossible un gite dispose encore de cette option";
				}
				else
				{
					$reqSuppression="delete from OPTIONRESA
					where idoption='".$_GET["idoption"]."'";
						$mysqli->query($reqSuppression);
				
					if(!$mysqli)
					{
						$MessageAction="ERREUR : Effacement de l option pas effectuee" ;  
					}
					else
					{ 
						$MessageAction="Option correctement effacee";
					}	
				} 
			break;
		case "AD": // ajout d une nouvelle option dans la BD
			$reqInsert="insert into OPTIONRESA (option_tarif,denomination) values ('".$_POST["option_tarif"]."','".$_POST["denomination"]."')";
				$mysqli->query($reqInsert);
			if(!$mysqli)
			{
				$MessageAction= "ERREUR : insertion d un nouveau tarif dans la BD" ;  
			}
			else 
			{
				$MessageAction= "insertion nouvelle option ok";
			}
			break;
	}

if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des options stock�es dans la base *
*												 *	
**************************************************/
$reqOptions="select idoption, option_tarif, denomination from OPTIONRESA";
$result_reqOptions=$mysqli->query($reqOptions);



// Creation du tableau pour afficher les options
$affichage_option_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="400px"><thead>
				<tr><td >idoption</td><td>Tarif</td><td>D�nomination</td><th colspan="2">Action</th></tr>
				</thead>';
//Boucle qui parcourt les options dans la base de donn�es
while ($row = $result_reqOptions->fetch_assoc())
{


	if (($actionOption=='M') and ($idoption==$row["idoption"])) // on affiche un formulaire pour modifier l option demandee
	{
		$affichage_option_ligne.='<tr ><th colspan="4"><form action="affichOptions.php?actionOption=S&idoption='.$row["idoption"].'" method="post">
							<table><tr><td><input name="idoption" type="text" size="5" readonly value="'.$row["idoption"].'"></td><td><input name="option_tarif" size="6" type="text" value="'.$row["option_tarif"].'"></td><td><input name="denomination" type="text" value="'.$row["denomination"].'"></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							<td><a href="affichOptions.php">
							<img src="images/cancel.gif" title="Annuler"></a></td>
							</tr></table>
							</form></th></tr>';
	
	
	}
	else // on affiche l option normalement
	{
	$affichage_option_ligne.='<tr height="28px"><td>'.$row["idoption"].'</td><td>'.$row["option_tarif"].'</td><td>'.$row["denomination"].'</td>
							<td><a href="affichOptions.php?idoption='.$row["idoption"].'&actionOption=M"><img src="images/edit.gif" title="Modifier"></a></td>
							<td><a href="affichOptions.php?idoption='.$row["idoption"].'&actionOption=D" onclick="return confirm(\'Etes vous s?re de vouloir supprimer cette option ?\');">
							<img src="images/delete.gif" title="Supprimer"></a></td>
							</tr>';
	}
	
}
//permet de creer un formulaire pour ajouter une nouvelle option dans la bd
	$affichage_option_ligne.='<tr ><th colspan="4"><form action="affichOptions.php?actionOption=AD" method="post">
							<table><tr><td><input name="idoption" type="text" size="5" readonly></td><td><input name="option_tarif" size="6" type="text"></td><td><input name="denomination" type="text"></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							
							</tr></table>
							</form></th></tr>';
$affichage_option_ligne.='</table>';
?>

<body>
		<div class="row">
			<div class="small-11 small-centered columns">		
			<?php
				echo $MessageAction;
				echo $affichage_option_ligne;
			?>
			</div>
		</div>
</body>

<?php
	require('includes/footer.php');
?>