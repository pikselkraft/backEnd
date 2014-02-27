<?php

require('includes/header.php');
ob_start(); // tamporisation de sortie -> fonctionnement du header (empêche envoie données cleint avant ob_end)

// INFORMATION PAYEMENT -> AVANT LE TUNNEL OU INFORMATION CHÈQUE

/**
	 * SESSION
	 * GESTION SESSION ET PAGE PRECEDENTE
	 * STOCKAGE DES VARIABLES DE SESSION
	 * GESTION CODE PROMO
	 * GESTION RESERVATION
	 * CALCUL DU COÛT DES OPTIONS
	 * INSERTION DES INFORMATIONS DANS LA TABLE RESERVATION --> STATUT IMPAYÉ
	 * INSERTION DES INFORMATIONS DANS LA TABLE COMMANDE --> STATUT IMPAYÉ
	 * DIFFÉRENCIATION MODE DE PAYEMENT CB ET CHÈQUE // FIN DEV --> FAIRE LE PROCESSUS ET DIFFÉRENCIER CB/CHÈQUE (OPTIMISATION)
	 * INSERTION DES INFORMATIONS DANS LA TABLE COMMANDERESERVER --> SELON CAS ET CHANGEMENT STATUT RESA
	 * RÉCAPITULATIF DE LA RÉSERVATION
	 * SI CB -> TUNNEL DE PAYEMENT / SI CHÈQUE --> HOME

	 * CREATION: 06/11/2013 
*/

/**************************************************/
/*******GESTION DES VARIABLES ******
/************************************************/
// variable session resaencours
$resaEncours = $_SESSION['resaEncours']; 
$monTab=$_SESSION['Mesresa'];
$resaPrecedente = $resaEncours	- 1 ;	
//testVar($monTab);


/* stockage dans des variables de post du "formulaire.php" */
$login 		= $monTab[0]['login']; // uniquement le login pour la première resa
$cheminot 	= $monTab[$resaEncours]['cheminot'];
$date_debut = $monTab[$resaEncours]['date_debut'];
$date_fin 	= $monTab[$resaEncours]['date_fin'];
$idgite 	= $monTab[$resaEncours]['idgite'];
$tarif 		= $monTab[$resaEncours]['tarif'];
$_SESSION['Mesresa'] = $monTab;	

/* capacite du gite -> test du nombre adultes et enfants conforment */
$cap = $_SESSION['gite_tab']['capacite'];

/* option en $_post */
$option 	= $_POST['option'];


/**************************************************/
/*******GESTION CODE PROMOTION ******
/************************************************/
if(isset($_POST['code-promo']))
{
	if($_SESSION['code']==1)
	{
		echo "</br> un seul code promotion par commande est accepté";
	}
	else
	{
		
		$_SESSION['code'] = 0;
		$reqCode = "SELECT code, nb, remise FROM CODEPROMO WHERE code ='".$_POST['code-promo']."' AND actif=1";
		echo $reqCode;
		$resultCode = $mysqli->query($reqCode);
									
		while ($ressqlCode = $resultCode->fetch_assoc()) 
		{
			echo "code promo validation à faire";
			$codeReq 		= $ressqlCode['code'];
			$nbCode  		= $ressqlCode['nb'];
			$valeurRemise   = $ressqlCode['remise'];
			
			if($nbCode>0) // test nombre de code restant
			{
				$nbCode--;
				echo "<br/> Code bon <br />";
				$sqlSuppCode = "UPDATE CODEPROMO
								SET nb ='".$nbCode."'
								WHERE code ='".$_POST['code']."'"; // mise à jour nombre codepromo
				
				$resultSuppCode = $mysqli->query($sqlSuppCode);
				
				if($mysqli)
				{
					echo"votre code promotion a été appliqué";
					echo "<ul><li>".$tarif."</li><li>".$valeurRemise."</li></ul>";
					$calculRemise = ($tarif * $valeurRemise) / 100;
					testVar($calculRemise);
					$_SESSION['code']++;			
				}else {echo"votre code promotion n'est plus valide";}
			}
			else 
			{
				echo "<br/> Code non valide <br />";
			}	
		} // fin while req codepromo
	} // fin if test session code
} else {echo"pas de code promo à vérifier";}

