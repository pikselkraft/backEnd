<?php
//fonction sie gîte le Metzval

/********************************************
 **FONCTIONS
	*	testVar -> fonction test rapide d'une variable
	*	testVar -> fonction test avec texte d'une variable
	*	secInput -> sécurité complète des input
	*	getResMois -> affichage jour du mois en fonction du gîte
	*	estReserve -> affichage des réservations du gîte
	*	getSaison -> les saisons de l'année
	*	typeSaison -> statut de saisons de l'année HS ou BS
	*	calculTarif -> calcul du tarif en fonction de la saison et du statut client
	*	verifIndispo -> Affichage des saisons indisponibles du gîte
	*	verifReservation -> vérification des réservations par rapport aux autres gîtes
	*	Cryptage -> Cryptage des mots de passe
	*	nbJours -> calcul une différence entre deux dates
	*	verifSemaineForce -> gestiopn des réservations en été (samedi-samedi, exception, sur 2 mois)
	*	calculTaxe -> gestion du calcul des taxes
	*	verifCapacite -> test de la conformité du nombre de personnes par rapport à un gîte
	*	chaineAleatoire -> Génération aléatoire de nombre
	*	verifIdTransaction -> Vérification de l'unicité de l'id paypal
	*	listageArray -> affichage tableau multidimensionnel
	*	envoiPwd
	*	envoiMail
	*	deco
	*	breadcrumbs -> fil d'ariane
	*	dateFr -> passage date anglais en français
*********************************************
	**	creation: 23/10/2013/

/************************************************************/
/* constante 
/***********************************************************/
define("MAIL_METZVAL", "contact@gite-lemetzval.fr");
define("MAIL_SDK", "dsalmon@cesncf-stra.org");
define("MAIL_OCT", "oct@cesncf-stra.org");

/************************************************************/
/* définir le fuseau horaire -> à mettre dans le header.php 
/***********************************************************/
date_default_timezone_set('Europe/Paris');

$script_tz = date_default_timezone_get();

//if (strcmp($script_tz, ini_get('date.timezone')))
//{
//    echo 'Le décalage horaire du script diffère du décalage horaire défini dans le fichier ini. <br />';
//} 


function testVar ($var) // fonction de debug simple
{
	echo "<div id=\"test\" style=\"border:3px solid red;width:50%;\">";
	echo "<h6 style=\"color:blue;\"> DEBUT TEST </h6>";
	echo "<br /> Test de la variable en cours : " . $var . "<br />";
	echo "<br />";
	echo '<pre>';	
	var_dump($var);
	echo '</pre>';
	echo "<br />";echo "<br />";
	print_r($var);
	echo "<h6 style=\"color:blue;\"> FIN TEST </h6>";
	echo "</div>";
	
}

function testVar2 ($var,$text,$titre) // fonction de debug +
{
	echo "<div id=\"test\" style=\"border:3px solid red;width:50%;\">";
	echo "<h6 style=\"color:blue;\"> DEBUT TEST " . $titre . " </h6>";
	echo "<br />" . $text . " : " . $var . "<br />";
	echo "<br />";
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
	echo "<br />";echo "<br />";
	print_r($var);
	echo "<h6 style=\"color:blue;\"> FIN TEST " . $titre . " </h6>";
	echo "</div>";
}

function secInput($data) // SÉCURISATION DES INPUT
{
            $data = stripslashes($data); 
			// Supprime les antislashs d'une chaîne
            $data = strip_tags($data); 
			//strip_tags() tente de retourner la chaîne str après avoir supprimé tous les octets nuls, toutes les balises PHP et HTML du code. Elle génère des alertes si les balises sont incomplètes ou erronées.
          
            $data = trim($data); 
			// trim() retourne la chaîne str, après avoir supprimé les caractères invisibles en début et fin de chaîne. Si le second paramètre charlist est omis, trim() supprimera les caractères suivants.
            $data = htmlentities($data); 
			// empêche les caractères html > htmlentities.
          
          return $data;
}


