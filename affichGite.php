<?php
	
	require('includes/header.php');
	
/********************************************
*											*
*											*
*											*
*											*
* Affichage, modification, suppression des	* 
* des gites et de leur tarifs     			*
*											*
*											*
*						2/12/2013 			*
*						V1.1				*
*											*
********************************************/

	


/*********************************************
*		Traitement des tarifs  des gites     *
**********************************************/

		
    // diff�rent valeur de la variable etatTarif pass� en argument 
	// vide : on affiche rien
	// A : on affiche les tarifs
	// M : on affiche un formulaire pour modifier les tarifs
	// S : on enregistre lesmodificaiton sur le tarif
	// D : on efface les tarifs
	// AD: on ajoute un nouveau tarif dans la base
	
if (!empty($_GET["etatTarif"]))
{
	$etatTarif=$_GET["etatTarif"];
}	
	
	
	
if (($_GET["etatTarif"]=='S') && (!empty($_GET["idtarif"]))) // mise � jour des infos du tarif d'un gite
{

	$reqUpdate="update TARIF	
				 SET prix ='".$_POST["prix"]."' ,
				 statut_client='".$_POST["statut_client"]."',
				 saison='".$_POST["saison"]."', commentaire='".$_POST["commentaire"]."'
				 where idtarif='".$_GET["idtarif"]."'" ;
			$mysqli->query($reqUpdate);

				  if(!$mysqli)
				  {
						echo "ERREUR : Mise � jour du tarif pas effectu�e" ;  
				  } 
	
	echo "save OK";
	$etatTarif='A';
				
}
if (($_GET["etatTarif"]=='D') && (!empty($_GET["idtarif"]))) // on supprime le tarif et sa relation avec le gite
{

	$reqSuppresion="delete from POSSEDETARIF	
				  where idgite='".$GET["idgite"]."'
				 and idtarif='".$_GET["idtarif"]."'" ;
				$mysqli->query($reqSuppresion);

				if(!$mysqli)
				{
					echo "ERREUR : Effacement du tarif pas effectu�e" ;  
				}
				// else
				// {
					// $reqSuppression="delete from TARIF where idtarif='".$_GET["idtarif"]."'";
					// $mysqli->query($reqSuppression);
					// if(!$mysqli)
					// {
						// echo "ERREUR : Effacement du tarif pas effectu�e" ;  
					// }
				// }

	$etatTarif='A';
				
}

/*********************************************
*		Traitement des options  des gites     *
**********************************************/

		
    // diff�rent valeur de la variable option pass� en argument 
	// vide : on affiche rien
	// A : on affiche les options
	// S : on enregistre lesmodificaiton sur le options
	// D : on efface les options
	// AD: on ajoute un nouveau options dans la base
	
if (!empty($_GET["options"]))
{
	$options=$_GET["options"];
}	
	
	
	
// if (($options=='S') && (!empty($_GET["options"]))) // mise � jour des infos des options d'un gite
// {

	// $reqUpdate="update OPTIONRESA	
				 // SET option_tarif ='".$_POST["option_tarif"]."' ,
				 // denomination='".$_POST["denomination"]."'
				 // where idoption='".$_GET["idoption"]."'" ;
			// $mysqli->query($reqUpdate);

				  // if(!$mysqli)
				  // {
						// echo "ERREUR : Mise � jour des options pas effectu�e" ;  
				  // } 
	
	// echo "save OK";
	// $options='A';
				
// }
if (($options=='D') && (!empty($_GET["idoption"]))) // on supprime le tarif et sa relation avec le gite
{

	$reqSuppression="delete from POSSEDEOPTION	
				  where idgite='".$_GET["idgite"]."'
				 and idoption='".$_GET["idoption"]."'" ;
				$mysqli->query($reqSuppression);
echo $reqSuppression;
				if(!$mysqli)
				{
					echo "ERREUR : Effacement de option pas effectu�e" ;  
				}
				// else
				// {
					// $reqSuppression="delete from OPTIONRESA where idoption='".$_GET["idoption"]."'";
					// $mysqli->query($reqSuppression);
					// if(!$mysqli)
					// {
						// echo "ERREUR : Effacement des options pas effectu�e" ;  
					// }
				// }

	$options='A';
				
}

