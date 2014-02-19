<?php

	include('includes/header.php');
/*********************************************************************************
-> Ce fichier permet de v�rifier la possibilit� d'une r�servation selon 2 dates
-> Si ok il affiche un formulaire de recherche de client 
-> possibilit� de cr�ation de client �galement
**********************************************************************************/

	
?>

<body>


	<div id="content" style="position:relative; float:left;">
<?php 
if(isset($_GET["idgite"]))
{
	$idgite= $_GET["idgite"];
	$_SESSION["idgite"]=$idgite ;
	$_SESSION['resa']['idgite']=$idgite;
}
else
{
	$idgite=$_SESSION["idgite"];
}
echo "IDEGITE :".$idgite;
//ENVOIE DES DATES VERS LA PAGE DE FORMULAIRE & TEST
if(isset($_POST) && !empty($_POST['date_fin']) && !empty($_POST['date_debut'])) // v�rification element dans POST et date_deb et fin non vide et different de 0
	{
		
		
		$date_debut  = $_POST["date_debut"];
		$date_fin  = $_POST["date_fin"];
		$resaPossibleFinal=false;
		
		if ($date_debut<$date_fin) 
		{
			if(!verifIndispo($date_debut,$date_fin)) // v�rification disponibilit�s des dates
			{
				echo "<p> R�servation indisponible</p>";
			}
			else
			{	
						if ($idgite==1)
						{
							$giteATester=2;
							$resaPossibleEnsemble=false;
							while ($giteATester<=8 and $resaPossibleEnsemble==false)
							 {
								$resaPossibleEnsemble=verifReservation ($date_debut,$date_fin,$giteATester);
								$giteATester++;
							 }
						
							if ($resaPossibleEnsemble)$resaPossibleFinal=true;
						}
						else
						{
							if (verifReservation ($date_debut,$date_fin,$idgite)) // v�rifie les r�servations d�j� effectu�es
							{
								echo "<p> Resa possible verif</p>";	
								$resaPossibleFinal=true;
							}
							else
							{
								if ($idgite!=1 and $idgite!=8) // v�rifie si le centre est disponible
								{
								$req1 = "SELECT idgite, capacite FROM `GITE` WHERE  idgite<>".$idgite." AND capacite = (SELECT capacite FROM GITE WHERE idgite =".$idgite.")";
									echo $req1;
								$result1=$mysqli->query($req1);
								
									while ($ressql1 = $result1->fetch_assoc())
										{ 		
											$idgiteFrere=$ressql1['idgite'];
											$capaciteFrere=$ressql1['capacite'];			
										} 
										if (verifReservation($date_debut,$date_fin,$idgiteFrere)) // proposition d'un g�te similaire
												{
													echo "Ce g�te n'est pas disponible mais un g�te de m�me capacit� est libre";
													echo 'R�servez un <a href="?page_id=20&idgite='.$idgite.'&date_debut='.$date_debut.'&date_fin='.$date_fin.'">G�te de m�me capacit�</a>';
													//***************faire le lien vers l'autre g�te***************//
													$idgite=$idgiteFrere;
													$resaPossibleFinal=true;
											
												}	
										else
										{
										$req2 = "SELECT idgite, capacite FROM `GITE` WHERE capacite ="; // proposition d'un g�te de capacit� sup�rieur
											switch ($capaciteFrere) //verif de la capacit� sup suivante
											{
											case 2:
											$cap = 4;
											break;
											
											case 4:
											$cap = 7;
											break;
											}
											$req2.=$cap;// capacit� = $cap
											echo $req2;
											$result2=$mysqli->query($req2);
											$trouve=false;
											while ($ressql2 = $result2->fetch_assoc() and $trouve==false)
											{ 		
												$idgiteFrere=$ressql2['idgite'];
												if(verifReservation ($date_debut,$date_fin,$idgiteFrere))
												{
													$trouve=true;	
												}
											} 
												if ($trouve==true)
												{
												echo "<p>Il n'y a aucun g�te de cette capacit� de disponible mais un gite de ".$cap. " personnes est libre pour cette p�riode</p>";
												echo '<a href="?page_id=20&idgite='.$idgiteFrere.'&date_debut='.$date_debut.'&date_fin='.$date_fin.'">G�te de m�me capacit�</a>';
													$resaPossibleFinal=false;
													//***************faire le lien vers l'autre g�te***************//
												} else 
													{
														echo "<p>Il n'y a pas de g�tes disponibles pour cette date</p>";	
													}
										} // fin else proposition d'un g�te similaire
								} // fin if test r�servation du centre complet
							} // fin else verifReservation
						}
							//				} // fin else verifSemaineForce
			} // fin else verif indispo
		} // fin if date d�but inf�rieur � date fin
	}
		else
			{
				echo "<p>Votre date de d�but doit proc�der votre date de fin<p>";
			}
	
		if ($resaPossibleFinal) // Si la r�servation est possible ou une r�servation alternative est possible lancement calcul
		{	
			$date_debut=$_POST["date_debut"];
			$date_fin =$_POST["date_fin"];
			$tarifMax = calculTarif($date_debut,$date_fin,$idgite); // pour cheminot = 0
			if($tarifMax==0) // gestion d'un affichage si la r�servation propose un g�te fr�re ou de capacit� sup�rieur
			{
				echo "<p>Pas de tarif disponible pour ces dates pour cause de r�servation</p>";
			}
			else 
				{
				// ************************afficher tarif � partir de ***************************
				echo "<p>Votre tarif � partir de ".$tarifMax." euros</p>";
				}
		
			$_SESSION['resa'] = array(
			'idgite' => $_POST['idgite'],  
			'date_debut' => $_POST['date_debut'],
			'date_fin' => $_POST['date_fin'],
			'tarif' => $tarifMax, ); //***********verief calcul tarif valeur*****************
	
		//header('Location:formulaire.php');
			
		}
	else {
		
		echo " Aucune r�servation n'est possible pour cette p�riode."; // si submit avec absence de dates	
		echo '<a href="javascript:history.go(-1)">Retour</a>';
		}


