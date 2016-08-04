<?php

session_start();
include 'clases/reservacionModelo.php';
include 'clases/formModelo.php';
include 'clases/userModelo.php';
$s = new userModelo();

require_once 'twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('/var/www/lte/forms/');
$twig = new Twig_Environment($loader);

/** datos * */
$str_datos = file_get_contents("data.json");
$datos = json_decode($str_datos, true);
fwrite($fh, json_encode($datos, JSON_UNESCAPED_UNICODE));
fclose($fh);
/* fin datos */

$datos['hoy']=date('d-m-Y');
$datos['menu']="sidebar-collapse";
$datos['nombre_usuario']="Usuario Demo";


if ($_POST['sesion']) {

    if (($_POST['usuario'] == 'demo') && ($_POST['password'] == 'h.continental')) {
        $datos['nombre_usuario'] = " Usuario DEMO ";
        $_GET['opcion'] = 'dashboard01';
    } else {
        echo $twig->render('login.html', array('dato' => $datos));
        exit;
    }
}

if ((!$_GET) && (!$_POST)) {

    echo $twig->render('login.html', array('dato' => $datos));
}


/* RESERVACION */

if ($_POST['busqueda01']) {

    $datos['menu']="";
    $m = new reservacionModelo();
    $m->desde = $_POST['desde'];
    $m->hasta = $_POST['hasta'];
    $hoy1 = date('d/m/Y');


    if ((strlen($_POST['desde']) > 6) && (strlen($_POST['hasta']) > 6)) {
        $hoy = $m->fecha_alreves($hoy1);
        $desde = $m->fecha_alreves($_POST['desde']);
        $hasta = $m->fecha_alreves($_POST['hasta']);
        $fechadesde = new DateTime($desde);
        $fechahasta = new DateTime($hasta);
        $fechahoy = new DateTime($hoy);

        $interval01 = $fechahoy->diff($fechadesde);
        $interval02 = $fechadesde->diff($fechahasta);

        $resultado01 = $interval01->format('%R%a');
        $resultado02 = $interval02->format('%R%a');


        if ($resultado01 < 0) {
            $datos['mensaje'] = " <i class='fa fa-times-circle '></i> Disculpe, la Fecha de Entrada no puede ser menor a la fecha actual ";
        } else if ($resultado02 < 0) {
            $datos['mensaje'] = " <i class='fa fa-times-circle '></i> Disculpe Verifique los rangos de Fechas estan Errados ";
        } else {
            $detalle = $m->salida_habitaciones_disponibles_detalle();
            $resumen = $m->salida_habitaciones_disponibles_resumen();
            $_SESSION['busqueda01']['desde'] = $datos['desde'] = $_POST['desde'];
            $_SESSION['busqueda01']['hasta'] = $datos['hasta'] = $_POST['hasta'];
        }
    } else {

        $datos['mensaje'] = " <i class='fa fa-times-circle '></i> Disculpe Verifique la Fecha de Entrada y Fecha de Salida ";
    }

    echo $twig->render('busqueda01.html', array('dato' => $datos, 'detalle' => $detalle, 'resumen' => $resumen));
}

