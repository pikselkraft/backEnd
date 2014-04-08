<?php

//recherche de clients des gites 

	require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		REcherche des clients  des gites     *
**********************************************/
		
		
	/**
		* note EVM (code pour dev envoi mail)
	*/
		
    /** 
			* differentes valeurs de la variable actionClient passee en argument 
			* vide : on affiche un formulaire de recherche de clients
			* R : on affiche les r�sultat
				* MS : update info clinet
				* D : suppresion client
				* A : ajout client
			* TE : trie mail
			* TN : trie nom
			* TP : trie prenom
			* MDP : creation nouveau mot de passe
			* EM : envoi new mp par mail
			* MAJ : afichage formulaire modification
			* CR : formulaire création client 
	*/
	
	/***
	
		* recherche client en fonction d'une commande (de la page recherche commande)
		
	*/
	if (!empty($_GET["idcommande"])) {
		$idcommande=$_GET["idcommande"];
		
		$req_IdClientCommande = "SELECT idclient FROM COMMANDERESERVER WHERE idcommande='".$idcommande."'";
		$result_IdClientCommande=$mysqli->query($req_IdClientCommande);
		
		while($row_IdClientCommande = $result_IdClientCommande->fetch_assoc()) {
			
			$idclient=$row_IdClientCommande['idclient'];
		}
	}

	/**
		* édition client sur page client 
	*/
	

	if (!empty($_GET["actionClient"]))
	{
		$actionClient=$_GET["actionClient"];
		$editionClient=$_GET["editionClient"];

		//on regarde si on a un idclient en parametre
		if (!empty($_GET["idclient"])) {
			$idclient=$_GET["idclient"];
		}
		
		if(!empty($_GET['email'])) {
			$email=$_GET['email'];
		} else {
			$email=$_POST['email'];
		}
		
		if($editionClient=='MS') {
			
			$reqUpdate="update CLIENTS	
					 SET nom='".$_POST["nom"]."', prenom='".$_POST["prenom"]."', entreprise='".$_POST["entreprise"]."', 
					 adresse='".$_POST["adresse"]."', codepostal='".$_POST["codepostal"]."', ville='".$_POST["ville"]."', 
					 pays='".$_POST["ville"]."', tel='".$_POST["tel"]."', port='".$_POST["port"]."', 
					 email='".$_POST["email"]."',date_naissance='".$_POST["date_naissance"]."', 
					 cheminot='".$_POST["cheminot"]."', code_cheminot='".$_POST["code_cheminot"]."', region='".$_POST["region"]."', 
					 newsletter='".$_POST["newsletter"]."', 
					 commentaire='".$_POST["commentaire"]."'
					 where idclient= ".$idclient ;
				$mysqli->query($reqUpdate);
				
			if($mysqli) {
				$MessageAction="Les information du client ont été mise à jour";
				
				/**
	
					!!* EVM envoi mail + template!!
				*/
			}
			else {
				$MessageAction="Problème lors de la mise à jour";
			}
		
		
		}
		else if ($editionClient=='A') {
		
				$email=strtolower($_POST["email"]); // recuperation email
				if (empty($email))
					$email = "aucun";
				
				$newPass = chaineAleatoire(8); // creation d'un mot de passe
			
				$Clef = "Matteo1234567890";

				  $pass		= Cryptage($newPass,$Clef) ;
				  $pass		= utf8_encode($pass); 

				$date_creation	= date("Y-m-d H:i:s"); 

				/* insertion dans la base*/
				$reqInsertClient="INSERT INTO CLIENTS (email,mp,nom,prenom,date_naissance,cheminot,code_cheminot,region,entreprise,adresse,codepostal,ville,pays,tel,port,creation,newsletter) VALUES ('".$email."','".$newPass."','".$_POST["nom"]."','".$_POST["prenom"]."','".$_POST["date_naissance"]."','".$_POST["cheminot"]."','".$_POST["region"]."','".$_POST["code_cheminot"]."','".$_POST["entreprise"]."','".$_POST["adresse"]."','".$_POST["codepostal"]."','".$_POST["ville"]."','".$_POST["pays"]."','".$_POST["tel"]."','".$_POST["port"]."','".$date_creation."','".$_POST["newsletter"]."')";;
			
				$mysqli->query($reqInsertClient);

			if($mysqli) {
				$MessageAction="Le client a été crée";
				
				/**
	
					!!* EVM envoi mail + template!!
				*/
				
				envoiMail($email, "Votre nouveau mot de passe","voici votre mot de passe : ".$newPass,$copy); // envoi par email (recap client +mp)
			}
			else {
				$MessageAction="Problème lors de la création du client";
			}
		
		}
		else if($editionClient=='D') {

				$req_TestCommande = "SELECT idcommande FROM COMMANDERESERVER WHERE idclient='".$idclient."'";

				$result_TestCommande=$mysqli->query($req_TestCommande);
				if(!$result_TestCommande) {
					$avertissementSuppression="Impossible de supprimer le client, supprimer d'abord les commandes en cours de ce client.";
				}
				else {
					
					/**
	
						!!* EVM envoi mail + template!!
					*/
					
					$suppClient="DELETE FROM CLIENTS WHERE idclient='".$idclient."'";
					
					$mysqli->query($suppClient);
					
					if($mysqli) {
						$MessageAction="Le client a été supprimé";
					}
					else {
						$MessageAction="Erreur lors de la suppression du client";
					}
				}

		}

		switch ($actionClient) 
		{
			case "R": //rechercher de clients
				
				$reqClient="SELECT idclient, nom, prenom, port, email	
						FROM CLIENTS";

					if(!empty($_GET["idcommande"]) || !empty($_GET["idclient"])) {
						 	 
						 $reqClient.=" WHERE idclient='".$idclient."'";		 
					}
					else {
						 
						if (!empty($email) and (!empty($_POST["nom"])))
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
								if (!empty($email))
								{
									$reqClient.=" where email like '".$email."'";
								}
								else
								{
									if (!empty($_POST["port"]))  $reqClient.=" where port like '".$_POST["port"]."'";
								}
							}
						}
						 
					}
					echo $reqClient;
			 
				$result_reqClient=$mysqli->query($reqClient);
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r�sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="Resultat de la recherche : ";
				}
							
				//Boucle qui parcourt les clients dans la base de donn�es
				
			
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
				
				/**
	
					!!* EVM envoi mail + template!!
				*/
				
				
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
				
			case "MAJ": //génération du formulaire de modifications
				
				$idclient=$_GET['idclient'];
				
				// construction de la requete
				$reqClient="SELECT idclient, nom, prenom, entreprise, adresse, codepostal, ville, pays, tel, port, email,date_naissance, creation, cheminot, code_cheminot, region, newsletter, commentaire 
									FROM CLIENTS
									WHERE idclient=".$idclient;
									
				$result_reqClient=$mysqli->query($reqClient);
				
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r�sultat " ;  
				} 
				else
				{
					$MessageAction="Client ok : ";
				}
							
				while ($row = $result_reqClient->fetch_assoc())
				{			
				$affichage_info_client='
				<form action="rechercheClient.php?actionClient=R&editionClient=MS&idclient='.$idclient.'" method="post">
					<ul>';
				$affichage_info_client.='
						<li>
							<label for="nom">Nom : 
								<input id="nom" name="nom" type="text" value="'.$row["nom"].'"'  .$modif. '>
							</label></li>
						<li>
							<label for="prenom">Pr�nom : 
								<input id="prenom" name="prenom" type="text" value="'.$row["prenom"].'"'.$modif.'>
							</label>
						</li>
						<li>
							<label for="entreprise">Entreprise : 
								<input id="entreprise" name="entreprise" type="text" value="'.$row["entreprise"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="adresse">Adresse : 
								<input id="Adresse" name="Adresse" type="text" value="'.$row["Adresse"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="codepostal">Codepostal : 
								<input id="codepostal" name="codepostal" type="text" value="'.$row["codepostal"].'"'  .$modif. '>
							</label>							</li>
						<li>
							<label for="ville">Ville :
								<input id="ville" name="ville" type="text" value="'.$row["ville"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="pays">Pays : 
								<input id="pays" name="pays" type="text" value="'.$row["pays"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="tel">Tel : 
								<input id="tel" name="tel" type="text" value="'.$row["tel"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="port">Portable : 
								<input id="port" name="port" type="text" value="'.$row["port"].'"'  .$modif. '>
							</label>
						</li>
						<li><label for="email">Email : 
								<input id="email" name="email" type="text" value="'.$row["email"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="date_naissance">Date naissance : 
								<input id="date_naissance" name="date_naissance" type="date" value="'.$row["date_naissance"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="creation">Date cr�ation: 
								<input id="creation" name="creation" type="text" value="'.date_format(date_create($row["creation"]),'d-m-Y H:i:s').'"'  .$modif. '>
							</label>
						</li>
						
						<li>
							<label for="cheminot">Cheminot : 
								<select name="cheminot">';
								if (((int)$row["cheminot"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["cheminot"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["statut"].'">Non</option>
															<option  value="1">Oui</option>';
								}
								$affichage_info_client.='</select>
							</label>
						</li>
						
						<li>
							<label for="code_cheminot">N� CP : 
								<input id="code_cheminot" name="code_cheminot" type="text" value="'.$row["code_cheminot"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="region">R�gion : 
								<select name="region">';
							
								if (((int)$row["region"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Non</option>
															<option  value="1">Oui</option>';
								}
								$affichage_info_client.='
								</select>
							</label>
						</li>
					
						<li>
							<label for="newsletter">Newsleters ';
					
								$affichage_info_client.='<select name="newsletter">';
								if (((int)$row["newsletter"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Non</option>
															<option  value="Oui">Oui</option>';
								}
								$affichage_info_client.='
								</select>
							</label>
						</li>
					<li>
						<label for="commentaire">Commentaire 
							<input id="commentaire" name="commentaire" type="text" value="'.$row["commentaire"].'"'  .$modif. '>
						</label>
					</li>
					<li>
						<input type="submit" value="Modifier">
					</li>
				</form>
			</ul>';	
			}
						
				break;
				
			case "CR":
				$affichage_info_client='
					<form action="rechercheClient.php?actionClient=R&editionClient=A" method="POST">
						<ul>';
					$affichage_info_client.='
						<li>
							<label for="nom">Nom : 
								<input id="nom" name="nom" type="text"'  .$modif. '>
							</label></li>
						<li>
							<label for="prenom">Pr�nom : 
								<input id="prenom" name="prenom" type="text"'.$modif.'>
							</label>
						</li>
						<li>
							<label for="entreprise">Entreprise : 
								<input id="entreprise" name="entreprise" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="adresse">Adresse : 
								<input id="Adresse" name="Adresse" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="codepostal">Codepostal : 
								<input id="codepostal" name="codepostal" type="text"'  .$modif. '>
							</label>							</li>
						<li>
							<label for="ville">Ville :
								<input id="ville" name="ville" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="pays">Pays : 
								<input id="pays" name="pays" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="tel">Tel : 
								<input id="tel" name="tel" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="port">Portable : 
								<input id="port" name="port" type="text"'  .$modif. '>
							</label>
						</li>
						<li><label for="email">Email : 
								<input id="email" name="email" type="text" placeholder="Laissez ce champ vide si le client n\'a pas d\'adresse email"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="date_naissance">Date naissance : 
								<input id="date_naissance" name="date_naissance" type="date"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="creation">Date cr�ation: 
								<input id="creation" name="creation" type="text"'  .$modif. '>
							</label>
						</li>
						
						<li>
							<label for="cheminot">Cheminot : 
								<select name="cheminot">
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>
								</select>
							</label>
						</li>
						
						<li>
							<label for="code_cheminot">N� CP : 
								<input id="code_cheminot" name="code_cheminot" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="region">R�gion : 
								<select name="region">
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</label>
						</li>
					
						<li>
							<label for="newsletter">Newsleters
								<select name="newsletter">
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</label>
						</li>
						<li>
							<label for="commentaire">Commentaire 
								<input id="commentaire" name="commentaire" type="text"'  .$modif. '>
							</label>
						</li>
						<li>
							<input type="submit" value="Modifier">
						</li>
					</form>
				</ul>';		
					
				break;	
	}
		
		
	if ((strcmp($actionClient,'R')==0) or (strcmp($actionClient,'TE')==0) or (strcmp($actionClient,'TN')==0) or (strcmp($actionClient,'TP')==0) or (strcmp($actionClient,'TT')==0) or (strcmp($actionClient,'MDP')==0) or (strcmp($actionClient,'MAJ')==0))
		{
			if ((strcmp($actionClient,'R')==0) and (strcmp($editionClient,'D')==0))
			{

				echo "client supp";
			}
			else {
		
		
		
			// Creation du tableau pour afficher les clients
					$affichage_client_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="600"><thead>
									<tr><td ><a href="rechercheClient?actionClient=TE">Email</a></td><td><a href="rechercheClient?actionClient=TN">Nom</a></td><td><a href="rechercheClient?actionClient=TP">Pr�nom</a></td><td><a href="rechercheClient?actionClient=TT">Portable</A></td><th colspan="6">Action</th></tr>
									</thead>';
				//boucle qui parcourt le r�sultats des requetes demand�es dans la BD
				while ($row = $result_reqClient->fetch_assoc())
				{
					$affichage_client_ligne.= '<tr>
											<td><a href="mailto:'.$row["email"].'">'.$row["email"].'</td>
											<td>'.$row["nom"].'</td>
											<td>'.$row["prenom"].'</td>
											<td>'.$row["port"].'</td>
											<td><a href="affichTous.php?idClient='.$row["idclient"].'"><i class="foundicon-calendar"></i></a></td>
											<td><a href="rechercheClient.php?actionClient=MAJ&idclient='.$row["idclient"].'&email='.$row["email"].'" alt="Modifier les informations du client"><i class="foundicon-edit"></i></a></td>
											<td><a href="rechercheClient.php?actionClient=EM&email='.$row["email"].'" alt="Envoie email"><i class="foundicon-mail"></i></a></td>
											<td><a href="rechercheClient.php?actionClient=MDP&email='.$row["email"].'" alt="Nouveau Mot de passe" onclick="return confirm(\'Etes vous sure de vouloir reg�n�rer un mot de passe?\');"><i class="foundicon-lock"></i></a></td>
											<td><a href="rechercheCommande.php?actionCommande=R&email='.$row["email"].'" alt="Voir les commandes du client"><i class="foundicon-cart"></i></a></td> 
											<td><a href="rechercheClient.php?actionClient=R&editionClient=D&idclient='.$row["idclient"].'" alt="Supprimer le client" onclick="return confirm(\'Etes vous sure de vouloir supprimer le client\');"><i class="foundicon-remove"></i></a></td>
											</tr>'; 
				}		

			$affichage_client_ligne.='</table>';	
			}
		}
	}
	
if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des clients stock�es dans la base *
*												 *	
**************************************************/


$affichage_recherche='Vous pouvez remplacer des carct�res inconnus par % pour effectuer la recherche';
$affichage_recherche.='<form action="rechercheClient.php?actionClient=R" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="port">Num�ro de portable: </label><input id="port" name="port" type="int">';
$affichage_recherche.='<input type="submit" class="button [tiny small large]" value="Rechercher"></form>';
$affichage_recherche.='<a href="rechercheClient.php?actionClient=CR" class="button [tiny small large] right">Creation d\'un client</a>';



?>
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
			<h2>Recherche</h2>
				<?= $affichage_recherche; ?>
				<?= $MessageAction; ?>
				<?= $avertissementSuppression?>
			
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<h2>Affichage Clients</h2>
				<?= $affichage_client_ligne;?>
				<?= $affichage_info_client;?>
			</div>
		</div>		
	</div>
<?php
	require('includes/footer.php');
?>