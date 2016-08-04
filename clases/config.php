<?php
session_start();


function validacion_admin(){
    
    $perfil = $_SESSION['perfil'];
    
    if($perfil!=0){
	header('Location: sesion.php');
    }

}











?>
