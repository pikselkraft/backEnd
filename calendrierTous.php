<?php


//Affiche le calendrier des gites


//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//demarrage des sessions****************************************************************************
//----   pour sauvegarder les selections de mois annees mettre en tout debut de page ce code    ----
//                session_start();                                                              ----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//----                  Script de gestion pour calendrier de reservation                        ----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//----   Param�tres possible dans l'url :                                                       ----
//----       mois      : numero du premier mois � afficher dans le calendrier                   ----
//----       an        : ann�e du premier mois a afficher dans le calendrier                    ----
//----       langue    : choix de la langue ( fr,francais, all,allemand, eng, anglais )         ----
//----       logement  : tri des r�servations suivant num�ro "id_logement"                      ----
//----       locataire : tri des r�servations suivant numero "id_locataire"                     ----
//----       date_lien : si �gale � 0, les dates sont cliquables                                ----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//----    Version 2.0                                                                         ----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//----     Param�tres de configurations g�n�rales et modifiables                               -----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------

//nom de la page ou se trouve le script*************************************************************
$adresse_page         = "calendrierTous.php";
//nom de la page a ouvrir lorsqu'on clic sur une date***********************************************
$adresse_destination  = "calendrierTous.php";

$avec_bdd            = false ;

// echo "mon id : ".$idgite;

				
					
					
//echo "IDGITE : ". $idgite;
//***************************************************************************************************
// fichier de param�trage de l'apparence du calendrier
//***************************************************************************************************
// si vous souhaitez avoir une apparence diff�rente pour le calendrier administrateur et
// pour la celndrier visiteurs, il faut cr�er de fichier parametres_calendrier.php
// et modifier le chemin vers ces fichiers
// d'autres param�tres propres � chaque calendrier peuvent etre s�lectionn�s au d�but des fichiers
// calendrier.php, ils permettent de conditionner l'affichage de s�lecteur d'ann�e, mois, couleur
// champs de r�servation
//***************************************************************************************************

 //***************************************************************************************************
//pour personnaliser facilement votre calendrier, rendez vous sur cette page :
//http://www.mathieuweb.fr/calendrier/personnaliser-calendrier.php
//***************************************************************************************************

//d�claration des variables initiales du tableau*****************************************************
$taille_police_mois          = 16;
$couleur_police_mois         = '#FFFFFF';
$taille_police_nom_jour      = 12 ;
$couleur_police_nom_jour     = '#666666';
$taille_police_jour          = 12 ;
$couleur_police_jour         = '#282828';
$police                      = 'Arial';
$nombre_mois_afficher        = 6;
$nombre_mois_afficher_ligne  = 12;
$avec_marquage_du_jour_d_aujourd_hui = true;
$couleur_jour_aujourd_hui    = '#0D96FF';
$decalage_ligne              = 0 ;
$bordure_du_tableau          = 1  ;
// jouer sur ce param�tre pour uniformiser la taille des calendriers
$hauteur_mini_cellule_date   = "17px";
$couleur_bordure_tableau     = "#000000" ;
$largeur_tableau             = "100px";
$espace_entre_cellule        = "1";
$espace_dans_cellule         = "1";
$couleur_nom_numero_semaine  = '#EFF5FC';
$couleur_numero_semaine      = '#FFFFFF';
$couleur_jour_semaine        = '#E6EFFB';
$couleur_nom_jour_week_end   = '#FFFFFF';
$couleur_jour_week_end       = '#DAE9F8';
$couleur_fond_mois           = '#ABCDEF';
$largeur_sel_mois_annee      = 60 ;
$taille_police_sel_mois_annee= 16 ;
$couleur_sel_mois_annee      = '#000000';
// si true alors les cellules "vides" des week end et numero semaines seront dans leur couleur respectif
// si false alors les cellules "vides" des week end et numero semaines seront dans la couleur $couleur_libre
$avec_continuite_couleur      = true;
// indiquer en toute lettre le nom du premier jour de la semaine *********************
// lundi, mardi, mercredi, jeudi, vendredi, samedi, dimanche *************************
//attention !!! le num�ro de la semaine indiqu�e sera toujours le num�ro de semaine commencant le lundi
$texte_jour_debut_semaine = "lundi";
// couleur libre est �galement la couleur de fond des dates du calendrier
$couleur_libre               = '#B9CBDD';
// pour pouvoir afficher plusieurs couleurs , il faut cr�er autant de variables $couleur_reserve[] que n�cessaire, en modifiant l'index
$couleur_reserve[1]          = '#FF0000';
$intitule_couleur_reserve[1] = "R�serv�";
$couleur_texte_jour_reserve[1]= '#FFFFFF';

