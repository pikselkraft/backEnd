<?php

require('includes/header.php'); 

/****************************/
/********MULTI RESA********
/***************************/
if($_GET['add']==1)
{
	$_SESSION['test'] = true;
	$_SESSION['resaEncours'] = count($_SESSION['Mesresa']);
	$resaEncours = $_SESSION['resaEncours'];
	$monTab = $_SESSION['Mesresa'];
	testVar($monTab,"monTab","monTab");
	$resaPrecedente = $resaEncours	- 1 ;
	testVar2($resaEncours,"resaencours","resaencours");
	testVar2($resaPrecedente,"resaPrece","resaPrece" );
	testVar2($monTab[$resaPrecedente],"monTab de resaPrece","monTab de resaPrece");
}
else 
{
	$_SESSION['resaEncours'] = 0;
	$resaEncours = $_SESSION['resaEncours'];
	$_SESSION['test'] = false;
	//	testVar($_SESSION['test']);
}

/****************************************/
/* V&eacute;rification multi resa du nombre d'enfants (multi resa)
/***************************************/
if((isset($_GET['add'])) or (isset($monTab[$resaPrecedente]['nb_adulte'])) or (isset($monTab[$resaPrecedente]['nb_enfant'])) or (isset($_POST['nb_adulte'])) )
{
		$nb_adulte 	= $monTab[$resaPrecedente]['nb_adulte'];
		$nb_enfant	= $monTab[$resaPrecedente]['nb_enfant']; // de moins de 13 ans
		$nb_enfantTotal	= $monTab[$resaPrecedente]['nb_enfantTotal'];

	if ( isset($_POST['nb_adulte']) or isset($_POST['nb_enfant']) ) {

		$nb_adulte 	= $_POST['nb_adulte'];
		$nb_enfant	= $_POST['nb_enfant']; // de moins de 13 ans
		$nb_enfantTotal	= $_POST['nb_enfantTotal'];
	}
	testVar($nb_adulte);
	$monTab[$resaPrecedente]['nb_adulte'] = $nb_adulte ;
	$monTab[$resaPrecedente]['nb_enfant'] = $nb_enfant; // de moins de 13 ans
	$monTab[$resaPrecedente]['nb_enfantTotal'] = $nb_enfantTotal;

	$cap = $_SESSION['gite_tab']['capacite']; // capacit&eacute; du g&icirc;te

	testVar($cap);


	$veriefNbPersonnes = $nb_adulte + $nb_enfant + $nb_enfantTotal;

	testVar($veriefNbPersonnes);
	
	if((!verifCapacite($nb_adulte,$nb_enfantTotal,$cap)) or ($nb_enfant>$nb_enfantTotal) or ($veriefNbPersonnes==0))
	{
	?>
		<div class='msg-error'><p> Erreur dans la saisi du nombre d'adulte(s) et d'enfant(s)</p></div>
			<div class="verief-nombre-form">
				<div class="fiche-contact">	
					<form action="<?php $_SERVER['SELF']; ?>" method="POST">
								<label for=nb_adulte>Nombre d'adultes :</label>
								<input id=nb_adulte name=nb_adulte type=number min=0 max="<?php echo $cap; ?>" required>
							
								<label for=nb_enfantTotal>Nombre d'enfants:</label>
								<input id=nb_enfantTotal name=nb_enfantTotal type=number min=0 max="<?php echo $cap; ?>" required>
				
								<label for=nb_enfant>Dont nombre d'enfants de plus 13 ans :</label>
								<input id=nb_enfant name=nb_enfant type=number  min=0 max="<?php echo $cap; ?>" required>
								<input type="submit" id="reservation" name="reservation" value="Changer vos informations" >
					</form>
				</div>
			</div>	
		<?php		
	} 
	else 
	{
		$monTab[$resaPrecedente]['nb_adulte'] = $nb_adulte;
		$monTab[$resaPrecedente]['nb_enfant'] = $nb_enfant;
		$_SESSION['Mesresa'] = $monTab;	
		//testVar($monTab);
		

		if (!empty($_GET['idgite'])) {

			$idgite=$_GET['idgite'];

			$monTab[$resaEncours]['idgite'] = $idgite;
			$monTab[$resaEncours]['date_debut'] = $monTab[$resaPrecedente]['date_debut'];
			$monTab[$resaEncours]['date_fin'] = $monTab[$resaPrecedente]['date_fin'];

			$_SESSION['Mesresa'] = $monTab;

			testVar($monTab);
			testVar($_SESSION['Mesresa']);

			header('Location:reservation_action.php?idgite='.$_GET['idgite'].'&add=1');
   			ob_end_flush(); // envoie des donn&eacute;es du flux
   			exit(); /* empeche insertion resa si multi reservation*/
		}
	}	
}

require('includes/footer.php');

 ?>