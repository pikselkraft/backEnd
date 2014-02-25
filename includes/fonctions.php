
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
/* définir le fuseau horaire -> à mettre dans le header.php 
/***********************************************************/
date_default_timezone_set('Europe/London');

$script_tz = date_default_timezone_get();

if (strcmp($script_tz, ini_get('date.timezone')))
{
    //echo 'Le décalage horaire du script diffère du décalage horaire défini dans le fichier ini. <br />';
} 


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
				  	//var_dump($nbBs++);
				  echo '</br>';
				  echo "BS++ boucle if";
				  echo '</br>';
			  }	
				else // si inféreieur à 7 calcul nombre jour HS
				{
					$nbHs++;
					//var_dump($nbHs++);
					echo '</br>';
					echo "HS++ boucle if";
					echo '</br>';
				}
		   }
			else
		   {
				$nbBs++; 
			  	var_dump($nbBs);   
		   }
		 $date2= date('Y-m-d', strtotime($date2." +1 day")); // INCREMENTATION EN FORMAT CHAINE MAIS VARIABLE AU FORMAT DATE
		 var_dump( $date2);
		 var_dump($nbBs); 
		
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
	echo $reqTarif ;
	$sqlTarif = $mysqli->query($reqTarif);
	$totalTarif=0;
	
	while ($resqlTarif = $sqlTarif->fetch_assoc())
	{

		if($resqlTarif['saison'])    /****** VERIFICATION BASSE SAISON SI + DE 7 JOURS */
		{
			$totalTarif=$totalTarif + ($resqlTarif['prix']*$nbHs);	// prix HS
		}
		else
		{
			$totalTarif=$totalTarif + ($resqlTarif['prix']*$nbBs);	// prix BS
			echo $resqlTarif['prix']."<br />";
			echo $nbBs;
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
			envoiMail($email, "Votre nouveau mot de passe","voici votre mot de passe : ".$newPass,$copy);
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



/**

	* Test ink mail foundation
*/


//fonction d'envoi mail avec template

	/**
		*	intégrer des variable pour mp, information resa
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
	$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
	
	
	
	//template du messaeg mail : 
	$message_html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width"/>
	<style>

/* Client-specific Styles & Reset */

#outlook a { 
  padding:0; 
} 

body{ 
  width:100% !important; 
  min-width: 100%;
  -webkit-text-size-adjust:100%; 
  -ms-text-size-adjust:100%; 
  margin:0; 
  padding:0;
}

.ExternalClass { 
  width:100%;
} 

.ExternalClass, 
.ExternalClass p, 
.ExternalClass span, 
.ExternalClass font, 
.ExternalClass td, 
.ExternalClass div { 
  line-height: 100%; 
} 

#backgroundTable { 
  margin:0; 
  padding:0; 
  width:100% !important; 
  line-height: 100% !important; 
}

img { 
  outline:none; 
  text-decoration:none; 
  -ms-interpolation-mode: bicubic;
  width: auto;
  max-width: 100%; 
  float: left; 
  clear: both; 
  display: block;
}

center {
  width: 100%;
  min-width: 580px;
}

a img { 
  border: none;
}

p {
  margin: 0 0 0 10px;
}

table {
  border-spacing: 0;
  border-collapse: collapse;
}

td { 
  word-break: break-word;
  -webkit-hyphens: auto;
  -moz-hyphens: auto;
  hyphens: auto;
  border-collapse: collapse !important; 
}

table, tr, td {
  padding: 0;
  vertical-align: top;
  text-align: left;
}

hr {
  color: #d9d9d9; 
  background-color: #d9d9d9; 
  height: 1px; 
  border: none;
}

/* Responsive Grid */

table.body {
  height: 100%;
  width: 100%;
}

table.container {
  width: 580px;
  margin: 0 auto;
  text-align: inherit;
}

table.row { 
  padding: 0px; 
  width: 100%;
  position: relative;
}

table.container table.row {
  display: block;
}

td.wrapper {
  padding: 10px 20px 0px 0px;
  position: relative;
}

table.columns,
table.column {
  margin: 0 auto;
}

table.columns td,
table.column td {
  padding: 0px 0px 10px; 
}

table.columns td.sub-columns,
table.column td.sub-columns,
table.columns td.sub-column,
table.column td.sub-column {
  padding-right: 10px;
}

