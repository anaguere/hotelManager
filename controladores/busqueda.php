<?php
#include 'classCluuf.php';
require_once '../twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('/var/www/lte/forms/');
$twig = new Twig_Environment($loader);

/** datos * */
#$str_datos = file_get_contents("data.json");
#$datos = json_decode($str_datos, true);
#fwrite($fh, json_encode($datos, JSON_UNESCAPED_UNICODE));
#fclose($fh);
/* fin datos */

print_r($_POST);



echo $twig->render('busqueda1.html', array('dato' => $_POST));

/*
if ($_POST) {
    $r = new classCluuf();
    $r->nuevojson($_POST);
}
*/


?>


