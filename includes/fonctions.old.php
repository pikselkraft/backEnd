<?php
//fonction sie gîte le Metzval
//
//version: 1.0
//
//creation: 23/10/2013/
?>
<?php 

function getResMois ($mois,$annee,$idgite)  // jours du mois en fonctino du gite
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
				$req.=" OR r.idgite=1)"; // vérifie si tout le centre est réservé
	} 
			
			$req.=  " AND (YEAR(r.date_debut)=".$annee." OR YEAR(r.date_fin)=".$annee.") 
					AND (MONTH(r.date_debut)=".$mois." OR MONTH(r.date_debut)=".$moism1.")";
	$sql = $mysqli->query($req);
	//$sql = mysql_query($req) or die("error sql");
	return $sql; 
			
}

function estReserve ($dateTest,$idgite)  // reservation gite
{
        		$EtatJour=0;
					
			    $trouve = false;                                

                $result = getResMois (date('m',$dateTest),date('Y',$dateTest),$idgite); // conversion dates
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

	
function getSaison ($mois,$annee)   // saison de l'année
	{
			global $mysqli; //connexion db
			
				$reqsaison= "SELECT idsaison,date_debut,date_fin,statut
						FROM SAISON";
						$moism1=$mois-1;
						if ($moism1==0) $moism1=12;
						
						$reqsaison.=  " WHERE (YEAR(date_debut)=".$annee." OR YEAR(date_fin)=".$annee.") 
										AND (MONTH(date_debut)=".$mois." OR MONTH(date_debut)=".$moism1.")";
						                  
				$sqlsaison = $mysqli->query($reqsaison);
				return $sqlsaison;
		
	}

function typeSaison ($dateSaison)  // staut des saisons de l'année
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
									
							}else
							{
									$trouvesaison = true;
									$EtatSaison="I";  // saison basse true
							}                  
						}                              
                   }
	return $EtatSaison;
}

function calculTarif ($date_debut,$date_fin,$idgite,$statutCheminot)  // calcul du tarif 
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
				  	echo '</br>';
				  echo "BS++ boucle if";
				  	echo '</br>';
			  }	
				else // si inféreieur à 7 calcul nombre jour HS
				{
					$nbHs++;
					echo '</br>';
					 echo "HS++ boucle if";
					echo '</br>';
				}
		   }
			else
		   {
				$nbBs++;   
			   
		   }
		 $date2= date('Y-m-d', strtotime($date2." +1 day")); // incrementation en format chaine mais variable au format date
		
	}
	
	$reqTarif = "SELECT t.prix, p.idgite, p.idtarif, t.saison, t.statut_client 
					FROM POSSEDETARIF p, TARIF t
					WHERE p.idtarif = t.idtarif
					AND p.idgite=".$idgite; // select du tarif en fonction du numéro de gîte

	if (isset($statutCheminot)) // si statut cheminot renseigné
	{
		$reqTarif.= " AND t.statut_client ='".$statutCheminot."'";
//		echo 'test requete table tarif '.$reqTarif.' fin test';
	}
	else {
		$reqTarif.= " AND t.statut_client ='EX'";	// sinon statut exterieur
	}
		
//		echo $reqTarif;
	$sqlTarif = $mysqli->query($reqTarif);
	$totalTarif=0;
	
	while ($resqlTarif = $sqlTarif->fetch_assoc())
	{

		if($resqlTarif['saison'])    // ****** !!!!!!!!  verification basse saison si + de 7 jours !!!!!!!!!!!!!!!!!!!  
		{
			$totalTarif=$totalTarif + ($resqlTarif['prix']*$nbHs);	// prix HS
		}
		else
		{
			$totalTarif=$totalTarif + ($resqlTarif['prix']*$nbBs);	// prix BS
		}
	}
	
	return 	$totalTarif;
}

function verifIndispo ($date_debut,$date_fin) // verification des fermetures du gîte
{
		global $mysqli;
		$reqSaison="SELECT date_debut, date_fin, statut FROM SAISON WHERE statut='IN'";
//		echo $reqSaison;
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



 function nbJours($debut, $fin) {
	 
	 
/**************************************************************
*                                                             *
* Calcul de la différence entre deux dates *
*															  *
**************************************************************/

						//60 secondes X 60 minutes X 24 heures dans une journée
						$nbSecondes= 60*60*24;
				 
						$debut_ts = strtotime($debut);
						$fin_ts = strtotime($fin);
						$diff = abs($fin_ts - $debut_ts); // evite valeur négatif, remplace par zéro
						return round($diff / $nbSecondes);
					}

function verifReservationOLD ($dateDeb,$dateFin,$idgite,$idresa) // verification des reservations en fonction des autres gîtes
{
/**************************************************************
*                                                             *
* Vérifie la possibilité d'une réservation pour un gite donné *
*															  *
**************************************************************/
//si $idresa est renseigné alors on ne prends pas en compte cette reservcation dans les recherches
			global $mysqli;
			
			$ResaPossible=true;
			$req=   "SELECT idreservation,date_debut,date_fin,idgite 
			FROM RESERVATION ";
			
			if ($idgite!=1) 
			{
				$req.=" WHERE  (idgite=".$idgite;
				$req.=" or  idgite=1) ";
			}
			else
			{
			$req.=" WHERE  idgite=".$idgite;
			
			
			}
			if (!empty($idresa)) $req.=" and  idreservation<>".$idresa;
			echo $req;
			$sql = $mysqli->query($req);

        	
			$dateDeb=date_create($dateDeb);
			$dateFin=date_create($dateFin);
            
				while ( ($row= $sql->fetch_assoc()) and $ResaPossible==true)
                { 	
										
					$datedebResa= date_create($row["date_debut"]);	
					$dateFinResa= date_create($row["date_fin"]);
					
				
					if (($dateDeb>$datedebResa and $dateDeb>$dateFinResa) or ($dateDeb<$datedebResa and $dateFin<$datedebResa))
					{
							$ResaPossible=true;
					}
					else
					{
							$ResaPossible=false;
					}
                }
	return $ResaPossible;
	
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
	
} // fin function semaineforce // fin function semaineforce
 /**************************************************************
*                                                             *
* Génération aléatoire de nombre*
*															  *
**************************************************************/

function chaineAleatoire()
{
    $character_set_array = array();
    $character_set_array[] = array('count' => 7, 'characters' => strtoupper('abcdefghijklmnopqrstuvwxyz'));
    $character_set_array[] = array('count' => 1, 'characters' => '0123456789');
    $temp_array = array();
    foreach ($character_set_array as $character_set) {
        for ($i = 0; $i < $character_set['count']; $i++) {
            $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
        }
    }
    shuffle($temp_array);
    return implode('', $temp_array);
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
	$message_txt = "Salut à tous, voici un e-mail envoyé par un script PHP.";
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



 function deco()
 {
	global $mysqli;
	$mysqli->close();
 }

?>