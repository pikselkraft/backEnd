<?php
	require('includes/header.php');
	

//formulaire de r�servation g�te le Metzval
//
//Page tunnel de vente formulaire
//
//creation: 06/11/2013/

?>

<body>
<?php 

// r�cup�ration POST formulaire.php
$_SESSION['resa']['login'] = strtolower(htmlspecialchars($_POST['login'])); // s�curit� : injection et majuscule
//$_SESSION['resa']['cheminot'] = $_POST['cheminot'];


// stockage dans des variables de POST du formulaire.php
$login 		= $_SESSION['resa']['login'];
//$cheminot 	= $_SESSION['resa']['cheminot'];
//echo $cheminot;
$date_debut = $_SESSION['resa']['date_debut'];
$date_fin 	= $_SESSION['resa']['date_fin'];
$idgite = $_GET['idgite'];
$_SESSION['resa']['idgite']=$idgite;
$tarif 		= $_SESSION['resa']['tarif'];

//echo $date_fin;
//echo date("l",strtotime($date_fin));

$nb_resa= count($_SESSION['Mesresa'])-1;

//	echo $nb_resa;
	echo "test avant if de la deuxi�me RESA ".$nb_resa;
	if($_SESSION['test'])
	{
		$nb_resa++;
		echo 'test nb resa '.$nb_resa;
		$_SESSION['test']=false;
	} else
	{
		$nb_resa = 0;	
	}

$_SESSION['Mesresa'][$nb_resa] = $_SESSION['resa']; // stockage commande 1

//print_r ($_SESSION['Mesresa'][$nb_resa]);
//var_dump ($_SESSION['Mesresa'][$nb_resa]);


if($etat==2)
{
			if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['naissance']) ) //verief presence argument -> lance la boucle, les arguments sont obligatoires et seront verif en js
			{ 
   				// on affiche nos r�sultats test
				echo 'Votre nom est '.$_POST['nom'].' et votre prenom est '.$_POST['prenom'];
				
				// recuperation des donn�es
				$mail= strtolower(htmlspecialchars($_POST['login']));
				$pass =$_POST['password'];
				$nom= htmlspecialchars($_POST['nom']);
				$prenom= htmlspecialchars($_POST['prenom']);
				$naissance= htmlspecialchars($_POST['naissance']);
				$cheminot= htmlspecialchars($_POST['cheminot']);
				$entreprise= htmlspecialchars($_POST['entreprise']);
				$adresse= htmlspecialchars($_POST['adresse']);
				$codepostal= htmlspecialchars($_POST['codepostal']);
				$ville= htmlspecialchars($_POST['ville']);
				$pays= htmlspecialchars($_POST['pays']);
				$tel= htmlspecialchars($_POST['tel']);
				$port= htmlspecialchars($_POST['port']);
				$datecrea=date("Y-m-d H:i:s"); 
				$news= htmlspecialchars($_POST['news']);
				$cheminotRegion = htmlspecialchars($_POST['nom']);   /*� definir*********************/
				$cheminotCode = htmlspecialchars($_POST['nom']); /*� definir********************/
				
				
				//insertion dans la db
		  		$sql = "INSERT INTO CLIENTS (email,mp,nom,prenom,date_naissance,cheminot,code_cheminot,region,entreprise,adresse,codepostal,ville,pays,tel,port,creation,newsletter) VALUES ('".$mail."','".$pass."','".$nom."','".$prenom."','".$naissance."','".$cheminot."','".$cheminotRegion."','".$cheminotCode."','".$entreprise."','".$adresse."','".$codepostal."','".$ville."','".$pays."','".$tel."','".$port."','".$datecrea."','".$news."')";
		  		$mysqli->query($sql);
		 		$enreTest=false;
		  		//si la requ�te s'est bien pass�e, on affiche un message de succ�s
				  if($mysqli)
				  {
					echo "L'inscription s'est bien d�roul�e" ;   // redirection vers page r�capitulative de la r�servation et permet la connexion
				  } 
				  else 
				  {
					  echo '<br/> Merci de vous enregistrer � nouveau';
				  }
			}
			else
			{
				echo '<br/> Veuillez remplir le formulaire, merci<br/>';  	// si erreur // � enlever apr�s dev
		 	}
}
		
		
		$LoginOk=false;
		$Clef = "Matteo1234567890";
		if($_POST["login"] != "" && $_POST["password"] != "" && empty($_POST['nom']))
		{
				 	$login     	= strtolower(htmlspecialchars($_POST['login']));
  					$pass2      = Cryptage($_POST["password"], $Clef) ;
					$pass2		= utf8_encode($pass2);
			
			
					$sql = "SELECT nom,prenom,mp,email FROM CLIENTS WHERE email = '".$login."'";
	
					  $result = $mysqli->query($sql);
					  
					  //on recup?re le resultat
						while ($row = $result->fetch_Assoc()) 
						{
				
							if  (utf8_decode($row['MP'])==$pass2)
								{
									echo "<p>mot de passe OK, formulaire de r�servation</p>";
									$LoginOk=true;
								
							}
							else 
							{		
								echo "<p>mot de passe KO Merci de recommencer</p>"; 
								echo $login;
								echo $pass2;
								// ****************redirection � faire***************
							}
								
						} // fin while
//			  if(is_object($result) and $LoginOk) // inutile?????
//			  {
//				//d�but de la session
//				echo "test condition stockage objet";
////				$_SESSION["login"] = $login ;
////				$_SESSION['connect']=true;
//			  }//fin if
//			  //sinon on retourne ? la page d'inscription
//			  else
//			  {
//				echo'<br/> test else 1 <br/>';
//			  }//fin else
		} // fin if		
	
