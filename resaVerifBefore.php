<?php
require('includes/header.php');
$resaEncours= $_SESSION['resaEncours']; 
if (empty($resaEncours)) {$resaEncours=0; $_SESSION['resaEncours']=0;   }

$monTab= $_SESSION['Mesresa'];

if (isset($_GET["idgite"]))
{
	$idgite=$_GET["idgite"];
}
else
{
$idgite=$_POST["idgite"];
}

if($mysqli) //GLOBALE MYSQL CONNEXION DB
{
	$req="SELECT idgite,nom,capacite,url,montant_caution,titre,description FROM GITE WHERE idgite=".$idgite; //recuperation information gîte
		echo $req;
	$result = $mysqli->query($req);
		
	$result->data_seek(0);
	$row = $result->fetch_assoc();
		
	$_SESSION['gite_tab'] = $row; // TABLEAU SESSION -> INFORMATION GÎTE
	$gite_tab = $row;
}
	
$monTab[$resaEncours]['idgite'] = $idgite;    /* stock les variables du post dans un tableau */
$monTab[$resaEncours]['date_debut'] = $_POST["date_debut"];
$monTab[$resaEncours]['date_fin'] = $_POST["date_fin"];
$_SESSION['Mesresa'] = $monTab;		

header('Location:formulaire.php');
?>