function getResMois ($mois,$annee,$idgite)  // JOURS DU MOIS EN FONCTINO DU GITE
{
	global $mysqli; 

	$req=   "SELECT distinct r.idreservation,r.date_debut,r.date_fin,r.idgite 
			FROM RESERVATION r, GITE g 
			WHERE r.idgite = g.idgite";
			$moism1=$mois-1;
	if ($moism1==0) $moism1=12;

	if($idgite!=0)
	{
				$req.=" AND (r.idgite=".$idgite;
				$req.=" OR r.idgite=1)"; // VÉRIFIE SI TOUT LE CENTRE EST RÉSERVÉ
	} 
			
			$req.=  " AND (YEAR(r.date_debut)=".$annee." OR YEAR(r.date_fin)=".$annee.") 
					AND (MONTH(r.date_debut)=".$mois." OR MONTH(r.date_debut)=".$moism1.")";
	$sql = $mysqli->query($req);
	//$sql = mysql_query($req) or die("error sql");
	return $sql; 
			
}

function estReserve ($dateTest,$idgite)  // RESERVATION GITE
{
        		$EtatJour[0]="";
					
			    $trouve = false;                                

                $result = getResMois (date('m',$dateTest),date('Y',$dateTest),$idgite); // CONVERSION DATES
				$dateTest=date('Y-m-d',$dateTest);

				while ( ($ressql = $result->fetch_assoc()) and $trouve==false) 
                { 		
					$dateDebutResa= $ressql["date_debut"];    
					$dateFinResa= $ressql["date_fin"];	
						
					if( explode("-", $dateTest) >= explode("-", $dateDebutResa) and explode("-", $dateTest) <= explode("-", $dateFinResa))  
								{
									$trouve=true; 
									$EtatJour[0]="-R-";
									$EtatJour[1]=$ressql["idreservation"];
					
								} 
										
                 }
				return $EtatJour;
	
}

	
function getSaison ($mois,$annee)   // SAISON DE L'ANNÉE
	{
			global $mysqli; //CONNEXION DB
			
				$reqsaison= "SELECT idsaison,date_debut,date_fin,statut
						FROM SAISON";
						$moism1=$mois-1;
						if ($moism1==0) $moism1=12;
						
						$reqsaison.=  " WHERE (YEAR(date_debut)=".$annee." OR YEAR(date_fin)=".$annee.") 
										AND (MONTH(date_debut)=".$mois." OR MONTH(date_debut)=".$moism1.")";
						                  
				$sqlsaison = $mysqli->query($reqsaison);
				return $sqlsaison;
		
	}

function typeSaison ($dateSaison)  // STAUT DES SAISONS DE L'ANNÉE
{
			global $mysqli;
	
			   	$trouvesaison = false;
			   	$EtatSaison="";

               	$resultsaison = getSaison (date('m',$dateSaison),date('Y',$dateSaison));
				$dateSaison=date('Y-m-d',$dateSaison); // conversion au format date
                                                    
				   while ( ($ressqlsaison = $resultsaison->fetch_assoc()) and $trouvesaison==false)
				   {
					   	
					$dateDebutSai= $ressqlsaison["date_debut"];    
					$dateFinSai= $ressqlsaison["date_fin"];	
					   
						if (explode("-", $dateSaison) >= explode("-", $dateDebutSai) and explode("-", $dateSaison) <= explode("-", $dateFinSai))      
						{
								if ($ressqlsaison["statut"]=="HS")
								{
									$trouvesaison = true;
									$EtatSaison="H";  // saison haute true
										
								}
	//							else if ($ressqlsaison["statut"]=="IN")
	//							{
	//								$trouvesaison = true;
	//								$EtatSaison="N";  // saison indispo true
	//							}
								else
								{
									$trouvesaison = true;
									$EtatSaison="I";  // saison basse true
								}                              
					   }
				   }

	return $EtatSaison;
}

