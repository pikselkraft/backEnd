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


/**
 	* récupération information calendrier Tous et Session
 */

$monTab = $_SESSION['Mesresa'];	

$resaEncours= $_SESSION['resaEncours']; 
if (empty($resaEncours)) {$resaEncours=0; $_SESSION['resaEncours']=0;   }

$monTab= $_SESSION['Mesresa'];

if (isset($_GET["idgite"])) {
	$idgite=$_GET["idgite"];
}
else if (isset($_POST["idgite"])) {
	$idgite=$_POST["idgite"];
}
else {
	$idgite=$monTab[$resaEncours]['idgite'];
}

if($mysqli) //GLOBALE MYSQL CONNEXION DB
{
	$req="SELECT idgite,nom,capacite,url,montant_caution,titre,description FROM GITE WHERE idgite=".$idgite; //recuperation information g�te
	$result = $mysqli->query($req);
		
	$result->data_seek(0);
	$row = $result->fetch_assoc();
		
	$_SESSION['gite_tab'] = $row; // TABLEAU SESSION -> INFORMATION G�TE
	$gite_tab = $row;
}
	
if(isset($_POST['date_debut']) and isset($_POST['date_fin'])) {

	$monTab[$resaEncours]['date_debut'] = $_POST["date_debut"];
	$monTab[$resaEncours]['date_fin']   = $_POST["date_fin"];
	$monTab[$resaEncours]['tarif'] = calculTarif($monTab[$resaEncours]['date_debut'],$monTab[$resaEncours]['date_fin'],$idgite);	
}


$monTab[$resaEncours]['idgite']     = $idgite;    /* stock les variables du post dans un tableau */
$_SESSION['Mesresa']                = $monTab;		

/************************************************/
/******* MULTI RESA *******************
/**********************************************/
$resaEncours    = $_SESSION['resaEncours'];
$monTab         = $_SESSION['Mesresa'];
$resaPrecedente = $resaEncours	- 1 ;

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
				$reqClient="select idclient, nom, prenom, port, tel, email	
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
									if (!empty($_POST["port"]))  $reqClient.=" WHERE port like '".$_POST["port"]."' OR tel like '".$_POST["port"]."'";
								}
							}
						}
			
								 
				$result_reqClient=$mysqli->query($reqClient);
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r&eacute;sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="R&eacute;sultat de la recherche : ";
				}
							
				//Boucle qui parcourt les clients dans la base de données
				
				break;
						
			case "TE": //tri par email
				$reqClient="select idclient, nom, prenom, port, tel, email	
						from CLIENTS order by email";
											 
				$result_reqClient=$mysqli->query($reqClient);
				break;
			case "TN": //tri par nom
				$reqClient="select idclient, nom, prenom, port, tel, email	
						from CLIENTS order by nom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TP": //tri par prenom
				$reqClient="select idclient, nom, prenom, port, tel, email	
						from CLIENTS order by prenom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TT": //tri par portable
				$reqClient="select idclient, nom, prenom, port, tel, email	
						from CLIENTS order by port"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TTF": //tri par tel fixe
				$reqClient="select idclient, nom, prenom, port, tel, email	
						from CLIENTS order by tel"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "MDP": //generation d'un nouveau mot de passe
				$newPass=envoiPwd($_GET["email"]);
				$MessageAction='<div class="messageInfo">Le nouveau mot de passe est : '.$newPass.'</div>';
				$reqClient="select idclient, nom, prenom, port, tel, email	
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
				$affichage_client_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="600">
				<thead>
					<tr>
						<th><a href="rechercheClient?actionClient=TE">Email</a></th>
						<th><a href="rechercheClient?actionClient=TN">Nom</a></th>
						<th><a href="rechercheClient?actionClient=TP">Prénom</a></th>
						<th><a href="rechercheClient?actionClient=TT">Portable</A></th>
						<th><a href="rechercheClient?actionClient=TTF">Telephone</A></th>
						<th colspan="4">Action</th>
					</tr>
				</thead>';
			//boucle qui parcourt le résultats des requetes demandées dans la BD
			while ($row = $result_reqClient->fetch_assoc())
			{
				$affichage_client_ligne.= '<tr>
					<td><a href="mailto:'.$row["email"].'">'.$row["email"].'</td>
					<td>'.utf8_encode($row["nom"]).'</td>
					<td>'.utf8_encode($row["prenom"]).'</td>
					<td>'.$row["port"].'</td>
					<td>'.$row["tel"].'</td>
					<td><a href="affichTous.php?idClient='.$row["idclient"].'"><img src="images/cal.gif" title="Agenda"></a></td>
					<td></td>
					<td>
						<form action="reservation_action.php?etat=2" method="POST"  id="connect-form">
							<input id="login" style="width: 150px;" name="login" type="text" value="'.$row["email"].'">
						
					</td>
					<td>
							<input class="button tiny" type="submit" value="S&eacute;lectionner">
						</form>
					</td>
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

	$affichage_recherche='<h3>Recherche Client</h3><p>Vous pouvez remplacer des carct&egrave;res inconnus par % pour effectuer la recherche</p>
		<form action="formulaire.php?actionClient=R" method="post">
			<label for="email">Email : </label>
			<input id="email" name="email" type="text">
			<label for="nom">Nom : </label>
			<input id="nom" name="nom" type="text">
			<label for="port">Num&eacute;ro de t&eacute;l&eacute;phone (fixe ou portabe): </label>
			<input id="port" name="port" type="text">
			<input type="submit" class="button" value="Rechercher">
		</form>';
}

