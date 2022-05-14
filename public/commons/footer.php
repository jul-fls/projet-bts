</div> <!-- Fin du div du contenu -->
<?php 
    $startyear = $_SERVER['START_YEAR']; //Récupération de l'année de début
    $actualyear = date("Y"); //Récupération de l'année actuelle
    if($startyear!=$actualyear){ //Si l'année de début est différente de l'année actuelle
        $string = "&copy; ".$startyear. " - ".$actualyear; //Création du string
    }else{ //Sinon
        $string = "&copy; ".$startyear; //Création du string
    }
    $string.= " ".$_SERVER['OWNER']; //Ajout du nom de l'auteur
?>
<?php ob_start();?> <!-- Démarrage de l'enregistrement du code HTML -->

<h3><?=$string?></h3> <!-- Affichage du copyright -->
<div style="display: flex;justify-content: space-evenly;"> <!-- Début du div de la liste des liens -->
        <a href="<?=$__WEB_ROOT__?>mentions_legales.php" target="_blank" class="w3-hover-opacity">Mentions légales</a> <!-- Affichage du lien vers les mentions légales -->
        <a href="<?=$__WEB_ROOT__?>rgpd.php" target="_blank" class="w3-hover-opacity">RGPD</a> <!-- Affichage du lien vers les RGPD -->
</div> <!-- Fin du div de la liste des liens -->
 
<?php $html = ob_get_clean(); ?> <!-- Arrêt de l'enregistrement du code HTML -->
<footer class="w3-container w3-light-green w3-center w3-card" style="opacity:0"> <!-- Début du footer -->
    <?=$html?> <!-- Affichage du code HTML -->
</footer> <!-- Fin du footer -->
<footer class="w3-container w3-light-green w3-center w3-card w3-bottom"> <!-- Début du footer -->
    <?=$html?> <!-- Affichage du code HTML -->
</footer> <!-- Fin du footer -->
</body> <!-- Fin du body -->
</html> <!-- Fin du html -->