/**************************************
	*	GESTION NB Adulte et Enfant 
*************************************/
if(isset($_POST['nb_adulte']) and isset($_POST['nb_enfant']))
{
	$nb_adulte= $_POST['nb_adulte'];
	$nb_enfant= $_POST['nb_enfant']; // de moins de 13 ans
	$nb_enfantTotal=$_POST['nb_enfantTotal'];
	$cap=$_SESSION['gite_tab']['capacite']; // capacité du gîte
	
	
	if((!verifCapacite($nb_adulte,$nb_enfant,$cap)) or ($nb_enfant>$nb_enfantTotal))
	{
		?>
		<p> Le nombre d'adultes et d'enfants dépassent la capacité du gîte</p>

		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

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
				<li><button type="submit" id="reservation" name="reservation">Changer vos informations</button> </li>

		</form>
		
		<?php	
	} 
	else 
	{
		echo "test nb true";
		$monTab[$resaEncours]['nb_adulte'] = $nb_adulte;
		$monTab[$resaEncours]['nb_enfant'] = $nb_enfant;
		$monTab[$resaEncours]['nb_enfantTotal'] = $nb_enfantTotal;
		$_SESSION['Mesresa'] = $monTab;	
		//testVar($monTab);
	}	
}

/*******************
	* Gestion Option
********************/

if(empty($option)) // test présence d'une option
{echo(" <br /> Vous n'avez pas sélectionnés d'options");} 
else
{
	$n = count($option); 
	for($i=0; $i<$n; $i++)
	{
		echo "<br/> compte $i: ".$i;
		echo "<br /> vous avez sélectionné l'option : " . $option[$i]; // parcours des options sélectionnées
		
		$reqOption = "SELECT idoption, option_tarif,denomination FROM OPTIONRESA WHERE idoption='".$option[$i]."'"; // une requête par option sélectionnée						
		$resultOption = $mysqli->query($reqOption);

		while ($ressqlOption = $resultOption->fetch_assoc()) 
			{
				$optionId[$i] = $ressqlOption['idoption']; /* à tester avec insert choix option */
				testVar2($optionId[$i],"optionId","optionId");
				echo "Vous avez choisis l'option '" . $ressqlOption['denomination'] . "' tarifée " . $ressqlOption['option_tarif'] . " euros <br />";
				$tarifOption = $tarifOption + $ressqlOption['option_tarif']; /* parcours tarifs sélectionnés et addition */
				echo "Le tarif de vos options" . $tarifOption; // détaille evolution calcul
			}
			$monTab[$resaEncours][$optionId[$i]] = $optionId[$i];
			testVar2($monTab,"test monTab","monTab");
	}
	
	$monTab[$resaEncours]['tarifOption'] = $tarifOption;
	$_SESSION['Mesresa'] = $monTab;	
	testVar2($optionId,"test idoption","idoption"); /* à tester avec insert choix option */
	//testVar($monTab);
} // fin else option

/************************************************
	* Redirection si multi resa et insertion données monTab
************************************************/
testVar2($_POST['radios_0'],"radios_0","radios_0");
if($_POST['radios_0']==1)
   {
		// stockage dans des variables de post du "formulaire.php"
		$monTab[$resaEncours]['tarifOption']	= $tarifOption;
		$monTab[$resaEncours]['nb_adulte']		= $nb_adulte;
		$monTab[$resaEncours]['nb_enfant']		= $nb_enfant;
		$monTab[$resaEncours]['code_promo']		= $code_promo;
	
		$_SESSION['Mesresa'] = $monTab;
		$monTab = $_SESSION['Mesresa'];
	
		$resaEncours = count($monTab);

		for ($i=0;$i<=$_SESSION['count'];$i++) // parcours des resa
		{
     		$_SESSION['resaEncours'] = $i;
		}
		header('Location:affichage_gite.php?add=1&idgite='.$_POST['gite-select']);
   		ob_end_flush(); // envoie des données du flux
   		exit(); /* empeche insertion resa si multi reservation*/
   }
   else{ echo "<br /> Le client effectue une seule réservation";}