td.sub-column, td.sub-columns {
  min-width: 0px;
}

table.row td.last,
table.container td.last {
  padding-right: 0px;
}

table.one { width: 30px; }
table.two { width: 80px; }
table.three { width: 130px; }
table.four { width: 180px; }
table.five { width: 230px; }
table.six { width: 280px; }
table.seven { width: 330px; }
table.eight { width: 380px; }
table.nine { width: 430px; }
table.ten { width: 480px; }
table.eleven { width: 530px; }
table.twelve { width: 580px; }

table.one center { min-width: 30px; }
table.two center { min-width: 80px; }
table.three center { min-width: 130px; }
table.four center { min-width: 180px; }
table.five center { min-width: 230px; }
table.six center { min-width: 280px; }
table.seven center { min-width: 330px; }
table.eight center { min-width: 380px; }
table.nine center { min-width: 430px; }
table.ten center { min-width: 480px; }
table.eleven center { min-width: 530px; }
table.twelve center { min-width: 580px; }

table.one .panel center { min-width: 10px; }
table.two .panel center { min-width: 60px; }
table.three .panel center { min-width: 110px; }
table.four .panel center { min-width: 160px; }
table.five .panel center { min-width: 210px; }
table.six .panel center { min-width: 260px; }
table.seven .panel center { min-width: 310px; }
table.eight .panel center { min-width: 360px; }
table.nine .panel center { min-width: 410px; }
table.ten .panel center { min-width: 460px; }
table.eleven .panel center { min-width: 510px; }
table.twelve .panel center { min-width: 560px; }

.body .columns td.one,
.body .column td.one { width: 8.333333%; }
.body .columns td.two,
.body .column td.two { width: 16.666666%; }
.body .columns td.three,
.body .column td.three { width: 25%; }
.body .columns td.four,
.body .column td.four { width: 33.333333%; }
.body .columns td.five,
.body .column td.five { width: 41.666666%; }
.body .columns td.six,
.body .column td.six { width: 50%; }
.body .columns td.seven,
.body .column td.seven { width: 58.333333%; }
.body .columns td.eight,
.body .column td.eight { width: 66.666666%; }
.body .columns td.nine,
.body .column td.nine { width: 75%; }
.body .columns td.ten,
.body .column td.ten { width: 83.333333%; }
.body .columns td.eleven,
.body .column td.eleven { width: 91.666666%; }
.body .columns td.twelve,
.body .column td.twelve { width: 100%; }

td.offset-by-one { padding-left: 50px; }
td.offset-by-two { padding-left: 100px; }
td.offset-by-three { padding-left: 150px; }
td.offset-by-four { padding-left: 200px; }
td.offset-by-five { padding-left: 250px; }
td.offset-by-six { padding-left: 300px; }
td.offset-by-seven { padding-left: 350px; }
td.offset-by-eight { padding-left: 400px; }
td.offset-by-nine { padding-left: 450px; }
td.offset-by-ten { padding-left: 500px; }
td.offset-by-eleven { padding-left: 550px; }

td.expander {
  visibility: hidden;
  width: 0px;
  padding: 0 !important;
}

table.columns .text-pad,
table.column .text-pad {
  padding-left: 10px;
  padding-right: 10px;
}

table.columns .left-text-pad,
table.columns .text-pad-left,
table.column .left-text-pad,
table.column .text-pad-left {
  padding-left: 10px;
}

table.columns .right-text-pad,
table.columns .text-pad-right,
table.column .right-text-pad,
table.column .text-pad-right {
  padding-right: 10px;
}

/* Block Grid */

.block-grid {
  width: 100%;
  max-width: 580px;
}

.block-grid td {
  display: inline-block;
  padding:10px;
}

.two-up td {
  width:270px;
}

.three-up td {
  width:173px;
}

.four-up td {
  width:125px;
}

.five-up td {
  width:96px;
}

.six-up td {
  width:76px;
}

.seven-up td {
  width:62px;
}

.eight-up td {
  width:52px;
}

/* Alignment & Visibility Classes */

table.center, td.center {
  text-align: center;
}

h1.center,
h2.center,
h3.center,
h4.center,
h5.center,
h6.center {
  text-align: center;
}

span.center {
  display: block;
  width: 100%;
  text-align: center;
}

img.center {
  margin: 0 auto;
  float: none;
}

.show-for-small,
.hide-for-desktop {
  display: none;
}

