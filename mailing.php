<?php

require_once 'includes/header.php';
require_once 'includes/ink/baseMailHTML.php';
?>
<style type="text/css"><?=$messageCSS?></style>
<div class="row">
	<div class="small-11 small-centered columns">
		<p id="alerte"></p>
		<h2>Envoyer un mail</h2>
		<div>
			<?php
				echo $messageBodyBefore.'<input type="text" autofocus id="sujetMail" placeholder="Sujet">
				<tr>
					<td>
						<textarea rows="10" id="contenumail" name="message">Votre texte ici.</textarea>
						<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
						<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
					</td>
					<td class="expander"></td>
				</tr>'.$messageBodyAfter.'
				<input class="button tiny" onclick="envoi()" value="Envoyer">';
			?>
		</div>
	</div>
</div>
<?php
require('includes/footer.php'); 
?>
<script>
function envoi(){
	$.post("includes/ink/newsletter.php", { sujet: $("#sujetMail").val(), message: $("#contenumail").val() }, 
		function(res){
			if (res == "ok")
				alert("Message envoyé avec succès.");
			else
				alert("Une erreur est survenue. "+res);
		}
	);
}
</script>