/* lancement processus reservation -> a la fin ajout dans le tableau "mesresa" avec le numero de commande en cours */

/**
	* Gestion multi resa -> stockage commande en cours 
*/

if(isset($_POST['nb_adulte']))
{
		//testVar($resaEncours);
		//testVar($_SESSION['Mesresa'][0]);
		//testVar($_SESSION['Mesresa'][1]);
	for($i=0;$i<=$resaEncours;$i++) 
	{					
		/***********************************
			* Calcul variables reservation courante
		***********************************/
		//testVar2($monTab[$i]['idgite'],"test loop monTab","test loop monTab");
		$insertIdgite = $monTab[$i]['idgite'];
		$insertNb_adulte = $monTab[$i]['nb_adulte'];
		$insertNb_enfant = $monTab[$i]['nb_enfant'];
		$insertDate_debut = $monTab[$i]['date_debut'];
		$insertDate_fin = $monTab[$i]['date_fin'];
		$insertType_statut = 'A';
		$date_creation = date("Y-m-d H:i:s");
		
		/*insertion réservation */
		$sqlResa = "INSERT INTO RESERVATION (idgite,nb_adulte,nb_enfant,date_debut,date_fin,statut,date_creation) VALUES ('".$insertIdgite."','".$insertNb_adulte ."','".$insertNb_enfant."','".$insertDate_debut ."','".$insertDate_fin ."','".$insertType_statut."','".$date_creation."')";  // réservation avec statut en attente
		
		//testVar2($sqlResa,"insert resa","insert resa");
		$mysqli->query($sqlResa);
		
		$sqlRecup_Idresa = "SELECT idreservation FROM RESERVATION WHERE idgite='".$insertIdgite."' AND date_debut='".$insertDate_debut."' AND date_fin='".$insertDate_fin."'";  // réservation avec statut en attente
		
		//testVar2($sqlRecup_Idresa,"select idresa","select idresa");
		$resultRecup_Idresa = $mysqli->query($sqlRecup_Idresa);
		
		while ($ressqlRecup_Idresa = $resultRecup_Idresa->fetch_assoc()) 
		{
			$resaId[$i] = $ressqlRecup_Idresa['idreservation'];
			//testVar2($resaId[$i],"test resaId","resaId");
		}
		
		$facteurEnfant = $monTab[$i]['nb_enfant'];
		$facteurAdulte = $monTab[$i]['nb_adulte'];
		//testVar2($facteurAdulte,"facteurAdulte","facteurAdulte" );
		//testVar2($facteurEnfant,"facteurEnfant","facteurEnfant" );
		
		$taxeAdulte = calculTaxe ("adulte",$facteurAdulte,$resaId[$i],"I");
		//testVar2($taxeAdulte,"taxeAdulte","taxeAdulte");
		$taxeEnfant = calculTaxe ("enfant",$facteurEnfant ,$resaId[$i],"I");
		//testVar2($taxeEnfant,"taxeEnfant","taxeEnfant");
		
		$taxe = $taxe + ($taxeAdulte+$taxeEnfant);
		//testVar2($taxe,"taxe","taxe");
		$monTab[$i]['taxe']= $taxe;
		$_SESSION['Mesresa'] = $monTab;
		//testVar2($taxe,"taxe","taxe");
	}

	if($mysqli)
		{
				/*******************************
					* Insertion de la commande
				*******************************/
				/*
					* taxe calcul pendant verief capacité
				*/
				for($i=0;$i<=$resaEncours;$i++) 
				{			
					$sqlCaution ="SELECT montant_caution FROM GITE WHERE idgite=".$idgite;

					$reqCaution = $mysqli->query($sqlCaution);
					
					while ($resqlCaution = $reqCaution->fetch_assoc())
					{
						$caution = $resqlCaution['montant_caution']; // valeur caution en fonction du gite
					}
					
					$monTab[$i]['caution'] = $caution;
					
					$cautionTotal = $caution + $caution;
					
					$option_resa	= $option_resa + $monTab[$i]['tarifOption'];
					//testVar($option_resa);
					
					$tarif_resa = $tarif_resa + $monTab[$i]['tarif'];
					//testVar2($tarif_resa,"testtarif","testtarif");
				}	
									
				$remise			= $calculRemise;  	// à stocker dans le calcul de la résa
				$code_promo		= $_POST['code']; // req
														
				$date_creation	= date("Y-m-d H:i:s");  ;
				$statut_facture	= 1;   // MODIFICATION APRÈS VALIDATION (INSERT)
				$accompte		= $cautionTotal;
				//testVar($accompte);
				$accompte_paye	= 0; // MODIFICATION APRÈS VALIDATION (INSERT)
				
				echo "<ul><li>".$tarif_resa."</li><li>".$taxe."</li><li>".$cautionTotal."</li><li> ".$option_resa."</li></pre>";
				$total = ($tarif_resa + $taxe+$cautionTotal+$option_resa) - $remise;
				$total_paye		= 0; // MODIFICATION APRÈS VALIDATION (INSERT)
		 
				$sqlCo = "INSERT INTO COMMANDE  (taxe,caution,montant_option,remise,code_promo,date_creation,statut_facture,accompte,accompte_paye,total,total_paye) VALUES ('".$taxe."','".$cautionTotal."','".$option_resa."','".$remise."','".$code_promo."','".$date_creation."','".$statut_facture."','".$accompte."','".$accompte_paye."','".$total."','".$total_paye."')";  
				echo $sqlCo;
				$mysqli->query($sqlCo); // INSERTION COMMANDE
		 
				if($mysqli)
				{
					echo "<p style='color: green'>La réservation s'est bien déroulée</p>" ;     
						/* recherche des elements pour insertion dans commandereserver */
						$reqIdReservation =	"SELECT idreservation FROM RESERVATION
													WHERE date_debut='".$date_debut."'
													AND date_fin='".$date_fin."'
													AND idgite=".$idgite;

						$sqlIdReservation = $mysqli->query($reqIdReservation);
							
						while ($resqlIdReservation = $sqlIdReservation->fetch_assoc())
						{
							$idReservation = $resqlIdReservation['idreservation'];
						}
												
						$reqIdCommande = "SELECT idcommande FROM COMMANDE 
										WHERE date_creation='".$date_creation."'";
												
						$sqlIdCommande = $mysqli->query($reqIdCommande);
						while ($resqlIdCommande = $sqlIdCommande->fetch_assoc())
						{
							$idCommande = $resqlIdCommande['idcommande'];
						}
							
						$reqIdClient =	"SELECT idclient FROM CLIENTS 
										WHERE email='".$login."'";
													
						$sqlIdClient = $mysqli->query($reqIdClient);
						while ($resqlIdClient = $sqlIdClient->fetch_assoc())
						{
							$idClient = $resqlIdClient['idclient'];
						}
		
						for ($i=0;$i<=$resaEncours;$i++)
						{
								/* requête sql insertion commandereserver */
							testVar2($resaId[$i],"resaId","resaId");
							$sqlComRes = "INSERT INTO COMMANDERESERVER (idcommande,idreservation,idclient) VALUES ('".$idCommande."','".$resaId[$i]."','".$idClient."')";
							echo $sqlComRes;
							$mysqli->query($sqlComRes);
							
								for($j=0;$j<$n;$j++)
								{
									$sqlChoixOption= "INSERT INTO CHOIXOPTION (idoption,idreservation) VALUES ('".$monTab[$i][$optionId[$j]]."','".$resaId[$i]."')";
									$mysqli->query($sqlChoixOption); /* parcours des option en fonction des resa -> à retester en multi resa */
								}
						}

							if($mysqli) // si insertion commandereserver true
							{
								echo "<p style='color: green'>réussite de l'insertion dans commandereserver pour la CB J-30</p>";		
								/************************************************/
								/* insertion idoption et idreservation dans CHOIXOPTION */
							}
							else
							{echo "<p style='color: red'>echec de l'insertion dans commandereserver pour le chèque</p>";}

					if(!empty($_POST['J-30']) or !empty($_POST['J+30']))
					{
						if($_POST['J-30']) /* test boolean si true -30j */
						{
							if($_POST['payementJ-30']) /* si 1 radio cb sinon 0 chèque */
							{
								echo "<p style='color: green'>J-30 CB</p><br />";
								/* distinction tarif et accompte et href du bouton validation */
								$payementCbComplet = true; 
							}
							else
							{
								echo "<p style='color: green'>J-30 Cheque </p><br />";
								echo "<p style='color: green'>Adresse du gîte</p><br />";
									$payementChequeComplet = true;
								/* validation backend -> insertion commande resa*/	
							}	
						} // fin si $testcb (boolean)
						else // sinon j +30
						{
							if(($_POST['payementJ+30']))
							{
								echo "<p style='color: green'>J+30 CB </p><br />";
								$payementCbComplet = true;
							} //fin si cb
							else // else chèque avec -30jours
							{
								echo "<p style='color: green'>J+30 Cheque </p><br />";
								echo "<p style='color: green'>Adresse du gîte</p><br />";
								$payementChequeComplet = false;
								/* validation backend -> insertion commande resa*/
							}
						}
					} // fin si payment vide
					else 
					{
						echo "<p style='color: red'>vous n'avez pas précisé de moyen de payement</p>";
					}
				} // fin si mysqli 
				else 
				{echo '<br/> Merci de saisir votre réservation à nouveau';}
			}
			else {'<br/> erreur dans la création de votre commande, veuillez contacter le gîte, excusez nous pour ce désagrément';}
		} 
		else 
		{echo '<br/> Merci de saisir votre réservation à nouveau';}
