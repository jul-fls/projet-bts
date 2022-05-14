<?php
	require_once "commons/global.php"; // Inclusion du fichier de configuration
	if($__MAINTENANCE_MODE__) { // Si le mode maintenance est activé
		//execute le reste du code de la page
    }else { // Sinon
        header('Location: '.$__WEB_ROOT__); // Redirection vers la page d'accueil
    }
?>

<!DOCTYPE HTML>
<title>Site Maintenance</title> // Titre de la page
<style> /* Style CSS */
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article> <!-- Début de l'article -->
    <h1>Nous reviendrons bientôt !</h1> <!-- Titre de l'article -->
    <div> <!-- Début du bloc -->
        <p>Désolé pour le désagrément mais nous effectuons une maintenance en ce moment. Si nécéssaire, vous pouvez toujours <a href="mailto:informatique@lyceesaintefamille.com">nous contacter</a>, sinon nous serons de retour en ligne sous peu !</p> <!-- Texte de l'article -->
        <p>&mdash; L'équipe Informatique du Lycée Sainte Famille Saintonge</p> <!-- Texte de l'article -->
    </div> <!-- Fin du bloc -->
</article> <!-- Fin de l'article -->
<br/> <!-- Saut de ligne -->
<br/> <!-- Saut de ligne -->
<br/> <!-- Saut de ligne -->
<br/> <!-- Saut de ligne -->
<br/> <!-- Saut de ligne -->
<p id="loginBtn" style="display:none"><a href="se_connecter.php">Se connecter</a></p> <!-- Bouton de connexion -->

<script> 
	function doc_keyUp(e) { // Fonction qui permet de déclencher l'action au clic d'une touche
        /* http://www.javascripter.net/faq/keycodes.htm */ 
        if (e.ctrlKey && e.altKey && e.keyCode == 32) { // Si la combinaison de touches Ctrl + Alt + Espace est appuyée
            document.getElementById('loginBtn').style.display = "block"; // Affiche le bouton de connexion
        } /* Ctrl+Alt+Space */
    }
    document.addEventListener('keyup', doc_keyUp, false); // Déclenche la fonction au clic d'une touche
</script>