/* Typography */

body, table.body, h1, h2, h3, h4, h5, h6, p, td { 
  color: #222222;
  font-family: "Helvetica", "Arial", sans-serif; 
  font-weight: normal; 
  padding:0; 
  margin: 0;
  text-align: left; 
  line-height: 1.3;
}

h1, h2, h3, h4, h5, h6 {
  word-break: normal;
}

h1 {font-size: 40px;}
h2 {font-size: 36px;}
h3 {font-size: 32px;}
h4 {font-size: 28px;}
h5 {font-size: 24px;}
h6 {font-size: 20px;}
body, table.body, p, td {font-size: 14px;line-height:19px;}

p.lead, p.lede, p.leed {
  font-size: 18px;
  line-height:21px;
}

p { 
  margin-bottom: 10px;
}

small {
  font-size: 10px;
}

a {
  color: #2ba6cb; 
  text-decoration: none;
}

a:hover { 
  color: #2795b6 !important;
}

a:active { 
  color: #2795b6 !important;
}

a:visited { 
  color: #2ba6cb !important;
}

h1 a, 
h2 a, 
h3 a, 
h4 a, 
h5 a, 
h6 a {
  color: #2ba6cb;
}

h1 a:active, 
h2 a:active,  
h3 a:active, 
h4 a:active, 
h5 a:active, 
h6 a:active { 
  color: #2ba6cb !important; 
} 

h1 a:visited, 
h2 a:visited,  
h3 a:visited, 
h4 a:visited, 
h5 a:visited, 
h6 a:visited { 
  color: #2ba6cb !important; 
} 

/* Panels */

.panel {
  background: #f2f2f2;
  border: 1px solid #d9d9d9;
  padding: 10px !important;
}

.sub-grid table {
  width: 100%;
}

.sub-grid td.sub-columns {
  padding-bottom: 0;
}

/* Buttons */

table.button,
table.tiny-button,
table.small-button,
table.medium-button,
table.large-button {
  width: 100%;
  overflow: hidden;
}

table.button td,
table.tiny-button td,
table.small-button td,
table.medium-button td,
table.large-button td {
  display: block;
  width: auto !important;
  text-align: center;
  background: #2ba6cb;
  border: 1px solid #2284a1;
  color: #ffffff;
  padding: 8px 0;
}

table.tiny-button td {
  padding: 5px 0 4px;
}

table.small-button td {
  padding: 8px 0 7px;
}

table.medium-button td {
  padding: 12px 0 10px;
}

table.large-button td {
  padding: 21px 0 18px;
}

table.button td a,
table.tiny-button td a,
table.small-button td a,
table.medium-button td a,
table.large-button td a {
  font-weight: bold;
  text-decoration: none;
  font-family: Helvetica, Arial, sans-serif;
  color: #ffffff;
  font-size: 16px;
}

table.tiny-button td a {
  font-size: 12px;
  font-weight: normal;
}

table.small-button td a {
  font-size: 16px;
}

table.medium-button td a {
  font-size: 20px;
}

table.large-button td a {
  font-size: 24px;
}

table.button:hover td,
table.button:visited td,
table.button:active td {
  background: #2795b6 !important;
}

table.button:hover td a,
table.button:visited td a,
table.button:active td a {
  color: #fff !important;
}

table.button:hover td,
table.tiny-button:hover td,
table.small-button:hover td,
table.medium-button:hover td,
table.large-button:hover td {
  background: #2795b6 !important;
}

table.button:hover td a,
table.button:active td a,
table.button td a:visited,
table.tiny-button:hover td a,
table.tiny-button:active td a,
table.tiny-button td a:visited,
table.small-button:hover td a,
table.small-button:active td a,
table.small-button td a:visited,
table.medium-button:hover td a,
table.medium-button:active td a,
table.medium-button td a:visited,
table.large-button:hover td a,
table.large-button:active td a,
table.large-button td a:visited {
  color: #ffffff !important; 
}

table.secondary td {
  background: #e9e9e9;
  border-color: #d0d0d0;
  color: #555;
}

table.secondary td a {
  color: #555;
}

table.secondary:hover td {
  background: #d0d0d0 !important;
  color: #555;
}

table.secondary:hover td a,
table.secondary td a:visited,
table.secondary:active td a {
  color: #555 !important;
}

