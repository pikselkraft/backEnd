<?php 
require 'includes/header.php'; 
// on recupere les noms dans la bdd au cas ou on les change
// $reqNomsGites = $mysqli->query("SELECT nom FROM GITE g WHERE idgite!=1 ORDER BY idgite");
// while ($ligne = $reqNomsGites->fetch_assoc())
// 	$nomGite[] = $ligne["nom"];
?>
</br></br></br></br></br></br>

<h1>Bonjour</h1>

<div class="row">
	<div class="small-6 large-centered columns">
		<h3>Gites</h3>
		<canvas id="canvas" height="350" width="500"></canvas>
	</div>
</div>
<div class="row">
	<div class="small-6 large-centered columns">
		<h3>Gite complet</h3>
		<canvas id="canvas1" height="350" width="500"></canvas>
	</div>
</div>

<?php
// ===================  pour tous les gites 
$donnees = array(array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),array(0,0,0,0,0,0,0,0,0,0,0,0,0,),);
$requ = "SELECT g.nom,g.idgite,MONTH( r.date_debut) as mois, r.date_fin, SUM(r.nb_adulte + r.nb_enfant) as personnes
	FROM GITE g, RESERVATION r
	WHERE g.idgite=r.idgite AND g.idgite !=1
	GROUP BY g.nom,mois
	ORDER BY mois";
$resRqu = $mysqli->query($requ);
while ($ligne = $resRqu->fetch_assoc())
	$donnees[(int)$ligne["idgite"]-2][(int)$ligne["mois"]] = $ligne["personnes"];
// la plus grande valeur arrondie
$maxVal = ( max( array_merge($donnees[0],$donnees[1],$donnees[2],$donnees[3],$donnees[4],$donnees[5],$donnees[6]) ) /10 );
$maxVal = (int) (ceil( ((float)$maxVal/10) )*10);

// ====================== pour l'ensemble des gites
$donnees1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
$requ1 = "SELECT g.nom,g.idgite,MONTH( r.date_debut) as mois, r.date_fin, SUM(r.nb_adulte + r.nb_enfant) as personnes
	FROM GITE g, RESERVATION r
	WHERE g.idgite=r.idgite AND g.idgite =1
	GROUP BY g.nom,mois
	ORDER BY mois";
$resRqu1 = $mysqli->query($requ1);
while ($ligne = $resRqu1->fetch_assoc())
	$donnees1[(int)$ligne["mois"]] = $ligne["personnes"];


?>
<script src="scripts/Chart.js"></script>
<script>
new Chart( $("#canvas").get(0).getContext("2d") ).Bar(
	{
		labels : ["Vide","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Decembre"],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				data : [<?php echo '"'.implode('","', $donnees[0]).'"' ?>]
			},
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,1)",
				data : [<?php echo '"'.implode('","', $donnees[1]).'"' ?>]
			},
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				data : [<?php echo '"'.implode('","', $donnees[2]).'"' ?>]
			},
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,1)",
				data : [<?php echo '"'.implode('","', $donnees[3]).'"' ?>]
			},
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				data : [<?php echo '"'.implode('","', $donnees[4]).'"' ?>]
			},
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,1)",
				data : [<?php echo '"'.implode('","', $donnees[5]).'"' ?>]
			},
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				data : [<?php echo '"'.implode('","', $donnees[6]).'"' ?>]
			},
		]
	},//options
	{
		//Boolean - If we show the scale above the chart data			
		scaleOverlay : false,
		
		//Boolean - If we want to override with a hard coded scale
		scaleOverride : true,
		
		//** Required if scaleOverride is true **
		//Number - The number of steps in a hard coded scale
		scaleSteps : 10,
		//Number - The value jump in the hard coded scale
		scaleStepWidth : <?=$maxVal?>,
		//Number - The scale starting value
		scaleStartValue : 0,

		//String - Colour of the scale line	
		scaleLineColor : "rgba(0,0,0,.1)",
		
		//Number - Pixel width of the scale line	
		scaleLineWidth : 1,

		//Boolean - Whether to show labels on the scale	
		scaleShowLabels : true,
		
		//Interpolated JS string - can access value
		scaleLabel : "<%=value%>",
		
		//String - Scale label font declaration for the scale label
		scaleFontFamily : "'Arial'",
		
		//Number - Scale label font size in pixels	
		scaleFontSize : 12,
		
		//String - Scale label font weight style	
		scaleFontStyle : "normal",
		
		//String - Scale label font colour	
		scaleFontColor : "#666",	
		
		///Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines : true,
		
		//String - Colour of the grid lines
		scaleGridLineColor : "rgba(0,0,0,.05)",
		
		//Number - Width of the grid lines
		scaleGridLineWidth : 1,	

		//Boolean - If there is a stroke on each bar	
		barShowStroke : true,
		
		//Number - Pixel width of the bar stroke	
		barStrokeWidth : 2,
		
		//Number - Spacing between each of the X value sets
		barValueSpacing : 5,
		
		//Number - Spacing between data sets within X values
		barDatasetSpacing : 1,
		
		//Boolean - Whether to animate the chart
		animation : true,

		//Number - Number of animation steps
		animationSteps : 60,
		
		//String - Animation easing effect
		animationEasing : "easeOutQuart",

		//Function - Fires when the animation is complete
		onAnimationComplete : null
	}
);

new Chart( $("#canvas1").get(0).getContext("2d") ).Bar(
	{
		labels : ["Vide","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Decembre"],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				data : [<?php echo '"'.implode('","', $donnees1).'"' ?>]
			},
		]
	},{
		scaleOverride : true,
		scaleSteps : 10,
		scaleStepWidth : <?=(int) (ceil( ((float)max($donnees1)/10) )*10)?>,
		scaleStartValue : 0,
	});
</script>