?>
		

<h2>R�capitulatif de votre R�servation</h2>
	<div class="row">
		<div class="small-11 small-centered columns">
		<ul> <!-- r�cup�ration des �l�ments de session, stock�s en d�but de page -->
			<li><?php echo "Vous avez s�lectionn� le ".$idgite; ?></li>
			<li><?php echo "Date de d�but le ".$date_debut; ?></li>
			<li><?php echo "Date de fin le ".$date_fin; ?></li>
			<li><?php echo "Pour un tarif maximum de ".$tarif; ?></li>
		</ul>
		</div>
	</div>
		
	<div class="row">
		<div class="small-11 small-centered columns">
			<form action="payement.php" method="post">
				<fieldset>
					<legend>Vos Dates</legend>

						<input type="hidden" name="login" <?php echo 'value="'.$login.'"'; ?> required>

						<li>
							<label for=nom>Date Arriv�e :</label>
							<input type="date" name="date_debut" <?php echo 'value="'.$date_debut.'"'; ?> required>
						</li>
						<li>
							<label for=nom>Date D�part :</label>
							<input type="date" name="date_fin" <?php echo 'value="'.$date_fin.'"'; ?> required>
						</li>
						<li>
							<label for=nb_adulte>Nombre d'adultes :</label>
							<input id=nb_adulte name=nb_adulte type=number required>
						</li>
						<li>
							<label for=nb_enfant>Nombre d'enfants :</label>
							<input id=nb_enfant name=nb_enfant type=number required>
						</li>


						<?php 	//  **************** gestion cheminot ou non et tarif *************************
								//	*************************************************************************
							$statutCheminot='EX';
							$reqCheminot = "SELECT c.cheminot,c.code_cheminot,c.region FROM CLIENTS c WHERE email='".$login."'"; // requete boolean cheminot
							echo $reqCheminot;
							$resultCheminot = $mysqli->query($reqCheminot);
							print_r($resultCheminot);
							while ($ressqlCheminot = $resultCheminot->fetch_assoc()) // parcours tableau r�cup�ratio statut, region
							{
								$testCheminot=$ressqlCheminot['cheminot'];
								$regionCheminot=$ressqlCheminot['region']; //***************** PB VARIABLE NULL///////////
																			//********************************////////////
																			// test processus complet id�e1 ///
																			// verief tab id�e2///
								var_dump($testCheminot);
								var_dump($regionCheminot);
							}
							/* !!!!!!!!!!! modification donn�es base si probl�me verief correspondance nom des champs en premier !!!!!!!!!!!!!*/

							if($testCheminot) // si statut cheminot = 1
							{
								if($regionCheminot) // si statut codecheminot = 1 alors t.statut_client ='".$statutCheminot'" -> cheminot_region (calculTarif)
								{
									$statutCheminot='CR';
									echo " test <br>";
									echo 'CHOUCROUTE <br>';

								}
								else // sinon statut codecheminot = 0 alors t.statut_client ='".$statutCheminot'" -> cheminot_externe (calculTarif)
								{
									$statutCheminot='CE';
									echo 'PAIN <br>';
								}

							} // fin if
							else // sinon statut cheminot = 0 alors t.statut_client ='".$statutCheminot'" -> externe (calculTarif)
							{
									$statutCheminot='EX';
									echo 'CIVIL <br>';
									$tarif = $_SESSION['resa']['tarif'];
							} // fin else
							echo $statutCheminot;
							$tarif = calculTarif ($date_debut,$date_fin,$idgite,$statutCheminot); 
							echo "<br> Le tarif apr�s v�rification ".$tarif; 
						?>

					<legend>Payement</legend>
						<p>Tunnel de payement</p>
						<li>
							  <label for=payement>Votre moyen de payement :</label>
								<input type="radio" name="payement" value="1" /><label for="payementcb">Carte bancaire</label>
								<input type="radio" name="payement" value="0" /><label for="payementpaypal">Ch�que</label>
						</li>

						<li>Vous pouvez ajouter un autre g�te sur la page de r�servation suivante</li>

					<button type=submit name="reservation">R�server !</button> <!-- test mode payement sur la page payement.php en fonction du POST -->

				</fieldset>
			</form>
		</div>
	</div>

</body>

<?php
	require('includes/footer.php');
?>		