/********************************************
*											*
*											*	
*         Integration formulaire Client		*
*											*
*											*
********************************************/
		?>	


<h2>R�capitulatif de votre R�servation</h2>
		
	<div class="fiche_recap"> <!-- recapitulatif date, gite et prix-->
		<ul>
			<li><?php echo "Vous avez s�lectionn� le ".$idgite; ?></li>
			<li><?php echo "Date de d�but le ".$_SESSION['resa']['date_debut']; ?></li>
			<li><?php echo "Date de fin le ".$_SESSION['resa']['date_fin']; ?></li>
			<li><?php echo "Pour un tarif maximum de ".$_SESSION['resa']['tarif'];?></li>
		</ul>
	</div>

	<a href="">Souhaitez-vous r�server un autre g�te</a>
		
<h2>Recherche Client</h2>	
		
		<div class="connexion_content">
		  
			<div class="enregistrement"> <!-- enregistrement de l'internaute-->
				<form action="<?php $_SERVER['PHP_SELF']; ?>?etat=R" method="post">
					<fieldset>
						<legend>Recherche</legend>
						<li>
							<label for=login>Email</label>
							<input id=login name=login type=email placeholder="exemple@domaine.com" >
						</li>
						<li>
							<label for=nom>Nom</label>
							<input id=nom name=nom type=text placeholder="votre nom" >
						</li>				
						<button type=submit>Rechercher</button>	
					</fieldset>
				</form>
			</div>
			<?php
			// Affichage du r�sultat de la rechercher
			if (isset($_GET["etat"]))
			{
				$reqClient="select * from CLIENTS where ";
				if(!empty($_POST["login"]))
				{
					$reqClient.="email like'".$_POST["login"]."'" ;
					
					if(!empty($_POST["nom"]))
					{
						$reqClient.=" and nom like '%".$_POST["nom"] ."%'" ; 
					}
				}
				else
				{
					if (isset($_POST["nom"]))
					{
						$reqClient.=" nom like '%".$_POST["nom"]."%'" ;
					}
				}
			
				$resultReqClient = $mysqli->query($reqClient);
					
							
				echo '<table border="2"  rules="groups" class="rechClient">'; 
				echo '<thead valign="top">';
				echo "<tr><td>IDClient</td><td>Nom</td><td>Pr�nom</td><td>Entreprise</td><td>Adresse</td><td>CP</td><td>Ville</td><td>Pays</td><td>port</td><td>email</td></tr>";
				echo '</thead>';
					while ($row = $resultReqClient->fetch_assoc())
					{
						echo '<tbody>';
						echo '<tr>';
						echo "<td>".$row["idclient"]."</td>";						
						echo "<td>".$row["nom"]."</td>";	
						echo "<td>".$row["prenom"]."</td>";	
						echo "<td>".$row["entreprise"]."</td>";	
						echo "<td>".$row["adresse"]."</td>";	
						echo "<td>".$row["codepostal"]."</td>";	
						echo "<td>".$row["ville"]."</td>";	
						echo "<td>".$row["pays"]."</td>";	
						echo "<td>".$row["port"]."</td>";	
						echo '<td><a href="resaCommande.php?idclient='.$row["idclient"].'&idgite='.$idgite.'">'.$row["email"].'</td>';	
						echo "</tr>";
						echo '</tbody>';
					}
				echo "<table>";
			}
			
			?>
			
			<div class="connexion"> <!-- connexion de l'internaute-->
				<form action="reservation_action.php?etat=2" method="post">
					<fieldset>
						<legend>Connexion</legend>
						<li>
							<label for=login>Email</label>
							<input id=login name=login type=email placeholder="exemple@domaine.com" required>
						</li>
						<li>
							<label for=password>Mot de passe</label>
							<input id=password name=password type=password  required>
						</li>
						<a href="">Mot de passe oubli�?</a>   <!--	****************procedure � dev***********************-->
						
						<button type=submit>Se connecter</button>
					</fieldset>
				</form>
			</div>
		</div>

	<?php 
