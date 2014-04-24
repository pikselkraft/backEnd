<?php
require('includes/header.php');


/**
 * SESSION
 * GESTION SESSION ET PAGE PRECEDENTE
 * STOCKAGE DES VARIABLES DE SESSION
 * GESTION CODE PROMO
 * GESTION RESERVATION
 * CALCUL DU COÛT DES OPTIONS
 * INSERTION DES INFORMATIONS DANS LA TABLE RESERVATION --> STATUT IMPAY&eacute;
 * INSERTION DES INFORMATIONS DANS LA TABLE COMMANDE --> STATUT IMPAY&eacute;
 * DIFF&eacute;RENCIATION MODE DE PAYEMENT CB ET CHÈQUE // FIN DEV --> FAIRE LE PROCESSUS ET DIFF&eacute;RENCIER CB/CHÈQUE (OPTIMISATION)
 * INSERTION DES INFORMATIONS DANS LA TABLE COMMANDERESERVER --> SELON CAS ET CHANGEMENT STATUT RESA
 * R&eacute;CAPITULATIF DE LA R&eacute;SERVATION
 * SI CB -> TUNNEL DE PAYEMENT / SI CHÈQUE --> HOME
 * CREATION: 06/11/2013 
*/

/**************************************************/
/*******GESTION DES VARIABLES ******
/************************************************/
// variable session resaencours
$resaEncours      = $_SESSION['resaEncours']; 
$monTab           = $_SESSION['Mesresa'];
$resaPrecedente   = $resaEncours	- 1 ;	
$_SESSION['test'] = true;

/* stockage dans des variables de post du "formulaire.php" */
$login               = $monTab[0]['login']; // uniquement le login pour la première resa
$cheminot            = $monTab[$resaEncours]['cheminot'];
$date_debut          = $monTab[$resaEncours]['date_debut'];
$date_fin            = $monTab[$resaEncours]['date_fin'];
$idgite              = $monTab[$resaEncours]['idgite'];
$nomGite             = $monTab[$resaEncours]['nom'];
$tarif               = $monTab[$resaEncours]['tarif'];
$_SESSION['Mesresa'] = $monTab;	

/* capacite du gite -> test du nombre adultes et enfants conforment */
$cap     = $_SESSION['gite_tab']['capacite'];
$nomGite = $_SESSION['gite_tab']['nom'];

/* option en $_post */
$option = $_POST['option'];

/**
	* Variable payement +/- 30j
*/
if(!empty($_POST['J-30']) or !empty($_POST['J+30']) or !empty($_POST['code-promo'])) { /* stockage en session en cas d'ereur sur nb adulte/enfant ou modification resa*/ 
	
	$_SESSION['joursMoins30']    = $_POST['J-30'];
	$_SESSION['joursPlus30']     = $_POST['J+30'];
	$_SESSION['payementMoins30'] = $_POST['payementJ-30'];
	$_SESSION['payementPlus30']  = $_POST['payementJ+30'];
	$_SESSION['code-promo']      = $_POST['code-promo'];
}

/**
	* recuperation des variables de payement
*/

$joursMoins30    = $_SESSION['joursMoins30'];
$joursPlus30     = $_SESSION['joursPlus30'];
$payementMoins30 = $_SESSION['payementMoins30'];
$payementPlus30  = $_SESSION['payementPlus30'];
$testCodePromo   = $_SESSION['code-promo'];


