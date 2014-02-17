<?php

//recherche de clients des gites 

//version: 1.0
//
//creation: 27/01/2014


	include('includes/header.php');

	
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
										<td><a href="affichClient.php?idclient='.$row["idclient"].'" alt="Afficher Info Client"><img src="images/edit.gif"></a></td>
										<td><a href="rechercheClient.php?actionClient=EM&email='.$row["email"].'" alt="envoie email"><img src="images/email.gif"></a></td>
										<td><a href="rechercheClient.php?actionClient=MDP&email='.$row["email"].'" alt="Nouveau Mot de passe" onclick="return confirm(\'Etes vous sure de vouloir regénérer un mot de passe?\');"><img src="images/pwd.gif"></a></td>
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
$affichage_recherche.='<form action="rechercheClient.php?actionClient=R" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="port">Numéro de portable: </label><input id="port" name="port" type="int">';
$affichage_recherche.='<input type="submit" value="Rechercher"></form>';



?>
<link rel="stylesheet" href="includes/onglet.css">
<body>
	<div id="menu" style="position:relative; float:left;">
		<?php

			include('menu.php');
		?>
	</div>

	<div id="content" style="position:relative; float:left;">

		<?php
			echo $affichage_recherche;
			echo $MessageAction;
			echo $affichage_client_ligne;
		?>
		
				
	</div>

</body>

<?php
	include('includes/footer.php');
?>