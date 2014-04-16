<?php
require('includes/header.php');
require('includes/ink/mailBienvenu.php');

/**
 * SESSION
 * GESTION SESSION
 * Stockage des variables de session
 * Test multi insertion
 * Insertion des donn&eacute;es clients ou Test validit&eacute; connexion
 * V&eacute;rification donn&eacute;es connexion
 * Calcul du tarif en enfonction du statut
 * Envoi des donn&eacute;es de payement (mode payement/nombre enfants, adultes) --> payement.php
 * Creation: 06/11/2013/ 
*/

/**************************************************/
/******* SESSION **************************
/************************************************/
$resaEncours      = $_SESSION['resaEncours'];
$monTab           = $_SESSION['Mesresa'];
$resaPrecedente   = $resaEncours - 1 ;

/* r&eacute;cup&eacute;ration post formulaire.php */

if(isset($_POST['login'])) { /* recuperation du login*/
	
	$login              = strtolower(secInput($_POST['login']));
	$monTab[0]['login'] = $login;
}

$_SESSION['Mesresa'] = $monTab; // on ajoute le login à la session

/* stockage des variables du post du formulaire.php */
$date_debut = $monTab[$resaEncours]['date_debut'];
$date_fin 	= $monTab[$resaEncours]['date_fin'];
$idgite 	= $monTab[$resaEncours]['idgite'];
$nomGite 	= $monTab[$resaEncours]['nom'];
$tarif 		= $monTab[$resaEncours]['tarif'];

/* capacite du gite -> test du nombre adultes et enfants conforment */
$cap = $_SESSION['gite_tab']['capacite'];


/********************************************
	*	Reprise reservation (session, arr&ecirc;t sur page reservation_action.php)
*********************************************/
if(isset($_GET['poursuite']))
{
	$repriseResa = $_GET['poursuite'];
	if($repriseResa==1)
	{echo "<div class='msg'><p>Saisissez &agrave; nouveau vos informations pour finaliser votre r&eacute;servation</p></div>";}
}

$LoginOk = false;
$etat=$_GET['etat'];
	if(isset($_POST["login"]) and isset($_POST["password"]) and isset($_POST['connexion']))
	{
		$login     	= strtolower(secInput($_POST['login']));
		$pass2      = md5($_POST["password"]);
		$sql = "SELECT nom,prenom,mp,email FROM CLIENTS WHERE email = '".$login."'";
		$result = $mysqli->query($sql);

			while ($row = $result->fetch_Assoc()) 
			{
				if  (($row['mp'])==$pass2)
				{
					$LoginOk=true;
					/* ajout du login dans la session */
					$_SESSION['login']             = $login;
					$monTab[$resaEncours]['login'] = $login;
					$_SESSION['Mesresa']           = $monTab;
					$_SESSION['test'] = true;

				}
				else 
				{		
					echo "<div class='msg-error'><p>Votre mot de passe est incorrect, merci de recommencer</p></div>";
					?>
					<script>
						alert("Votre mot de passe est incorrect, merci de recommencer");
						location.replace("?page_id=192");
					</script>
					<?php
					exit();
				}
			}
	}
if(isset($_POST["login"]) and isset($_POST["password"]) and isset($_POST['nom']))
{
		$pass2 = md5($_POST["password"]);
		if($etat==2)
		{
			if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['naissance']) ) 
			/* verief presence argument -> lance la boucle, les arguments sont obligatoires et seront verif en js */
			{ 
				
				// r&eacute;cup&eacute;ration des donn&eacute;es
				$mail           = strtolower(secInput($_POST['login']));
				$pass           = $pass2;
				$nom            = secInput($_POST['nom']);
				$prenom         = secInput($_POST['prenom']);
				$civilite       = secInput($_POST['civilite']);
				$naissance      = secInput($_POST['naissance']);
				$cheminot       = secInput($_POST['cheminot']);
				$cheminotRegion = $_POST['ce_cheminot'];
				$cheminotCode   = secInput($_POST['region']);
				$entreprise     = secInput($_POST['entreprise']);
				$adresse        = secInput($_POST['adresse']);
				$codepostal     = secInput($_POST['codepostal']);
				$ville          = secInput($_POST['ville']);
				$pays           = secInput($_POST['pays']);
				$tel            = secInput($_POST['tel']);
				$port           = secInput($_POST['port']);
				$datecrea       = date("Y-m-d H:i:s");
				$news           = secInput($_POST['news']);

				//insertion dans la db
				$sql = "INSERT INTO CLIENTS (email,mp,nom,prenom,civilite,date_naissance,cheminot,code_cheminot,region,entreprise,adresse,codepostal,ville,pays,tel,port,creation,newsletter) VALUES ('".$mail."','".$pass."','".$nom."','".$prenom."','".$civilite."','".$naissance."','".$cheminot."','".$cheminotCode."','".$cheminotRegion."','".$entreprise."','".$adresse."','".$codepostal."','".$ville."','".$pays."','".$tel."','".$port."','".$datecrea."','".$news."')";
				testVar($sql);
				$mysqli->query($sql);
				$enreTest=false;
				
				/* si la requ&ecirc;te s'est bien pass&eacute;e, on affiche un message de succès */
				  if($mysqli)
				  {
				  	echo "<div class='msg'><p>L'inscription s'est bien d&eacute;roul&eacute;e</p></div>" ;   // redirection vers page r&eacute;capitulative (post) de la r&eacute;servation et permet la connexion
				  	//envoiBienvenu($mail);
				  } 
				  else 
				  {echo "<div class='msg-error'><p>Erreur, merci de vous enregistrer &agrave; nouveau</p></div>";}
			}
			else
			{echo "<div class='msg-error'><p>Veuillez remplir le formulaire, merci</p></div>";}
		} // fin if etat = 2
	} // fin if	isset post
