

<?php
	include('includes/header.php');
/***********************************************
Affiche tous les calendriers des gites du Site *
***********************************************/
	
		
	
	
	
	?>

<body>
	<div id="menu" style="position:relative; float:left;">
		<?php

		include('menu.php');
		?>
	</div>

	<div id="content"  style="position: relative; float:left;" >
		<?php
		
/****************************************************
Fonctions permettant de construire le calendrier
*****************************************************/		
		function jour_debut_semaine ($jour,$mois ,$annee) {
  $premier_jour_mois = date("w",mktime ( 0,0,0,$mois ,1,$annee)) ;
  switch ($jour) {
    case "lundi":
    if ( $premier_jour_mois == 0)
       $premier_jour_mois = 7;
    break;
    case "mardi":
     $premier_jour_mois = $premier_jour_mois + 6;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7;
    break;
    case "mercredi":
     $premier_jour_mois = $premier_jour_mois + 5;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7 ;
    break;
    case "jeudi":
     $premier_jour_mois = $premier_jour_mois + 4;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7 ;
    break;
    case "vendredi":
     $premier_jour_mois = $premier_jour_mois + 3;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7 ;
    break;
    case "samedi":
     $premier_jour_mois = $premier_jour_mois + 2;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7 ;
    break;
    case "dimanche":
     $premier_jour_mois  = $premier_jour_mois + 1;
     if ( $premier_jour_mois > 7)
     $premier_jour_mois = $premier_jour_mois - 7 ;
    break;
    }
  return ($premier_jour_mois);
  }
  /****************************************************
Fonctions permettant de construire le calendrier
*****************************************************/	

function correction_debut_semaine ($jour,$cle) {
  global $index_jour_lundi;
  global $index_jour_samedi;
  global $index_jour_dimanche;
  $nouvelle_cle = $cle ;
  switch ($jour) {
    case "lundi":
      $nouvelle_cle = $cle ;
    break;
    case "mardi":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 1;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    case "mercredi":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 2;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    case "jeudi":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 3;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    case "vendredi":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 4;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    case "samedi":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 5;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    case "dimanche":
      if ( $cle < 8)
      $nouvelle_cle = $cle + 6;
      if ( $nouvelle_cle >= 7)
         $nouvelle_cle = $nouvelle_cle - 7;
      if ( $cle > 7)
      $nouvelle_cle = $cle ;
    break;
    }
  //recherche index du lundi 
  if ( $nouvelle_cle == 1 )
      $index_jour_lundi = $cle;
    //recherche index du samedi
  if ( $nouvelle_cle == 6 )
      $index_jour_samedi = $cle;
    //recherche index du dimanche
  if ( $nouvelle_cle == 0 || $nouvelle_cle == 7)
      $index_jour_dimanche = $cle;
  return ($nouvelle_cle);
  }
  
  
/****************************************************************************
*																			*
* On regarde le nombre de gite et on appelle le calendrier pour chaque gite *
*																			*  
*****************************************************************************/
		$gt=1;
		 $AffichChoixDate=true;
		while($gt<=8)
		 {
			$idgite=$gt;
			
			include('calendrierTous.php');
			$gt++;
			$AffichChoixDate=false;
		 }
							 
		?>
	</div>

</body>

<?php
	include('includes/footer.php');
?>