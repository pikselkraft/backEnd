<?php

require_once 'includes/header.php';

$requCliens = "SELECT nom, prenom, email FROM CLIENTS WHERE email!='aucun'";
$result_requCliens=$mysqli->query($requCliens);
$selectClient = '';
while ($row = $result_requCliens->fetch_assoc()){
	if ( isset($_POST["email"]) ){
		if ( $_POST["email"] ==  $row["email"] )
			$clientSelected = ucfirst($row["prenom"]).' '.strtoupper($row["nom"]);
	}
	$selectClient .= '<option value="'.$row["email"].'">'.ucfirst($row["prenom"]).' '.strtoupper($row["nom"]).'</option>';
}
$selectClient .= '</select></form>';
if ( isset($clientSelected) )
	$selectClient = '<form method="POST" action="mailing.php"><select id="selectClients" name="email"><option>'.$clientSelected.'</option>'.$selectClient;
else
	$selectClient = '<form method="POST" action="mailing.php"><select id="selectClients" name="email"><option></option>'.$selectClient;

if (isset($_POST["email"]) ) {
	$email = $_POST["email"];
	$reqStatutCommande = "SELECT idstatut, designation FROM STATUTCOMMANDE";
	$result_reqStatutCommande=$mysqli->query($reqStatutCommande);
	while ($row = $result_reqStatutCommande->fetch_assoc())
		$statut[(int)$row["idstatut"]]["designation"]=$row["designation"];		
	$reqCommandeResa="SELECT distinct CO.idcommande,  CM.idclient, C.nom, C.prenom, C.email, C.civilite, CO.taxe, CO.caution, 
	CO.caution_paye, CO.montant_option, CO.remise, CO.code_promo, CO.date_creation, CO.statut_facture, CO.accompte, 
	CO.accompte_paye, CO.total, CO.total_paye, G.nom as nom_gite, G.idgite, CO.remise_taux
	FROM COMMANDE CO, COMMANDERESERVER CM, CLIENTS C, RESERVATION R, GITE G
	WHERE CM.idclient=C.idclient AND CM.idreservation=R.idreservation AND CO.idcommande=CM.idcommande 
	AND G.idgite=R.idgite AND C.email LIKE '".$email."' ORDER BY CO.statut_facture";
	$result_reqCommandeResa=$mysqli->query($reqCommandeResa); /* execution req recherche commande*/
	/**
	* Dev si requete est nul -> message
	*/
	$affichage_commande_ligne.='<table><thead>
		<th width="100px">Num&eacute;ro de la Commande</th>
		<th width="50px">Nom et num&eacute;ro du g&icirc;te</th>
		<th width="50px">Periode de r&eacute;servation</th>
		<th width="50px">Date de Commande</th>
		<th width="50px">Nom</th>
		<th width="50px">Statut</th>
		<th width="50px" data-tooltip class="has-tip" title="A:attente/P:Pay&eacute;/R:Rendu">Caution</th>
		<th width="50px">Accompte</th>
		<th width="50px">Remise</th>
		<th width="50px">Total</th>
		<th width="50px">Total pay&eacute;</th>
		<th>Action</th></tr>
	</thead>';
	//Boucle qui parcourt les clients dans la base de donn�es
	while ($row = $result_reqCommandeResa->fetch_assoc()){
		switch ((int)$row["statut_facture"])
		{
			case 0 : $couleurStatut ='#000000';
			break;
			case 1 : $couleurStatut ='#d9534f';
			break;
			case 2 : $couleurStatut ='#f0ad4e';
			break;
			case 3 : $couleurStatut ='#5bc0de';
			break;
			case 4 : $couleurStatut ='#5cb85c';
			break;
		}

		$couleurCommande='style=" border:2px solid '.$couleurStatut.';"';
		if ($row["accompte_paye"] == 0)
			$accompte_paye_symbole = '<i data-tooltip class="foundicon-error has-tip" title="Acompte non pay&eacute;" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';
		else
			$accompte_paye_symbole = '<i data-tooltip class="foundicon-checkmark has-tip" title="Acompte pay&eacute;" style="font-style: normal;"> '.$row["accompte"].' &euro;</i>';

		$affichage_commande_ligne.= '<tr>
			<td '.$couleurCommande.'>'.$row["idcommande"].'</td>
			<td '.$couleurCommande.'>'.$row["nom_gite"].'('.$row["idgite"].')</td>
			<td '.$couleurCommande.'>'.dateFr($row["date_debut"]).' - '.dateFr($row["date_fin"]).'</td>
			<td '.$couleurCommande.'>'.dateFr($row["date_creation"]).'</td>
			<td '.$couleurCommande.'>'.$row["nom"]." ".$row["prenom"].'</td>
			<td '.$couleurCommande.'>'.$statut[(int)$row["statut_facture"]]["designation"].'</td>
			<td data-tooltip class="has-tip" title="A:attente/P:Pay&eacute;/R:Rendu" '.$couleurCommande.'>('.$row["caution_paye"].') '.$row["caution"].' &euro;</td>
			<td '.$couleurCommande.'>'.$accompte_paye_symbole.'</td>
			<td '.$couleurCommande.'>'.$row["remise_taux"].' %</td>
			<td '.$couleurCommande.'>'.$row["total"].' &euro;</td>
			<td '.$couleurCommande.'>'.$row["total_paye"].' &euro;</td>
			<td '.$couleurCommande.'><a onclick="apercuMail(\''.$row["email"].'\',\''.ucfirst($row["civilite"]).'. '.ucfirst($row["prenom"]).' '.ucfirst($row["nom"]).'\');" title="Envoyer un mail"><i class="foundicon-mail"></i></a></td>
			</tr>';	
	}
	$affichage_commande_ligne .= '</table>';
}
?>