//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
// s�lection de l'affichage des modules ************************************************************
//avec selection possible du mois--------------------------------------------------------------------
$selection_mois    = true ;
//avec selection possible des annn�es----------------------------------------------------------------
$selection_an      = true ;

//format de date sur le lien des jours dans le calendrier--------------------------------------------
// si true alors selection format francais, si false alors format date anglais-----------------------
$format_date_fr    = false ;

//d�claration des noms des mois et jours en francais************************************************
$mois_fr           = Array ( "", "Janvier", "F�vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "D�cembre" );
$jour_fr           = Array ( "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa", "Di", "S" );
//d�claration des noms des mois et jours en allemand************************************************
$mois_all          = Array ( "", "Januar", "Februar", "M�rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" );
$jour_all          = Array ( "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So", "W" );
//d�claration des noms des mois et jours en anglais*************************************************
$mois_eng          = Array ( "", "Jaunary", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
$jour_eng          = Array ( "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su", "W" );
//d�claration des noms des mois et jours en italien*************************************************
$mois_it           = Array ( "", "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" );
$jour_it           = Array ( "Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa", "Do", "S" );
//d�claration des noms des mois et jours en espagnol*************************************************
$mois_esp           = Array ( "", "Enero", "FebreroO", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" );
$jour_esp           = Array ( "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do", "S" );

//langue par d�faut*********************************************************************************
if ( !(isset($_SESSION['langue'])) || ((empty($_SESSION['langue']))) )
$langue = 'fr' ;
//controle si choix de la langue dans l'url*********************************************************
if ( (isset($_GET['langue'])) && (!(empty($_GET['langue']))) )
    $_SESSION['langue'] = $_GET['langue'] ;
//si session langue existe alors la langue de la session devient prioritaire************************
if ( (isset($_SESSION['langue'])) && (!(empty($_SESSION['langue']))) )
   $langue = $_SESSION['langue'];
//s�lection des tableaux suivant la langue choisie**************************************************
if ( $langue == 'fr' ) {
     $mois_texte = $mois_fr ;
     $jour_texte = $jour_fr ; }
if ( $langue == 'all' ) {
     $mois_texte = $mois_all ;
     $jour_texte = $jour_all ; }
if ( $langue == 'eng' ) {
     $mois_texte = $mois_eng ;
     $jour_texte = $jour_eng ; }
if ( $langue == 'it' ) {
     $mois_texte = $mois_it  ;
     $jour_texte = $jour_it ; }
if ( $langue == 'esp' ) {
     $mois_texte = $mois_esp  ;
     $jour_texte = $jour_esp ; }


//choix du mois*************************************************************************************
$selection_mois_depart = 0;
$offset_annee          = 0;
$premier_mois       = date ("m") + $selection_mois_depart;
if ($premier_mois >12) {
    $premier_mois = 1;
    $offset_annee = 1; }
if ($premier_mois < 1) {
    $premier_mois = 12; 
    $offset_annee = -1; }

//controle si choix du mois dans l'url**************************************************************
if ( (isset($_GET['mois'])) && (empty($_GET['mois'])) )
    $_SESSION['mois'] = '' ;
if ( (isset($_GET['mois'])) && (!(empty($_GET['mois']))) )  {
    $_SESSION['mois'] = (int)$_GET['mois'] ;
    //fixe les limites de valeur ***
    if ( $_SESSION['mois'] < 1 )
         $_SESSION['mois'] = 1 ;
    else if ( $_SESSION['mois'] >12 )
         $_SESSION['mois'] = 12 ;
    }
//si session mois existe alors la session devient prioritaire***************************************
if ( (isset($_SESSION['mois'])) && (!(empty($_SESSION['mois']))) )
   $premier_mois = $_SESSION['mois'] ;

//choix de l'ann�e**********************************************************************************
$annee_premier_mois       = date ("Y") + $offset_annee ;
//controle si choix de l'ann�e dans l'url***********************************************************
if ( (isset($_GET['an'])) && (empty($_GET['an'])) )
    $_SESSION['an'] = '' ;
if ( (isset($_GET['an'])) && (!(empty($_GET['an']))) )  {
    $_SESSION['an'] = (int)$_GET['an'] ;
    //fixe les limites de valeur ***
    if ( $_SESSION['an'] < 1980 )
         $_SESSION['an'] = 1980 ;
    else if ( $_SESSION['an'] > 2030)
         $_SESSION['an'] = 2030 ;
    }
//si session ann�e existe alors la session devient prioritaire**************************************
if ( (isset($_SESSION['an'])) && (!(empty($_SESSION['an']))) )
   $annee_premier_mois = $_SESSION['an'] ;




//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//----     Ne plus rein modifi�                                                                -----
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------



$largeur_div = $largeur_tableau * $nombre_mois_afficher_ligne ;



//selection du mois et ann�e en cours***************************************************************
$mois_en_cours  = (int)$premier_mois ;
$annee_en_cours = $annee_premier_mois ;


// affichage s�lection mois, ann�e, couleur et champs de r�servations ********************************
if ($AffichChoixDate)
{

	echo '<Table border = 0 >
		  <tr>
		  <td width = "140">';
	   // si n�cessaire affichage du s�lecteur d'ann�e **************************************************
	   if ( $selection_an ) {
			echo '<a href="affichTous.php?an=',$annee_en_cours - 1, '" class = selection><font style="font-size:',$taille_police_sel_mois_annee,'px" color="',$couleur_sel_mois_annee,'" face="',$police,'" >&nbsp;<< </a></font>';
			echo '<b><font style="font-size:',$taille_police_sel_mois_annee,'px" color="',$couleur_sel_mois_annee,'" face="',$police,'" >&nbsp;',$annee_en_cours,'&nbsp;</font></b>';
			echo '<a href="affichTous.php?an=',$annee_en_cours + 1, '" class = selection><font style="font-size:',$taille_police_sel_mois_annee,'px" color="',$couleur_sel_mois_annee,'" face="',$police,'" >&nbsp;>> </a></font>';
			}
	echo '</td>
		  </tr>
		  <tr>
		  <td width = "140">';
	   // si n�cessaire affichage du s�lecteur de mois **********************************************
	   if ( $selection_mois ) {
			echo '<form name="sel_mois" method="get" action="affichTous.php" id="Form1">';
			echo '<select name="mois" size="1" id="Combobox1" onchange="document.sel_mois.submit();return false;" style="position:font-family:',$police,';font-size:',$taille_police_sel_mois_annee,'px;z-index:2">';
			for ($i=1; $i<13; $i++)  {
				if  ( $premier_mois == $i )
					  echo '<option selected value="',$i,'">',$mois_texte[$i],'</option>' ;
				 else
					  echo '<option value="',$i,'">',$mois_texte[$i],'</option>' ;
			}
			echo '</select>';
			echo '</form>';
			}
	echo '</td>
		  </tr>
		  </table> ';
// s�lection affichage avec lien vers page de gestion des locataires logements *********************
}
echo '<div id="Calendrier" style="">';
/* Affichage des infos de base du Gite
*/
$req="SELECT idgite,nom,capacite,url,montant_caution,titre,description FROM GITE WHERE idgite=".$idgite;
					
					$result = $mysqli->query($req);
					
							

					while ($row = $result->fetch_assoc())
					{
						$nom=$row['nom'];
						$capacite=$row['capacite'];
						$montant_caution=$row['montant_caution'];
						$titre=$row['titre'];
						//$surface=$row['surface'];
					}
					
				
//initailisation compteur de mois par ligne*********************************************************
$compteur_mois_ligne = 1 ;

echo '<table >';
echo '<tr>';
echo '<td>';
echo '<div style="width:',$largeur_div,'px;">';

 echo "<p>". $nom . " : ".$titre." Capacit� : ".$capacite." personnes</p>";
//affichage des tableaux des mois desir�s***********************************************************
for ( $compteur_mois = 1; $compteur_mois <= $nombre_mois_afficher; $compteur_mois++ )
 {

 $compteur_mois_ligne = $compteur_mois_ligne + 1 ;

//creation du tableau des mois**********************************************************************
echo '<table cellPadding="',$espace_entre_cellule,'" cellSpacing="',$espace_dans_cellule,'" style = "width:',$largeur_tableau,'px;border :',$couleur_bordure_tableau,' ',$bordure_du_tableau,'px solid " align="left">';
//affichage du mois*********************************************************************************
echo '<TR><TD align=center bgColor=',$couleur_fond_mois,' colspan = 8><b><font style="font-size:',$taille_police_mois,'px" color="',$couleur_police_mois,'" face="',$police,'" >',$mois_texte[$mois_en_cours],' ',$annee_en_cours,'</b></font></TD></TR>';

//affichage nom des jours et num�ro de semaine******************************************************
echo '<TR>';
//temporaire pour initailisation variable globales
for ($j=1; $j<9; $j++)
     $tempor = $jour_texte[correction_debut_semaine ($texte_jour_debut_semaine,$j)];
for ($j=1; $j<9; $j++)
     {
       if  ($j == $index_jour_samedi || $j == $index_jour_dimanche)
          $couleur_fond_nom_jour = $couleur_nom_jour_week_end;
       elseif ( $j == 8)
          $couleur_fond_nom_jour = $couleur_nom_numero_semaine;
        else
          $couleur_fond_nom_jour = $couleur_jour_semaine ;
       echo '<TD align = center bgColor=',$couleur_fond_nom_jour,'><font style="font-size:',$taille_police_nom_jour,'px" color="',$couleur_police_nom_jour,'" face="',$police,'" >',$jour_texte[correction_debut_semaine ($texte_jour_debut_semaine,$j)],'</font></td>';
     }
echo '</TR>';

//initialisation des calendriers*******************************************************************
$fin_tableau              = false ;
$premier_jour_depasse     = false ;
$numero_premier_jour_mois = jour_debut_semaine ($texte_jour_debut_semaine,$mois_en_cours ,$annee_en_cours) ;
$temp_annee_mois_suivant  = $annee_en_cours ;
$temp_mois_suivant        = $mois_en_cours + 1 ;
if ( $temp_mois_suivant > 12 )  {
    $temp_mois_suivant = 1;
    $temp_annee_mois_suivant++;
    }
$numero_dernier_jour_mois = strftime("%d",mktime ( 0,0,0,$temp_mois_suivant ,0,$temp_annee_mois_suivant)) ;
$compteur_jour            = 1 ;
//variable pour uniformiser la taille des tableau mois en nombre de ligne pour tous les mois *******
$compteur_ligne           = 0 ;
$lundi_trouve = false;

//creation du tableau avec numero des jours*********************************************************
while ( !($fin_tableau) )
      {
        echo '<TR>';
        $compteur_ligne++;
        $au_moins_une_date_sur_la_ligne = false;
        //creation des cases par semaine************************************************************
        for ($j=1; $j<9; $j++)
             {
              $couleur_disponibilite = $couleur_libre ;
              //Test pour debut tableau pour premier jour du mois***********************************
              if ( $numero_premier_jour_mois == $j  )
                  $premier_jour_depasse = true ;
              if ( $premier_jour_depasse && ($compteur_jour <= $numero_dernier_jour_mois) && $j < 8)
                  {
                    if ( $j == $index_jour_samedi || $j == $index_jour_dimanche)
                        $couleur_disponibilite = $couleur_jour_week_end ;
                    // test si le jour affich� correspond au jour d'aujourd'hui *******************
                    if ( $avec_marquage_du_jour_d_aujourd_hui ) {
                        $date_aujourd_hui = date("Y")."-".(int)date("m")."-".(int)date("d");
                        $jour_aujourd_hui = $annee_en_cours."-".$mois_en_cours."-".$compteur_jour;
                        if ( $date_aujourd_hui ==  $jour_aujourd_hui )
                            $couleur_disponibilite = $couleur_jour_aujourd_hui ;
                        }
                    //test si jour est reserv�******************************************************
                    $coul_police_jour = $couleur_police_jour ;
                    $class_date_lien = '' ;
					/****************************/	
					/*							*/
					/* Recherche de r�servation */
					/*        et affichage      */
					/****************************/	
					$dateAtester =strtotime($annee_en_cours.'-'.$mois_en_cours.'-'.$compteur_jour);
					$dateAtester =date($dateAtester);
				
					$choixSaison= typeSaison ($dateAtester);
					$retRes=estReserve($dateAtester,$idgite);
						//echo $compteur_jour.estReserve($dateAtester,$idgite).$choixSaison;
					if ($retRes[0]=='-R-' and $idgite !=1)
					{
						
							echo '<TD bgColor=#FF0000 align=center><font style="font-size:',$taille_police_jour,'px" color="#000000" face="',$police,'" >';
					}
                    else
					{
						if ($idgite==1)
						{
							$giteATester=2;
							$reserve=false;
							while ($giteATester<=8 and $reserve==false)
							 {
								$retRes=estReserve($dateAtester,$giteATester);
								if($retRes[0]=='-R-') $reserve=true;
								$giteATester++;
							 }
							 if ($reserve==true)
								{
									echo '<TD bgColor=#FF0000 align=center><font style="font-size:',$taille_police_jour,'px" color="#000000" face="',$police,'" >';
								}
							else
								{
									echo '<TD bgColor=',$couleur_disponibilite,' align=center><font style="font-size:',$taille_police_jour,'px" color="',$coul_police_jour,'" face="',$police,'" >';
								
								}
							
						}
						else
						{
							echo '<TD bgColor=',$couleur_disponibilite,' align=center><font style="font-size:',$taille_police_jour,'px" color="',$coul_police_jour,'" face="',$police,'" >';
						}
					}
                    //memoire date du lundi de la semaine en cours ****************************************
                    //recherche de la date du lundi de la semaine
                    if ( $j == $index_jour_lundi )  {
                        $memoire_numero_premier_jour_sem_en_cours =  $compteur_jour;
                        $memoire_numero_mois_premier_jour_sem_en_cours =  $mois_en_cours;
                        $memoire_numero_annee_premier_jour_sem_en_cours =  $annee_en_cours;
                        $lundi_trouve = true;
                        }
					if (!empty($retRes[1]))
					{
						echo '<a href="affichResa.php?idresa='.$retRes[1].'">'.$compteur_jour.'</a></TD>';
					}
					else 
					{
						echo $compteur_jour.'</TD>';
					}
		
                    $compteur_jour++ ;
                    $au_moins_une_date_sur_la_ligne = true ;
                  }
              elseif  ( $j == 8  && $au_moins_une_date_sur_la_ligne)  {
                    //indique num�ro de semaine*************************************************************************************************
                    if ( !$lundi_trouve && $compteur_ligne == 1) {  // si aucun lundi dans premier ligne, calcul num�ro semaine sur dernier lundi du mois pr�c�dent****
                    $temp_mois_precedent = $mois_en_cours -1 ;
                    $temp_annee_precedent = $annee_en_cours ;
                    if  ( $temp_mois_precedent <= 0 ) {
                      $temp_mois_precedent = 12;
                      $temp_annee_precedent = $annee_en_cours - 1 ;
                      }
                    $numero_dernier_jour_calcul_semaine = strftime("%d",mktime ( 0,0,0,$mois_en_cours,0,$temp_annee_precedent)) ;
                    $premiere_boucle_recherche_lundi = true;
                    while ( !$lundi_trouve) {
                        if ( !$premiere_boucle_recherche_lundi )
                           $numero_dernier_jour_calcul_semaine = $numero_dernier_jour_calcul_semaine - 1 ;
                        $premiere_boucle_recherche_lundi = false;
                        $nom_jour_temp_calcul_semaine = strftime("%a",mktime ( 0,0,0,$temp_mois_precedent,$numero_dernier_jour_calcul_semaine,$temp_annee_precedent)) ;
                        if ( $nom_jour_temp_calcul_semaine == "Mon" ) {
                        $memoire_numero_premier_jour_sem_en_cours =  $numero_dernier_jour_calcul_semaine;
                        $memoire_numero_mois_premier_jour_sem_en_cours =  $temp_mois_precedent;
                        $memoire_numero_annee_premier_jour_sem_en_cours =  $temp_annee_precedent;
                        $lundi_trouve = true;
                           }
                        }
                      }
                    $temp_semaine_en_cours = date("W",mktime ( 0,0,0,$memoire_numero_mois_premier_jour_sem_en_cours ,$memoire_numero_premier_jour_sem_en_cours ,$memoire_numero_annee_premier_jour_sem_en_cours ));
                    echo '<TD bgColor=',$couleur_numero_semaine,' align=center><font style="font-size:',$taille_police_jour,'px" color="',$couleur_police_jour,'" face="',$police,'" >';
                    $lundi_trouve = false;
                    echo $temp_semaine_en_cours;
                    echo '</td>';
                    }
              else  {
                     if ( ( $j == $index_jour_samedi || $j == $index_jour_dimanche)  && $avec_continuite_couleur )
                        $couleur_disponibilite = $couleur_jour_week_end ;
                    if ( $j == 8 && $avec_continuite_couleur )
                        $couleur_disponibilite = $couleur_numero_semaine ;
                    echo '<TD bgColor=',$couleur_disponibilite,' height="',$hauteur_mini_cellule_date,'"></TD>';
                    }
             }
        echo '</TR>';
        if ( $compteur_jour > $numero_dernier_jour_mois && $compteur_ligne >= 6)
                        $fin_tableau = true ;
      }
//fin de la table du mois
echo '</TABLE>';

//incrementation du mois et annee en cours********************************************************
$mois_en_cours = $mois_en_cours + 1;
if ( $mois_en_cours > 12 )
    {
     $mois_en_cours = 1;
     $annee_en_cours = $annee_en_cours + 1 ;
    }
 if ( $compteur_mois_ligne > $nombre_mois_afficher_ligne )
    {
     echo '</tr></td><tr><td>';
     $compteur_mois_ligne = 1;
    }
 }
//fin de paragraphe du tableau*********************************************************************
echo '</div>';
echo '</td>';
echo '</tr>';
echo '</table>';
?>

		<form action="resaVerifBefore.php?idgite=<?php echo $idgite;?>" method="post"> 
			
			<fieldset style="position:relative;float:left;height:25px;">
				
				<label for=nom>Date Arriv�e</label>	<input type="date" name="date_debut">
				
				<label for=nom>Date D�part</label><input type="date" name="date_fin">
				<input type="int" name="capacite" hidden>
			</fieldset style="position:relative;float:left;">
			
			<fieldset style="height:25px;">
				<button type=submit>Reserver !</button> <!-- test dans le header et redirection-->
		  	</fieldset>
		</form>