if ($_POST['busqueda011']) {
    $datos['menu']="";
    /* variables entrada */
    $m = new reservacionModelo();
    $m->desde = $_SESSION['busqueda01']['desde'];
    $m->hasta = $_SESSION['busqueda01']['hasta'];
    $cantidad = $_POST['cantidad'];
    $categoria = $_POST['categoria'];

    /* proceso */
    $habitaciones_reservadas = $m->habitaciones_para_reservar02($categoria, $cantidad);

    /* salida */
    $datos['desde'] = $_SESSION['busqueda01']['desde'];
    $datos['hasta'] = $_SESSION['busqueda01']['hasta'];

    $_SESSION['habitaciones_reservadas'] = $habitaciones_reservadas;

    $m->codigo = $habitaciones_reservadas[0]['codigo'];
    $_SESSION['prereservacion']['codigo'] = $habitaciones_reservadas[0]['codigo'];
    $prereservaciones = $m->ver_prereservaciones();
    $m->asignacion_tarifa_prereservacion();
    $tabla_tarifa = $m->generar_tabla_tarifas();


    echo $twig->render('reservacion01.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['reservacion_busqueda_huesped']) {
    $datos['menu']="";
    /* variables entrada */
    $m = new reservacionModelo();
    $m->numero_documento = $_POST['numero_documento'];


    $huesped = $m->busqueda_cliente();
    $datos['desde'] = $_SESSION['busqueda01']['desde'];
    $datos['hasta'] = $_SESSION['busqueda01']['hasta'];
    $habitaciones_reservadas = $_SESSION['habitaciones_reservadas'];

    $m->codigo = $_SESSION['prereservacion']['codigo'];
    $prereservaciones = $m->ver_prereservaciones();
    $tabla_tarifa = $m->generar_tabla_tarifas();

    $_SESSION['huesped'] = $huesped;
    $datos['documento_titular'] = $_POST['numero_documento'];

    echo $twig->render('reservacion01.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'huesped' => $huesped, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['reservacion01']) {
    $m = new reservacionModelo();
    $habitaciones_reservadas = $_SESSION['habitaciones_reservadas'];
    $m->codigo = $m->habitaciones_para_reservar03($_POST, $habitaciones_reservadas);
    $reservacion = $m->reporte_reservacion01();
    $tabla_tarifa = $m->generar_tabla_tarifas01();
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['reservacion03']) {


    $m = new reservacionModelo();
    $m->cliente_id = $_SESSION['edc']['cliente_id'];
    $m->set_codigoid($_SESSION['edc']['codigoid']);
    $m->registro_cliente02($_POST);
    $m->registro_facturacion02($_POST);
    $reservacion = $m->reporte_reservacion03();
    $tabla_tarifa = $m->generar_tabla_tarifas01();
    unset($_SESSION['edc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['checkamallaves']) {
    $m = new reservacionModelo();
    $m->habitacion_id = $_POST['habitacion_id'];
    $m->observacion = $_POST['observacion'];

    if ($_POST['VL'])
        $m->estadoocupacion_id = 19;
    if ($_POST['VS'])
        $m->estadoocupacion_id = 20;
    if ($_POST['OL'])
        $m->estadoocupacion_id = 21;
    if ($_POST['OS'])
        $m->estadoocupacion_id = 22;

    $m->registro_amallaves01();
    $habitaciones = $m->ver_habitaciones_rack01();

    echo $twig->render('amallaves.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_POST['reservacion04']) {

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['cr']['reservacion_id'];
    $m->registro_reservacion02($_POST);
    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    echo $twig->render('resevacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}

if ($_POST['garantiareservacion01']) {
    $m = new reservacionModelo();
    $m->garantiareservacion_id = $_SESSION['tdc']['garantiareservacion_id'];
    $_POST['cliente_id'] = $_SESSION['tdc']['cliente_id'];

    $m->registro_validacion_garantia_reservacion01($_POST);

    $m->set_codigoid($_SESSION['tdc']['codigoid']);
    $reservacion = $m->reporte_reservacion01();
    $tabla_tarifa = $m->generar_tabla_tarifas01();
    unset($_SESSION['tdc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['opciones_reservacion']) {
    $m = new reservacionModelo();
    /* variables entrada */
    $_SESSION['codigo'] = $_POST['reservacion_codigo'];
    $_SESSION['reservacion_id'] = $_POST['reservacion_id'];

    if ($_POST["cancelar"]) {
        $m = new reservacionModelo();

        $m->reservacion_id = $_POST['reservacion_id'];
        $reservacion = $m->reporte_reservacion03();
        $_SESSION['cr']['reservacion_id'] = $_POST['reservacion_id'];

        echo $twig->render('reservacion04.html', array('dato' => $datos, 'reservaciones' => $reservacion));
    }

    if ($_POST["recalcular"]) {
        echo $twig->render('recalcular01.html', array('dato' => $datos, 'habitaciones' => $habitaciones_reservadas, 'huesped' => $huesped));
    }

    if ($_POST["editardatos"]) {
        $m = new reservacionModelo();
        $m->reservacion_id = $_POST['reservacion_id'];
        $reservacion = $m->reporte_reservacion03();
        $_SESSION['edc']['cliente_id'] = $reservacion[0]['cliente_id'];
        $_SESSION['edc']['codigoid'] = $reservacion[0]['codigo_id'];
        echo $twig->render('reservacion03.html', array('dato' => $datos, 'reservaciones' => $reservacion));
    }

    if ($_POST["verificaciontdc"]) {
        $m = new reservacionModelo();
        $m->reservacion_id = $_POST['reservacion_id'];
        $reservacion = $m->reporte_reservacion03();
        $_SESSION['tdc']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
        $_SESSION['tdc']['cliente_id'] = $reservacion[0]['cliente_id'];
        $_SESSION['tdc']['codigoid'] = $reservacion[0]['codigo_id'];
        echo $twig->render('garantiareservacion01.html', array('dato' => $datos, 'reservaciones' => $reservacion));
    }

    if ($_POST["checkin"]) {
        $m = new reservacionModelo();
        $m->reservacion_id = $_POST['reservacion_id'];
        $reservacion = $m->reporte_reservacion03();



        $_SESSION['checkin']['codigo_id'] = $reservacion[0]['codigo_id'];
        $_SESSION['checkin']['codigo_id'] = $reservacion[0]['codigo_id'];
        $_SESSION['checkin']['reservacion_desde'] = $reservacion[0]['reservacion_desde'];
        $_SESSION['checkin']['reservacion_hasta'] = $reservacion[0]['reservacion_hasta'];
        $_SESSION['checkin']['habitacion_id'] = $reservacion[0]['habitacion_id'];
        $_SESSION['checkin']['empresa_id'] = $reservacion[0]['empresa_id'];
        $_SESSION['checkin']['cliente_id'] = $reservacion[0]['cliente_id'];
        $_SESSION['checkin']['reservacion_id'] = $reservacion[0]['reservacion_id'];
        $_SESSION['checkin']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
        $_SESSION['checkin']['ocupacionreservacion'] = "No especifica";

        $m->codigoid = $_SESSION['checkin']['codigo_id'];
        $data = $m->ver_acompanantes();
        $acompanante = $m->reporte_tabla_acompanante($data);

        echo $twig->render('checkin01.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'acompanante' => $acompanante));
    }


    exit;
}

if ($_POST['opciones_checkin']) {

    $m = new reservacionModelo();

    if ($_POST["editardatos"]) {
        $m = new reservacionModelo();
        $m->ocupacion_id = $_SESSION['checkout']['ocupacion_id'];
        $ocupacion = $m->reporte_ocupacion01();
        $_SESSION['checkout']['cliente_id'] = $ocupacion[0]['cliente_id'];

        $combo['tipocliente_id'] = $m->combomaestro(31, 'clientetipo_id', $ocupacion[0]['cliente_tipocliente_id']);
        $combo['tipo_documento'] = $m->combomaestro(14, 'tipo_documento', $ocupacion[0]['cliente_tipodocumento_id']);
        $combo['motivo'] = $m->motivo($ocupacion[0]['ocupacion_motivo']);
        $combo['clientecivil'] = $m->combomaestro(3, 'civil', $ocupacion[0]['civil_id']);
        $combo['formapago_id'] = $m->combomaestro(9, 'formapago_id', $ocupacion[0]['formapago_id']);
        $combo['genero'] = $m->genero('genero', $ocupacion[0]['cliente_genero']);

        echo $twig->render('checkin04.html', array('dato' => $datos, 'ocupaciones' => $ocupacion, 'combos' => $combo));
    }

    if ($_POST["checkout"]) {
        $m = new reservacionModelo();
        $m->ocupacion_id = $_SESSION['checkout']['ocupacion_id'];
        $ocupacion = $m->reporte_ocupacion01();
        echo $twig->render('checkout01.html', array('dato' => $datos, 'reservaciones' => $ocupacion));
    }
     
    
    
}

if ($_POST['reservacion_resumen_recalcular01']) {

    $m = new reservacionModelo();
    $m->desde = $_POST['desde'];
    $m->hasta = $_POST['hasta'];

    $detalle = $m->salida_habitaciones_disponibles_detalle();
    $resumen = $m->salida_habitaciones_disponibles_resumen();

    $datos['sp'] = $datos['pk'] = $datos['pq'] = "";

    $_SESSION['desde'] = $datos['desde'] = $_POST['desde'];
    $_SESSION['hasta'] = $datos['hasta'] = $_POST['hasta'];

    if ($_POST['sp'])
        $datos['sp'] = "checked";
    if ($_POST['pk'])
        $datos['pk'] = "checked";
    if ($_POST['pq'])
        $datos['pq'] = "checked";

    echo $twig->render('recalcular01.html', array('dato' => $datos, 'detalle' => $detalle, 'resumen' => $resumen));
}

if ($_POST['reservacion_resumen_recalcular02']) {

    /* variables entrada */
    $m = new reservacionModelo();
    $m->desde = $_SESSION['desde'];
    $m->hasta = $_SESSION['hasta'];
    $cantidad = $_POST['cantidad'];
    $categoria = $_POST['categoria'];

    /* proceso */
    $habitaciones_reservadas = $m->habitaciones_para_reservar02($categoria, $cantidad);


    $m->codigo = $_SESSION['codigo'];
    $m->reservacion_id = $_SESSION['reservacion_id'];
    ;
    $m->cliente_id = $_SESSION['cliente_id'];
    $m->garantiareservacion_id = $_SESSION['garantiareservacion_id'];
    $m->empresa_id = $_SESSION['empresa_id'];

    $m->habitaciones_para_recalcular01($habitaciones_reservadas);


    /* salida */
    $datos['desde'] = $_SESSION['desde'];
    $datos['hasta'] = $_SESSION['hasta'];

    $_SESSION['habitaciones_reservadas'] = $habitaciones_reservadas;


    $m->reporte_tabla_tarifa01($tarifa01);


    $reservacion = $m->reporte_reservacion01();
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['ocupacion_checkin01']) {
    $m = new reservacionModelo();

    $m->cliente_id = $_SESSION['checkin']['cliente_id'];
    $m->garantiareservacion_id = $_SESSION['checkin']['garantiareservacion_id'];
    $m->empresa_id = $_SESSION['checkin']['empresa_id'];
    $m->habitacion_id = $_SESSION['checkin']['habitacion_id'];
    $m->reservacion_id = $_SESSION['checkin']['reservacion_id'];
    $m->codigo = $_SESSION['checkin']['codigo_id'];
    $m->reservacion_desde = $_SESSION['checkin']['reservacion_desde'];
    $m->reservacion_hasta = $_SESSION['checkin']['reservacion_hasta'];

    $m->registro_cliente01($_POST);
    $m->registro_facturacion01($_POST);
    $m->registro_garantia_reservacion01($_POST);
    $m->registro_ocupacion($_POST);
    $m->registro_reservacion03();
    $m->registro_amallaves02();
    $ocupaciones = $m->ver_ocupaciones();



    echo $twig->render('checkin02.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));


    exit;
}

if ($_POST['ocupacion_checkin01']) {
    $m = new reservacionModelo();

    $_SESSION['codigo'] = $_POST['reservacion_codigo'];
    $_SESSION['reservacion_id'] = $_POST['reservacion_id'];


    if ($_POST["recalcular"]) {
        echo $twig->render('recalcular01.html', array('dato' => $datos, 'habitaciones' => $habitaciones_reservadas, 'huesped' => $huesped));
    }

    if ($_POST["checkin"]) {
        $m->reservacion_id = $_POST['reservacion_id'];
        $reservacion = $m->reporte_reservacion03();
        echo $twig->render('checkin01.html', array('dato' => $datos, 'reservaciones' => $reservacion));
    }
    exit;
}

if ($_POST['checkin04']) {

    $m = new reservacionModelo();

    $m->cliente_id = $_SESSION['checkout']['cliente_id'];
    $m->ocupacion_id = $_SESSION['checkout']['ocupacion_id'];

    $m->registro_cliente01($_POST);
    $m->registro_ocupacion01($_POST);
    $m->registro_facturacion03($_POST);
    $ocupacion = $m->reporte_ocupacion01();
    $_SESSION['checkout']['ocupacion_id'] = $ocupacion[0]['ocupacion_id'];
    $_SESSION['checkout']['cliente_id'] = $ocupacion[0]['cliente_id'];
    echo $twig->render('checkout02.html', array('dato' => $datos, 'reservaciones' => $ocupacion));
}

if ($_POST['confirmacion_checkout']) {
    $m = new reservacionModelo();
    $m->ocupacion_id = $_SESSION['checkout']['ocupacion_id'];
    $m->observacion = $_POST['observacion_checkout'];
    $m->registro_checkout();

    $ocupaciones = $m->reporte_ocupacion02();
    echo $twig->render('checkout03.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));
}

if ($_POST['formusuario']) {
    $u = new userModelo();
    $u->registro_usuario($_POST);
     
}




if ($_GET['opcion'] == 'busquedareservacion') {

    $m = new reservacionModelo();
  
    $m->codigo = $_GET['codigo'];

    $reservacion = $m->reporte_reservacion02();

    $_SESSION['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['reservacion_id'] = $reservacion[0]['reservacion_id'];

    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa = $m->generar_tabla_tarifas01();

    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_GET['opcion'] == 'busquedacheckin') {
    /* variables entrada */

    $m = new reservacionModelo();
    $m->ocupacion_id = $_GET['codigo'];
    $ocupacion = $m->reporte_ocupacion01();
    $_SESSION['checkout']['ocupacion_id'] = $ocupacion[0]['ocupacion_id'];
    echo $twig->render('checkin03.html', array('dato' => $datos, 'reservaciones' => $ocupacion));
}

if ($_GET['opcion'] == 'checkout') {
    $m = new reservacionModelo();
    $ocupaciones = $m->reporte_ocupacion02();
    echo $twig->render('checkout03.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));
}

if ($_GET['opcion'] == 'dashboard01') {
    $datos['menu']="";
    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    $clientes = $m->ver_clientes();
    $checkin = $m->dashboard_tabla_ocupaciones();

    $dashboard['ocupaciones'] = $m->dashboard_ocupaciones();
    $dashboard['reservaciones'] = $m->dashboard_reservaciones();

    $fem = 0;
    $mas = 0;
    for ($i = 0; $i < count($checkin); $i++) {
        $m->codigoid = $checkin[$i]['ocupacion_reservacion_codigo'];
        $checkin[$i]['personas'] = ($m->contar_acompanantes() + 1);
        $fem = $m->contar_acompanantes_f() + $fem;
        $mas = $m->contar_acompanantes_m() + $mas;
    }

    $dashboard['femenino'] = ($m->genero_cliente_f() + $fem);
    $dashboard['masculino'] = ($m->genero_cliente_m() + $mas);
    $dashboard['clientes'] = $dashboard['femenino'] + $dashboard['masculino'];


    
    echo $twig->render('dashboard01.html', array('dashboard' => $dashboard, 'dato' => $datos, 'clientes' => $clientes, 'checkin' => $checkin, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'rack') {
    $m = new reservacionModelo();
    $habitaciones = $m->ver_habitaciones_rack01();


    echo $twig->render('rack.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'amallaves') {
    $m = new reservacionModelo();
    $habitaciones = $m->ver_habitaciones_rack01();

    echo $twig->render('amallaves.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'rack01') {



    echo $twig->render('rack01.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'reservaciones') {

    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'nuevareservacion') {
    $datos['menu']="";
    echo $twig->render('busqueda01.html', array('dato' => $datos));
}

if ($_GET['opcion'] == 'ocupaciones02') {

    $m = new reservacionModelo();

    $ocupaciones = $m->ver_ocupaciones();

    $fem = 0;
    $mas = 0;
    for ($i = 0; $i < count($ocupaciones); $i++) {
        $m->codigoid = $ocupaciones[$i]['ocupacion_reservacion_codigo'];
        $ocupaciones[$i]['personas'] = ($m->contar_acompanantes() + 1);
        $fem = $m->contar_acompanantes_f() + $fem;
        $mas = $m->contar_acompanantes_m() + $mas;
    }

    $genero['femenino'] = ($m->genero_cliente_f() + $fem);
    $genero['masculino'] = ($m->genero_cliente_m() + $mas);


    echo $twig->render('checkin02.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));
}

if ($_GET['opcion'] == 'login') {

    echo $twig->render('login.html', array('dato' => $datos));
}


if ($_GET['opcion'] == 'usuario') {

    $combo['grupo'] = $s->grupo('3');
    
    echo $twig->render('usuario01.html', array('dato' => $datos,'combo' => $combo));
}

?>


