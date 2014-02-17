<?php

//GEstion des saisons des gites 

//version: 1.0
//
//creation: 23/01/2014


	include('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		Traitement des saisons  des gites     *
**********************************************/
		
    // differentes valeurs de la variable actionSaison passee en argument 
	// vide : on affiche toutes les saisons
	// M : on affiche un formulaire pour modifier les saisons
	// S : on enregistre les modifications sur la saison modifiee
	// D : on efface la saison
	// AD: on ajoute une nouvelle saison dans la base
	// SAM : on ajoute tous les samedis d'une année
	if (!empty($_GET["actionSaison"]))
	{
		$actionSaison=$_GET["actionSaison"];
	}
	else
	{ $actionSaison=""; }
	
	
	//on regarde si on a un idption en parametre
	if (!empty($_GET["idsaison"]))
	{
		$idsaison=$_GET["idsaison"];
	}

	switch ($actionSaison) 
	{
		case "S": //enregistrement des modifs de l saison
			$reqUpdate="update SAISON	
					 SET date_debut ='".$_POST["date_debut"]."',
					 date_fin='".$_POST["date_fin"]."',
					 statut='".$_POST["statut"]."'
					 where idsaison='".$_GET["idsaison"]."'" ;
				$mysqli->query($reqUpdate);

			if(!$mysqli)
			{
				$MessageAction ="ERREUR : Mise a jour saison impossible" ;  
			} 
			else
			{
				$MessageAction="Enregistrement des modifications OK";
			}
					
		
			$actionSaison='';
			break;
		case "D": //suppression d une saison
				$reqSuppression="delete from SAISON
				where idsaison='".$_GET["idsaison"]."'";
				$mysqli->query($reqSuppression);
				if(!$mysqli)
				{
					$MessageAction="ERREUR : Effacement de la saison pas effectuee" ;  
				}
				else
				{ 
					$MessageAction="Saison correctement effacee";
				}	
				
			break;
		case "AD": // ajout d une nouvelle saison dans la BD
			$reqInsert="insert into SAISON (date_debut,date_fin,statut) values ('".$_POST["date_debut"]."','".$_POST["date_fin"]."','".$_POST["statut"]."')";
				$mysqli->query($reqInsert);
				
			if(!$mysqli)
			{
				$MessageAction= "ERREUR : insertion d une nouvelle saison dans la BD" ;  
			}
			else 
			{
				$MessageAction= "insertion nouvelle saison ok";
			}
			break;
		case "SAM": // ajout de tous les samedis pour une année
			if(!empty($_POST["annee"]))
			{
				$date_debut = $_POST["annee"].'-01-01';
				//on regarde la date de fin d'année demandée
				$date_final=date('Y-m-d', strtotime($date_debut.' +364 days'));
				//on calcule la date +1 jour
				$date_test = date('Y-m-d', strtotime($date_debut.' +1 days'));
				
				while ($date_test<$date_final) // tant que l'on parcourt les dates de l'année en cours
				{
					if (typeSaison(strtotime($date_test))) // on teste si la date n'est pas déjà inclue dans une saison déjà présente
					{
						//echo "<br>".$date_test .":". typeSaison(strtotime($date_test));
					}
					else //si la date n'est pas dans une saison on l'insere dans la bd
					{
						if (date('D',strtotime($date_test))=='Sat') // la date est elle bien un samedi ? oui on l'insere sinon on passe au jour suivant
						{
							$reqInsert="insert into SAISON (date_debut,date_fin,statut) values ('".$date_test."','".$date_test."','HS')";
							$mysqli->query($reqInsert);
					
						}
					
					}
					//on incrémente pour parcourir l'annéee
					$date_test = date('Y-m-d', strtotime($date_test.' +1 days'));
				}
				
				// $reqInsert="insert into SAISON (date_debut,date_fin,statut) values ('".$_POST["date_debut"]."','".$_POST["date_fin"]."','".$_POST["statut"]."')";
					// $mysqli->query($reqInsert);
					// echo $reqInsert;
				// if(!$mysqli)
				// {
					// $MessageAction= "ERREUR : insertion d une nouvelle saison dans la BD" ;  
				// }
				// else 
				// {
					// $MessageAction= "insertion nouvelle saison ok";
			// }
			}
			else
			{
				$MessageAction= "le format de l'annee n'est pas correcte";
			
			}
			break;	
	}

if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des saisons stockées dans la base *
*												 *	
**************************************************/
$reqSaison="select idsaison, date_debut,date_fin,statut from SAISON";
$result_reqSaison=$mysqli->query($reqSaison);



// Creation du tableau pour afficher les saisons
$affichage_saison_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="600"><thead>
				<tr><td >idsaison</td><td>Date Debut</td><td>Date fin</td><td>Statut</td><th colspan="2">Action</th></tr>
				</thead>';
//Boucle qui parcourt les saisons dans la base de données
while ($row = $result_reqSaison->fetch_assoc())
{


	if (($actionSaison=='M') and ($idsaison==$row["idsaison"])) // on affiche un formulaire pour modifier l saison demandee
	{
		$affichage_saison_ligne.='<tr ><th colspan="5"><form action="affichSaisons.php?actionSaison=S&idsaison='.$row["idsaison"].'" method="post">
							<table width="100%"><tr><td><input name="idsaison" type="text" size="5" readonly value="'.$row["idsaison"].'"></td>
							<td><input name="date_debut" size="6" type="date" value="'.$row["date_debut"].'"></td>
							<td><input name="date_fin" type="date" value="'.$row["date_fin"].'"></td>
							<td>
							<select name="statut">';
							if ($row["statut"]=='IN') 
							{
								$affichage_saison_ligne.='<option selected="selected" value="'.$row["statut"].'">'.$row["statut"].'</option>
														<option  value="HS">HS</option>';
							}
							else							
							{
								$affichage_saison_ligne.='<option selected="selected" value="'.$row["statut"].'">'.$row["statut"].'</option>
														<option  value="IN">IN</option>';
							}
							
							$affichage_saison_ligne.='</select>
							
						
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							<td><a href="affichSaisons.php">
							<img src="images/cancel.gif" title="Annuler"></a></td>
							</tr></table>
							</form></th></tr>';
	
	
	}
	else // on affiche la saison normalement
	{
	$affichage_saison_ligne.='<tr height="28px"><td>'.$row["idsaison"].'</td><td>'.$row["date_debut"].'</td><td>'.$row["date_fin"].'</td><td>'.$row["statut"].'</td>
							<td><a href="affichSaisons.php?idsaison='.$row["idsaison"].'&actionSaison=M"><img src="images/edit.gif" title="Modifier"></a></td>
							<td><a href="affichSaisons.php?idsaison='.$row["idsaison"].'&actionSaison=D" onclick="return confirm(\'Etes vous s?re de vouloir supprimer cette saison ?\');">
							<img src="images/delete.gif" title="Supprimer"></a></td>
							</tr>';
	}
	
}
//permet de creer un formulaire pour ajouter une nouvelle saison dans la bd
	$affichage_saison_ligne.='<tr ><th colspan="5"><form action="affichSaisons.php?actionSaison=AD" method="post">
							<table><tr><td><input name="idsaison" type="text" size="5" readonly></td><td><input name="date_debut" size="6" type="date"></td><td><input name="date_fin" size="6" type="date"></td><td><select name="statut"><option value="HS">HS</option><option value="IN">IN</option></select></td>
							<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
							
							</tr></table>
							</form></th></tr>';
$affichage_saison_ligne.='</table>';
?>

<body>
	<div id="menu" style="position:relative; float:left;">
		<?php

			include('menu.php');
		?>
	</div>

	<div id="content" style="position:relative; float:left;">

		<?php
			echo $MessageAction;
			echo $affichage_saison_ligne;
		?>
		<p> Pour insérer tous les samedi de l'année en Saison Type Haute Saison, hors période déjà existante</p>
		<form action="affichSaisons.php?actionSaison=SAM" method="POST" onsubmit="return verif_action()">	
			<label for="annee">Merci de renseigner l'année : </label><input id="annee" name="annee" type="text">
			<input type="submit" value="Insérer">
		</form>
	</div>

</body>

<?php
	include('includes/footer.php');
?>