function calculTarif ($date_debut,$date_fin,$idgite,$statutCheminot)  // CALCUL DU TARIF 
{
	
	global $mysqli;
	
	$nbHs = 0 ;
	$nbBs = 0 ;

	$date2=$date_debut;
	$dateFintest=$date_fin;

	while ($date2<$dateFintest)  // comparaison date format date
	{
		if(typeSaison(strtotime($date2)))  // conversion en chaîne
		{
			if(nbJours($date_debut, $date_fin)>=7) // calcul nombre jours de la résa si supérieur à 7 uniquement tarif BS
			{
				$nbBs++;
			}	
			else // si inféreieur à 7 calcul nombre jour HS
			{
				$nbHs++;
			}
		}
		else
		{
			$nbBs++; 
			//var_dump($nbBs);   
		}
		$date2= date('Y-m-d', strtotime($date2." +1 day")); // INCREMENTATION EN FORMAT CHAINE MAIS VARIABLE AU FORMAT DATE
		
	}

	$reqTarif = "SELECT t.prix, p.idgite, p.idtarif, t.saison, t.statut_client 
					FROM POSSEDETARIF p, TARIF t
					WHERE p.idtarif = t.idtarif
					AND p.idgite=".$idgite; // select du tarif en fonction du numéro de gîte
					
	if (isset($statutCheminot)) // si statut cheminot renseigné
	{
		$reqTarif.= " AND t.statut_client ='".$statutCheminot."'";
	}
	else {
		$reqTarif.= " AND t.statut_client ='EX'";	// sinon statut exterieur
	}
	//echo $reqTarif ;
	$sqlTarif = $mysqli->query($reqTarif);
	$totalTarif=0;
	
	while ($resqlTarif = $sqlTarif->fetch_assoc())
	{

		if($resqlTarif['saison'])    /****** VERIFICATION BASSE SAISON SI + DE 7 JOURS */
		{
			$totalTarif += ($resqlTarif['prix']*$nbHs);	// prix HS
		}
		else
		{
			$totalTarif = ($resqlTarif['prix']*$nbBs);	// prix BS
		}
	}
	
	return 	$totalTarif;
}

function verifIndispo ($date_debut,$date_fin) // VERIFICATION DES FERMETURES DU GÎTE
{
		global $mysqli;
		$reqSaison="SELECT date_debut, date_fin, statut FROM SAISON WHERE statut='IN'";
		$ressultSaison=$mysqli->query($reqSaison);
		$resaPossible=true;
	
			$date_debut=date_create($date_debut);
			$date_fin=date_create($date_fin);
	
			while (($ressqlSaison = $ressultSaison->fetch_assoc()) and $resaPossible==true)
				{ 		
						$debutSaison=date_create($ressqlSaison['date_debut']);
						$finSaison=date_create($ressqlSaison['date_fin']);	

					if (($date_debut>$debutSaison and $date_debut>$finSaison) or ($date_debut<$debutSaison and $date_fin<$debutSaison))
					{
						
						$resaPossible = true;
					}
					else
					{	
						$resaPossible = false;
					}
			}
		return $resaPossible;
}


function verifReservation ($dateDeb,$dateFin,$idgite) // VERIFICATION DES RESERVATIONS EN FONCTION DES AUTRES GÎTES
{
/**************************************************************
*                                                             *
* VÉRIFIE LA POSSIBILITÉ D'UNE RÉSERVATION POUR UN GITE DONNÉ *
*															  *
**************************************************************/

			global $mysqli;
			
			$ResaPossible=true;
			$req=   "SELECT idreservation,date_debut,date_fin,idgite 
			FROM RESERVATION ";
			$req.=" WHERE  idgite=".$idgite. " AND statut!='A'";
	
			if ($idgite!=1) {$req.=" or  idgite=1 ";} // SI CENTRE RÉSERVÉ RESERVATION AUTRES GÎTES IMPOSSIBLES
			else 
			{
				if (date("l",strtotime($dateDeb))=="Saturday" or date("l",strtotime($dateDeb))=="Sunday")
					{
						$req.=" or idgite IN(2,3,4,5,6,7,8)";
					}
					else
					{
						$req.=" or idgite IN(4,5,6,7,8)";
					}
			}
			
			$sql = $mysqli->query($req);
			$dateDeb=date_create($dateDeb);
			$dateFin=date_create($dateFin);
            
				while ( ($row= $sql->fetch_assoc()) and $ResaPossible==true)
                { 	
										
					$datedebResa= date_create($row["date_debut"]);	
					$dateFinResa= date_create($row["date_fin"]);
					
				
					if (($dateDeb>$datedebResa and $dateDeb>$dateFinResa) or ($dateDeb<$datedebResa and $dateFin<$datedebResa))
					{
							testVar("test if 1");
							if(($dateDeb<$datedebResa) and ($dateFin>$dateFinResa))
							{
								$ResaPossible=false;
							}
							else 
							{
								testVar("test if 2 true");
								$ResaPossible=true;
							}
							
					}
					else
					{
							$ResaPossible=false;
					}
                }
	return $ResaPossible;
	
}

 
function Cryptage($MDP, $Clef) // cryptage des passwords
{
                         
    $LClef = strlen($Clef);
    $LMDP = strlen($MDP);
                         
    if ($LClef < $LMDP){
                 
        $Clef = str_pad($Clef, $LMDP, $Clef, STR_PAD_RIGHT);
     
    }
                 
    elseif ($LClef > $LMDP){
 
        $diff = $LClef - $LMDP;
        $_Clef = substr($Clef, 0, -$diff);
 
    }
             
    return $MDP ^ $Clef; // La fonction envoie le texte crypté
             
}

 function nbJours($dateDeb, $dateFin) {
	 
	 
/**************************************************************
*                                                             *
* Calcul de la différence entre deux dates *
*															  *
**************************************************************/

						//60 secondes X 60 minutes X 24 heures dans une journée
						$nbSecondes= 60*60*24;
				 
						$debutDiff = strtotime($dateDeb);
						$finDiff = strtotime($dateFin);
						// $diff = abs($finDiff - $debutDiff); // evite valeur négatif, remplace par zéro
	 					$diff = $finDiff - $debutDiff;
						return round($diff / $nbSecondes);
					}