<!-- Modal d'apercu du mail -->
<div id="modalApercuEmail" class="reveal-modal" data-reveal>
	<div>
		<?php
		if (isset($email)){
			require_once 'includes/ink/baseMailHTML.php';
			echo //$messageCSS.
			$messageBodyBefore.'<tr>
					<td>
						<h1 id="titreMail"></h1>
						<form>
							<input id="hiddenEmail" type="hidden" name="email" value="">
							<textarea id="contenumail" name="message">Votre texte ici.</textarea>
						</form>
						<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
						<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
					</td>
				<td class="expander"></td>
			</tr>'.$messageBodyAfter.'
			<input class="button tiny" onclick="envoi()" value="Envoyer">';
		}
		?>
	</div>
	<a class="close-reveal-modal">&#215;</a>
</div>
<!-- fin du Modal d'apercu du mail -->
<div class="row">
	<div class="small-11 small-centered columns">
			<p id="alerte"></p>
			<h2>Envoyer un mail</h2>
			<?=$selectClient?>
			
	</div>
</div>
<?php
if (isset($affichage_commande_ligne) )
	echo  '<hr/><h3> R&eacute;sultat</h3>'.$affichage_commande_ligne;
require('includes/footer.php'); 
?>
<link rel="stylesheet" href="includes/css/select2.css"  type="text/css">
<script src="includes/js/select2.js"></script>
<script>
$(window).load(function(){
	$("#selectClients").select2({ placeholder: "Selectionnez un client",width: 500 }).change(function(e) {
		e.target.parentNode.submit();
	});
});
function apercuMail(emailAdd, nomClient){
	$("#titreMail").text("Bonjour "+nomClient+",");
	$("#hiddenEmail").val(emailAdd);
	$("#modalApercuEmail").foundation('reveal', 'open');
	//$("#bouttonEnvoyer").click(this.test());
	// 	function(){
	// 	$.post("includes/ink/mailFacture.php", { email: emailAdd, message: $("#contenumail").text() } );
	// });
}
function envoi(){
	$.post("includes/ink/mailFacture.php", { email: $("#hiddenEmail").val(), message: $("#contenumail").val() } );
	$("#modalApercuEmail").foundation('reveal', 'close');

}
</script>