if($etat==1)
{
		$Clef = "Matteo1234567890";
		if($_POST["login"] != "" && $_POST["password"] != "" && $_POST["loginConfirm"] !="")
		{
			  $mail		= $_POST["login"];
			  $pass		= Cryptage($_POST["password"],$Clef) ;
			  $pass		= utf8_encode($pass); 
 
			 $sqlVerifExistant= "SELECT email,mp from clients WHERE email ='".$mail."'" ; //verif mail unique
			 
			 $result=$mysqli->query($sqlVerifExistant);
			 if ($row=$result->fetch_Assoc())
			 {
				echo "Adresse email d�j� enregistr�";  // redirection � faire
			 }
			 else
			 {
	?>
		 
		<h2>Formulaire clients</h2>
			<form action="reservation_action.php?etat=2" method="post">
			  <fieldset>
				   <legend>Votre identit�</legend>
				<ol>
						<input id=login name=login type=hidden value="<?php echo $mail;?>" required>
					
						<input id=password name=password type=hidden value="<?php echo $pass;?>" required>
					
						<?php echo '<br/>'.$pass.'<br/>';?> 
					<li>
						<label for=nom>Nom :</label>
						<input id=nom name=nom type=text required>
					</li>
					<li>
						<label for=prenom>Pr�nom :</label>
						<input id=prenom name=prenom type=text required>
					</li>
					<li>
						<label for=naissance>Date de naissance :</label>
						<input id=naissance name=naissance type=date  required>
					</li>
					<li>
					  <label for=cheminot>�tes-vous cheminot ?</label>
						<input type="radio" name="cheminot" value="1" required /><label for="cheminotoui">Oui</label>
						<input type="radio" name="cheminot" value="0" required /><label for="cheminotnon">Non</label> <!--*******detection oui ouverture demande 					****region**** 
				****code_cheminot**** asynchrone -> ajax   // PARITE JS

					<label for=region>�tes-vous un cheminot de la r�gion Alsace ?</label>
						<input type="radio" name="region" value="1" required/><label for="regionoui">Oui</label>
						<input type="radio" name="region" value="0" required/><label for="regionnon">Non</label> 
					
					<label for=region>Entrez vote code cheminot</label>
					<input type="number"  name="region" min="0" max="10">



*********************************************************************-->
					</li>
					<li>
						<label for=entreprise>Si vous repr�sentez une entreprsie, son nom :</label>
						<input id=entreprise name=entreprise type=text  required>
					</li>
					<li>
					  <label for=adresse>Adresse :</label>
					  <textarea id=adresse name=adresse rows=5 required></textarea>
					</li>
					<li>
					  <label for=codepostal>Code postal :</label>
					  <input id=codepostal name=codepostal type=text required>
					</li>
					<li>
					  <label for=ville>Ville :</label>
					  <input id=ville name=ville type=text required>
					</li>
					<li>
					  <label for=pays>Pays :</label>
					  <input id=pays name=pays type=text required>
					</li>
					<li>
					  <label for=tel>Telephone :</label>
					  <input id=tel name=tel type=text required>
					</li>
					<li>
					  <label for=port>Portable :</label>
					  <input id=port name=port type=text required>
					</li>
					<li>
					  <label for=news>Voulez-vous recevoir notre newsletter :</label>
						<input type="radio" name="news" value="1" required /><label for="newsoui">Oui</label>
						<input type="radio" name="news" value="0" required /><label for="newsnon">Non</label> 
					</li>
				</ol>
<!--	************************************************************captcha !!!*********************************************************
		********************************************************************************************************************************
-->
				  <button type=submit>S'enregistrer</button>

			</fieldset>
		</form>
	<?php
				} // fin else
	} // fin if
} // fin if		
	
	
	




	include('includes/footer.php');
?>