table.success td {
  background: #5da423;
  border-color: #457a1a;
}

table.success:hover td {
  background: #457a1a !important;
}

table.alert td {
  background: #c60f13;
  border-color: #970b0e;
}

table.alert:hover td {
  background: #970b0e !important;
}

table.radius td {
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}

table.round td {
  -webkit-border-radius: 500px;
  -moz-border-radius: 500px;
  border-radius: 500px;
}

/* Outlook First */

body.outlook p {
  display: inline !important;
}

/*  Media Queries */

@media only screen and (max-width: 600px) {

  table[class="body"] img {
    width: auto !important;
    height: auto !important;
  }

  table[class="body"] center {
    min-width: 0 !important;
  }

  table[class="body"] .container {
    width: 95% !important;
  }

  table[class="body"] .row {
    width: 100% !important;
    display: block !important;
  }

  table[class="body"] .wrapper {
    display: block !important;
    padding-right: 0 !important;
  }

  table[class="body"] .columns,
  table[class="body"] .column {
    table-layout: fixed !important;
    float: none !important;
    width: 100% !important;
    padding-right: 0px !important;
    padding-left: 0px !important;
    display: block !important;
  }

  table[class="body"] .wrapper.first .columns,
  table[class="body"] .wrapper.first .column {
    display: table !important;
  }

  table[class="body"] table.columns td,
  table[class="body"] table.column td {
    width: 100% !important;
  }

  table[class="body"] .columns td.one,
  table[class="body"] .column td.one { width: 8.333333% !important; }
  table[class="body"] .columns td.two,
  table[class="body"] .column td.two { width: 16.666666% !important; }
  table[class="body"] .columns td.three,
  table[class="body"] .column td.three { width: 25% !important; }
  table[class="body"] .columns td.four,
  table[class="body"] .column td.four { width: 33.333333% !important; }
  table[class="body"] .columns td.five,
  table[class="body"] .column td.five { width: 41.666666% !important; }
  table[class="body"] .columns td.six,
  table[class="body"] .column td.six { width: 50% !important; }
  table[class="body"] .columns td.seven,
  table[class="body"] .column td.seven { width: 58.333333% !important; }
  table[class="body"] .columns td.eight,
  table[class="body"] .column td.eight { width: 66.666666% !important; }
  table[class="body"] .columns td.nine,
  table[class="body"] .column td.nine { width: 75% !important; }
  table[class="body"] .columns td.ten,
  table[class="body"] .column td.ten { width: 83.333333% !important; }
  table[class="body"] .columns td.eleven,
  table[class="body"] .column td.eleven { width: 91.666666% !important; }
  table[class="body"] .columns td.twelve,
  table[class="body"] .column td.twelve { width: 100% !important; }

  table[class="body"] td.offset-by-one,
  table[class="body"] td.offset-by-two,
  table[class="body"] td.offset-by-three,
  table[class="body"] td.offset-by-four,
  table[class="body"] td.offset-by-five,
  table[class="body"] td.offset-by-six,
  table[class="body"] td.offset-by-seven,
  table[class="body"] td.offset-by-eight,
  table[class="body"] td.offset-by-nine,
  table[class="body"] td.offset-by-ten,
  table[class="body"] td.offset-by-eleven {
    padding-left: 0 !important;
  }

  table[class="body"] table.columns td.expander {
    width: 1px !important;
  }

  table[class="body"] .right-text-pad,
  table[class="body"] .text-pad-right {
    padding-left: 10px !important;
  }

  table[class="body"] .left-text-pad,
  table[class="body"] .text-pad-left {
    padding-right: 10px !important;
  }

  table[class="body"] .hide-for-small,
  table[class="body"] .show-for-desktop {
    display: none !important;
  }

  table[class="body"] .show-for-small,
  table[class="body"] .hide-for-desktop {
    display: inherit !important;
  }
}

  </style>
  <style>

    table.facebook td {
      background: #3b5998;
      border-color: #2d4473;
    }

    table.facebook:hover td {
      background: #2d4473 !important;
    }

    table.twitter td {
      background: #00acee;
      border-color: #0087bb;
    }

    table.twitter:hover td {
      background: #0087bb !important;
    }

    table.google-plus td {
      background-color: #DB4A39;
      border-color: #CC0000;
    }

    table.google-plus:hover td {
      background: #CC0000 !important;
    }

    .template-label {
      color: #ffffff;
      font-weight: bold;
      font-size: 11px;
    }

    .callout .panel {
      background: #ECF8FF;
      border-color: #b9e5ff;
    }

    .header {
      background: #999999;
    }

    .footer .wrapper {
      background: #ebebeb;
    }

    .footer h5 {
      padding-bottom: 10px;
    }

    table.columns .text-pad {
      padding-left: 10px;
      padding-right: 10px;
    }

    table.columns .left-text-pad {
      padding-left: 10px;
    }

    table.columns .right-text-pad {
      padding-right: 10px;
    }

    @media only screen and (max-width: 600px) {

      table[class="body"] .right-text-pad {
        padding-left: 10px !important;
      }

      table[class="body"] .left-text-pad {
        padding-right: 10px !important;
      }
    }

  </style>