function verifSemaineForce($dateDeb,$dateFin,$idgite) 

	{	 
/**************************************************************
*                                                             *
* vérificationd des réservations en été *
*															  *
	*	retourne true si la resa est possible
**************************************************************/

	global $mysqli;

	$ResaSemaineForce=false;
	$sqlSemaine = "SELECT idsemaine, date_debut, date_fin FROM SEMAINEFORCE";
	// test dev sur 2ans 	
	
	$resultSemaine=$mysqli->query($sqlSemaine);
		
	
		while ($ressqlSemaine = $resultSemaine->fetch_assoc() and $ResaSemaineForce==false) // parcours de la requête
			{ 		
				$debutSemaine=$ressqlSemaine['date_debut'];
				$finSemaine=$ressqlSemaine['date_fin'];
//				echo $debutSemaine;
//				echo '<br/>';
//				echo $finSemaine;

				if((($dateDeb<$debutSemaine) and ($dateFin<$debutSemaine)) or (($dateDeb>$finSemaine) and ($dateFin>$finSemaine))) // si on est en semaine forcée
				{
					echo " <br />pas en été <br />";
				}
				else 
				{
						if($idgite==8) // exception du dortoir
						{
							echo "le dortoir peut-être réservé en été sans restriction";
						} // fin if idgite==8 (dortoir)
						else 
						{
					
							if ($dateDeb<$debutSemaine and date("l",strtotime($dateFin))=="Saturday") // cas 1 date en juin et réservation un samedi en juillet
							{
									echo " 1er test resa SemaineForce";
									$ResaSemaineForce=true;
							}
							else
							{
									if($dateFin>$finSemaine and date("l",strtotime($dateDeb))=="Saturday") // cas2 date début un samedi en août et date de fin en septembre
									{	
										$ResaSemaineForce=true;
										echo " 2eme test resa SemaineForce la dernière reservation d'aout doit être un samedi";
									}
									else
									{
												if(($dateDeb>=$debutSemaine and $dateFin<=$finSemaine) and (date("l",strtotime($dateDeb))=="Saturday" and date("l",strtotime($dateFin))=="Saturday")) // cas3 en été début=samedi, fin=samedi
												{
													$ResaSemaineForce=true;
													echo " 3eme test resa SemaineForce";
												}
												else 
												{
													$ResaSemaineForce=false; // si dates non conformes
													echo " else -> false";
												}
									}		
							} // fin cas2
						} // fin else test dortoir

				} // fin else test date
		} // fin while
		return $ResaSemaineForce; // true (resa possible) or false
	
} // fin function semaineforce

/**************************************************************
*                                                             *
* Calcul des taxes *
*															  *
**************************************************************/

