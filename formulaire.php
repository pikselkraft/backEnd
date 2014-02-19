<?php

	require('includes/header.php');

//FORMULAIRE D'ENREGISTREMENT GÎTE LE METZVAL

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
echo var_dump($monTab);
$resaPrecedente 		= $resaEncours	- 1 ;
//testVar($monTab);

if($_SESSION['test']===true) /* si multi resa alors redirection car deja log */
{
	header('Location:reservation_action.php');
	exit();	
}


$etat=$_GET['etat'];


/**************************************************/
/*******GESTION SESSION ET PAGE PRECEDENTE*****
/************************************************/
if(empty($_SESSION['Mesresa'])) // si pas de resa en cours
{
	if(empty($_SESSION['login'])) // et pas de login
	{
		//header('Location:affichage_gite.php');
		
	}
}
else
{
	$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
	/*********************************************
	*		REcherche des clients  des gites     *
	**********************************************/
		
    // differentes valeurs de la variable actionClient passee en argument 
	// vide : on affiche un formulaire de recherche de clients
	// R : on affiche les résultat
	
	
	if (!empty($_GET["actionClient"]))
	{
			$actionClient=$_GET["actionClient"];
			
		//on regarde si on a un idclient en parametre
		if (!empty($_GET["idclient"]))
		{
			$idclient=$_GET["idclient"];
		}

		switch ($actionClient) 
		{
			case "R": //rechercher de clients
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS"
						 ;
						 
						if (!empty($_POST["email"]) and (!empty($_POST["email"])))
						{
							$reqClient.=" where email like '".$_POST["email"]."' and nom like '%".$_POST["nom"]."%'";
						 
							 
						}
						 else
						{
							if (!empty($_POST["nom"]) ) 					 
							{
								$reqClient.=" where nom like '".$_POST["nom"]."'";
							}
							else
							{
								if (!empty($_POST["email"]))
								{
									$reqClient.=" where email like '".$_POST["email"]."'";
								}
								else
								{
									if (!empty($_POST["port"]))  $reqClient.=" where port like '".$_POST["port"]."'";
								}
							}
						}
			
								 
				$result_reqClient=$mysqli->query($reqClient);
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de résultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="Resultat de la recherche : ";
				}
							
				//Boucle qui parcourt les clients dans la base de données
				
				break;
						
			case "TE": //tri par email
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS order by email";
											 
				$result_reqClient=$mysqli->query($reqClient);
				break;
			case "TN": //tri par nom
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS order by nom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TP": //tri par prenom
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS order by prenom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TT": //tri par portable
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS order by port"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "MDP": //generation d'un nouveau mot de passe
				$newPass=envoiPwd($_GET["email"]);
				$MessageAction='<div class="messageInfo">Le nouveau mot de passe est : '.$newPass.'</div>';
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS where email='".$_GET["email"]."'";
						$result_reqClient=$mysqli->query($reqClient);
				break;
			case "EM": //generation d'un nouveau mot de passe
				if (envoiMail($_GET["email"],"mon objet","dfdf",true))
				 { echo "envoi ok";}
			
				break;		
		}
		
		if ((strcmp($actionClient,'R')==0) or (strcmp($actionClient,'TE')==0) or (strcmp($actionClient,'TN')==0) or (strcmp($actionClient,'TP')==0) or (strcmp($actionClient,'TT')==0) or (strcmp($actionClient,'MDP')==0))
		{
		
		// Creation du tableau pour afficher les clients
				$affichage_client_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="600"><thead>
								<tr><td ><a href="rechercheClient?actionClient=TE">Email</a></td><td><a href="rechercheClient?actionClient=TN">Nom</a></td><td><a href="rechercheClient?actionClient=TP">Prénom</a></td><td><a href="rechercheClient?actionClient=TT">Portable</A></td><th colspan="4">Action</th></tr>
								</thead>';
			//boucle qui parcourt le résultats des requetes demandées dans la BD
			while ($row = $result_reqClient->fetch_assoc())
			{
				$affichage_client_ligne.= '<tr>
										<td><a href="mailto:'.$row["email"].'">'.$row["email"].'</td>
										<td>'.$row["nom"].'</td>
										<td>'.$row["prenom"].'</td>
										<td>'.$row["port"].'</td>
										<td><a href="affichTous.php?idClient='.$row["idclient"].'"><img src="images/cal.gif" title="Agenda"></a></td>
										<td><form action="reservation_action.php?etat=2" method="POST"  id="connect-form"><input id=login name=login type=email value="'.$row["email"].'" hidden><button type=submit>Sélectionner</button></form></td>
										</tr>';
			}		
			
		$affichage_client_ligne.='</table>';	
		}
	}
if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des clients stockées dans la base *
*												 *	
**************************************************/

$affichage_recherche='Vous pouvez remplacer des carctères inconnus par % pour effectuer la recherche';
$affichage_recherche.='<form action="formulaire.php?actionClient=R" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="port">Numéro de portable: </label><input id="port" name="port" type="int">';
$affichage_recherche.='<input type="submit" value="Rechercher"></form>';


}
?>

<h2>Récapitulatif de votre Réservation</h2>
		
	<div class="row">
		<div class="small-11 small-centered columns">		 <!-- recapitulatif date, gite et prix -->
			<ul>
				<li><?php echo "Vous avez sélectionné le gîte ".$monTab[$resaEncours]['idgite']; ?></li>
				<li><?php echo "Date de début le ".$monTab[$resaEncours]['date_debut']; ?></li>
				<li><?php echo "Date de fin le ".$monTab[$resaEncours]['date_fin']; ?></li>
				<li><?php echo "Pour un tarif maximum de ".$monTab[$resaEncours]['tarif'];?></li>
			</ul>
		</div>
	</div>
		
<h2>Recherche Client</h2>	
		
	<div class="row">
		<div class="small-11 small-centered columns">			
			<? echo $affichage_recherche;
			echo $MessageAction;
			echo $affichage_client_ligne; ?>
		</div>
	</div>
			
	<div class="row">
		<div class="small-11 small-centered columns">	
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
	<div class="row">
		<div class="small-11 small-centered columns">	
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
		</div>
	</div>
	<?php
				} // fin else
	} // fin if test POST
} // fin if etat == 1
	?>
<?php	
require('includes/footer.php'); ?>