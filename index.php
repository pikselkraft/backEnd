<?php
session_start();
require('/var/www/resa/dev/config.php'); 
require('includes/fonctions.php'); 
?> 
<html dir="ltr" lang="fr-FR">

<head>

	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" href="includes/css/foundation.css">
</head>

<body>

	<?php
	if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])) {
	     header('Location:affichTous.php');
	}
	elseif(!empty($_POST['username']) && !empty($_POST['password'])) {

		/* securisation input */
	    $username = mysql_real_escape_string($_POST['username']);
	    $password = md5(mysql_real_escape_string($_POST['password']));

		$query="SELECT email,mp,nom,iduser FROM USER WHERE email = '" . $username . "' AND mp = '" . $password . "'";

		if ($stmt = $mysqli->prepare($query)) {

		    /* execute query */
		    $stmt->execute();

		    /* store result */
		    $stmt->store_result();

		    if($stmt->num_rows == 1) {
			    	$stmt->close();
			    	$sqlLog = $mysqli->query($query);
			    	while ($row = $sqlLog->fetch_assoc()) {

			        $email = $row['email'];
			        $username = $row['nom'];
			        $idUser = $row['iduser'];

			        /*stockage session */
			        $_SESSION['Username'] = $username;
			        $_SESSION['EmailAddress'] = $email;
			        $_SESSION['idUser'] = $idUser;
			        $_SESSION['LoggedIn'] = 1;

			   		header('Location:affichTous.php');
		    	}
		    }
		    else
		    {
		    	echo '<div class="row">
						<div class="large-12 columns">
							<div class="panel">';

		        			echo "<h1>Erreur</h1>";
		        			echo "<p>Le compte n'a pas &eacute;t&eacute; trouv&eacute;. S'il-vous pla&icirc;t <a href=\"index.php\">cliquez-ici pour r&eacute;essayer</a>.</p>";
		        
		        echo '	</div>
					</div>
				</div>';
		    }
		}
	}
	else {
	    ?>
	     
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">

			   <h1>Zone Membre</h1>
			     
			    <form method="post" action="index.php" name="loginform" id="loginform">
			    <fieldset>
			        <label for="username">Mail:</label><input type="text" name="username" id="username" /><br />
			        <label for="password">Mot de passe:</label><input type="password" name="password" id="password" /><br />
			        <input type="submit" name="login" id="login" value="Login" class="button" />
			    </fieldset>
			    </form>

			</div>
		</div>
	</div>

	   <?php
	}
	?>
</body>
</html>