function calculTaxe ($denomination,$facteur,$idreservation,$action) // calcul d'une taxe en fonction de sa dénomination et d'un nombre adultes/enfants
	{
		
		global $mysqli; // obligatoire dans une fonction pour rappel d'un objet mysqli
	
		$reqTaxe = "SELECT tarif, denomination, idtaxe FROM TAXE WHERE denomination='".$denomination."'"; 
		// recherche de la taxe en fonction de sa dénomination
	
		$sqlTaxe = $mysqli->query($reqTaxe); 
				 
		while ($resqlTaxe = $sqlTaxe->fetch_assoc()) // parcours requête
		{
			$tarif = $resqlTaxe['tarif']; // récupération tarif de la taxe
			$idtaxe = $resqlTaxe['idtaxe']; 
		}
		
		$taxe = $tarif * $facteur; // calcul de la taxe
	
		if($action=="I")
		{
			$reqInsert = "INSERT INTO FIXERTAXE (idreservation,idtaxe,facteur) VALUE ('".$idreservation."','".$idtaxe."','".$facteur."',"; 
			// insertion du lien entre une taxe et une commande
				
			$mysqli->query($reqInsert);

				  if(!$mysqli)
				  {
					echo "L'inscription ne s'est pas bien déroulée" ;   // redirection vers page récapitulative de la réservation et permet la connexion
				  } 
				
		}
		else
		{
			echo "pas d'insertion";
		}
			 
		return $taxe;	// retourne le montant de la taxe
	}


/**************************************************************
*                                                             *
* Fonction capacités et comparaisons*
*															  *
**************************************************************/

function verifCapacite($nb1,$nb2,$nb3) // comparaison d'une addition avec un nombre
{
	if($nb1 + $nb2 <= $nb3)
	{
		return true;
	}
	else {return false;}
}

function compareVal($val1,$val2,$val3,$val4) // comparaison de 4 valeurs
{
    if ($val1===$val2 or $val1===$val3 or $val1===$val4 or $val2===$val3 or $val2===$val4 or $val3===$val4)
    {
        return true;
    }
    else {return false;}    
}
	
	
/**************************************************************
*                                                             *
* Génération aléatoire de nombre*
*															  *
**************************************************************/

function chaineAleatoire( $longueur)
{
    $character_set_array = array();
    $character_set_array[] = array('count' =>  $longueur, 'characters' => strtoupper('abcdefghijklmnopqrstuvwxyz'));
    $character_set_array[] = array('count' =>  1, 'characters' => '0123456789');
    $temp_array = array();
    foreach ($character_set_array as $character_set) {
        for ($i = 0; $i < $character_set['count']; $i++) {
            $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
        }
    }
    shuffle($temp_array);
    return implode('', $temp_array);
}

/**************************************************************
*                                                             *
* Vérification de l'unicité de l'id paypal *
*															  *
**************************************************************/
function verifIdTransaction($val)
{
	global $mysqli; // obligatoire dans une fonction pour rappel d'un objet mysqli
	
	$sqlVeriefTransaction = "SELECT txn_id FROM TRANSACTION WHERE txn_id='".$val."'";
	echo $sqlVeriefTransaction;
	if ($stmt = $mysqli->prepare($sqlVeriefTransaction))  // prépare la requête à l'éxecution et retourne un objet
	{
		/* Exécution de la requête */
		$stmt->execute();
	
		/* Stockage du résultat */
		$stmt->store_result();
		//printf("Nombre de lignes : %d.\n", $stmt->num_rows); // test nombre ligne de la rêquete
		
		if(($stmt->num_rows)>=1) // si supérieur ou = 1 alors l'id existe déjà
		{
			return false;
			$stmt->close();
		} else 
			{
				return true;
				$stmt->close();	
			}
	}
}

function listageArray($tab) /* affiche tableau multidimentionnel des réservations (fonction récursive) */
{
        //Pour chaque élément du tableau
        foreach($tab as $key => $value)
        {
                //Si l'élément est un tableau, on appelle la fonction pour qu'elle le parcoure
                if(is_array($value))
                {
                        echo "Réservation numéro ".$key.' :<ul>';
                        ListageArray($value);
                        echo '</ul><br />';
                }
                else //Sinon, c'est un élément à afficher, liste
                {
                        echo '<li>'.$key.': '.$value.'</li>';
                }
        }
}

