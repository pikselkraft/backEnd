<?php
session_start();	
require('config.php');
require('fonctions.php');

//FORMULAIRE D'ENREGISTREMENT GÎTE LE METZVAL
//
//VERSION: 1.0
//
//TEMPLATE NAME: FORMULAIRE
//
//********************************************
// RECUPERATION DES VARIABLES DE SESSIONS
// TEST PRESENCE RESERVATION EN COURS
// GESTION MULTI RESERVATION
// ETAT1 = FORMULAIRE ENREGISTREMENT --> ACTION = SELF --> AFFICAHGE FORMULAIRE ENREGISTREMENT --> RÉSERVATION_ACTION.PHP
// ETAT2= FORMULAIRE CONNEXION --> RÉSERVATION_ACTION.PHP
// ETAT3= ERREUR MP --> REDIRECTION
// RÉCUPÉRATION DES DATES DE RÉSERVATIONS <- AFFICHAGE_GITE.PHP
// RÉCAPITULATIF DES INFORMATIONS DE COMMANDES
// FORMULAIRE ENREGISTREMENT/INSCRIPTION CLIENT
//********************************************
//
//CREATION: 06/11/2013/


/************************************************/
/******* MULTI RESA *******************
/**********************************************/
$resaEncours				= $_SESSION['resaEncours']; 
$monTab 					= $_SESSION['Mesresa'];
$resaPrecedente 		= $resaEncours	- 1 ;
//testVar($monTab);

if($_SESSION['test']===true) /* si multi resa alors redirection car deja log */
{
	header('Location:reservation_action.php');
	exit();	
}


$etat=$_GET['etat'];
if($etat==3)
{
	echo "MP INCORRECT"; 
	// + refresh
}

/**************************************************/
/*******GESTION SESSION ET PAGE PRECEDENTE*****
/************************************************/
if(empty($_SESSION['Mesresa'])) // si pas de resa en cours
{
	if(empty($_SESSION['login'])) // et pas de login
	{
		header('Location:affichage_gite.php');
		exit();
	}
}
else
{
	
	if(substr($_SERVER["HTTP_REFERER"],0,30) !== substr("http://srvweb/resa/dev/affichage_gite.php",0,30)) 
	/* si la page précédente est différent d'affichage gîte (comparaison des 41 premières lettres) */
	{
		if (substr($_SERVER["HTTP_REFERER"],0,30) !== substr("http://srvweb/resa/dev/formulaire.php",0,30))
		{
			echo "Attention l'utilisation de la touche précédente risque d'annuler votre réservation";
		?>
		<script type="text/javascript">	
			
				if(confirm("Voulez continuer votre réservation (vous serez redirigé vers la page suivante) ou annuler vos données de réservation")) 
				/* donne le choix entre continuer la resa ou se connecter/enregistrer */
				{	
					location.replace("reservation_action.php"); // touche precedente lors de la resa
				}
				else
				{	
					<?php unset($monTab[$resaEncours]); ?>
					location.replace("affichage_gite.php");
					alert("Vous pouvez utiliser cette page pour recommencer votre commande");
				}	
		</script>
	<?php
		} else { echo "<br /> processus de réservation normal";}
	}else { echo "<br /> processus de réservation normal";}
}
?>

<?php require('header.php'); ?>

<h2>Récapitulatif de votre Réservation</h2>
		
	<div class="fiche_recap"> <!-- recapitulatif date, gite et prix -->
		<ul>
			<li><?php echo "Vous avez sélectionné le gîte ".$monTab[$resaEncours]['idgite']; ?></li>
			<li><?php echo "Date de début le ".$monTab[$resaEncours]['date_debut']; ?></li>
			<li><?php echo "Date de fin le ".$monTab[$resaEncours]['date_fin']; ?></li>
			<li><?php echo "Pour un tarif maximum de ".$monTab[$resaEncours]['tarif'];?></li>
		</ul>
	</div>
		