if (($options=='AD') && (!empty($_GET["idgite"]))&& (!empty($_POST["idoption"]))) // mise � jour des infos du tarif d'un gite
{

	
				
				$reqInsert="insert into POSSEDEOPTION (idgite,idoption) values ('".$_GET["idgite"]."','".$_POST["idoption"]."')";
				$mysqli->query($reqInsert);
			if(!$mysqli)
					{
						echo "ERREUR : Effacement des options pas effectu�e" ;  
					}
	

	$options='A';
				
}
/*******************************
Test de l'argument img
********************************/

/*********************************************
*		Traitement de l'affichage  des la gestion des photos    *
**********************************************/

   // diff�rent valeur de la variable img pass� en argument 
	// vide : on affiche aucune info de photo
	// A : on affiche un tableau avec la liste des urls des photos
	// S : on enregistre les modification d'une photo
	// D : on efface la photo
	// AD : on ajoute une photo
	
if (!empty($_GET["img"]))
{
	$img=$_GET["img"];
}


if (($img=='D') && (!empty($_GET["idimage"]))) // on supprime l'image d un gite
{

	$reqSuppression="delete from IMAGES	
				  where idimage='".$_GET["idimage"]."'";
echo $reqSuppression;				
				$mysqli->query($reqSuppression);

				if(!$mysqli)
				{
					echo "ERREUR : Effacement de l image pas effectu�e" ;  
				}
				

	$img='A';
				
}

	// si l'�tat d'img= S alors on met � jour les donn�es de l image
if (($img=='S') && (!empty($_GET["idimage"])))
{

	$reqUpdate="update IMAGES
				 SET titre_image ='".$_POST["titre_image"]."' ,
				 une='".$_POST["une"]."',
				 description_image='".$_POST["description_image"]."',
				 url='".$_POST["url"]."'
				  where idimage='".$_GET["idimage"]."'" ;
echo $reqUpdate;
			$mysqli->query($reqUpdate);

				  if(!$mysqli)
				  {
						echo "ERREUR : Mise � jour pas effectu�e" ;  
				  } 
		$img='A';
					 
				
}
if (($_GET["img"]=='AD') && (!empty($_GET["idgite"]))) // insertion d une nouvelle image pour un gite
{ echo "insert";

		$reqInsert="insert into IMAGES (idgite, titre_image, une,url, description_image) values ('".$_GET["idgite"]."','".$_POST["titre_image"]."','".$_POST["une"]."','".$_POST["url"]."','".$_POST["description_image"]."')";
	
	
					
		$mysqli->query($reqInsert);

		
			 
			 if(!$mysqli)
			  {
				echo "ERREUR : Mise � jour du tarif pas effectu�e" ;  
			  }
			  
	

	$img='A';
				
}

/*******************************
TEst de l'argument etatG
********************************/

/*********************************************
*		Traitement de l'affichage  des gites     *
**********************************************/

   // diff�rent valeur de la variable etatG pass� en argument 
	// vide : on affiche les infos gite
	// M : on affiche un formulaire pour modifier les infos du gite
	// S : on enregistre les modification des infos du Gites
	// D : on efface le gite
	
	
	// si l'�tat = S alors on met � jour les donn�es du Gite en question
if (($_GET["etatG"]=='S') && (!empty($_GET["idgite"]))&& (!empty($_POST["nom"])))
{

	$reqUpdate="update GITE
				 SET nom ='".$_POST["nom"]."' ,
				 capacite='".$_POST["capacite"]."',
				 url='".$_POST["url"]."',
				 montant_caution='".$_POST["montant_caution"]."',
				 titre='".$_POST["titre"]."',
				 description='".$_POST["description"]."',
				 surface='".$_POST["surface"]."'
				 where idgite='".$_GET["idgite"]."'" ;

			$mysqli->query($reqUpdate);

				  if(!$mysqli)
				  {
						echo "ERREUR : Mise � jour pas effectu�e" ;  
				  } 
				 
				
}