//} // fin if test rafraichissement	
?>

<?php 	
/** 
		* gestion cheminot ou non et tarif 
*/
				
	$statutCheminot ='EX';
	$reqCheminot    = "SELECT c.cheminot,c.code_cheminot,c.region FROM CLIENTS c WHERE email='".$login."'"; // requete boolean cheminot
	$resultCheminot = $mysqli->query($reqCheminot);

	while ($ressqlCheminot = $resultCheminot->fetch_assoc()) /* parcours tableau r&eacute;cup&eacute;ration statut, region */
	{
		$testCheminot   =$ressqlCheminot['cheminot'];
		$regionCheminot =$ressqlCheminot['region']; 
	}
	if($testCheminot) // si statut cheminot = 1
	{
		if($regionCheminot) // si statut codecheminot = 1 alors t.statut_client ='".$statutCheminot'" -> cheminot_region (calculTarif)
		{
			$statutCheminot='CR';						
		}
		else // sinon statut codecheminot = 0 alors t.statut_client ='".$statutCheminot'" -> cheminot_externe (calculTarif)
		{
			$statutCheminot='CE';
		}
	}
	else // sinon statut cheminot = 0 alors t.statut_client ='".$statutCheminot'" -> externe (calculTarif)
	{
			$statutCheminot='EX';
	}
	$tarif2                          = calculTarif($date_debut,$date_fin,$idgite,$statutCheminot); 
	$monTab[$resaEncours]['tarif']   =	$tarif2;
	$_SESSION['Mesresa'][0]['tarif'] = $monTab[$resaEncours]['tarif'];