<h2>Connectez-vous</h2>	
		
		<div class="connexion_content">
		  
			<div class="enregistrement"> <!-- enregistrement du client-->
				
				<form action="<?php $_SERVER['PHP_SELF']; ?>?etat=1" method="POST" id="register-form">
					<fieldset>
						<legend>Enregistrement</legend>
						<li>
							<label for=login>Votre Email</label>
							<input id=login name=login type=email placeholder="exemple@domaine.com" required>
						</li>
						<li>
							<label for=loginConfirm>Confirmation de votre Email</label>
							<input id=loginConfirm name=loginConfirm type=email placeholder="exemple@domaine.com" required>
						</li>
						<li>
							<label for=password>Mot de passe</label>
							<input id=password name=password type=password pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" required>
						</li>	
						<li>
							<label for=passwordConfirm>Confirmation de mot de passe</label>
							<input id=passwordConfirm name=passwordConfirm type=password  pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" required>
						</li>
						<button type=submit>S'enregistrer</button>	
					</fieldset>
				</form>
			</div>
			
			
			<div class="connexion"> <!-- connexion du client-->
				<form action="reservation_action.php?etat=2" method="POST"  id="connect-form">
					<fieldset>
						<legend>Connexion</legend>
						<li>
							<label for=login>Votre Email</label>
							<input id=login name=login type=email placeholder="exemple@domaine.com" required>
						</li>
						<li>
							<label for=password>Mot de passe</label>
							<input id=password name=password type=password pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" required>
						</li>
						<a href="mail_password.php">Mot de passe oublié?</a>   <!--	procedure à tester-->
						
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
 			  $_SESSION['login'] = $mail;
			  $sqlVerifExistant= "SELECT email,mp from CLIENTS WHERE email ='".$mail."'" ; //verification du mail unique
			 
			 $result=$mysqli->query($sqlVerifExistant);
			 if ($row=$result->fetch_Assoc())
			 {
				echo "Adresse email déjà enregistré";
				 ?>
				 	<script type="text/javascript">	// redirection js pour connexion
					 	alert("Vous allez être redirigé pour pouvoir vous connecter");
						location.replace("connexion.php");
					</script>
				<?php
			 }
			 else
			 {
	?>
			<h2>Formulaire clients</h2>
			<form action="reservation_action.php?etat=2" method="POST" >
			  <fieldset>
				   <legend>Votre identité</legend>
				<ul>
						<input id="login" name="login" type="hidden" value="<?php echo $mail;?>" required>
					
						<input id="password" name="password" type="hidden" value="<?php echo $pass;?>" required>
					<li>
						<label for=nom>Nom :</label>
						<input id=nom name=nom type=text placeholder="Votre nom"  pattern="[a-zA-Z ]*" required >
					</li>
					<li>
						<label for=prenom>Prénom :</label>
						<input id=prenom name=prenom type=text placeholder="vote prénom"  pattern="[a-zA-Z ]*" required>
					</li>
					<li>
						<label for=naissance>Date de naissance :</label>
						<input id=naissance name=naissance type=date  placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" id="datepicker" required>
					</li>
					<li>
					  <label for=cheminot>Êtes-vous cheminot ?</label>
						<input type="radio" name="cheminot" id="cheminotoui" value="1" onClick="GereControle('cheminotoui', 'ce_cheminot', '0');GereControle('cheminotoui', 'region', '0');" CHECKED required /><label for="cheminotoui">Oui</label>
						<input type="radio" name="cheminot" id="cheminotnon" value="0" onClick="GereControle('cheminotoui', 'ce_cheminot', '0');GereControle('cheminotoui', 'region', '0');" required /><label for="cheminotnon">Non</label>
					</li>
					
					<li>
						<label for=ce_cheminot>Sélectionné votre Ce de région :</label>
							<select name="ce_cheminot" id="ce_cheminot"> 
							<?php
					 		
					 		/* requête de la liste des CE france avec id et si il y a réduction ou non (1 ou 0) */
							$reqCe = "SELECT idce, nom_ce, reduction FROM CELISTE";
							$sqlCe=$mysqli->query($reqCe);
							
							while ($resqlCe=$sqlCe->fetch_Assoc()) 
							{
								echo '<option value='.$reduction=$resqlCe['reduction'].'>'.$nom_ce=$resqlCe['nom_ce'].'</option>';	
								/* affichage de la liste de ce et value = boolean réduction ou non -> $_post['ce_cheminot'] == 0 ou 1 */
							}
							?>
							</select>
				 	</li>
					<li>
						<label for=region>Entrez vote code cheminot</label>
						<input type="text"  id="region" name="region" placeholder="votre code de cheminot" >
					</li>
					<li>
						<label for=entreprise>Si vous représentez une entreprsie, son nom :</label>
						<input id=entreprise name=entreprise type=text  placeholder="Votre entreprise"  pattern="[a-zA-Z0-9]+" >
					</li>
					<li>
					  <label for=adresse>Adresse :</label>
					  <textarea id=adresse name=adresse rows=5 placeholder="Votre adresse"  required></textarea>
					</li>
					<li>
					  <label for=codepostal>Code postal :</label>
					  <input id=codepostal name=codepostal type=text placeholder="67530"  pattern="[0-9]*" required>
					</li>
					<li>
					  <label for=ville>Ville :</label>
					  <input id=ville name=ville type=text placeholder="Votre ville" pattern="[a-zA-Z ]*" required>
					</li>
					<li>
					  <label for=pays>Pays :</label>
					  <input id=pays name=pays type=text placeholder="Votre pays"  pattern="[a-zA-Z ]*" required>
					</li>
					<li>
					  <label for=tel>Telephone :</label>
					  <input id=tel name=tel type=tel placeholder="ex:031122334455"  pattern="^0[1-689][0-9]{8}$" required>
					</li>
					<li>
					  <label for=port>Portable :</label>
					  <input id=port name=port type=tel placeholder="ex:061122334455" pattern="^0[6-7][0-9]{8}$">
					</li>
					<li>
					  <label for=news>Voulez-vous recevoir notre newsletter :</label>
						<input type="radio" name="news" value="1" required /><label for="newsoui">Oui</label>
						<input type="radio" name="news" value="0" required /><label for="newsnon">Non</label> 
					</li>
				</ul>
							<!-- CREATE A CAPTCHA WORDPRESS -->
				  <button type=submit>S'enregistrer</button>

			</fieldset>
		</form>
	<?php
				} // fin else
	} // fin if test POST
} // fin if etat == 1
	?>
<?php	
			testVar2($resaEncours,"resaEncours","resaEncours");
			testVar2($monTab[$resaPrecedente],"Recapitulatif des resas 0","Recapitulatif des resas 0");
			testVar2($monTab[$resaEncours],"Recapitulatif des resas 1","Recapitulatif des resas 1");
require('footer.php'); ?>