/**************************************************/
/*******GESTION CODE PROMOTION ******
/************************************************/
if(isset($testCodePromo))
{
	if($_SESSION['code']==1)
	{echo "<div class='msg-error'><p>Un seul code promotion par commande est accept&eacute;</p></div>";}
	else
	{	
		$_SESSION['code'] = 0;
		$reqCode = "SELECT code, nb, remise FROM CODEPROMO WHERE code ='".$_POST['code-promo']."' AND actif=1";
		$resultCode = $mysqli->query($reqCode);
									
		while ($ressqlCode = $resultCode->fetch_assoc()) 
		{
			$codeReq 		= $ressqlCode['code'];
			$nbCode  		= $ressqlCode['nb'];
			$valeurRemise   = $ressqlCode['remise'];

			if($nbCode>0) // test nombre de code restant
			{
				$nbCode--;
				$sqlSuppCode = "UPDATE CODEPROMO
								SET nb ='".$nbCode."'
								WHERE code ='".$_POST['code-promo']."'"; // mise &agrave; jour nombre codepromo
				$resultSuppCode = $mysqli->query($sqlSuppCode);
				
				if($mysqli)
				{
					echo"<div class='msg'><p>Votre code de promotion a &eacute;t&eacute; appliqu&eacute;.</p></div>";
					$calculRemise = ($tarif * $valeurRemise) / 100;
					$_SESSION['code']++;			
				}else {echo"<div class='msg-error'><p>Votre code promotion n'est plus valide.</p></div>";}
			}
			else 
			{echo "<div class='msg-error'><p>Ce code promotion n'est plus valide.</p></div>";}	
		} // fin while req codepromo
	} // fin if test session code
} else {echo"<div class='msg-none'><p>Il n'y pas de code promotion &agrave; v&eacute;rifier.</p></div>";}

/**************************************
	*	GESTION NB Adulte et Enfant 
*************************************/
if(isset($_POST['nb_adulte']) and isset($_POST['nb_enfant']))
{
	$nb_adulte         = $_POST['nb_adulte'];
	$nb_enfant         = $_POST['nb_enfant']; // de moins de 13 ans
	$nb_enfantTotal    = $_POST['nb_enfantTotal'];
	$cap               = $_SESSION['gite_tab']['capacite']; // capacit&eacute; du g&icirc;te
	$veriefNbPersonnes = $nb_adulte + $nb_enfant + $nb_enfantTotal;

	if((!verifCapacite($nb_adulte,$nb_enfantTotal,$cap)) or ($nb_enfant>$nb_enfantTotal) or ($veriefNbPersonnes==0))
	{
		?>
		<div class='msg-error'><p> Erreur dans la saisi du nombre d'adulte(s) et d'enfant(s)</p></div>
			<div class="verief-nombre-form">
				<div class="fiche-contact">	
					<form action="<?php $_SERVER['REQUEST_URI']; ?>" method="POST">
								<label for="nb_adulte">Nombre d'adultes :</label>
								<input id="nb_adulte" name="nb_adulte" type="number" min=0 max="<?php echo $cap;?>" required>
							
								<label for="nb_enfantTotal">Nombre d'enfants:</label>
								<input id="nb_enfantTotal" name="nb_enfantTotal" type="number" min=0 max="<?php echo $cap;?>" required>
				
								<label for="nb_enfant">Dont nombre d'enfants de plus 13 ans :</label>
								<input id="nb_enfant" name="nb_enfant" type="number"  min=0 max="<?php echo $cap;?>" required>
								<input type="submit" id="reservation" name="reservation" value="Changer vos informations" >
					</form>
				</div>
			</div>	
		<?php	
	} 
	else 
	{
		$monTab[$resaEncours]['nb_adulte']      = $nb_adulte;
		$monTab[$resaEncours]['nb_enfant']      = $nb_enfant;
		$monTab[$resaEncours]['nb_enfantTotal'] = $nb_enfantTotal;
		$_SESSION['Mesresa']                    = $monTab;
		
		/**
			* Variables choix payement
		*/
		$joursMoins30    = $_SESSION['joursMoins30'];
		$joursPlus30     = $_SESSION['joursPlus30'];
		$payementMoins30 = $_SESSION['payementMoins30'];
		$payementPlus30  = $_SESSION['payementPlus30'];
	}	
}

/*******************
	* Gestion Option
********************/