?>
<div class="row">
	<div class="small-11 small-centered columns">	
	<div class="reservation-content">
		<div class="reservation-centre">	
		<form action="payement.php" method="POST" class="reservation-form" id="register-form">
		<fieldset>
			<legend>Votre R&eacute;servation</legend>
				<span class="label-block">
					<input type="hidden" name="login" <?php echo 'value="'.$login.'"'; ?> required>
			
					<label for=date_debut>Date d'arriv&eacute;e :</label><input type="date" name="date_debut" <?php echo 'value="'.$date_debut.'"'; ?> readonly>
					<label for=date_fin>Date de d&eacute;part :</label><input type="date" name="date_fin" <?php echo 'value="'.$date_fin.'"'; ?> readonly>
				</span>
				<span class="label-block">
					<label id="message-capacite">La capacit&eacute; maximum du g&icirc;te s&eacute;lectionn&eacute; est de <span class="txtbold"><?= $cap;?> personnes.</span></label>
					<span class="label-block">
						<label for=nb_adulte>Nombre d'adultes :</label>
						<input id=nb_adulte name=nb_adulte type=number min=0 max="<?= $cap; ?>" value=0 required>
					</span>
					<span class="label-block">
						<label for=nb_enfantTotal>Nombre d'enfants:</label>
						<input id=nb_enfantTotal name=nb_enfantTotal type=number min=0 max="<?= $cap; ?>" value=0 required>
			
						<label for=nb_enfant>Enfants de plus 13 ans :</label>
						<input id=nb_enfant name=nb_enfant type=number  min=0 max="<?= $cap; ?>" value=0 required>
					</span>
				</span>
				<!-- <span class="label-block">
					<label for="choix-option" >Choix des options:</label> -->
					<?php
					// $reqOption = "SELECT idoption, denomination, option_tarif FROM OPTIONRESA"; // requ&ecirc;te de la liste des options		
					// $sqlOption=$mysqli->query($reqOption);
							
					// while ($resqlOption=$sqlOption->fetch_Assoc()) // affichage liste dynamiquement avec value correspondant &agrave; l'idoption
					// {
					// 	$num_option  =$resqlOption['idoption'];
					// 	$nom_option  =$resqlOption['denomination'];
					// 	$prix_option =$resqlOption['option_tarif'];
						
					// 	echo '<span class="paye-label"><input class="paye-input" type="checkbox" name="option[]" value='.$num_option.' />'.ucfirst($nom_option).' ('.$prix_option.' euros)</span>&nbsp;&nbsp;&nbsp;';
					// }
					?>
		<!-- 		</span> -->
		</fieldset>
		<fieldset>
		<legend>Modalit&eacute;s de payement et r&eacute;servation</legend>
				<?php
				if($idgite==1)
				{
					echo "";
				}
				else {
				?>
				
				<p class="label-inline">Valider votre r&eacute;servation ou r&eacute;server un autre g&icirc;te:</p>
				<span class="label-block">
					<div class="centent-paye-center">
						<input type="radio" id="un-gite" name="radios_0" value="0" CHECKED required class="paye-input"><label for="un-gite" id="label-un-gite"><span class="txtbold">Payer votre r&eacute;servation</span></label>
						<input type="radio" id="multi-gite" name="radios_0" value="1" class="paye-input"><label for="multi-gite" id="label-multi-gite"><span class="txtbold">Ou r&eacute;server un autre g&icirc;te pour les m&ecirc;me dates</span></label>
					</div>
				</span>
					
					
					<span class="label-inline">
						<label class="label-inline" id="label-selector-gite">S&eacute;lectionnez un autre g&icirc;te: </label>
						<select id="gite-select" name="gite-select"> 
					<?php
							$reqGite = "SELECT idgite,nom, capacite FROM GITE WHERE idgite!=1 AND idgite !=".$idgite;
							$sqlGite =$mysqli->query($reqGite);

							while ($resqlGite=$sqlGite->fetch_Assoc()) 
							{
								$idGite       = $resqlGite['idgite'];
								$nomGite      = $resqlGite['nom'];
								$capaciteGite = $resqlGite['capacite'];
								
								/* liste des g&icirc;te, value = idgite s&eacute;lectionn&eacute; */
								echo '<option value='.$idGite.' id="gite-select2" name="gite-select2" >'.$nomGite.' ('.$capaciteGite.' personnes)</option>';	
							}
						}
						?>
						</select>
					</span>
					
					<span class="label-block">
						<label for='label-code-promo' id='label-code-promo'>Si vous avez un code promotion: </label><input type="text" name="code-promo" class="paye-input" size="10" id="code-promo">
					</span>		

					<span class="label-block">
						<p id="information-resa">Vous serez redirig&eacute; vers la page du g&icirc;te s&eacute;lectionn&eacute;, votre r&eacute;servation en cours sera r&eacute;gl&eacute;e au moment de la validation de votre prochaine r&eacute;servation</p>
					</span>
					<span class="label-block label-margin">
				<?php
					
					/* INFORMER LE CLIENT DES MODALITES -> PAYEMENT -30 ET RISQUE DE PAYER PAR CHEQUE */
					if(nbJours(date("Y-m-d H:i:s"),$date_debut)<=30) /* test une r&eacute;servation avec +/- 30 jours d'interval */
					{
						$testCb = true; //	permet test dans payement.php
					?>
						
						<label for="payementJ-30" id="payementJ-30">Votre moyen de payement :</label><input type="hidden" name="J-30" value="J-30">  <!--	permet test dans payement.php-->
						<div class="centent-paye-center">
							<input type="radio" name="payementJ-30" id="payementJ-30" value="1" class="paye-input-payement" required /><label for="payementcbJ-30" id="payementcbJ-30"><span class="txtbold">Carte bancaire</span>&nbsp;&nbsp;&nbsp;<img src="https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg" border="0" alt="PayPal Acceptance Mark" width="25%" height="25%"></label>&nbsp;&nbsp;&nbsp;
							<input type="radio" name="payementJ-30" id="payementJ-30" value="0" class="paye-input-payement" /><label for="payementchequeJ-30" id="payementchequeJ-30"><span class="txtbold">Ch&egrave;que</span>&nbsp;&nbsp;&nbsp;<img src="../../wp-content/uploads/2013/12/cheque-icon.png" alt="cheque-icon" width="6%" height="6%"></label>
						</div>
					<?php
					}
					else
					{
						$testCb = false; //	permet test dans payement.php
					?>
					
						<label for="payementJ+30" id="payementJ+30">Votre moyen de payement :</label><input type="hidden" name="J+30" id="J+30" value="J+30" class="paye-input" /> <!--	permet test dans payement.php-->
						<div class="centent-paye-center">
							<input type="radio" name="payementJ+30" id="payementJ+30" value="1" class="paye-input-payement" required /><label for="payementcbJ+30" id="payementcbJ+30"><span class="txtbold">Carte bancaire</span>&nbsp;&nbsp;&nbsp;
							<img src="https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg" border="0" alt="PayPal Acceptance Mark" width="25%" height="25%"></label>&nbsp;&nbsp;&nbsp;
							<input type="radio" name="payementJ+30" id="payementJ+30" value="0" class="paye-input-payement" /><label for="payementchequeJ+30" id="payementchequeJ+30"><span class="txtbold">Ch&egrave;que</span>&nbsp;&nbsp;&nbsp;
							<img src="images/cheque-icon.png" alt="cheque-icon" width="6%" height="6%"></label>
						</div>
				
					<?php
					}
					?>
					</span>
					<span class="label-block">
						<label for="condition" class="label-inline">Accepter les conditions de vente<input type="checkbox" id="cgv" name="condition" value="1" required /></label>
					</span>
		</fieldset>
		<button type="submit" id="reservation" name="reservation">R&eacute;server</button> <!-- test mode payement sur la page payement.php en fonction du post -->
	</form>
	</div>
</div>
</div>

<?php require('includes/footer.php'); ?>