?>


<h2>Récapitulatif de votre Réservation</h2>
	<div class="row">
		
		<ul> <!-- récupération des éléments de session, stockés en début de page -->
			<li><?php echo "Vous avez sélectionné le ".$idgite; ?></li>
			<li><?php echo "Date de début le ".$date_debut; ?></li>
			<li><?php echo "Date de fin le ".$date_fin; ?></li>
			<li><?php echo "Pour un tarif maximum de ".$tarif; ?></li>
			<li>test</li>
		</ul>

		<h3>Votre Commande</h3>
			<ul><?php listageArray($monTab); ?></ul>
	</div>
	
		<?php 

			if(isset($payementCbComplet))
			{
		?>
				<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST">
					<fieldset>
						<input name="amount" type="hidden" value="
									<?php 
												if($payementCbComplet)
												{
													echo $tarif;
												}
												else
												{
													echo $accompte;
												}
										?>"	/>
						<input name="currency_code" type="hidden" value="EUR" />
						<input name="shipping" type="hidden" value="0.00" />
						<input name="tax" type="hidden" value="<?php echo $taxe ?>	" />
						<input name="return" type="hidden" value="http://gite-lemetzval.fr/dev/paypal_success.php" />
						<input name="cancel_return" type="hidden" value="http://gite-lemetzval.fr/dev/paypal_cancel.php" />
						<input name="notify_url" type="hidden" value="http://gite-lemetzval.fr/dev/ipn.php" />
						<input name="cmd" type="hidden" value="_xclick" />
						<input name="business" type="hidden" value="baseK@cesncf-stra.org" />
						<input name="item_name" type="hidden" value="Reservation Gite" />
						<input name="no_note" type="hidden" value="1" />
						<input name="lc" type="hidden" value="FR" />
						<input name="bn" type="hidden" value="PP-BuyNowBF" />
						<input name="custom" type="hidden" value="<?php echo $idClient ?>" />
						<input name="invoice" type="hidden" value="<?php echo $idCommande ?>" />
						<input type="submit" value="Valider votre réservation" class="btn primary">
				</fieldset>
			</form>
		<?php
			}
			else if(isset($payementChequeComplet))
			{
			?>
				<a href="https://paypal_cheque.php">Adresse du Gite</a>
			<?php
			}
		?>
	
<?php	
			testVar2($monTab[$resaPrecedente],"Recapitulatif des resas 0","Recapitulatif des resas 0");
			testVar2($monTab[$resaEncours],"Recapitulatif des resas 1","Recapitulatif des resas 1");
require('includes/footer.php'); ?>