if(empty($option)) // test pr&eacute;sence d'une option
{echo("<div class='msg-none'><p>Vous n'avez pas s&eacute;lectionn&eacute;s d'options</p></div>");} 
else
{
	$n = count($option); 
	for($i=0; $i<$n; $i++)
	{
		$reqOption = "SELECT idoption, option_tarif,denomination FROM OPTIONRESA WHERE idoption='".$option[$i]."'"; // une requ&ecirc;te par option s&eacute;lectionn&eacute;e						
		$resultOption = $mysqli->query($reqOption);

		while ($ressqlOption = $resultOption->fetch_assoc()) 
			{
				$optionDenomination[$i] = $ressqlOption['denomination'];
				$optionId[$i]           = $ressqlOption['idoption']; /* &agrave; tester avec insert choix option */
				$tarifOption            = $tarifOption + $ressqlOption['option_tarif']; /* parcours tarifs s&eacute;lectionn&eacute;s et addition */
			}
			$monTab[$resaEncours][$optionId[$i]] = $optionId[$i];
	}
	
	$monTab[$resaEncours]['tarifOption'] = $tarifOption;
	$_SESSION['Mesresa']                 = $monTab;	
} // fin else option

/************************************************
	* Redirection si multi resa et insertion donn&eacute;es monTab
************************************************/
if($_POST['radios_0']==1)
   {
		// stockage dans des variables de post du "formulaire.php"
		$monTab[$resaEncours]['tarifOption']	= $tarifOption;
		$monTab[$resaEncours]['nb_adulte']		= $nb_adulte;
		$monTab[$resaEncours]['nb_enfant']		= $nb_enfant;
		$monTab[$resaEncours]['code_promo']		= $code_promo;
	
		$_SESSION['Mesresa'] = $monTab;
		$monTab              = $_SESSION['Mesresa'];
		$resaEncours         = count($monTab);

		for ($i=0;$i<=$_SESSION['count'];$i++) // parcours des resa
		{
     		$_SESSION['resaEncours'] = $i;
		}
		header('Location:?page_id=20&idgite='.$_POST['gite-select'].'&add=1');
   		ob_end_flush(); // envoie des donn&eacute;es du flux
   		exit(); /* empeche insertion resa si multi reservation*/
   }

	/* lancement processus reservation -> a la fin ajout dans le tableau "mesresa" avec le numero de commande en cours */

	/**
		* Gestion multi resa -> stockage commande en cours 
	*/