/**
 * formulaire d'enregistrement client
 */

$enregistrementClient = '<h3>Enregistrement Client</h3>	
	<form action='. $_SERVER['PHP_SELF'].'?etat=1&idgite='.$idgite.' method="POST" id="register-form">
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
			<button type=submit>Enregistrer le client</button>	
		</fieldset>
	</form>';

/**
 * if login existe message d'erreur
 * else affichage formulaire client / cache autres formulaires
 */
$formulaireClient = "";
if($etat==1)
{

	if($_POST["login"] != "" && $_POST["password"] != "" && $_POST["loginConfirm"] !="")
	{
		$mail              = $_POST["login"];
		$pass              = md5($_POST["password"]) ;
		$_SESSION['login'] = $mail;
		$sqlVerifExistant  = "SELECT email,mp from CLIENTS WHERE email ='".$mail."'" ; //verification du mail unique
		 
		 $result=$mysqli->query($sqlVerifExistant);
		 if ($row=$result->fetch_Assoc())
		 {
			$messageErreur = '<div data-alert class="alert-box">
							 Adresse email d&eacute;j&agrave; enregistr&eacute;
							  <a href="#" class="close">&times;</a>
							</div>';
		 }
		 else
		 {
		 	$enregistrementClient="";

			$formulaireClient = '<h3>Formulaire clients</h3>
			<form action="reservation_action.php?etat=2" method="POST" class="enregistrement-form2">
			<fieldset>
				<legend>Votre identit&eacute;</legend>
					<input id="login" name="login" type="hidden" value="<?= $mail;?>" required>

					<input id="password" name="password" type="hidden" value="<?= $pass;?>" required>

					<label for=nom>Nom :</label>
					<input id="nom" name="nom" type="text" placeholder="Votre nom" required >

					<label for=prenom>Pr&eacute;nom :</label>
					<input id="prenom" name="prenom" type="text" placeholder="Vote pr&eacute;nom" required>
					
					<span class="label-block">
					<label for=civilite >Civilit&eacute; :</label>
					<select name="civilite" id="civilite"> 
						<option value="Madame">Madame</option>
						<option value="Monsieur">Monsieur</option>
					</select>

					<label for="naissance" class="long-label">Date de naissance :</label>
					<input id="naissance" name="naissance" type="date"  placeholder="jj/mm/aaaa" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" id="datepicker" required>
					</span>
			</fieldset>
			<fieldset>
				<legend>Statut Cheminot</legend>
					<label for=cheminot >&ecirc;tes-vous cheminot ?</label>

					<label for="cheminotoui"><input type="radio" name="cheminot" id="cheminotoui" value="1" required />&nbsp;&nbsp;Oui</label>

					<label for="cheminotnon"><input type="radio" name="cheminot" id="cheminotnon" value="0" CHECKED required />&nbsp;&nbsp;Non</label>

					<label for="ce_cheminot" id="label-ce_cheminot" class="long-label">S&eacute;lectionnez le Ce de votre r&eacute;gion :</label>
					<select name="ce_cheminot" id="ce_cheminot">';
					
					/* requ&ecirc;te de la liste des CE france avec id et si il y a r&eacute;duction ou non (1 ou 0) */
					$reqCe = "SELECT idce, nom_ce, reduction FROM CELISTE";
					$sqlCe=$mysqli->query($reqCe);

					while ($resqlCe=$sqlCe->fetch_Assoc()) 
					{
						/* reduction ou non en fonction de la table celiste */
						$formulaireClient .= '<option value='.$reduction=$resqlCe['reduction'].'>'.$nom_ce=$resqlCe['nom_ce'].'</option>';	
						/* affichage de la liste de ce et value = boolean r&eacute;duction ou non -> $_post['ce_cheminot'] == 0 ou 1 */
					}
					$formulaireClient .= '</select>
				<span class="label-inline">	
					<label for=region id="label-region">Entrez vote code cheminot:</label>
					<input type="text"  id="region" name="region" placeholder="votre code de cheminot" >
				</span>
			</fieldset>
			<fieldset>
				<legend>Autres informations</legend>
				<span class="label-block">
					<label for=entreprise class="extra-long-label">Si vous repr&eacute;sentez une entreprise, son nom :</label>
					<input id=entreprise name=entreprise type=text  placeholder="Votre entreprise"  pattern="[a-zA-Z0-9]+" >
				</span>
				<span class="label-block">					
					<label for=adresse class="label-vertical-top">Votre adresse :</label>
					<textarea id=adresse name=adresse rows=5 placeholder="Votre adresse" class="label-inline" required></textarea>
				</span>
				<span class="label-block">
					<label for=codepostal>Code postal :</label>
					<input id=codepostal name=codepostal type=text placeholder="Votre code postal"  pattern="[0-9]*" required>

					<label for=ville>Ville :</label>
					<input id=ville name=ville type=text placeholder="Votre ville" pattern="[a-zA-Z ]*" required>

					<label for=pays>Pays :</label>
					<input id=pays name=pays type=text placeholder="Votre pays"  pattern="[a-zA-Z ]*" required>
				</span>
				<span class="label-block">	
					<label for=tel>T&eacute;l&eacute;phone :</label>
					<input id=tel name=tel type=tel placeholder="ex:031122334455"  pattern="^0[1-689][0-9]{8}$" required>

					<label for=port>Portable :</label>
					<input id=port name=port type=tel placeholder="ex:061122334455" pattern="^0[6-7][0-9]{8}$">
				</span>
					<label for=news class="label-inline extra-long-label">Voulez-vous recevoir notre newsletter: </label>

					<label for="newsoui" class="label-inline"><input type="radio" name="news" value="1" required />&nbsp;&nbsp;Oui</label>
					<label for="newsnon" class="label-inline"><input type="radio" name="news" value="0" required />&nbsp;&nbsp;Non</label> 
			</fieldset>
				  <button type=submit>Enregistrer le client</button>
			</form>';
		} // fin else
	} // fin if test POST
} // fin if etat == 1
	?>

		
	<div class="row">
		<div class="small-11 small-centered columns panel liste-none">		 <!-- recapitulatif date, gite et prix -->
		<h3>R&eacute;capitulatif de votre R&eacute;servation</h3>
			<ul class="list-none">
				<li><?= "Vous avez s&eacute;lectionn&eacute; le g&icirc;te ".$monTab[$resaEncours]['idgite']; ?></li>
				<li><?= "Date de d&eacute;but le ".dateFr($monTab[$resaEncours]['date_debut'])?></li>
				<li><?= "Date de fin le ".dateFr($monTab[$resaEncours]['date_fin'])?></li>
				<li><?= "Pour un tarif maximum de ".$monTab[$resaEncours]['tarif']." &euro;";?></li>
			</ul>
		</div>
	</div>
		
		
	<div class="row">
		<div class="small-11 small-centered columns">			
			<?= $messageErreur; ?>
			<?= $affichage_recherche; ?>
			<?= $MessageAction; ?>
			<?= $affichage_client_ligne; ?>	
		</div>
	</div>
			
	<div class="row">
		<div class="small-11 small-centered columns">
			<?= $enregistrementClient; ?>
		</div>
	</div>

	<div class="row list-none">
		<div class="small-11 small-centered columns">	
			<?= $formulaireClient; ?>	
		</div>
	</div>

<?php require('includes/footer.php'); ?>