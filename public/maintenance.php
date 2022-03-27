<?php
	require_once "commons/global.php";
	if($__MAINTENANCE_MODE__) {
		//execute le reste du code de la page
    }else {
        header('Location: '.$__WEB_ROOT__);
    }
?>

<!doctype html>
<title>Site Maintenance</title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article>
    <h1>Nous reviendrons bientôt !</h1>
    <div>
        <p>Désolé pour le désagrément mais nous effectuons une maintenance en ce moment. Si nécéssaire, vous pouvez toujours <a href="mailto:informatique@lyceesaintefamille.com">nous contacter</a>, sinon nous serons de retour en ligne sous peu !</p>
        <p>&mdash; L'équipe Informatique du Lycée Sainte Famille Saintonge</p>
    </div>
</article>
<br/>
<br/>
<br/>
<br/>
<br/>
<p id="loginBtn" style="display:none"><a href="se_connecter.php">Se connecter</a></p>

<script>
	function doc_keyUp(e) {
    /* http://www.javascripter.net/faq/keycodes.htm */
    if (e.ctrlKey && e.altKey && e.keyCode == 32) {
        document.getElementById('loginBtn').style.display = "block";
    } /* Ctrl+Alt+Space */
}
document.addEventListener('keyup', doc_keyUp, false);
</script>