if (($_GET["etatTarif"]=='AD') && (!empty($_GET["idgite"]))) // mise � jour des infos du tarif d'un gite
{

		$reqInsert="insert into TARIF (prix, statut_client, saison, commentaire) values ('".$_POST["prix"]."','".$_POST["statut_client"]."','".$_POST["saison"]."','".$_POST["commentaire"]."')";
	
	
					
		$mysqli->query($reqInsert);

		//on recherche l'idtarif que l'on vient d'ins�rer
		$reqMax="select max(idtarif) as maxiTarif from TARIF";
				$result_reqMax=$mysqli->query($reqMax);
				while ($row = $result_reqMax->fetch_assoc())
				{
					$maxiidtarif=$row["maxiTarif"];
				
				//echo "idtarif maxi ".$maxiidtarif;
				}
			 
			 if(!$mysqli)
			  {
				echo "ERREUR : Mise � jour du tarif pas effectu�e" ;  
			  }
			  else
			  {
				
				$reqInsert="insert into POSSEDETARIF (idgite,idtarif) values ('".$_GET["idgite"]."','".$maxiidtarif."')";
				$mysqli->query($reqInsert);
			  }
	

	$etatTarif='A';
				
}
?>
<body>
	<div class="row">
		<div class="small-11 small-centered columns">		
		<?php 
	
		$idgite=$_GET["idgite"];

		/* on affiche la liste des gites et les informations les concernant*/
		$reqGite="select * from GITE" ;
		$resultReqGite=$mysqli->query($reqGite);
	
		echo '<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="800px">'; 
				echo '<thead >';
				echo '<tr><td width="90px">Nom</td><td width="60px">Capacit�</td><td width="60px">URL</td><td width="30px">Caution</td><td>Titre</td><td>Description</td><td>Surface</td><td >action</td></tr>';
				echo '</thead>';
		
					while ($row = $resultReqGite->fetch_assoc())
					{
					
						//si l'�tatG= M alors on affiche un formulaire pour modifier les donn�es du gite selectionn�
						if (($_GET["etatG"]=='M') && ($row["idgite"]==$_GET["idgite"]))
						{
							echo '<tr align="left"><th colspan="10">
							<form action="affichGite.php?etatG=S&idgite='.$row["idgite"].'" method="post">';
							echo '<table width="800px" >';
							echo '<tbody>';
								echo '<tr >';
								echo '<td width="90px"><input type="text" name="nom" value="'.$row["nom"].'" /></td>';		
							//	echo '<td></td>';
								echo '<td width="60px"><input type="text" name="capacite" value="'.$row["capacite"].'" /></td>';	
								echo '<td width="60px"><input type="text" name="url" value="'.$row["url"].'" /></td>';	
								echo '<td width="30px"><input type="text" name="montant_caution" value="'.$row["montant_caution"].'" /></td>';	
								echo '<td><input type="text" name="titre" value="'.$row["titre"].'" /></td>';	
								echo '<td><input type="text" name="description" value="'.$row["description"].'" /></td>';
								echo '<td><input type="text" name="surface" value="'.$row["surface"].'" /></td>';
								echo '<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>';
							echo '<td><a href="affichGite.php"><img src="images/cancel.gif" title="Annuler"></a></td>';
							echo '<td><a href="affichGite.php?idgite='.$row["idgite"].'"><img src="images/delete.gif" title="Effacer"></a></td>';
						
							echo '</tr>';
							echo '</tbody>';
						echo '</table></form></th></tr>';
						}
						//sinon on affiche juste les infos du gite
						else 
						{
							echo '<tr align="left"><th colspan="11"><form action="affichGite.php?etatG=M&idgite='.$row["idgite"].'" method="post">';
							echo '<table width="800px" >';
							echo '<tbody>';
								echo '<tr >';
								echo '<td width="90px"><input type="hidden" name="nom" value="'.$row["nom"].'" />'.$row["nom"].'</td>';		
							//	echo '<td></td>';
								echo '<td width="60px"><input type="hidden" name="capacite" value="'.$row["capacite"].'" />'.$row["capacite"].'</td>';	
								echo '<td width="60px"><input type="hidden" name="url" value="'.$row["url"].'" />'.$row["url"].'</td>';	
								echo '<td width="30px"><input type="hidden" name="montant_caution" value="'.$row["montant_caution"].'" />'.$row["montant_caution"].'</td>';	
								echo '<td><input type="hidden" name="titre" value="'.$row["titre"].'" />'.$row["titre"].'</td>';	
								echo '<td><input type="hidden" name="description" value="'.$row["description"].'" />'.$row["description"].'</td>';
								echo '<td><input type="hidden" name="surface" value="'.$row["surface"].'" />'.$row["surface"].'</td>';
								echo '<td><INPUT src="images/edit.gif" title="Editer" type="image" name="envoi" Value="submit"></td>';
							
							
							echo '<td><a href="calendrier.php?idgite='.$row["idgite"].'"><img src="images/cal.gif" title="Agenda"></a></td>';
							echo '<td><a href="affichGite.php?img=A&idgite='.$row["idgite"].'"><img src="images/img.gif" title="photos"></a></td>';
							echo '<td><a href="affichGite.php?etatTarif=A&idgite='.$row["idgite"].'"><img src="images/tarif.gif" title="Tarifs"></a></td>';
							echo '<td><a href="affichGite.php?options=A&idgite='.$row["idgite"].'"><img src="images/options.gif" title="Options"></a></td>';
							
							echo '</tr>';
							echo '</tbody>';
						echo '</table></form></th></tr>';
						
						/*************************************					
						**************************************
						     Gestion des tarifs des gites
						**************************************					
						**************************************/
						
						// si etatTarif est rensign� ou affiche les tarifs ou un formulaire pour le modifier
							if (!empty($etatTarif) and ($row["idgite"]==$idgite) )
							{
						
							echo '<tr align="left"><th colspan="10">';
									
							/*affichage des tarifs*/
								$reqTarif="select t.idtarif, t.prix, t.statut_client,t.saison, p.idgite,t.commentaire  ";
								$reqTarif.="from TARIF t, POSSEDETARIF p ";
								$reqTarif.="where t.idtarif=p.idtarif and p.idgite=".$idgite;
							
								$resultreqTarif=$mysqli->query($reqTarif);
								$k=0;
								while ($rowReqTarif = $resultreqTarif->fetch_assoc())
								{
									
									if (($etatTarif=='A') or (($etatTarif=='E') && ($rowReqTarif["idtarif"]!=$_GET["idtarif"]))) 
									// si l'etatTarif= A  ou si l'�tat est Editer E mais que ce n'est pas le tarif que l'on d�sire
									//modifier alors on affiche les tarifs du gite normalement
									{
									
										echo '<form action="affichGite.php?idgite='.$row["idgite"].'&etatTarif=E&idtarif='.$rowReqTarif["idtarif"].'" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
										if ($k==0) //affichage de l'entete du tableau prix
										{
											echo '<thead >';
											echo '<tr><td width="90px">Tarif</td><td width="60px">Statut Client</td><td width="60px">Saison</td><td width="60px">Commentaire</td><th colspan="3" >action</th></tr>';
											echo '</thead>';
										}
										
										echo '
												<tr>
												
													<td width="90px"><input type="hidden" name="prix" value="'.$rowReqTarif["prix"].'" />'
														.$rowReqTarif["prix"].'
													</td>
													<td width="60px"><input type="hidden" name="statut_client" value="'.$rowReqTarif["statut_client"].'" />'
														.$rowReqTarif["statut_client"].'
													</td>
													<td width="25%"><input type="hidden" name="saison" value="'.$rowReqTarif["saison"].'" />'
														.$rowReqTarif["saison"].'
													</td>
													<td width="25%"><input type="hidden" name="commentaire" value="'.$rowReqTarif["commentaire"].'" />'
														.$rowReqTarif["commentaire"].'
													</td>
																									
													<td width="10px"><INPUT src="images/edit.gif" title="Editer" type="image" name="envoi" Value="submit"></td>
													
												<td width="10px"><a href="affichGite.php?idgite='.$row["idgite"].'&etatTarif=D&idtarif='.$rowReqTarif["idtarif"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer ce tarif ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												</tr>
											</table>
										</form>';
									}
									
									if (($etatTarif=='E') && ($rowReqTarif["idtarif"]==$_GET["idtarif"]))// si l'etatTarif= E alors on affiche un formulaire pour editer les tarifs
									{
									
										echo '<form action="affichGite.php?idgite='.$row["idgite"].'&etatTarif=S&idtarif='.$rowReqTarif["idtarif"].'" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
										if ($k==0) //affichage de l'entete du tableau prix
										{
											echo '<thead >';
											echo '<tr><td width="90px">Tarif</td><td width="60px">Statut Client</td><td width="60px">Saison</td><th colspan="3" >action</th></tr>';
											echo '</thead>';
										}
										
										echo '
												<tr>
												
													<td width="90px"><input type="text" name="prix" value="'.$rowReqTarif["prix"].'" />
													
													</td>
													<td width="60px"><select name="statut_client"><option selected>'.$rowReqTarif["statut_client"].'<option>EX<option>CE<option>CR</select>
													</td>';
													
													if ($rowReqTarif["saison"]==1)
													{
														$saisonTxt="Haute";
													}
													else 
													{
														$saisonTxt="basse";
													}
													echo '<td width="25%"><select name="saison"><option value="'.$rowReqTarif["saison"].'" selected>'.$saisonTxt.'<option value="1">Haute<option value="0">Basse</select>
													</td>
															<td width="25%"><input type="text" name="commentaire" value="'.$rowReqTarif["commentaire"].'" size="130" />
													</td>										
													<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
													<td><a href="affichGite.php?idgite='.$row["idgite"].'&etatTarif=A&idtarif='.$rowReqTarif["idtarif"].'"><img src="images/cancel.gif" title="Annuler"></a></td>
													<td><a href="affichGite.php?idgite='.$row["idgite"].'&etatTarif=D&idtarif='.$rowReqTarif["idtarif"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer ce tarif ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												</tr>
											</table>
										</form>';
										$etatTarif='A';
									}
						
									$k++;
								}
									echo '</th></tr>';
									
									// **********************************************************
									// on affiche une ligne vierge pour ins�rer un nouveau tarif
									// **********************************************************
									
								echo '<tr align="left"><th colspan="10">';
								echo '<form action="affichGite.php?idgite='.$row["idgite"].'&etatTarif=AD" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
									
										echo '<tr><th colspan="4">Ajout un d\' nouveau tarif</th></tr>';
										echo '
												<tr>
												
													<td width="90px"><input type="text" name="prix" size="8"/>
													</td>
													<td width="60px"><select name="statut_client"><option>EX<option>CE<option>CR</select>
													
													</td>
													<td width="25%"><select name="saison"><option value="0"> Basse<option value="1">Haute</select>
													
													</td><td width="90px"><input type="text" name="commentaire" size="100"/>
													</td>
																									
													<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
													
													
												</tr>
											</table>
										</form>';
								echo '</th></tr>';
							}
						
						
						}
						/*************************************					
						**************************************
						     Gestion des options des gites
						**************************************					
						**************************************/
						$listeOption=""; //pour stocker toutes les options déjà enregistré pour ce gite
						// si options est rensign� ou affiche les options ou un formulaire pour le modifier
							if (!empty($options) and ($row["idgite"]==$idgite) )
							{
						
							echo '<tr align="left"><th colspan="10">';
									
							/*affichage des options*/
								$reqOption="select t.idoption, t.option_tarif, t.denomination, p.idgite  ";
								$reqOption.="from OPTIONRESA t, POSSEDEOPTION p ";
								$reqOption.="where t.idoption=p.idoption and p.idgite=".$idgite;
							
								$resultreqOption=$mysqli->query($reqOption);
								$k=0;
								while ($rowreqOption = $resultreqOption->fetch_assoc())
								{
									
									
									
									if (($options=='A')) 
									// si l'option= A  ou si l'�tat est Editer E mais que ce n'est pas le option que l'on d�sire
									//modifier alors on affiche les options du gite normalement
									{
									$listeOption.=$rowreqOption["idoption"].",";
										echo '<form action="affichGite.php?idgite='.$row["idgite"].'&options=E&idoption='.$rowreqOption["idoption"].'" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
										if ($k==0) //affichage de l'entete du tableau options
										{
											echo '<thead >';
											echo '<tr><td width="90px">Option Tarif</td><td width="60px">Dénomination</td><th colspan="2" >action</th></tr>';
											echo '</thead>';
										}
										
										echo '
												<tr>
												
													<td width="90px"><input type="hidden" name="option_tarif" value="'.$rowreqOption["option_tarif"].'" />'
														.$rowreqOption["option_tarif"].'
													</td>
													<td width="60px"><input type="hidden" name="denomination" value="'.$rowreqOption["denomination"].'" />'
														.$rowreqOption["denomination"].'
													</td>

													
												<td width="10px"><a href="affichGite.php?idgite='.$row["idgite"].'&options=D&idoption='.$rowreqOption["idoption"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer cette option ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												</tr>
											</table>
										</form>';
									}
									
									// if (($options=='E') && ($rowreqOption["idoption"]==$_GET["idoption"]))// si l'options= E alors on affiche un formulaire pour editer les options
									// {
									
										// echo '<form action="affichGite.php?idgite='.$row["idgite"].'&options=S&idoption='.$rowreqOption["idoption"].'" method="post">
										
										
											// <table border="1" width="100%" rules="groups" class="rechClient">';
										// if ($k==0) //affichage de l'entete du tableau des options
										// {
											// echo '<thead >';
											// echo '<tr><td width="90px">Tarif</td><td width="60px">Dénomination</td><th colspan="3" >action</th></tr>';
											// echo '</thead>';
										// }
										
										// echo '
												// <tr>
												
													// <td width="90px"><input type="text" name="option_tarif" value="'.$rowreqOption["option_tarif"].'" />
													
													// </td>
													// <td width="25%"><input type="text" name="denomination" value="'.$rowreqOption["denomination"].'" size="130" />
													// </td>										
													// <td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
													// <td><a href="affichGite.php?idgite='.$row["idgite"].'&options=A&idoption='.$rowreqOption["idoption"].'"><img src="images/cancel.gif" title="Annuler"></a></td>
													// <td><a href="affichGite.php?idgite='.$row["idgite"].'&options=D&idoption='.$rowreqOption["idoption"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer cette option ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												// </tr>
											// </table>
										// </form>';
										// $options='A';
									// }
						
									$k++;
								}
									echo '</th></tr>';
									
									// ***************************************************************
									// on affiche une ligne vierge pour inserer une nouvelle option  *
									// ***************************************************************
									$listeOption.="0";
								echo '<tr align="left"><th colspan="10">';
								echo '<form action="affichGite.php?idgite='.$row["idgite"].'&options=AD" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
									

										// on affiche la liste d'option disponible et pas déjà attaché à ce gite	
										$reqTteOption="select idoption, option_tarif, denomination from OPTIONRESA where idoption not in (" .$listeOption.")";
										
										$resultreqTteOption=$mysqli->query($reqTteOption);
										
										if (mysqli_num_rows($resultreqTteOption)>0)
										{
											echo '
												<tr>
												<td>Ajouter une option</td>
													<td >
													<select name="idoption">';
											while ($rowreqTteOption = $resultreqTteOption->fetch_assoc())
											{
												echo '<option value="'.$rowreqTteOption["idoption"].'">'.$rowreqTteOption["denomination"].' -  prix :  '.$rowreqTteOption["option_tarif"].' Euro</option>';
											}
											echo '</select>';
											echo '</td>
														
																										
														<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>';
														
											echo '</tr>';
										}				
										echo'		
											</table>
										</form>';
								echo '</th></tr>';
							}
						

						/*************************************					
						**************************************
						     Gestion des images des gites
						**************************************					
						**************************************/
						
						// si img est renseigne on affiche les images ou un formulaire pour les modifier
							if (!empty($_GET['img']) and ($row["idgite"]==$idgite) )
							{
							//$img=$_GET['img'];
							echo '<tr align="left"><th colspan="10">';
							
							/**********************/		
							
							/*affichage des images*/
							
							/**********************/
							
								$reqImage="select idimage, idgite, titre_image,une,description_image,url  ";
								$reqImage.="from IMAGES";
								$reqImage.=" where idgite=".$idgite;
							
								$resultreqImage=$mysqli->query($reqImage);
								$k=0;
								while ($rowReqImage= $resultreqImage->fetch_assoc())
							
								{
									
									if (($img=='A') or (($img=='E') && ($rowReqImage["idimage"]!=$_GET["idimage"]))) 
									// si l'image= A  ou si l'�tat est Editer E mais que ce n'est pas l'image que l'on desire
									//modifier alors on affiche les images du gite normalement
									{ echo "toto";
									
										echo '<form action="affichGite.php?idgite='.$row["idgite"].'&img=E&idimage='.$rowReqImage["idimage"].'" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
										if ($k==0) //affichage de l'entete du tableau prix
										{
											echo '<thead >';
											echo '<tr><td width="90px">idimage</td><td width="60px">Titre image</td><td width="60px">Une</td><td width="60px">Description</td><td width="60px">Url</td><th colspan="3" >action</th></tr>';
											echo '</thead>';
										}
										
										echo '
												<tr>
												
													<td width="90px"><input type="hidden" name="idimage" value="'.$rowReqImage["idimage"].'" />'
														.$rowReqImage["idimage"].'
													</td>
													<td width="60px"><input type="hidden" name="titre_image" value="'.$rowReqImage["titre_image"].'" />'
														.$rowReqImage["titre_image"].'
													</td>
													<td width="25%"><input type="hidden" name="une" value="'.$rowReqImage["une"].'" />'
														.$rowReqImage["une"].'
													</td>
													<td width="25%"><input type="hidden" name="description_image" value="'.$rowReqImage["description_image"].'" />'
														.$rowReqImage["description_image"].'
													</td>
														<td width="25%"><input type="hidden" name="url" value="'.$rowReqImage["url"].'" />'
														.$rowReqImage["url"].'
													</td>											
													<td width="10px"><INPUT src="images/edit.gif" title="Editer" type="image" name="envoi" Value="submit"></td>
													
												<td width="10px"><a href="affichGite.php?idgite='.$row["idgite"].'&img=D&idimage='.$rowReqImage["idimage"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer cette image ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												</tr>
											</table>
										</form>';
									}
									
									if (($img=='E') && ($rowReqImage["idimage"]==$_GET["idimage"]))// si l'etatTarif= E alors on affiche un formulaire pour editer les tarifs
									{
									
										echo '<form action="affichGite.php?idgite='.$row["idgite"].'&img=S&idimage='.$rowReqImage["idimage"].'" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
										if ($k==0) //affichage de l'entete du tableau prix
										{
											echo '<thead >';
											echo '<tr><td width="90px">idimage</td><td width="60px">Titre image</td><td width="60px">Une</td><td width="60px">Description</td><td width="60px">Url</td><th colspan="3" >action</th></tr>';
											echo '</thead>';
										}
										
										echo '
												<tr>
												
													<td width="90px"><input type="text" name="idimage" value="'.$rowReqImage["idimage"].'" />
													
													</td>
													<td width="60px"><input type="text" name="titre_image" value="'.$rowReqImage["titre_image"].'" />
													</td>
													<td width="60px"><input type="text" name="une" value="'.$rowReqImage["une"].'" />
													</td>
															<td width="25%"><input type="text" name="description_image" value="'.$rowReqImage["description_image"].'"/>
													</td>	
<td width="25%"><input type="text" name="url" value="'.$rowReqImage["url"].'" size="130" />
													</td>													
													<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
													<td><a href="affichGite.php?idgite='.$row["idgite"].'&img=A&idimage='.$rowReqImage["idimage"].'"><img src="images/cancel.gif" title="Annuler"></a></td>
													<td><a href="affichGite.php?idgite='.$row["idgite"].'&img=D&idimage='.$rowReqImage["idimage"].'" onclick="return confirm(\'Etes vous s�re de vouloir supprimer cette image ?\');"><img src="images/delete.gif" title="Supprimer"></a></td>
												</tr>
											</table>
										</form>';
										$img='A';
									}
						
									$k++;
								}
									echo '</th></tr>';
									
									// **********************************************************
									// on affiche une ligne vierge pour inserer une nouvelle image
									// **********************************************************
									
								echo '<tr align="left"><th colspan="10">';
								echo '<form action="affichGite.php?idgite='.$row["idgite"].'&img=AD" method="post">
										
										
											<table border="1" width="100%" rules="groups" class="rechClient">';
									echo '<thead >';
											echo '<tr><td width="90px">idimage</td><td width="60px">Titre image</td><td width="60px">Une</td><td width="60px">Description</td><td width="60px">Url</td><th colspan="3" >action</th></tr>';
											echo '</thead>';
										echo '<tr><th colspan="4">Ajout Nouvelle image</th></tr>';
										echo '
												<tr>
												
													<td width="90px"><input type="text" name="idimage" size="8" readonly/><input type="hidden" name="idgite" value="'.$row["idgite"].'"/>
													</td>
													<td width="90px"><input type="text" name="titre_image" size="8"/>
													</td>
													<td width="90px"><input type="text" name="une" size="8"/>
													</td>
													
													</td><td width="90px"><input type="text" name="description_image" size="100"/>
													</td>
														</td><td width="90px"><input type="text" name="url" size="100"/>
													</td>											
													<td><INPUT src="images/save.gif" title="Enregistrer" type="image" name="envoi" Value="submit"></td>
													
													
												</tr>
											</table>
										</form>';
								echo '</th></tr>';
							}
	
					}

			echo "</table>";
		
		?>
		</div>
	</div>

</body>

<?php
	include('includes/footer.php');
?>