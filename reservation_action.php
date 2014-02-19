<?php
require('includes/header.php');

//formulaire de réservation gîte le Metzval

/**
 * SESSION
 *GESTION SESSION
 * Stockage des variables de session
 * Test multi insertion
 * Insertion des données clients ou Test validité connexion
 * Vérification données connexion
 * Calcul du tarif en enfonction du statut
 * Envoi des données de payement (mode payement/nombre enfants, adultes) --> payement.php
 *
 * Creation: 06/11/2013/ 
*/

/**************************************************/
/******* SESSION **************************
/************************************************/
$resaEncours = $_SESSION['resaEncours'];
$monTab=$_SESSION['Mesresa'];
$resaPrecedente = $resaEncours	- 1 ;

/* récupération post formulaire.php */
$login = strtolower(secInput($_POST['login'])); 
echo "voici le login : " . $login;
//testVar($login);

/* stockage des variables du post du formulaire.php */
$date_debut = $monTab[$resaEncours]['date_debut'];
$date_fin 	= $monTab[$resaEncours]['date_fin'];
$idgite 	= $monTab[$resaEncours]['idgite'];
$tarif 		= $monTab[$resaEncours]['tarif'];

/* ajout du login dans la session */
$_SESSION['login'] = $login;
$monTab[$resaEncours]['login'] = $login;
$_SESSION['Mesresa']=$monTab;
//testVar2($monTab,"test monTab","monTab");


/* capacite du gite -> test du nombre adultes et enfants conforment */
$cap = $_SESSION['gite_tab']['capacite'];



	if(isset($_POST["login"])) 
	{
		$login     	= strtolower(secInput($_POST['login']));
		// $pass2      = Cryptage($_POST["password"], $Clef) ;
		// $pass2		= utf8_encode($pass2);

		// $sql = "SELECT nom,prenom,mp,email FROM CLIENTS WHERE email = '".$login."'";
		// $result = $mysqli->query($sql);

			// while ($row = $result->fetch_Assoc()) 
			// {
	
				// if  (utf8_decode($row['MP'])==$pass2)
				// {
						// echo "<p>mot de passe OK, formulaire de réservation</p>";
						// $LoginOk=true;
				// }
				// else 
				// {		
					// echo "<p>mot de passe KO Merci de recommencer</p>"; 
					//****************redirection à faire***************
				// }
					
			// } // fin while
		
		
		// $LoginOk=false;
		// $Clef = "Matteo1234567890";
		$etat=$_GET['etat'];	
		if($etat==2)
		{
			if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['naissance']) ) 
			/* verief presence argument -> lance la boucle, les arguments sont obligatoires et seront verif en js */
			{ 
				echo 'Votre nom est '.$_POST['nom'].' et votre prenom est '.$_POST['prenom']; // on affiche nos résultats test
				
				// récupération des données
				$mail= strtolower(secInput($_POST['login']));
				$pass =$_POST['password'];
				$nom= secInput($_POST['nom']);
				$prenom= secInput($_POST['prenom']);
				$naissance= secInput($_POST['naissance']);
				$cheminot= secInput($_POST['cheminot']);
				$entreprise= secInput($_POST['entreprise']);
				$adresse= secInput($_POST['adresse']);
				$codepostal= secInput($_POST['codepostal']);
				$ville= secInput($_POST['ville']);
				$pays= secInput($_POST['pays']);
				$tel= secInput($_POST['tel']);
				$port= secInput($_POST['port']);
				$datecrea=date("Y-m-d H:i:s");
				$news= secInput($_POST['news']);
				$cheminotRegion = $_POST['ce_cheminot'];
				$cheminotCode = secInput($_POST['code_cheminot']);
				
				//insertion dans la db
				$sql = "INSERT INTO CLIENTS (email,mp,nom,prenom,date_naissance,cheminot,code_cheminot,region,entreprise,adresse,codepostal,ville,pays,tel,port,creation,newsletter) VALUES ('".$mail."','".$pass."','".$nom."','".$prenom."','".$naissance."','".$cheminot."','".$cheminotRegion."','".$cheminotCode."','".$entreprise."','".$adresse."','".$codepostal."','".$ville."','".$pays."','".$tel."','".$port."','".$datecrea."','".$news."')";
				echo $sql;
				$mysqli->query($sql);
				$enreTest=false;
				
					/* si la requête s'est bien passée, on affiche un message de succès */
				  if($mysqli)
				  {
					echo "L'inscription s'est bien déroulée" ;   // redirection vers page récapitulative (post) de la réservation et permet la connexion
				  } 
				  else 
				  {
					  echo '<br/> Merci de vous enregistrer à nouveau';
				  }
			}
			else
			{
				echo '<br/> Veuillez remplir le formulaire, merci<br/>';  	// else erreur 
			}
		} // fin if etat = 2
	} // fin if	isset post