if((isset($_POST['nb_adulte'])) || (isset($monTab[$resaEncours]['nb_adulte'])))
{
	for($i=0;$i<=$resaEncours;$i++) 
	{					
		/***********************************
			* Calcul variables reservation courante
		***********************************/
		$insertIdgite      = $monTab[$i]['idgite'];
		$insertNb_adulte   = $monTab[$i]['nb_adulte'];
		$insertNb_enfant   = $monTab[$i]['nb_enfant'];
		$insertDate_debut  = $monTab[$i]['date_debut'];
		$insertDate_fin    = $monTab[$i]['date_fin'];
		$insertType_statut = 'A';
		$date_creation     = date("Y-m-d H:i:s");
		
		/*insertion r&eacute;servation */
		$sqlResa = "INSERT INTO RESERVATION (idgite,nb_adulte,nb_enfant,date_debut,date_fin,statut,date_creation) VALUES ('".$insertIdgite."','".$insertNb_adulte ."','".$insertNb_enfant."','".$insertDate_debut ."','".$insertDate_fin ."','".$insertType_statut."','".$date_creation."')";  // r&eacute;servation avec statut en attente
		$mysqli->query($sqlResa);
		
		$sqlRecup_Idresa = "SELECT idreservation FROM RESERVATION WHERE idgite='".$insertIdgite."' AND date_debut='".$insertDate_debut."' AND date_fin='".$insertDate_fin."'";  // r&eacute;servation avec statut en attente
		
		$resultRecup_Idresa = $mysqli->query($sqlRecup_Idresa);
		
		while ($ressqlRecup_Idresa = $resultRecup_Idresa->fetch_assoc()) 
		{
			$resaId[$i] = $ressqlRecup_Idresa['idreservation'];
		}
		
		$facteurEnfant = $monTab[$i]['nb_enfant'];
		$facteurAdulte = $monTab[$i]['nb_adulte'];
		$taxeAdulte    = calculTaxe ("adulte",$facteurAdulte,$resaId[$i],"I");
		$taxeEnfant    = calculTaxe ("enfant",$facteurEnfant ,$resaId[$i],"I");
		
		$taxe                = $taxe + ($taxeAdulte+$taxeEnfant);
		$monTab[$i]['taxe']  = $taxe;
		$_SESSION['Mesresa'] = $monTab;
	}

	if($mysqli) {
		
		/*******************************
			* Insertion de la commande
		*******************************/
		/*
			* taxe calcul pendant verief capacit&eacute;
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
			$cautionTotal          = $cautionTotal + $caution;
			$option_resa           = $option_resa + $monTab[$i]['tarifOption'];
			$tarif_resa            = $tarif_resa + $monTab[$i]['tarif'];
		}	
									
			$remise          = $calculRemise;  	// &agrave; stocker dans le calcul de la r&eacute;sa
			$code_promo      = $_POST['code-promo']; // req
			
			$date_creation   = date("Y-m-d H:i:s");
			$statut_facture  = 1;   // MODIFICATION APRÈS VALIDATION (INSERT)
			
			$accompte_paye   = 0; // MODIFICATION APRÈS VALIDATION (INSERT)
			$calcul_accompte = ($tarif_resa + $option_resa) - $remise;
			$accompte        = (30*$calcul_accompte)/100;
			$total           = ($tarif_resa + $taxe + $cautionTotal + $option_resa) - $remise;
			$totalPaypal     = ($tarif_resa + $option_resa) - $remise;
			$total_paye      = 0; // MODIFICATION APRÈS VALIDATION (INSERT)


			$monTab[$resaEncours]['total']           = $total;
			$monTab[$resaEncours]['montantPlus30J']  = $accompte; /* montant &agrave; payer */ 
			$monTab[$resaEncours]['montantMoins30J'] = $total - $cautionTotal; /* montant &agrave; payer */ 

			$sqlCo = "INSERT INTO COMMANDE (tarif_reservation,taxe,caution,caution_paye,montant_option,remise,code_promo,date_creation,statut_facture,accompte,accompte_paye,total,total_paye) VALUES ('".$tarif_resa."','".$taxe."','".$cautionTotal."','A','".$option_resa."','".$remise."','".$code_promo."','".$date_creation."','".$statut_facture."','".$accompte."','".$accompte_paye."','".$total."','".$total_paye."')";  
			$mysqli->query($sqlCo); // INSERTION COMMANDE
		 
				if($mysqli)
				{
					$message = "<div class='msg-none'><p>La r&eacute;servation s'est bien d&eacute;roul&eacute;e</p></div><br />" ;     
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
							/* requ&ecirc;te sql insertion commandereserver */
							$sqlComRes = "INSERT INTO COMMANDERESERVER (idcommande,idreservation,idclient) VALUES ('".$idCommande."','".$resaId[$i]."','".$idClient."')";
							$mysqli->query($sqlComRes);
							
								for($j=0;$j<$n;$j++)
								{
									$sqlChoixOption= "INSERT INTO CHOIXOPTION (idoption,idreservation) VALUES ('".$monTab[$i][$optionId[$j]]."','".$resaId[$i]."')";
									$mysqli->query($sqlChoixOption); /* parcours des option en fonction des resa -> &agrave; retester en multi resa */
								}
						}

							if($mysqli) // si insertion commandereserver true
							{
								$message = "<div class='msg-none'><p>Vous devez valider votre commande pour finaliser votre r&eacute;servation</p></div>";		
								/* insertion idoption et idreservation dans CHOIXOPTION */
							}
							else
							{$message = "<div class='msg-error'><p>Un probl&egrave;me est survenu, veuillez nous excuser. Contactez le g&icirc;te pour finaliser r&eacute;server.</p></div>";}

					if(!empty($joursMoins30) or !empty($joursPlus30))
					{
						if($joursMoins30) /* test boolean si true -30j */
						{
							if($payementMoins30) /* si 1 radio cb sinon 0 chèque */
							{
								$payementCbComplet = true; 
							}
							else
							{
								$payementChequeComplet = true;
							}	
						} // fin si $testcb (boolean)
						else // sinon j +30
						{
							if(($payementPlus30))
							{
								$payementCbComplet = false;
							} //fin si cb
							else // else chèque avec -30jours
							{
								$payementChequeComplet = false;
							}
						}
					} // fin si payment vide
					else 
					{$message = "<div class='msg-error'><p>Vous n'avez pas pr&eacute;cis&eacute; de moyen de payement</p></div>";}
				} // fin si mysqli 
				else 
				{$message = "<div class='msg-error'><p>Merci de saisir votre r&eacute;servation &agrave; nouveau</p></div>";}
			}
			else {$message = "<div class='msg-error'><p>Erreur dans la cr&eacute;ation de votre commande, veuillez contacter le g&icirc;te, excusez nous pour ce d&eacute;sagr&eacute;ment</p></div>";}
		} 
		else {$message = "<div class='msg-error'><a>Merci de saisir votre r&eacute;servation &agrave; nouveau</p></div>";}

		/**
			*	affiche adresse gite (payement par cheque)
		*/
