</div>
<?php
    $startyear = $_SERVER['START_YEAR'];
    $actualyear = date("Y");
    if($startyear!=$actualyear){
        $string = "&copy; ".$startyear. " - ".$actualyear;
    }else{
        $string = "&copy; ".$startyear;
    }
    $string.= " ".$_SERVER['OWNER'];
?>
<?php ob_start();?>

<h3><?=$string?></h3>
<div style="display: flex;justify-content: space-evenly;">
        <a href="<?=$__WEB_ROOT__?>mentions_legales.php" target="_blank" class="w3-hover-opacity">Mentions légales</a>
        <a href="<?=$__WEB_ROOT__?>rgpd.php" target="_blank" class="w3-hover-opacity">RGPD</a>
</div>

<?php $html = ob_get_clean(); ?>
<footer class="w3-container w3-light-green w3-center w3-card" style="opacity:0">
    <?=$html?>
</footer>
<footer class="w3-container w3-light-green w3-center w3-card w3-bottom">
    <?=$html?>
</footer>
</body>
</html>