</head>
<body>
	<table class="body">
		<tr>
			<td class="center" align="center" valign="top">
        <center>

          <table class="row header">
            <tr>
              <td class="center" align="center">
                <center>

                  <table class="container">
                    <tr>
                      <td class="wrapper last">

                        <table class="twelve columns">
                          <tr>
                            <td class="six sub-columns">
                              <img src="includes/img/metzval-logo.png">
                            </td>
                            <td class="six sub-columns last" style="text-align:right; vertical-align:middle;">
                              <span class="template-label">Le gîte Le Metzval</span>
                            </td>
                            <td class="expander"></td>
                          </tr>
                        </table>

                      </td>
                    </tr>
                  </table>

                </center>
              </td>
            </tr>
          </table>

				 <br>

          <table class="container">
            <tr>
              <td>

              <!-- content start -->

                <table class="row">
                  <tr>
                    <td class="wrapper">

                      <table class="six columns">
                        <tr>
                          <td>
                            <h2>Bonjour,<br> Han Fastolfe</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet.</p>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                      <table class="six columns">
                        <tr>
                          <td class="panel">
                            <p>Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae. <a href="#">Click it! »</a></p>
                          </td>

                          <td class="expander"></td>
                        </tr>
                      </table>

                      <table class="six columns">
                        <tr>
                          <td>
                            <br>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et.</p>

                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet.</p>

                            <table class="button">
                              <tr>
                                <td>
                                  <a href="www.gite-lemetzval.fr">Visiter notre site!</a>
                                </td>
                              </tr>
                            </table>

                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                    </td>
                    <td class="wrapper last">

                      <table class="six columns">
                        <tr>
                          <td class="panel">
                            <h6>Nos gîtes</h6>
                            <p>de 2 à 40 personnes</p>
                            <table>
                              <tr>
                                <td>
                                  <a href="#"Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                            <hr>
                            <table>
                              <tr>
                                <td>
                                  <a href="#">Gîte 2 personnes &raquo;</a>
                                </td>
                              </tr>
                            </table>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                      <br>

                      <table class="six columns">
                        <tr>
                          <td class="panel">
                            <h6 style="margin-bottom:5px;">Nos réseaux sociaux</h6>
                            <table class="tiny-button facebook">
                              <tr>
                                <td>
                                  <a href="#">Facebook</a>
                                </td>
                              </tr>
                            </table>

                            <hr>

                            <table class="tiny-button twitter">
                              <tr>
                                <td>
                                  <a href="#">Google+</a>
                                </td>
                              </tr>
                            </table>

                            <hr>

                            <table class="tiny-button google-plus">
                              <tr>
                                <td>
                                  <a href="#">TripAdvisor</a>
                                </td>
                              </tr>
                            </table>
                            <br>
                            <h6 style="margin-bottom:5px;">Nous contacter:</h6>
                            <p>Phone: <b>408.341.0600</b></p>
                            <p>Email: <a href="mailto:hseldon@trantor.com">contact@</a></p>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>
                <br>
                <br>
                <!-- Legal + Unsubscribe -->
                <table class="row">
                  <tr>
                    <td class="wrapper last">

                      <table class="twelve columns">
                        <tr>
                          <td align="center">
                            <center>
                              <p style="text-align:center;"><a href="#">Terms</a> | <a href="#">Privacy</a> | <a href="#">Unsubscribe</a></p>
                            </center>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>

              <!-- container end below -->
              </td>
            </tr>
          </table>

        </center>
			</td>
		</tr>
	</table>
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

?>