?>

<div class="row">
	<div class="small-11 small-centered columns">	
		<?= $message; ?>
	</div>
</div>	
	
<div class="row">
	<div class="small-11 small-centered columns">
		<div class="fiche-commande">
			<h3>Votre Commande</h3>
				<?php
				if(count($monTab)==1) { /* affichage une commande */
	
					for($i=0;$i<=$resaEncours;$i++)
					{
						$reqTitre= "SELECT titre FROM GITE WHERE idgite='".$monTab[$i]['idgite']."'";
						$sqlTitre = $mysqli->query($reqTitre);
						
						while ($resqlTitre = $sqlTitre->fetch_assoc())
						{
							$titreGite = $resqlTitre['titre'];
						}
					
				?>
				<table border="2" frame="hsides" rules="groups" summary="Tarif du g&icirc;te le Metzval">
						<colgroup align="center"></colgroup>
						<colgroup align="left"></colgroup>
						<colgroup align="center" span="2"></colgroup>
						<colgroup align="center" span="3"></colgroup>
					<thead valign="top">
						<tr>
							<th>Informations</th>
							<th>Vos donn&eacute;es</th>
						</tr>
					</thead>
					<tbody class="tbodyb">
						<tr>
							<td>Votre g&icirc;te</td>
							<td><?= $nomGite.' ('.$cap.' personnes)'; ?></td>
						</tr>
						<tr> 
							<td>Vos dates de r&eacute;servation</td>
							<td><?= "Du ".dateFr($monTab[$i]['date_debut'])." au ".dateFr($monTab[$i]['date_fin']); ?></td>
						</tr>
						<tr>
							<td>Le nombre d'adultes/enfants</td>
							<td><?= $monTab[$i]['nb_adulte']." adulte(s) et ".$monTab[$i]['nb_enfant']." enfant(s)"; ?></td>
						</tr>
						<tr>
							<td>Le nombre d'enfants de plus de 13 ans</td>
							<td><?= $monTab[$i]['nb_enfantTotal']." enfant(s)"; ?></td>
						</tr>
						<tr>
						<tr style="border-color:red;">
							<td><span class="txtbold">Le tarif de votre r&eacute;servation</span></td>
							<td><span class="txtbold"><?= $monTab[$i]['tarif']." &euro;"; ?></span></td>
						</tr>

						<!-- Gestion des options -->

						<!-- <tr>
							<td><span class="txtbold">Le montant de vos options</span></td>
								<td><span class="txtbold"><?php 
									//if(empty($option)) {echo "<span class='txtbold'>vous n'avez pas s&eacute;lectionn&eacute; d'option</span>"; }
									//else { echo '<span class="txtbold">'.$monTab[$i]['tarifOption'].' &euro;</span>';} 
									?>
								</span></td>
						</tr> -->
						<?php
						//if(empty($option)) {
								// on affiche rien
						//}
						//else {
							?>
							<!-- <tr>
								<td><span class="txtbold">Vos options</span></td>
									<td><span class="txtbold"><?php //for ($i=0; $i < count($option) ; $i++) {  echo $optionDenomination[$i].' '; } ?></span></td>
							</tr> -->
							<?php
							//}	// fin else
						?>
						<tr>
							<td><span class="txtbold">Le montant des taxes</span></td>
							<td><span class="txtbold"><?= $monTab[$i]['taxe']." &euro;"; ?></span></td>
						</tr>
						<tr>
							<td><span class="txtbold">Le montant de la caution (&agrave; régler sur place)</span></td>
							<td><span class="txtbold"><?= $monTab[$i]['caution']." &euro;"; ?></span></td>
						</tr>
						<tr>
							<td><span class="txtbold">Montant total (avec caution)</span></td>
							<td><span class="txtbold"><?= $monTab[$i]['total']." &euro;"; ?></span></td>
						</tr>
						<?php
						if($payementCbComplet==false || $payementChequeComplet==false) {
						?>
						<tr>
							<td><span class="txtbold">Montant de l'accompte</span></td>
							<td><span class="txtbold"><?= $monTab[$resaEncours]['montantPlus30J']." &euro;"; ?></span></td>
						</tr>
						<?php
						}
						?>
						<tr style="border-color:red;">
							<td><span class="txtbold-red">Montant &agrave; r&eacute;gler aujourd'hui</span></td>
							<td>
								<?php 
									if($payementCbComplet==true || $payementChequeComplet==true)
									{echo '<span class="txtbold-red">'.$monTab[$resaEncours]['montantMoins30J'].' &euro; </span>';}
									else
									{echo '<span class="txtbold-red">'.$monTab[$resaEncours]['montantPlus30J']. ' &euro;</span>';}
								?>
							</td>
						</tr>
						<tr style="border-color:red;">
							<td><span class="txtbold-red">Reste &agrave; r&eacute;gler avant votre arriv&eacute;e</span></td>
							<td>
								<?php 
									if($payementCbComplet==true || $payementChequeComplet==true)
									{
										$reste = "La caution lors de votre arriv&eacute;e.";
										echo '<span class="txtbold-red">'.$reste.'</span>';
									}
									else
									{
										$reste = $total - $accompte - $cautionTotal;
										echo '<span class="txtbold-red">'.$reste. ' &euro; et la caution sur place</span>';
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
					}
				} // fin if count montab = 1
				else { /* affiche une commande de plusieurs resa et des resas */
				?>
					<table border="2" frame="hsides" rules="groups" summary="Tarif du g&icirc;te le Metzval">
						<colgroup align="center"></colgroup>
						<colgroup align="left"></colgroup>
						<colgroup align="center" span="2"></colgroup>
						<colgroup align="center" span="3"></colgroup>
					<thead valign="top">
						<tr>
							<th>Informations</th>
							<th>Vos donn&eacute;es</th>
						</tr>
					</thead>
					<tbody class="tbodyb">
						<tr>
							<td>Votre num&eacute;ro de commande</td>
							<td><?= $idCommande; ?></td>
						</tr>
						<!-- <tr>
							<td><span class="txtbold">Le montant de vos options (total)</span></td>
								<td><span class="txtbold"><?php 
									//if(empty($option)) {echo "<span class='txtbold'>vous n'avez pas s&eacute;lectionn&eacute; d'option</span>"; }
									//else { echo '<span class="txtbold">'.$option_resa.' &euro;</span>';} 
									?>
								</span></td>
						</tr> -->

						<?php
						//if(empty($option)) {
								// on affiche rien
						//}
						//else {
							?>
							<!-- <tr>
								<td><span class="txtbold">Vos options</span></td>
									<td><span class="txtbold"><?php //for ($i=0; $i < count($option) ; $i++) {  echo $optionDenomination[$i].' '; } ?></span></td>
							</tr> -->
							<?php
							//}	// fin else
						?>

						<tr>
							<td><span class="txtbold">Le montant des taxes (total)</span></td>
							<td><span class="txtbold"><?= $taxe." &euro;"; ?></span></td>
						</tr>
						<tr>
							<td><span class="txtbold">Le montant des cautions (&agrave; régler sur place)</span></td>
							<td><span class="txtbold"><?= $cautionTotal." &euro;"; ?></span></td>
						</tr>
						<tr>
							<td><span class="txtbold">Montant total (avec les cautions)</span></td>
							<td><span class="txtbold"><?= $total." &euro;"; ?></span></td>
						</tr>
						<?php
						if($payementCbComplet==false || $payementChequeComplet==false) {
						?>
						<tr>
							<td><span class="txtbold">Montant de l'accompte</span></td>
							<td><span class="txtbold"><?= $accompte." &euro;"; ?></span></td>
						</tr>
						<?php
						}
						?>
						<tr style="border-color:red;">
							<td><span class="txtbold-red">Montant &agrave; r&eacute;gler aujourd'hui</span></td>
							<td>
								<?php 
									if($payementCbComplet==true || $payementChequeComplet==true)
									{
										$totalApayer = $total - $cautionTotal;
										echo '<span class="txtbold-red">'.$totalApayer.' &euro; </span>';
									}
									else
									{echo '<span class="txtbold-red">'.$accompte. ' &euro;</span>';}
								?>
							</td>
						</tr>
						<tr style="border-color:red;">
							<td><span class="txtbold-red">Reste &agrave; r&eacute;gler avant votre arrivée</span></td>
							<td>
								<?php 
									if($payementCbComplet==true || $payementChequeComplet==true)
									{
										$reste ="La caution lors de votre arriv&eacute;e.";
										echo '<span class="txtbold-red">'.$reste.' &euro; </span>';
									}
									else
									{
										$reste = $total - $accompte - $cautionTotal;
										echo '<span class="txtbold-red">'.$reste. ' &euro; et la caution sur place</span>';
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>

				<?php
					/**
						* requete afficahge resa
					*/
				?>
					<h3>Vos R&eacute;servation</h3>
				<?php
					for ($i=0;$i<=$resaEncours;$i++)
					{	

						$reqAffichGite="SELECT idreservation, idgite, nb_adulte, nb_enfant, date_debut, date_fin FROM RESERVATION 
										WHERE date_debut='".$monTab[$i]['date_debut']."' 
										AND date_fin='".$monTab[$i]['date_fin']."' 
										AND idgite=".$monTab[$i]['idgite'];

						$sqlAffichGite = $mysqli->query($reqAffichGite);

						while ($ressqlAffichGite = $sqlAffichGite->fetch_assoc()) 
						{
							$affichIdResa[$i]    = $ressqlAffichGite['idreservation'];
							$affichIdGite[$i]    = $ressqlAffichGite['idgite'];
							$affichNbAdulte[$i]  = $ressqlAffichGite['nb_adulte'];
							$affichNbEnfant[$i]  = $ressqlAffichGite['nb_enfant'];
							$affichDateDebut[$i] = $ressqlAffichGite['date_debut'];
							$affichDateFin[$i]   = $ressqlAffichGite['date_fin'];
						}
						
						$reqNom="SELECT nom FROM GITE 
								WHERE idgite=".$affichIdGite[$i];
						
						$sqlNom = $mysqli->query($reqNom);

						while ($ressqlNom = $sqlNom->fetch_assoc()) 
						{
							$affichNom[$i] = $ressqlNom['nom'];
						}
						?>
						<table border="2" frame="hsides" rules="groups" summary="Tarif du g&icirc;te le Metzval">
								<colgroup align="center"></colgroup>
								<colgroup align="left"></colgroup>
								<colgroup align="center" span="2"></colgroup>
								<colgroup align="center" span="3"></colgroup>
							<thead valign="top">
								<tr>
									<th>Informations</th>
									<th>Vos donn&eacute;es</th>
								</tr>
							</thead>
							<tbody class="tbodyb">
								<tr>
									<td>Votre num&eacute;ro de r&eacute;servation</td>
									<td><?= $affichIdResa[$i]; ?></td>
								</tr>
								<tr>
									<td>Le g&icirc;te</td>
									<td><?= $affichNom[$i]; ?></td>
								</tr>
								<tr>
									<td>Le nombre d'adulte(s)</td>
									<td><?= $affichNbAdulte[$i]; ?></td>
								</tr>
								<tr>
									<td>Le nombre d'enfant(s)</td>
									<td><?= $affichNbEnfant[$i]; ?></td>
								</tr>
								<tr>
									<td>La date d'arriv&eacute;e</td>
									<td><?= dateFr($affichDateDebut[$i]); ?></td>
								</tr>
								<tr>
									<td>La date de d&eacute;part/td>
									<td><?= dateFr($affichDateFin[$i]); ?></td>
								</tr>
							</tbody>
						</table>
					<?php
					}
				} // fin else -> affich multi resa
				?>		
		<?php 

			if(isset($payementCbComplet)) /* si on paye par paypal */
			{
				if($payementCbComplet==false) {echo $taxe=0;} /* on ne paye pas de taxe pour l'accompte*/
		?>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="POST" id="paypal-form">
				<!-- <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST" id="paypal-form"> -->
					<fieldset>
						<input name="amount" type="hidden" value="
									<?php 
												if($payementCbComplet==true)
												{echo $totalPaypal;}
												else
												{echo $accompte;}
										?>"	/>
						<input name="currency_code" type="hidden" value="EUR" />
						<input name="shipping" type="hidden" value="0.00" />
						<input name="tax" type="hidden" value="<?php echo $taxe ?>	" />
						<input name="return" type="hidden" value="http://gite-lemetzval.fr/?page_id=203" />
						<input name="cancel_return" type="hidden" value="http://gite-lemetzval.fr/?page_id=201" />
						<input name="notify_url" type="hidden" value="http://gite-lemetzval.fr/?page_id=213" />
						<input name="cmd" type="hidden" value="_xclick" />
						<input name="business" type="hidden" value="baseK@cesncf-stra.org" />
						<input name="item_name" type="hidden" value="Reservation Gite" />
						<input name="no_note" type="hidden" value="1" />
						<input name="lc" type="hidden" value="FR" />
						<input name="bn" type="hidden" value="PP-BuyNowBF" />
						<input name="custom" type="hidden" value="<?php echo $idClient ?>" />
						<input name="invoice" type="hidden" value="<?php echo $idCommande ?>" />
						<input type="submit" value="Valider votre r&eacute;servation" class="btn-paypal" />
						<!-- <a href="?page_id=205&etat=E">Modifier votre r&eacute;servation</a> -->
				</fieldset>
			</form>
		<?php
			}
		?>
		</div>
	</div>
</div>
	<!-- </div> -->
<?php
		if($payementChequeComplet==true)
		{
			?>
			<div class="row">
			<div class="small-11 small-centered columns">
				<div class="fiche-recap-final">
					<h4>Merci d'envoyer votre ch&egrave;que au:</h4>
					<div class="liste-adresse">
						<ul>
							<li>G&icirc;te le Metzval</li>
							<li>Port: 06 25 14 37 06</li>
							<li>7 Rue de la Gare</li>			
							<li>68380 Metzeral</li>
						</ul>
					</div>
					<p>Un mail de confirmation vous a &eacute;t&eacute; envoy&eacute; (pensez &agrave; v&eacute;rifier vos spam).</p>
				</div>
			</div>
			</div>
				<?php
				
				if($payementCbComplet || $payementChequeComplet) {$sommeRegler = $monTab[$resaEncours]['montantMoins30J'];}
				else {$sommeRegler = $monTab[$resaEncours]['montantPlus30J'];}
				
				require('includes/ink/mailCheque.php');
				envoiCheque($login,$idCommande,$sommeRegler,$date_debut,$date_fin,MAIL_METZVAL);
				require_once 'includes/pdf/factures/mailPDF.php';
				require_once 'includes/ink/mailFacture.php';
				envoiFacture($login,generationPdf($idCommande),"Voici la facture de votre derni&egrave;re commande.");
		}
?>

<div class="row">
	<div class="small-11 small-centered columns">
		<a href="affichTous.php">Retourner sur l'accueil</a>
	</div>
</div>

<?php
	require('includes/footer.php');
?>