?>

		<?php 	/** 
						* gestion cheminot ou non et tarif 
					*/
					$statutCheminot='EX';
					$reqCheminot = "SELECT c.cheminot,c.code_cheminot,c.region FROM CLIENTS c WHERE email='".$login."'"; // requete boolean cheminot
					$resultCheminot = $mysqli->query($reqCheminot);

					while ($ressqlCheminot = $resultCheminot->fetch_assoc()) /* parcours tableau récupération statut, region */
					{
						$testCheminot=$ressqlCheminot['cheminot'];
						$regionCheminot=$ressqlCheminot['region']; 
					}

					if($testCheminot) // si statut cheminot = 1
					{
						if($regionCheminot) // si statut codecheminot = 1 alors t.statut_client ='".$statutCheminot'" -> cheminot_region (calculTarif)
						{
							$statutCheminot='CR';
//							echo 'CHOUCROUTE <br>';
//							
						}
						else // sinon statut codecheminot = 0 alors t.statut_client ='".$statutCheminot'" -> cheminot_externe (calculTarif)
						{
							$statutCheminot='CE';
//							echo 'PAIN <br>';
						}
						
					} // fin if
					else // sinon statut cheminot = 0 alors t.statut_client ='".$statutCheminot'" -> externe (calculTarif)
					{
							$statutCheminot='EX';
//							echo 'CIVIL <br>';
					} // fin else
					//testVar2($statutCheminot,"testcheminot","testcheminot");
					$tarif2 = calculTarif($date_debut,$date_fin,$idgite,$statutCheminot); 
					$monTab[$resaEncours]['tarif']=	$tarif2;
					//testVar2($monTab[$resaEncours]['tarif']);
					//testVar2($_SESSION['Mesresa']);
					$_SESSION['Mesresa'][0]['tarif']= $monTab[$resaEncours]['tarif'];
					//testVar2($_SESSION['Mesresa']);
					echo "<br> Le tarif après vérification ".$tarif2; 
				?>


	
	<h2>Récapitulatif de votre Réservation</h2>
		<div class="row">
			<div class="small-11 small-centered columns">
			
				<ul> <!-- récupération des éléments de session, stockés en début de page -->
					<li><?php echo "Vous avez sélectionné le gîte ".$idgite; ?></li>
					<li><?php echo "Date de début le ".$date_debut; ?></li>
					<li><?php echo "Date de fin le ".$date_fin; ?></li>
					<li><?php echo "Pour un tarif de ".$tarif2; ?></li>
				</ul>
			</div>
		</div>
		
	<div class="row">
		<div class="small-11 small-centered columns">	
			<form action="payement.php" method="post">
				<fieldset>
					<legend>Vos Dates</legend>

						<input type="hidden" name="login" <?php echo 'value="'.$login.'"'; ?> required>

						<li>
							<label for=nom>Date Arrivée :</label>
							<input type="date" name="date_debut" <?php echo 'value="'.$date_debut.'"'; ?> readonly>
						</li>
						<li>
							<label for=nom>Date Départ :</label>
							<input type="date" name="date_fin" <?php echo 'value="'.$date_fin.'"'; ?> readonly>
						</li>
						<li>
							<label for=nb_adulte>Nombre d'adultes :</label>
							<input id=nb_adulte name=nb_adulte type=number min=0 max="<?php echo $cap;?>" required>
						</li>
						<li>
							<label for=nb_enfantTotal>Nombre d'enfants:</label>
							<input id=nb_enfantTotal name=nb_enfantTotal type=number min=0 max="<?php echo $cap;?>" required>
						</li>
						<li>
							<label for=nb_enfant>Dont nombre d'enfants de plus 13 ans :</label>
							<input id=nb_enfant name=nb_enfant type=number  min=0 max="<?php echo $cap;?>" required>
						</li>

						<li>

							<label for=choixOption>Choix des options</label>
							<?php

							$reqOption = "SELECT idoption, denomination FROM OPTIONRESA"; // requête de la liste des options

							$sqlOption=$mysqli->query($reqOption);

							while ($resqlOption=$sqlOption->fetch_Assoc()) // affichage liste dynamiquement avec value correspondant à l'idoption
							{
								echo '<input type="checkbox" name="option[]" value='.$num_option=$resqlOption['idoption'].' />'.$nom_option=$resqlOption['denomination'];
							}

							?>
						</li>


							<?php

							/* INFORMER LE CLIENT DES MODALITES -> PAYEMENT -30 ET RISQUE DE PAYER PAR CHEQUE */

							if(nbJours(date("Y-m-d H:i:s"),$date_debut)<=30) /* test une réservation avec +/- 30 jours d'interval */
							{

								$testCb = true; //	permet test dans payement.php
							?>
								<li>
								<input type="hidden" name="J-30" value="J-30" required>  <!--	permet test dans payement.php-->
									<label for=payementJ-30>Votre moyen de payement :</label>
									<input type="radio" name="payementJ-30" value="1" /><label for="payementcbJ-30">Carte bancaire</label>
									<input type="radio" name="payementJ-30" value="0" /><label for="payementchequeJ-30">Chèque</label>
								</li>
							<?php
							}
							else
							{
								$testCb = false; //	permet test dans payement.php
							?>
								<li>
									<input type="hidden" name="J+30" value="J+30" required> <!--	permet test dans payement.php-->
									<label for=payementJ+30>Votre moyen de payement :</label>
									<input type="radio" name="payementJ+30" value="1" /><label for="payementcbJ+30">Carte bancaire</label>
									<input type="radio" name="payementJ+30" value="0" /><label for="payementchequeJ+30">Chèque</label>
								</li>
							<?php
							}

							if($idgite==1)
							{
								echo "Vous ne pouvez réserver un autre gîte en même temsp que le centre complet <br />";
								echo "Pour réservez un autre gîte pendant une autre date, validez cette réservation ou vous pouvez contacter le gîte";
							}
							else {
							?>

								<label for=resa2>Sélectionné un autre gîte à réserver</label>

								<input type="radio" id="multi-gite" name="radios_0" value="1"  CHECKED required>&nbsp;<label for="multi-gite" id="label-multi-gite">Réservez un autre gîte</label> <!-- sélection input masque input resa2, reservation ou non -->

			<br />					
								<input type="radio" id="un-gite" name="radios_0" value="0" required>&nbsp;<label for="un-gite" id="label-un-gite">Valider votre réservation</label>

								<select id="gite-select" name="gite-select"> 
							<?php

									$reqGite = "SELECT idgite,nom FROM GITE WHERE idgite!=1 AND idgite !=".$idgite;

									//echo $reqGite;
									//testVar2($reqGite,"REQ","REQ");

									$sqlGite=$mysqli->query($reqGite);

									while ($resqlGite=$sqlGite->fetch_Assoc()) 
									{
										/* liste des gîte, value = idgite sélectionné */
										echo '<option value='.$idGite=$resqlGite['idgite'].'  id="gite-select2" name="gite-select2" >'.$nomGite=$resqlGite['nom'].'</option>';	
									}
								}
								?>
								</select>

								<label id='label-code-promo'>Code Promotion</label><input type="text" name="code-promo" value="XXXXXXXX" size="10" id="code-promo" >


							<p id="information-resa">Vous serez redirigés vers la page du gîte sélectionnée, votre réservation en cours sera réglé au moment de la validation de votre prochaine réservation</p>

					<button type="submit" id="reservation" name="reservation">Réserver !</button> 
					<!-- test mode payement sur la page payement.php en fonction du post -->

				</fieldset>
			</form>
		</div>
	</div>
	
	 			
<?php		
			testVar2($resaEncours,"resaEncours","resaEncours");
			testVar2($monTab[$resaPrecedente],"Recapitulatif des resas 0","Recapitulatif des resas 0");
			testVar2($monTab[$resaEncours],"Recapitulatif des resas 1","Recapitulatif des resas 1");;

require('includes/footer.php'); ?>