function envoiPwd($email) // fonction qui génére un nouveau mot de passe
{
	global $mysqli;
	if(isset($email))
		{
			 

		 $sqlVerifExistant 	= "SELECT email,mp from CLIENTS WHERE email ='".$email."'" ; //verif mail unique
		
		 $result=$mysqli->query($sqlVerifExistant);
		 
		 if ($row=$result->fetch_Assoc())
		 {
						
			$newPass = chaineAleatoire(8);
			
			$Clef = "Matteo1234567890";
		
			  $pass		= Cryptage($newPass,$Clef) ;
			  $pass		= utf8_encode($pass); 
 		
			$reqUpdate="Update CLIENTS SET mp= '".$pass."' where email='".$email."'";
			$mysqli->query($reqUpdate);
			//envoiMail($email, "Votre nouveau mot de passe","voici votre mot de passe : ".$newPass,$copy);
			return $newPass;
		 }
			
			
			
		 }
		else{echo "Email invalide";}


}

//fonction d'envoi mail 
function envoiMail($destinataire, $sujet,$message,$copy)
{
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}
	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = $message;
	$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
	
	
	
	//template du messaeg mail : 
	$message_html='<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="initial-scale=1.0">    <!-- So that mobile webkit will display zoomed in -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->

    <title>Antwort - responsive Email Layout</title>
    <style type="text/css">

        /* Resets: see reset.css for details */
        .ReadMsgBody { width: 100%; background-color: #ebebeb;}
        .ExternalClass {width: 100%; background-color: #ebebeb;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table {border-spacing:0;}
        table td {border-collapse:collapse;}
        .yshortcuts a {border-bottom: none !important;}


        /* Constrain email width for small screens */
        @media screen and (max-width: 600px) {
            table[class="container"] {
                width: 95% !important;
            }
        }

        /* Give content more room on mobile */
        @media screen and (max-width: 480px) {
            td[class="container-padding"] {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
         }

    </style>
</head>
<body style="margin:0; padding:10px 0;" bgcolor="#ebebeb" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<br>

<!-- 100% wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#ebebeb">
  <tr>
    <td align="center" valign="top" bgcolor="#ebebeb" style="background-color: #ebebeb;">

      <!-- 600px container (white background) -->
      <table border="0" width="600" cellpadding="0" cellspacing="0" class="container" bgcolor="#ffffff">
        <tr>
          <td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 14px; line-height: 20px; font-family: Helvetica, sans-serif; color: #333;">
            <br>

            <!-- ### BEGIN CONTENT ### -->
            <div style="font-weight: bold; font-size: 18px; line-height: 24px; color: #D03C0F">
        '.$sujet.'
            </div><br>
';
$message_html.=$message.'

          </td>
        </tr>
      </table>
      <!--/600px container -->

    </td>
  </tr>
</table>
<!--/100% wrapper-->
<br>
<br>
</body>
</html>';


	
	
	//==========
	 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	
	//=========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"Gite le Metzval\"<contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "Reply-to: \"Gite le Metzval\" <contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//==========
	 
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	 
	//=====Envoi de l'e-mail.
	mail($destinataire,$sujet,$message,$header);
	//==========
}

function envoyerEmail($email, $sujet, $message){
	global $mysqli;

	require_once('includes/ink/phpmailer/class.phpmailer.php');
	require_once('includes/ink/baseMailHTML.php');

	//infos client
	$sqlVerifExistant 	= "SELECT civilite, nom, prenom from CLIENTS WHERE email ='".$email."'" ;
	$result=$mysqli->query($sqlVerifExistant);
	if ($row=$result->fetch_Assoc()) {	
		$civilite = $row['civilite'];
		$nom = $row['nom'];
		$prenom = $row['prenom'];

		$message_html=$messageHeader.$messageCSS.$messageBodyBefore.
			'<tr>
				<td>
				<h1>Bonjour '.$civilite.'&nbsp; '.$nom.' '.$prenom.'</h1>
							'.nl2br('<p class="lead">'.htmlspecialchars($message).'</p>').'
							<p>Ci-joint votre facture au format pdf pour votre r&eacute;servation au g&icirc;te Le Metzval.</p>
							<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
							<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
				</td>
				<td class="expander"></td>
			</tr>'.$messageBodyAfter;

		$mail = new PHPMailer(); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
		$mail->AddReplyTo(MAIL_METZVAL, 'G&icirc;te le metzval');
		$mail->AddAddress($email, $nom.' '.$prenom);
		$mail->SetFrom(MAIL_METZVAL, 'G&icirc;te le metzval');
		$mail->Subject = htmlspecialchars($sujet);
		$mail->MsgHTML($message_html);
		$mail->Send();
	}else
		echo "Erreur, l'email est invalide.";

}


/**************************************************************
* déconnexion *				
**************************************************************/
 function deco()
 {
	global $mysqli;
	$mysqli->close();
 }
 
/**************************************************************
* fil d'ariane par rapport à home *				
**************************************************************/
 function breadcrumbs($text = 'Vous êtes: ', $sep = ' &raquo; ', $home = 'Home') {
//Use RDFa breadcrumb, can also be used for microformats etc.
$bc     =   '<div xmlns:v="http://rdf.data-vocabulary.org/#" id="crums">'.$text;
//Get the website:
$site   =   'http://'.$_SERVER['HTTP_HOST'];
//Get all vars en skip the empty ones
$crumbs =   array_filter( explode("/",$_SERVER["REQUEST_URI"]) );
//Create the home breadcrumb
$bc    .=   '<span typeof="v:Breadcrumb"><a href="'.$site.'" rel="v:url" property="v:title">'.$home.'</a>'.$sep.'</span>'; 
//Count all not empty breadcrumbs
$nm     =   count($crumbs);
$i      =   1;
//Loop the crumbs
foreach($crumbs as $crumb){
    //Make the link look nice
    $link    =  ucfirst( str_replace( array(".php","-","_"), array(""," "," ") ,$crumb) );
    //Loose the last seperator
    $sep     =  $i==$nm?'':$sep;
    //Add crumbs to the root
    $site   .=  '/'.$crumb;
    //Make the next crumb
    $bc     .=  '<span typeof="v:Breadcrumb"><a href="'.$site.'" rel="v:url" property="v:title">'.$link.'</a>'.$sep.'</span>';
    $i++;
}
$bc .=  '</div>';
//Return the result
return $bc;}

/**************************************************************
* conversion date *				
**************************************************************/
function dateFr($date) {
	return date('d/m/Y',strtotime($date));
}

/**************************************************************
* conversion date *				
**************************************************************/
function dateSql($date) {
	return date('Y-m-d',strtotime($date));
}



/**

	* Test ink mail foundation
*/


//fonction d'envoi mail avec template

	/**
		*	intégrer des variable pour mp, information resa
	*/
function templateMail($action,$destinataire, $sujet,$message,$copy)
{
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}
	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = $message;
	$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
	
	
	
	//template du messaeg mail : 
	$message_html='';

	//==========
	 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	
	//=========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"Gite le Metzval\"<contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "Reply-to: \"Gite le Metzval\" <contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//==========
	 
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	 
	//=====Envoi de l'e-mail.
	mail($destinataire,$sujet,$message,$header);
	//==========
}


/**

	* fonction template Mail
*/

function envoiMail2($destinataire, $sujet,$message,$copy)
{
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}
	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = $message;
	
	
	//template du messaeg mail : 
	$message_html=file_get_contents('http://srvweb/resa/dev-sdk-git/develop/includes/ink/template/mailTest.html');

	//==========
	 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	
	//=========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"Gite le Metzval\"<contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "Reply-to: \"Gite le Metzval\" <contact@gite-lemetzval.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//==========
	 
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	 
	//=====Envoi de l'e-mail.
	mail($destinataire,$sujet,$message,$header);
	//==========
}


function test() {
	$texteMail = "blablablabla";
	$test = include('ink\template\mailTest.php');
	return $test;
}
	
function test2() {
	$test2 = fopen(".\ink\template\mailTest.html", "r");
	return $test2;
}

function get_email( )
{
    ob_start( ) ;
    include 'template/mailTest.html' ;
    return ob_get_clean( ) ;
}


?>