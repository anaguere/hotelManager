<?php

session_start();
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#ini_set("display_errors", 1);


include 'clases/reservacionModelo.php';
include 'clases/formModelo.php';
include 'clases/userModelo.php';
include 'clases/maestroModelo.php';
include 'clases/configuracionModelo.php';


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

$datos['hoy'] = date('d-m-Y');
$datos['menu'] = "sidebar-collapse";
$datos['nombre_usuario'] = "Usuario Demo";

function aprobacion($x, $y) {
    $se = new userModelo();
    if (!$se->secuencia($x, $y)) {
        header('Location: index.php?opcion=nuevareservacion');
    }
}

function aprobacion01($x) {
    if (!$x) {
        header('Location: index.php?opcion=nuevareservacion');
    }
}

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
    aprobacion(0, 0);
    $_SESSION['aprobacion'] = 1;

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
    aprobacion(1, $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 2;

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
    $datos['noches'] = $m->restar_fechas($datos['desde'], $datos['hasta']);
    /* MANEJO DE TARIFAS */
    $tarifas = $m->combostarifa01($valor1);

    echo $twig->render('reservacion01.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'tablatarifa' => $tabla_tarifa, 'combotarifa' => $tarifas));
}

if ($_POST['busqueda012']) {
    aprobacion(1, $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 2;
    /* variables entrada */
    $m = new reservacionModelo();
    $m->desde = $_SESSION['busqueda01']['desde'];
    $m->hasta = $_SESSION['busqueda01']['hasta'];
    $habitaciones_reservadas = $m->habitaciones_para_reservar04($_POST['habitaciones']);


    $datos['desde'] = $_SESSION['busqueda01']['desde'];
    $datos['hasta'] = $_SESSION['busqueda01']['hasta'];
    $_SESSION['habitaciones_reservadas'] = $habitaciones_reservadas;

    $m->codigo = $habitaciones_reservadas[0]['codigo'];
    $_SESSION['prereservacion']['codigo'] = $habitaciones_reservadas[0]['codigo'];
    $prereservaciones = $m->ver_prereservaciones();
    $m->asignacion_tarifa_prereservacion();
    $tabla_tarifa = $m->generar_tabla_tarifas();
    /* MANEJO DE TARIFAS */
    $tarifas = $m->combostarifa01($valor1);

    echo $twig->render('reservacion01.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'tablatarifa' => $tabla_tarifa, 'combotarifa' => $tarifas));
}

if ($_POST['reservacion_busqueda_huesped']) {
    aprobacion(2, $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 2;

    /* $datos['menu'] = ""; */
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
    $datos['noches'] = $m->restar_fechas($datos['desde'], $datos['hasta']);

    echo $twig->render('reservacion01.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'huesped' => $huesped, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['reservacion01']) {
    aprobacion(2, $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 0;

    $m = new reservacionModelo();
    $habitaciones_reservadas = $_SESSION['habitaciones_reservadas'];
    $m->codigo = $m->habitaciones_para_reservar03($_POST, $habitaciones_reservadas);


    /* $reservacion = $m->reporte_reservacion01();
      $m->codigo = $reservacion[0]['reservacion_codigo'];
      $tabla_tarifa['reporte'] = $m->generar_tabla_tarifas02();
      $tabla_tarifa['edicion'] = $m->generar_tabla_tarifas_paraeditar();

      $m->reservacion_id = $reservacion[0]['reservacion_id'];
      $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
      $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
     */

    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    $reservaciones = $m->diasemana_reservacion($reservaciones);
    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
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
    $m->reservacion_id = $_SESSION['tdc']['reservacion_id'];
    $_POST['cliente_id'] = $_SESSION['tdc']['cliente_id'];
    $m->registro_validacion_garantia_reservacion01($_POST);
    $m->actualizar_tipoctahuesped();
    $m->actualizar_ctahuesped_garantiareservacion();
    $m->actualizacion_estatusreserva_congarantia();
    $m->set_codigoid($_SESSION['tdc']['codigoid']);
    $reservacion = $m->reporte_reservacion01();
    $tabla_tarifa = $m->generar_tabla_tarifas01();
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['tdc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

/*
  if ($_POST['opciones_reservacion']) {
  $m = new reservacionModelo();

  $_SESSION['codigo'] = $_POST['reservacion_codigo'];
  $_SESSION['reservacion_id'] = $_POST['reservacion_id'];

  if ($_POST["cancelar"]) {
  $m = new reservacionModelo();

  $m->reservacion_id = $_POST['reservacion_id'];
  $reservacion = $m->reporte_reservacion03();
  $_SESSION['cr']['reservacion_id'] = $_POST['reservacion_id'];

  echo $twig->render('reservacion04.html', array('dato' => $datos, 'reservaciones' => $reservacion));
  exit;
  }

  /* if ($_POST["verificaciontdc"]) {
  $m = new reservacionModelo();
  $m->reservacion_id = $_POST['reservacion_id'];
  $reservacion = $m->reporte_reservacion03();
  $_SESSION['tdc']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
  $_SESSION['tdc']['cliente_id'] = $reservacion[0]['cliente_id'];
  $_SESSION['tdc']['codigoid'] = $reservacion[0]['codigo_id'];
  $_SESSION['tdc']['reservacion_id'] = $_POST['reservacion_id'];
  echo $twig->render('garantiareservacion01.html', array('dato' => $datos, 'reservaciones' => $reservacion));
  exit;
  } */
/*
  if ($_POST["checkin"]) {
  $m = new reservacionModelo();
  $config = new configuracionModelo();
  $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
  $reservacion = $m->reporte_reservacion06_reservacion_id();
  $m->cliente_id = $reservacion[0]['cliente_id'];
  $cliente = $m->ver_cliente();

  $combo['tipodocumento'] = $config->combos(14, $cliente[0]['cliente_tipodocumento_id']);
  $combo['genero'] = $config->combos(39);
  $combo['civil'] = $config->combos(3, $cliente[0]['civil_id']);
  $combo['tipocliente'] = $config->combos(31, $cliente[0]['cliente_tipocliente_id']);
  $combo['motivoviaje'] = $config->combos(35);
  $combo['formapago'] = $config->combos(9);
  $combo['motivoviaje'] = $config->combos(35);
  $combo['permiso'] = $config->combos(74);
  $combo['tipocredito'] = $config->combos(71);

  $_SESSION['checkin']['codigo'] = $reservacion[0]['reservacion_codigo'];
  $_SESSION['checkin']['codigo_id'] = $reservacion[0]['codigo_id'];
  $_SESSION['checkin']['reservacion_desde'] = $reservacion[0]['reservacion_desde'];
  $_SESSION['checkin']['reservacion_hasta'] = $reservacion[0]['reservacion_hasta'];
  $_SESSION['checkin']['habitacion_id'] = $reservacion[0]['habitacion_id'];
  $_SESSION['checkin']['empresa_id'] = $reservacion[0]['empresa_id'];
  $_SESSION['checkin']['cliente_id'] = $reservacion[0]['cliente_id'];
  $_SESSION['checkin']['reservacion_id'] = $reservacion[0]['reservacion_id'];
  $_SESSION['checkin']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
  $_SESSION['checkin']['ocupacionreservacion'] = "No especifica";

  $reservacion[0]['cliente_nacimiento'] = $cliente[0]['cliente_nacimiento'];

  $m->codigoid = $_SESSION['checkin']['codigo_id'];
  $m->codigo = $reservacion[0]['reservacion_codigo'];

  $data = $m->ver_acompanantes();
  $acompanante = $m->reporte_tabla_acompanante($data);

  $tabla_tarifa = $m->generar_tabla_tarifas02();

  echo $twig->render('checkin01.html', array('tabla_tarifa' => $tabla_tarifa, 'dato' => $datos, 'reservaciones' => $reservacion, 'acompanante' => $acompanante, 'combo' => $combo));
  exit;
  }
  } */



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






/* if ($_POST['edicionreservacion']) {
  $m = new reservacionModelo();
  echo $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
  $reservacion = $m->reporte_reservacion06_reservacion_id();
  $m->cliente_id = $reservacion[0]['cliente_id'];
  $m->garantiareservacion_id = $reservacion[0]['garantiareservacion_id'];
  $m->empresa_id = $reservacion[0]['empresa_id'];

  # $m->habitacion_id = $_SESSION['checkin']['habitacion_id'];

  $m->codigo = $reservacion[0]['reservacion_codigoid'];
  ;
  $m->reservacion_desde = $reservacion[0]['reservacion_desde'];
  $m->reservacion_hasta = $reservacion[0]['reservacion_hasta'];

  $m->registro_cliente01($_POST);
  $m->registro_facturacion01($_POST);
  $m->registro_garantia_reservacion01($_POST);
  $m->verificar_garantia_reservacion_automatica();
  #$m->registro_ocupacion($_POST);
  #$m->actualizacion_checkin_reservacion();
  #$m->actualizacion_estatusreserva_checkin();
  #$m->registro_ctacliente($_POST);
  #$m->registro_ctaclientereservacion($_POST);
  #$m->registro_reservacion03();
  #$m->registro_amallaves02();
  $ocupaciones = $m->ver_ocupaciones();

  echo $twig->render('checkin02.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));

  exit;
  }
 */
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

if ($_GET['opcion'] == 'checkout') {
    $m = new reservacionModelo();
    $ocupaciones = $m->reporte_ocupacion02();
    echo $twig->render('checkout03.html', array('dato' => $datos, 'ocupaciones' => $ocupaciones));
}

if ($_GET['opcion'] == 'dashboard01') {
    $datos['menu'] = "";
    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    $clientes = $m->ver_clientes();
    $checkin = $m->dashboard_tabla_ocupaciones();
    $supervision = $m->ver_supervisiones();
    $supervision = $m->icono_supervisiones02($supervision);
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



    echo $twig->render('dashboard01.html', array('dashboard' => $dashboard, 'supervisiones' => $supervision, 'dato' => $datos, 'clientes' => $clientes, 'checkin' => $checkin, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'rack') {
    $m = new reservacionModelo();
    $habitaciones = $m->RCK_ver_habitaciones_rack01();
    $resumen_amallaves = $m->AML_ver_resumen_rack();
    $resumen_reservaciones['reservaciones_hoy'] = $m->RCK_datos_reservacion_para_hoy();
    $resumen_reservaciones['checkin_hoy'] = $m->RCK_datos_reservacion_checkin();
    $resumen_reservaciones['checkout_hoy'] = $m->RCK_datos_reservacion_checkout();




    echo $twig->render('rack.html', array('dato' => $datos, 'habitaciones' => $habitaciones, 'resumen_amallaves' => $resumen_amallaves, 'resumen_reservaciones' => $resumen_reservaciones));
}



if ($_GET['opcion'] == 'rack01') {



    echo $twig->render('rack01.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'nuevareservacion') {
    $datos['menu'] = "sidebar-collapse";
    echo $twig->render('busqueda01.html', array('dato' => $datos));
}

if ($_GET['opcion'] == 'login') {

    echo $twig->render('login.html', array('dato' => $datos));
}

if ($_GET['opcion'] == 'usuario') {
    $combo['grupo'] = $s->grupo('3');
    $combo['acciones'] = $s->acciones('14');
    $tabla['usuarios'] = $s->reporte_tabla_usuario();
    $tabla['acciones'] = $s->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $s->reporte_tabla_acciongrupo();


    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_GET['opcion'] == 'consultausuario') {

    $s->usuario_id = $_GET['code'];
    $combo['acciones'] = $s->grupo('14');
    $usuario = $s->ver_usuario();
    $combo['grupo'] = $s->grupo($usuario['usuario_grupo_id']);
    $tabla['usuarios'] = $s->reporte_tabla_usuario();
    $tabla['acciones'] = $s->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $s->reporte_tabla_acciongrupo();


    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'usuario' => $usuario, 'combo' => $combo));
}

if ($_GET['opcion'] == 'consultaacciongrupo') {

    $s->acciongrupo_id = $_GET['code'];
    $_SESSION['usuario']['acciongrupo_id'] = $_GET['code'];

    $acciongrupo = $s->ver_acciongrupo();
    $combo['grupo'] = $s->grupo($acciongrupo['grupo_id']);
    $combo['acciones'] = $s->acciones($acciongrupo['accion_id']);

    $tabla['usuarios'] = $s->reporte_tabla_usuario();
    $tabla['acciones'] = $s->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $s->reporte_tabla_acciongrupo();


    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'usuario' => $usuario, 'combo' => $combo));
}

if ($_GET['opcion'] == 'supervision') {
    $m = new reservacionModelo();
    $habitaciones = $m->ver_habitaciones_rack01();

    echo $twig->render('supervision.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'inventario01') {
    $ma = new maestroModelo();
    $combo['padre'] = $ma->combopadre();
    $tabla['maestro'] = $ma->reporte_maestro();
    echo $twig->render('inventario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_GET['opcion'] == 'roomservice') {
    $m = new reservacionModelo();
    $habitaciones = $m->ver_habitaciones_rack01();
    echo $twig->render('roomservice01.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_GET['opcion'] == 'roomservice01') {
    $m = new reservacionModelo();
    $m->habitacion_id = $_GET['codigo'];
    $habitacion = $m->ver_habitacion_rack01();
    $productos = $m->ver_productos();
    $productos2 = $m->ver_productos_por_habitacion();


    $_SESSION['temp']['habitacion_id'] = $habitacion['habitacion_id'];
    echo $twig->render('roomservice.html', array('dato' => $datos, 'productos' => $productos, 'productos2' => $productos2, 'habitacion' => $habitacion));
}


if ($_GET['codeinventario']) {
    $ma = new maestroModelo();
    $ma->id = $_GET['codeinventario'];
    $maestro = $ma->reporte_maestro01();
    $combo['padre'] = $ma->combopadre($maestro[0]['maestro_padreid']);
    $tabla['maestro'] = $ma->reporte_maestro();
    echo $twig->render('inventario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo, 'maestro' => $maestro));
}

if ($_POST['formacciones']) {

    $u = new userModelo();

    $u->registro_acciones($_POST);
    $tabla['usuarios'] = $u->reporte_tabla_usuario();
    $tabla['acciones'] = $u->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $u->reporte_tabla_acciongrupo();

    $combo['grupo'] = $u->grupo('4');
    $combo['acciones'] = $u->acciones('14');
    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_POST['formacciongrupo']) {

    $u = new userModelo();

    $u->acciongrupo_id = $_SESSION['usuario']['acciongrupo_id'];

    $u->registro_acciongrupo($_POST);
    unset($_SESSION['usuario']['acciongrupo_id']);
    $tabla['usuarios'] = $u->reporte_tabla_usuario();
    $tabla['acciones'] = $u->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $u->reporte_tabla_acciongrupo();

    $combo['grupo'] = $u->grupo('4');
    $combo['acciones'] = $u->acciones('14');
    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_POST['formusuario']) {

    $u = new userModelo();

    $u->registro_usuario($_POST);
    $tabla['usuarios'] = $u->reporte_tabla_usuario();
    $tabla['acciones'] = $u->reporte_tabla_acciones();
    $tabla['acciongrupo'] = $u->reporte_tabla_acciongrupo();

    $combo['grupo'] = $u->grupo('4');
    $combo['acciones'] = $u->acciones('14');

    echo $twig->render('usuario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_POST['formmaestro']) {

    $ma = new maestroModelo();
    $ma->id = $_POST['id'];
    $ma->registro_maestro($_POST);
    $combo['padre'] = $ma->combopadre();
    $tabla['maestro'] = $ma->reporte_maestro();
    echo $twig->render('inventario01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}



if ($_GET['opcion'] == 'busquedasupervision') {
    /* variables entrada */

    $m = new reservacionModelo();
    $m->supervision_id = $_GET['codigo'];
    $supervision = $m->ver_supervision();
    $_SESSION['supervision']['supervision_id'] = $supervision['supervision_id'];
    echo $twig->render('supervision03.html', array('dato' => $datos, 'supervision' => $supervision));
}

if ($_GET['opcion'] == 'supervisiones') {

    $m = new reservacionModelo();
    $supervisiones = $m->ver_supervisiones();
    $supervisiones = $m->icono_supervisiones02($supervisiones);
    echo $twig->render('supervision02.html', array('dato' => $datos, 'supervisiones' => $supervisiones));
}

if ($_POST['solventar_supervision']) {

    $m = new reservacionModelo();
    $m->idtabla = $_SESSION['supervision']['supervision_id'];
    $m->registro_supervision01($_POST);
    $supervisiones = $m->ver_supervisiones();
    $supervisiones = $m->icono_supervisiones02($supervisiones);
    echo $twig->render('supervision02.html', array('dato' => $datos, 'supervisiones' => $supervisiones));
}



if ($_GET['opcion'] == 'configuracion01') {

    $datos['cuadro1'] = "collapsed-box";
    $datos['cuadro2'] = "";

    $ma = new configuracionModelo();
    $combo['padre'] = $ma->combopadre();
    $tabla['maestro'] = $ma->reporte_maestro();
    echo $twig->render('configuracion01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}

if ($_GET['codeconfiguracion']) {

    $datos['cuadro1'] = "";
    $datos['cuadro2'] = "collapsed-box";

    $ma = new configuracionModelo();
    $ma->id = $_GET['codeconfiguracion'];
    $maestro = $ma->reporte_maestro01();
    $combo['padre'] = $ma->combopadre($maestro[0]['configuracion_padreid']);
    $tabla['maestro'] = $ma->reporte_maestro();

    echo $twig->render('configuracion01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo, 'maestro' => $maestro));
}

if ($_POST['formconfiguracion']) {

    $datos['cuadro1'] = "collapsed-box";
    $datos['cuadro2'] = "";

    $ma = new configuracionModelo();
    $ma->id = $_POST['id'];
    $ma->registro_maestro($_POST);
    $combo['padre'] = $ma->combopadre();
    $tabla['maestro'] = $ma->reporte_maestro();
    echo $twig->render('configuracion01.html', array('dato' => $datos, 'tabla' => $tabla, 'combo' => $combo));
}











if ($_POST['profilectahuesped']) {
    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $profiles = $m->ver_profiles();

    echo $twig->render('profilenuevo.html', array('profiles' => $profiles, 'clientes' => $clientes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}


if ($_POST['registrarnuevoprofile']) {
    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->registro_cliente($_POST);


    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
# $ctahuesped = $m->icono_garantiareservacion02($reservaciones);


    echo $twig->render('ctahuesped03.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'asociacionreservas' => $asociacionreservas, 'ctahuesped' => $ctahuesped));
}



if ($_POST['agregarcuentahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->asociacion_ctacliente($_POST['ctahuesped']);

    $ctasasociadas = $m->ver_ctahuespedgrupo();


    $ctahuesped = $m->ver_ctahuesped01();
    $detallereservacion = $m->ver_ctahuesped_detalle01();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    echo $twig->render('ctahuesped03.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'detallereservacion' => $detallereservacion, 'asociacionreservas' => $asociacionreservas, 'ctahuesped' => $ctahuesped));
}


if ($_POST['crearctahuesped']) {

    $config = new configuracionModelo();
    $combo['tipodocumento'] = $config->combos(14);
    $combo['genero'] = $config->combos(39);
    $combo['civil'] = $config->combos(3);
    $combo['tipocliente'] = $config->combos(31);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['formapago'] = $config->combos(9);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['permiso'] = $config->combos(74);
    $combo['tipocredito'] = $config->combos(71);

    echo $twig->render('ctahuesped04.html', array('combo' => $combo, 'dato' => $datos));
}


if ($_POST['verificarctahuesped']) {

    $m = new reservacionModelo();
    $m->numero_documento = $_POST['documento'];
    $cliente = $m->busqueda_ctacliente();

    $config = new configuracionModelo();
    $combo['tipodocumento'] = $config->combos(14, $cliente['cliente_tipodocumento_id']);
    $combo['genero'] = $config->combos(39);
    $combo['civil'] = $config->combos(3, $cliente['civil_id']);
    $combo['tipocliente'] = $config->combos(31, $cliente['cliente_tipocliente_id']);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['formapago'] = $config->combos(9);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['permiso'] = $config->combos(74);
    $combo['tipocredito'] = $config->combos(71);
    echo $twig->render('ctahuesped04.html', array('combo' => $combo, 'cliente' => $cliente, 'dato' => $datos));
}


if ($_POST['registrarctahuesped']) {

    $m = new reservacionModelo();
    $m->registro_cliente_ctahuesped($_POST);
    $m->registro_garantia_reservacion($_POST);
    $_POST['ch_tipocta'] = 85;
    $m->registro_ctacliente($_POST);

    $ctahuesped = $m->ver_ctahuesped();
    echo $twig->render('ctahuesped01.html', array('dato' => $datos, 'ctahuesped' => $ctahuesped));
}

/*
  if ($_POST['registrarpagoctahuesped']) {

  $m = new reservacionModelo();
  $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];

  $_SESSION['detallereservacion_ids'] = $_POST['ctahuespedreservacion'];


  $ctahuesped = $m->ver_ctahuesped01();
  $detallereservacion = $m->ver_ctahuesped_detalle_pago($_POST['ctahuespedreservacion']);



  $m->totalizar($detallereservacion,'detallereservacion_precio',false);


  $config = new configuracionModelo();
  $combo['formapago'] = $config->combos(9);
  $combo['tipotarjeta'] = $config->combos(90);
  // $ctasasociadas = $m->ver_ctahuespedgrupo();
  echo $twig->render('ctahuesped05.html', array('combo' => $combo, 'dato' => $datos, 'detallereservacion' => $detallereservacion, 'ctahuesped' => $ctahuesped));
  }
 */

if ($_POST['registrarpagoctahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];

# $_SESSION['detallereservacion_ids'] = $_POST['ctahuespedreservacion'];
# $detallereservacion = $m->ver_ctahuesped_detalle_pago($_POST['ctahuespedreservacion']);    $config = new configuracionModelo();
# $ctasasociadas = $m->ver_ctahuespedgrupo();


    $ctahuesped = $m->ver_ctahuesped01();
    $detallereservacion02 = $m->ver_ctahuesped_detalle01();
    $detallereservacion03 = $m->ver_ctahuesped_detallereservacion($detallereservacion02);
    $config = new configuracionModelo();
    $combo['formapago'] = $config->combos(9);
    $combo['tipotarjeta'] = $config->combos(90);
    $combo['profiles'] = $m->comboprofiles();
    $ctahuesped = $m->get_ctahuesped();
    $datos['vista_cargarpagos'] = true;
    $datos['vista_anularpagos'] = false;
    echo $twig->render('ctahuesped05.html', array('combo' => $combo, 'dato' => $datos, 'detallereservacion' => $detallereservacion03, 'ctahuesped' => $ctahuesped));
}


if ($_POST['cargarpagoctahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->registro_ctaclienterepago($_POST);

    $ctahuesped = $m->get_ctahuesped();
    $config = new configuracionModelo();
    $combo['formapago'] = $config->combos(9);
    $combo['tipotarjeta'] = $config->combos(90);
// $ctasasociadas = $m->ver_ctahuespedgrupo();
    echo $twig->render('ctahuesped05.html', array('combo' => $combo, 'dato' => $datos, 'detallereservacion' => $detallereservacion03, 'ctahuesped' => $ctahuesped));
}

if ($_POST['transferenciacargos']) {
    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $listacargos = $m->ver_cargosparatransferir($_POST['ctcargos_id']);
    $_SESSION['ctahuesped']['ctacargos_id'] = $_POST['ctcargos_id'];
    $ctahuespedes = $m->ver_ctahuespedesactivas();
    $ctahuesped = $m->get_ctahuesped();
    echo $twig->render('ctahuesped06.html', array('ctahuespedes' => $ctahuespedes, 'listacargos' => $listacargos, 'combo' => $combo, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['tranferircargos01']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_POST['tranferircargos01'];
    $listacargos = $m->actualizar_cargosparatransferir01($_SESSION['ctahuesped']['ctacargos_id']);

    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $ctahuesped = $m->get_ctahuesped();
    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}



if ($_POST['garantiactahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $ctahuesped = $m->get_ctahuesped();
    echo $twig->render('ctahuesped08.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}


if ($_POST['garantiareservacion_ctahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $ctahuesped = $m->get_ctahuesped();
    $m->garantiareservacion_id = $ctahuesped['ctabasicos']['garantiareservacion_id'];
    $m->registro_validacion_garantia_reservacion01($_POST);
    $m->actualizar_tipoctahuesped();
    $m->actualizar_ctahuesped_garantiareservacion();
    $m->actualizacion_estatusreserva_congarantia();
    $ctahuesped = $m->get_ctahuesped();
    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['iratrasctahuesped']) {
    aprobacion01($_SESSION['ctahuesped']['ctahuesped_id']);

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];
    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}



if ($_POST['iratrasroomservice']) {

    $m = new reservacionModelo();
    $habitaciones = $m->ver_habitaciones_rack01();
    echo $twig->render('roomservice01.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}



if ($_POST['facturacioncargos']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $listacargos = $m->ver_cargosparatransferir($_POST['ctcargos_id']);

    $m->listacargos = $_POST['ctcargos_id'];
    $_SESSION['ctahuesped']['ctacargos_id'] = $_POST['ctcargos_id'];
#$ctahuespedes = $m->ver_ctahuespedesactivas();
    $ctahuesped = $m->get_ctahuesped();


    $totalesfacturacion = $m->buscar_cargosparafacturar();


    echo $twig->render('facturacion01.html', array('total' => $totalesfacturacion, 'listacargos' => $listacargos, 'combo' => $combo, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_GET['opcion'] == 'anularpago') {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->pago_id = $_GET['codigo'];
    $detalle_pago = $m->ver_pago();
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];
    $_SESSION['ctahuesped']['pago_id'] = $m->pago_id;
    $datos['vista_cargarpagos'] = false;
    $datos['vista_anularpagos'] = true;
    echo $twig->render('ctahuesped05.html', array('detallepago' => $detalle_pago, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['anularpagoctahuesped']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->pago_id = $_SESSION['ctahuesped']['pago_id'];
    $m->anulacion_pago($_POST);
    $config = new configuracionModelo();
    $combo['formapago'] = $config->combos(9);
    $combo['tipotarjeta'] = $config->combos(90);
    $ctahuesped = $m->get_ctahuesped();
    $datos['vista_cargarpagos'] = true;
    $datos['vista_anularpagos'] = false;
    echo $twig->render('ctahuesped05.html', array('combo' => $combo, 'dato' => $datos, 'detallereservacion' => $detallereservacion03, 'ctahuesped' => $ctahuesped));
}







if ($_POST['recalcularreservacion']) {

    $m->codigo = $_SESSION['temp']['reservacion_codigoid'];
    $reservacion = $m->reporte_reservacion02();
    echo $twig->render('recalcular01.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'habitaciones' => $habitaciones_reservadas, 'huesped' => $huesped));
}


if ($_POST['recalcularreservacionbusqueda01']) {

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

    $m->codigo = $_SESSION['temp']['reservacion_codigoid'];
    $reservacion = $m->reporte_reservacion02();
    echo $twig->render('recalcular01.html', array('detalle' => $detalle, 'resumen' => $resumen, 'dato' => $datos, 'reservaciones' => $reservacion, 'habitaciones' => $habitaciones_reservadas, 'huesped' => $huesped));
}

if ($_POST['recalcularreservacionbusqueda011']) {

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
    /* MANEJO DE TARIFAS */
    $tarifas = $m->combostarifa01($valor1);

    echo $twig->render('recalcular02.html', array('dato' => $datos, 'prereservaciones' => $prereservaciones, 'tablatarifa' => $tabla_tarifa, 'combotarifa' => $tarifas));
}

if ($_POST['recalcularreservacionbusqueda012']) {

    /* variables entrada */
    $m = new reservacionModelo();
    $m->desde = $_SESSION['busqueda01']['desde'];
    $m->hasta = $_SESSION['busqueda01']['hasta'];

    $habitaciones_reservadas = $m->habitaciones_para_reservar04($_POST['habitaciones']);


    $datos['desde'] = $_SESSION['busqueda01']['desde'];
    $datos['hasta'] = $_SESSION['busqueda01']['hasta'];

    $_SESSION['habitaciones_reservadas'] = $habitaciones_reservadas;

    $m->codigo = $habitaciones_reservadas[0]['codigo'];
    $_SESSION['prereservacion']['codigo'] = $habitaciones_reservadas[0]['codigo'];
    $prereservaciones = $m->ver_prereservaciones();
    $m->asignacion_tarifa_prereservacion();
    $tabla_tarifa = $m->generar_tabla_tarifas();
    /* MANEJO DE TARIFAS */
    $tarifas = $m->combostarifa01($valor1);

    $m->codigo = $_SESSION['temp']['reservacion_codigoid'];
    $reservacion = $m->reporte_reservacion02();


    echo $twig->render('recalcular02.html', array('reservaciones' => $reservacion, 'dato' => $datos, 'prereservaciones' => $prereservaciones, 'tablatarifa' => $tabla_tarifa, 'combotarifa' => $tarifas));
}

if ($_POST['recalcularreservacion01']) {
    $m = new reservacionModelo();
    $habitaciones_reservadas = $_SESSION['habitaciones_reservadas'];
    $m->ctahuesped_id = $_SESSION['temp']['ctacliente_id'];
    $m->cliente_id = $_SESSION['temp']['cliente_id'];
    $m->garantiareservacion_id = $_SESSION['temp']['garantiareservacion_id'];
    $m->empresa_id = $_SESSION['temp']['empresa_id'];

    $m->codigo = $m->recalcularhabitaciones_para_reservar03($_POST, $habitaciones_reservadas);
    $reservacion = $m->reporte_reservacion01();
    $tabla_tarifa = $m->generar_tabla_tarifas01();
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['transferenciaprofile']) {
    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['ctahuesped']['ctahuesped_id'];
    $m->ver_cargosparatransferir($_POST['ctcargos_id']);
    $m->ver_pedidosparatransferir($_POST['ctcargos_id']);
    $m->cliente_id = $_POST['profile_id'];
    $m->transferencia_profile_cargos();
    $m->transferencia_profile_pedidos();


    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];

    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['confirmacioncambiohabitacion']) {


    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['reservacion_id_nuevo'];



    echo $twig->render('cambiohabitacion01.html', array('dato' => $datos, 'reservacion' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['verctahuesped']) {
    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->buscar_ctahuesped_con_reservacion_id();
    unset($_SESSION['ctahuesped']);

    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];

    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['cargarproductoshab']) {
    $m = new reservacionModelo();
    $m->habitacion_id = $_SESSION['temp']['habitacion_id'];
    $m->buscar_ctahuesped_roomservice();
    $m->registrar_pedido($_POST['producto'], $_POST['cantidad']);
    $habitaciones = $m->ver_habitaciones_rack01();
    echo $twig->render('roomservice01.html', array('dato' => $datos, 'habitaciones' => $habitaciones));
}

if ($_POST['actualizacionresponsable']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['temp']['ctacliente_id'];
    $m->cliente_id = $_POST['cliente_id'][0];
    $m->nuevoresponsable_profile();

    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];

    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}




/* CHECK OUT */

if ($_POST['checkoutreservacion']) {


    $_SESSION['aprobacion'] = "CHECKOUT";
    aprobacion01($_SESSION['temp']['reservacion_id']);

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->verificar_garantia_reservacion_automatica();


    $_SESSION['edc']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['edc']['codigoid'] = $reservacion[0]['codigo_id'];

    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();

    echo $twig->render('checkout01.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
    exit;
}

if ($_POST['checkoutreservacion01']) {


    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->habitacion_id = $reservacion[0]['habitacion_id'];
    $m->RSV_checkout_reservacion($_POST);
    $m->RSV_registro_checkout_estadohabitacion();
    $m->RSV_checkout_detallereservacion();


    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();

    if ($reservaciones) {
        $m->reservacion_id = $reservaciones[$i]['reservacion_id'];
        $reservaciones = $m->icono_garantiareservacion02($reservaciones);
        $reservaciones = $m->diasemana_reservacion($reservaciones);
        $reservaciones = $m->encriptar_codigo_get($reservaciones);
    }

    unset($_SESSION['checkin']);
    unset($_SESSION['habitaciones_reservadas']);
    unset($_SESSION['prereservacion']);
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['busqueda01']);
    unset($_SESSION['huesped']);
    unset($_SESSION['edc']);

    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}




/* RESERVACIONES */

if ($_POST['iratrasreservacion']) {
    aprobacion01($_SESSION['temp']['reservacion_id']);

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $_SESSION['temp']['reservacion_codigo'] = $reservacion['0']['reservacion_codigo'];
    $m->codigo = $_SESSION['temp']['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $ctahuesped = $m->get_ctahuesped();
    unset($_SESSION['tdc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_GET['opcion'] == 'reservaciones') {
    aprobacion(10, 10);
    $_SESSION['aprobacion'] = 10;

    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();

    if ($reservaciones) {
        $m->reservacion_id = $reservaciones[$i]['reservacion_id'];
        $reservaciones = $m->icono_garantiareservacion02($reservaciones);
        $reservaciones = $m->diasemana_reservacion($reservaciones);
        $reservaciones = $m->encriptar_codigo_get($reservaciones);
    }

    unset($_SESSION['checkin']);
    unset($_SESSION['habitaciones_reservadas']);
    unset($_SESSION['prereservacion']);
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['busqueda01']);
    unset($_SESSION['huesped']);
    unset($_SESSION['edc']);

    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'busquedareservacion') {


    $m = new reservacionModelo();

//  $m->actualizacion_habitacion_detallereservacion01();

    aprobacion01($_GET['codigo']);
    $m->codigo = $m->decrypt($_GET['codigo']);
    $reservacion = $m->reporte_reservacion02();



    aprobacion01($reservacion);
    $m->reservacion_id = $reservacion[0]['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();


    $_SESSION['temp']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['temp']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['temp']['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['temp']['reservacion_id'] = $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
    $_SESSION['temp']['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['ctacliente_id'] = $reservacion[0]['ctacliente_id'];

    $ctahuesped = $m->get_ctahuesped();
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();

    unset($_SESSION['tdc']);
    aprobacion01($ctahuesped);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}





/* EDITAR LA RESERVACION */

if ($_POST["editardatosreservacion"]) {
    $config = new configuracionModelo();
    $_SESSION['aprobacion'] = "EDICIONRESERVACI0N";
    aprobacion01($_SESSION['temp']['reservacion_id']);

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->verificar_garantia_reservacion_automatica();

    $m->cliente_id = $reservacion[0]['cliente_id'];
    $cliente = $m->ver_cliente();

    $combo['tipodocumento'] = $config->combos(14, $cliente[0]['cliente_tipodocumento_id']);
    $combo['genero'] = $config->combos(39);
    $combo['civil'] = $config->combos(3, $cliente[0]['civil_id']);
    $combo['tipocliente'] = $config->combos(31, $cliente[0]['cliente_tipocliente_id']);
    $combo['motivoviaje'] = $config->combos(35, $reservacion[0]['reservacion_motivo']);
    $combo['formapago'] = $config->combos(9, $reservacion[0]['formapago_id']);
    $combo['permiso'] = $config->combos(74);
    $combo['tipocredito'] = $config->combos(71);


    $_SESSION['edc']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['edc']['codigoid'] = $reservacion[0]['codigo_id'];

    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();

    echo $twig->render('reservacion03.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa, 'combo' => $combo));
    exit;
}

if ($_POST['edicionreservacion']) {

    aprobacion("EDICIONRESERVACI0N", $_SESSION['aprobacion']);
    aprobacion01($_SESSION['temp']['reservacion_id']);


    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();

    $m->cliente_id = $reservacion['0']['cliente_id'];
    $m->garantiareservacion_id = $reservacion['0']['garantiareservacion_id'];
    $m->set_codigoid($_SESSION['edc']['codigoid']);
    $m->empresa_id = $reservacion['0']['empresa_id'];

    $m->registro_cliente02($_POST);
    $m->registro_facturacion($_POST);
    $m->registro_garantia_reservacion01($_POST);
    $m->RSV_registro_reservacion04($_POST);

    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['aprobacion'] = "10";
    unset($_SESSION['edc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_GET['opcion'] == 'nuevohuespedresponsable') {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['temp']['ctacliente_id'];
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $profiles = $m->ver_profiles();

    echo $twig->render('reservacion_nuevo_responsable.html', array('profiles' => $profiles, 'clientes' => $clientes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['registrarhuespedresponsable']) {
    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->RSV_registro_cliente($_POST);
    $m->RSV_actualizacion_huesped_principal();


    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $_SESSION['temp']['reservacion_codigo'] = $reservacion['0']['reservacion_codigo'];
    $m->codigo = $_SESSION['temp']['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $ctahuesped = $m->get_ctahuesped();
    unset($_SESSION['tdc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['actualizacionhuespedresponsable']) {

    
    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->cliente_id = $_POST['cliente_id'][0];
    $m->RSV_actualizacion_huesped_principal01();

    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $_SESSION['temp']['reservacion_codigo'] = $reservacion['0']['reservacion_codigo'];
    $m->codigo = $_SESSION['temp']['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $ctahuesped = $m->get_ctahuesped();
    unset($_SESSION['tdc']);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST['nuevoacompañante']) {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['temp']['ctacliente_id'];
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $acompanantes = $m->RSV_ver_huespedes_reservacion();

    echo $twig->render('reservacion_acompanante.html', array('profiles' => $profiles, 'clientes' => $clientes, 'acompanantes' => $acompanantes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}


if ($_POST['registrarhuespedacompañante']) {

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->RSV_registro_cliente($_POST);
    $m->RSV_registro_huesped_reservacion();

    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $acompanantes = $m->RSV_ver_huespedes_reservacion();

    echo $twig->render('reservacion_acompanante.html', array('profiles' => $profiles, 'clientes' => $clientes, 'acompanantes' => $acompanantes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_POST['actualizacionhuespedacompañante']) {

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->cliente_id = $_POST['cliente_id'][0];
    $m->RSV_registro_huesped_reservacion();

    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $acompanantes = $m->RSV_ver_huespedes_reservacion();

    echo $twig->render('reservacion_acompanante.html', array('profiles' => $profiles, 'clientes' => $clientes, 'acompanantes' => $acompanantes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_GET['opcion'] == 'huespedelete') {

    
    
    
    
    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->cliente_id = $_GET['codigo'];
    $m->RSV_delete_huesped();
  
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    /* ESTE CODIGO SE DEBE DEJAR ASI FUE UN ERROR Y BUENO QUE SE LE HACE EL PROYETO ESTA AVANZADO */
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $acompanantes = $m->RSV_ver_huespedes_reservacion();

    echo $twig->render('reservacion_acompanante.html', array('profiles' => $profiles, 'clientes' => $clientes, 'acompanantes' => $acompanantes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));

    
}



/*  CAMBIO DE HABITACION    */

if ($_POST['cambiarhabitacionctahuesped']) {

    $_SESSION['aprobacion'] = "CAMBI0HABITACI0N";
    aprobacion01($_SESSION['temp']['reservacion_id']);



    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];


    if ($m->CH_reservaciones_bloqueadas()) {
        $reservacion = $m->reporte_reservacion06_reservacion_id();
        $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
        $codigo = $m->encrypt($reservacion[0]['codigo_id']);
        header('Location: index.php?opcion=busquedareservacion&codigo=' . $codigo);
    }


    $m->CH_CtaHuesped();
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();


    if ($reservacion[0]['checkin']) {
        $fecha = $m->CH_fechas_extremos_con_checkin();
        $m->desde = date('d/m/Y');
    } else {
        $fecha = $m->CH_fechas_extremos_sin_checkin();
        $m->desde = $fecha['desde'];
    }


    $m->hasta = $fecha['hasta'];

    $hoy1 = date('d/m/Y');
    $hoy = $m->fecha_alreves($hoy1);
    $desde = $m->fecha_alreves($m->desde);
    $hasta = $m->fecha_alreves($m->hasta);
    $fechadesde = new DateTime($desde);
    $fechahasta = new DateTime($hasta);
    $fechahoy = new DateTime($hoy);

    $interval01 = $fechahoy->diff($fechadesde);
    $interval02 = $fechadesde->diff($fechahasta);

    $resultado01 = $interval01->format('%R%a');
    $resultado02 = $interval02->format('%R%a');

    $detalle = $m->CH_salida_habitaciones_disponibles_detalle();
    $resumen = $m->CH_salida_habitaciones_disponibles_resumen();
    $_SESSION['busqueda01']['desde'] = $datos['desde'] = $m->desde;
    $_SESSION['busqueda01']['hasta'] = $datos['hasta'] = $m->hasta;


    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['edc']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['edc']['codigoid'] = $reservacion[0]['codigo_id'];
    $datos['desde'] = $m->desde;
    $datos['hasta'] = $m->hasta;

    echo $twig->render('cambiohabitacion.html', array('detalle' => $detalle, 'dato' => $datos, 'ctahuesped' => $ctahuesped, 'reservaciones' => $reservacion));
}

if ($_POST['busquedacambiohabitacion']) {

    aprobacion01($_SESSION['temp']['reservacion_id']);
    aprobacion("CAMBI0HABITACI0N", $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 10;

    /* variables entrada */
    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->desde = $_SESSION['busqueda01']['desde'];
    $m->hasta = $_SESSION['busqueda01']['hasta'];
    $m->habitacion_id = $_POST['habitaciones'][0];
    $res = $m->CH_actualizacion_habitacion_reservacion01();
    $m->CH_actualizacion_habitacion_detallereservacion01();
    $_SESSION['temp']['reservacion_id'] = $m->nuevo_reservacion_id;
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $tabla_tarifa['reporte'] = $m->generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->generar_tabla_tarifas_paraeditar();


    /*
      $datos['desde'] = $_SESSION['busqueda01']['desde'];
      $datos['hasta'] = $_SESSION['busqueda01']['hasta'];
      $_SESSION['habitaciones_reservadas'] = $habitaciones_reservadas;

      $m->codigo = $habitaciones_reservadas[0]['codigo'];
      $_SESSION['prereservacion']['codigo'] = $habitaciones_reservadas[0]['codigo'];
      $prereservaciones = $m->ver_prereservaciones();
      $m->asignacion_tarifa_prereservacion();

      $tarifas = $m->combostarifa01($valor1);
     */

    echo $twig->render('cambiohabitacion01.html', array('dato' => $datos, 'reservacion' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}



/* CHECKIN */

if ($_GET['opcion'] == 'ocupaciones02') {

    aprobacion(10, 10);
    $_SESSION['aprobacion'] = 10;

    $m = new reservacionModelo();
    $reservaciones = $m->CHK_ver_reservaciones();

    if ($reservaciones) {
        $m->reservacion_id = $reservaciones[$i]['reservacion_id'];
        $reservaciones = $m->icono_garantiareservacion02($reservaciones);
        $reservaciones = $m->diasemana_reservacion($reservaciones);
        $reservaciones = $m->encriptar_codigo_get($reservaciones);
    }

    unset($_SESSION['checkin']);
    unset($_SESSION['habitaciones_reservadas']);
    unset($_SESSION['prereservacion']);
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['busqueda01']);
    unset($_SESSION['huesped']);
    unset($_SESSION['edc']);

    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'busquedacheckin') {
    /* variables entrada */

    aprobacion01($_GET['codigo']);

    /*
      $m = new reservacionModelo();

      $ocupacion = $m->reporte_ocupacion01();

      # print_r($ocupacion);
      #ocupacion_reservacion_id

      $_SESSION['checkout']['ocupacion_id'] = $ocupacion[0]['ocupacion_id'];

      #$m->codigo = $reservacion[0]['reservacion_codigo'];
      #$tabla_tarifa = $m->generar_tabla_tarifas01();

      echo $twig->render('checkin03.html', array('dato' => $datos, 'reservaciones' => $ocupacion));
     */

    $m = new reservacionModelo();
    $m->ocupacion_id = $m->decrypt($_GET['codigo']);
    $m->reservacion_id = $m->ocupacion_id_reservacion_codigo();
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();
    $_SESSION['temp']['reservacion_id'] = $m->reservacion_id;



    /*
      $_SESSION['cliente_id'] = $reservacion[0]['cliente_id'];
      $_SESSION['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
      $_SESSION['empresa_id'] = $reservacion[0]['empresa_id'];
      $_SESSION['reservacion_id'] = $reservacion[0]['reservacion_id'];
      $_SESSION['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
      $_SESSION['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
     */
    $_SESSION['temp']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['temp']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['temp']['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['temp']['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
    $_SESSION['temp']['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['ctacliente_id'] = $reservacion[0]['ctacliente_id'];

    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $ctahuesped = $m->get_ctahuesped();

    unset($_SESSION['checkin']);
    unset($_SESSION['habitaciones_reservadas']);
    unset($_SESSION['prereservacion']);
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['busqueda01']);
    unset($_SESSION['huesped']);
    unset($_SESSION['edc']);

    aprobacion01($ctahuesped);
    aprobacion01($reservacion);
    echo $twig->render('reservacion02.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}

if ($_POST["checkin001"]) {
    $m = new reservacionModelo();
    $config = new configuracionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->cliente_id = $reservacion[0]['cliente_id'];
    $cliente = $m->ver_cliente();

    $combo['tipodocumento'] = $config->combos(14, $cliente[0]['cliente_tipodocumento_id']);
    $combo['genero'] = $config->combos(39);
    $combo['civil'] = $config->combos(3, $cliente[0]['civil_id']);
    $combo['tipocliente'] = $config->combos(31, $cliente[0]['cliente_tipocliente_id']);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['formapago'] = $config->combos(9);
    $combo['motivoviaje'] = $config->combos(35);
    $combo['permiso'] = $config->combos(74);
    $combo['tipocredito'] = $config->combos(71);

    $_SESSION['checkin']['codigo'] = $reservacion[0]['reservacion_codigo'];
    $_SESSION['checkin']['codigo_id'] = $reservacion[0]['codigo_id'];
    $_SESSION['checkin']['reservacion_desde'] = $reservacion[0]['reservacion_desde'];
    $_SESSION['checkin']['reservacion_hasta'] = $reservacion[0]['reservacion_hasta'];
    $_SESSION['checkin']['habitacion_id'] = $reservacion[0]['habitacion_id'];
    $_SESSION['checkin']['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['checkin']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['checkin']['reservacion_id'] = $reservacion[0]['reservacion_id'];
    $_SESSION['checkin']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['checkin']['ocupacionreservacion'] = "No especifica";

    $reservacion[0]['cliente_nacimiento'] = $cliente[0]['cliente_nacimiento'];

    $m->codigoid = $_SESSION['checkin']['codigo_id'];
    $m->codigo = $reservacion[0]['reservacion_codigo'];

    $data = $m->ver_acompanantes();
    $acompanante = $m->reporte_tabla_acompanante($data);

    $tabla_tarifa = $m->generar_tabla_tarifas02();

    echo $twig->render('checkin01.html', array('tabla_tarifa' => $tabla_tarifa, 'dato' => $datos, 'reservaciones' => $reservacion, 'acompanante' => $acompanante, 'combo' => $combo));
    exit;
}

if ($_POST['checkinreservacion']) {

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->cliente_id = $reservacion[0]['cliente_id'];
    $m->garantiareservacion_id = $reservacion[0]['garantiareservacion_id'];
    $m->empresa_id = $reservacion[0]['empresa_id'];
    $m->codigo = $reservacion[0]['reservacion_codigoid'];
    $m->reservacion_desde = $reservacion[0]['reservacion_desde'];
    $m->reservacion_hasta = $reservacion[0]['reservacion_hasta'];

    $m->habitacion_id = $_SESSION['checkin']['habitacion_id'];


    $m->registro_cliente01($_POST);
    $m->registro_facturacion01($_POST);
    $m->registro_garantia_reservacion01($_POST);

    if ($m->validacion_fecha_checkin()) {
        $m->registro_ocupacion($_POST);
        $m->CHK_actualizacion_reservacion($_POST);

        $m->actualizacion_estatusreserva_checkin();
        $m->actualizacion_cargado_detallereservacion();
        $m->registro_amallaves02();


        /* listado de ocupaciones */
        $reservaciones = $m->CHK_ver_reservaciones();

        if ($reservaciones) {
            $m->reservacion_id = $reservaciones[$i]['reservacion_id'];
            $reservaciones = $m->icono_garantiareservacion02($reservaciones);
            $reservaciones = $m->diasemana_reservacion($reservaciones);
            $reservaciones = $m->encriptar_codigo_get($reservaciones);
        }

        unset($_SESSION['checkin']);
        unset($_SESSION['habitaciones_reservadas']);
        unset($_SESSION['prereservacion']);
        unset($_SESSION['ctahuesped']);
        unset($_SESSION['busqueda01']);
        unset($_SESSION['huesped']);
        unset($_SESSION['edc']);

        echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
    } else {
        $m->alertas("Disculpe la Fecha de Entrada  no coinciden con la Fecha Actual ");
        $_SESSION['temp']['reservacion_codigo'] = $reservacion['0']['reservacion_codigo'];
        $m->codigo = $_SESSION['temp']['reservacion_codigo'];
        $tabla_tarifa['reporte'] = $m->generar_tabla_tarifas02();
        $tabla_tarifa['edicion'] = $m->generar_tabla_tarifas_paraeditar();
        unset($_SESSION['tdc']);
        echo $twig->render('reservacion02.html', array('dato' => $datos, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
    }


    exit;
}

/* CHECK OUT */

if ($_GET['opcion'] == 'checkout_list') {

    aprobacion(10, 10);
    $_SESSION['aprobacion'] = 10;

    $m = new reservacionModelo();
    $reservaciones = $m->RSV_ver_reservaciones_checkout();

    if ($reservaciones) {
        $m->reservacion_id = $reservaciones[$i]['reservacion_id'];
        $reservaciones = $m->icono_garantiareservacion02($reservaciones);
        $reservaciones = $m->diasemana_reservacion($reservaciones);
        $reservaciones = $m->encriptar_codigo_get($reservaciones);
    }

    unset($_SESSION['checkin']);
    unset($_SESSION['habitaciones_reservadas']);
    unset($_SESSION['prereservacion']);
    unset($_SESSION['ctahuesped']);
    unset($_SESSION['busqueda01']);
    unset($_SESSION['huesped']);
    unset($_SESSION['edc']);

    echo $twig->render('reservacion05.html', array('dato' => $datos, 'reservaciones' => $reservaciones));
}

if ($_GET['opcion'] == 'busquedacheckout') {
    /* variables entrada */
    $m = new reservacionModelo();

//  $m->actualizacion_habitacion_detallereservacion01();

    aprobacion01($_GET['codigo']);
    $m->codigo = $m->decrypt($_GET['codigo']);
    $reservacion = $m->reporte_reservacion02();



    aprobacion01($reservacion);
    $m->reservacion_id = $reservacion[0]['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();



    /* $_SESSION['cliente_id'] = $reservacion[0]['cliente_id'];
      $_SESSION['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
      $_SESSION['empresa_id'] = $reservacion[0]['empresa_id'];
      $_SESSION['reservacion_id'] = $reservacion[0]['reservacion_id'];
      $_SESSION['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
      $_SESSION['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
     */

    $_SESSION['temp']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['temp']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['temp']['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['temp']['reservacion_id'] = $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
    $_SESSION['temp']['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['ctacliente_id'] = $reservacion[0]['ctacliente_id'];

    $ctahuesped = $m->get_ctahuesped();
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();

    unset($_SESSION['tdc']);
    aprobacion01($ctahuesped);
    echo $twig->render('reservacion07.html', array('dato' => $datos, 'cta' => $ctahuesped, 'reservaciones' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}



/* CAMBIO FECHA */

if ($_POST['extenderreservacion']) {


    $_SESSION['aprobacion'] = "CAMBI0FECHA";
    aprobacion01($_SESSION['temp']['reservacion_id']);

    $m = new reservacionModelo();
    $m->reservacion_id = $_SESSION['temp']['reservacion_id'];
    $m->CF_CtaHuesped();
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];

    $datos['vista_desde'] = $m->CF_verificar_detallereservacion_sin_cargos();

    if ($datos['vista_desde'] == 0) {
        $fecha = $m->CF_fechas_extremos_con_checkin();
    }

    if ($datos['vista_desde'] == 1) {
        $fecha = $m->CF_fechas_extremos_sin_checkin();
    }


    $m->desde = $fecha['desde'];
    $m->hasta = $fecha['hasta'];
    $hoy1 = date('d/m/Y');

    $hoy = $m->fecha_alreves($hoy1);
    $desde = $m->fecha_alreves($fecha['desde']);
    $hasta = $m->fecha_alreves($fecha['hasta']);
    $fechadesde = new DateTime($desde);
    $fechahasta = new DateTime($hasta);
    $fechahoy = new DateTime($hoy);

    $interval01 = $fechahoy->diff($fechadesde);
    $interval02 = $fechadesde->diff($fechahasta);

    $resultado01 = $interval01->format('%R%a');
    $resultado02 = $interval02->format('%R%a');


    $_SESSION['busqueda01']['desde'] = $datos['desde'] = $fecha['desde'];
    $_SESSION['busqueda01']['hasta'] = $datos['hasta'] = $fecha['hasta'];

    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $_SESSION['edc']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['edc']['codigoid'] = $reservacion[0]['codigo_id'];

    $datos['desde'] = $fecha['desde'];
    $datos['hasta'] = $fecha['hasta'];



    $_SESSION['temp']['vista_desde'] = $datos['vista_desde'];

    echo $twig->render('extenderreservacion.html', array('detalle' => $detalle, 'dato' => $datos, 'ctahuesped' => $ctahuesped, 'reservaciones' => $reservacion));
}

if ($_POST['extenderreservacion01']) {

    aprobacion01($_SESSION['temp']['reservacion_id']);
    aprobacion("CAMBI0FECHA", $_SESSION['aprobacion']);
    $_SESSION['aprobacion'] = 10;

    /* CON CARGOS  */
    if ($_SESSION['temp']['vista_desde'] == 0) {
        $m = new reservacionModelo();
        $m->reservacion_id = $_SESSION['temp']['reservacion_id'];

        $fecha = $m->CF_fechas_extremos_con_checkin();

        $fecha['hasta'] = $m->CF_restar_dias($fecha['hasta'], 1);

        $hastareves = $m->fecha_alreves($fecha['hasta']);
        $fechahasta = new DateTime($hastareves);


        if ($_POST['dias'] > 0) {
            $vardate = "P" . $_POST['dias'] . "D";
            $fechahasta->add(new DateInterval($vardate));
            $fecha_hasta = $fechahasta->format('d/m/Y');
            $m->desde = $m->fecha_alreves($fecha['hasta']);
            $m->hasta = $m->fecha_alreves($fecha_hasta);
        } else {
            $m->desde = $hastareves;
            $m->hasta = $m->fecha_alreves($_POST['hasta']);

            $fechadesde = new DateTime($m->desde);
            $fechahasta = new DateTime($m->hasta);
            $fechahoy = new DateTime($hoy);

            $interval01 = $fechahoy->diff($fechadesde);
            $interval02 = $fechadesde->diff($fechahasta);

            $resultado01 = $interval01->format('%R%a');
            $resultado02 = $interval02->format('%R%a');
        }


        $res = $m->CF_actualizacion_extension_reservacion_con_cargo();
        $m->CF_actualizacion_extension_detallereservacion();
        $reservacion = $m->reporte_reservacion06_reservacion_id();
        $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
        $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    }

    /* SIN CARGOS  */
    if ($_SESSION['temp']['vista_desde'] == 1) {


        $m = new reservacionModelo();
        $m->reservacion_id = $_SESSION['temp']['reservacion_id'];

        ;

        $desde2 = $m->fecha_alreves($_POST['desde']);
        $hasta2 = $m->fecha_alreves($_POST['hasta']);
        $fechadesde2 = new DateTime($desde2);
        $fechahasta2 = new DateTime($hasta2);
        $inter2 = $fechadesde2->diff($fechahasta2);
        $resultado02 = $inter2->format('%R%a');





        $m->desde = $desde2;
        $m->hasta = $hasta2;


        $m->CF_actualizacion_estatus_detallereservacion();

        $res = $m->CF_actualizacion_extension_reservacion_sin_cargo();
        $m->CF_actualizacion_extension_detallereservacion();
        $reservacion = $m->reporte_reservacion03();
        $m->codigo = $reservacion[0]['reservacion_codigo'];
        $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
        $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    }

    unset($_SESSION['temp']['vista_desde']);
    echo $twig->render('cambiohabitacion01.html', array('dato' => $datos, 'reservacion' => $reservacion, 'tablatarifa' => $tabla_tarifa));
}



/* CUENTA HUESPED */

if ($_GET['opcion'] == 'busquedactahuesped') {
    /* variables entrada */
    unset($_SESSION['ctahuesped']);
    $m = new reservacionModelo();
    $m->ctahuesped_id = $m->decrypt($_GET['codigo']);
    $ctahuesped = $m->get_ctahuesped();
    $_SESSION['ctahuesped']['ctahuesped_id'] = $ctahuesped['ctabasicos']['ctahuesped_id'];
    echo $twig->render('ctahuesped02.html', array('ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_GET['opcion'] == 'ctacliente') {
    $m = new reservacionModelo();
    $m->proceso_verificacion_pago_checkin();

    $ctahuesped = $m->CTH_detalle_ctahuespedes();
    $ctahuesped = $m->CTH_icono_garantiareservacion02($ctahuesped);

    echo $twig->render('ctahuesped01.html', array('dato' => $datos, 'ctahuesped' => $ctahuesped));
}

if ($_GET['opcion'] == 'nuevoresponsable') {

    $m = new reservacionModelo();
    $m->ctahuesped_id = $_SESSION['temp']['ctacliente_id'];
    $ctahuesped = $m->get_ctahuesped();
    $ctasasociadas = $m->ver_ctahuespedgrupo();
    $asociacionreservas = $m->ver_ctahuesped_asociacion();
    $clientes = $m->ver_clientes();
    $profiles = $m->ver_profiles();

    echo $twig->render('nuevoresponsable.html', array('profiles' => $profiles, 'clientes' => $clientes, 'ctasasociadas' => $ctasasociadas, 'dato' => $datos, 'ctahuesped' => $ctahuesped));
}


/* AMA DE LLAVES */

if ($_GET['opcion'] == 'amallaves') {
    $m = new reservacionModelo();
    $habitaciones = $m->RCK_ver_habitaciones_rack01();
    $resumen_amallaves = $m->AML_ver_resumen_rack();
    echo $twig->render('amallaves.html', array('dato' => $datos, 'habitaciones' => $habitaciones, 'resumen_amallaves' => $resumen_amallaves));
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

    if ($_POST['FS'])
        $m->estadoocupacion_id = 56;

    $m->AML_registro_amallaves01();
    $habitaciones = $m->ver_habitaciones_rack01();
    $resumen_amallaves = $m->AML_ver_resumen_rack();
    echo $twig->render('amallaves.html', array('dato' => $datos, 'habitaciones' => $habitaciones, 'resumen_amallaves' => $resumen_amallaves));
}

if ($_GET['opcion'] == 'tarjeta') {



    $m = new reservacionModelo();

//  $m->actualizacion_habitacion_detallereservacion01();

    aprobacion01($_GET['codigo']);
    $m->codigo = $m->decrypt($_GET['codigo']);
    $reservacion = $m->reporte_reservacion02();



    aprobacion01($reservacion);
    $m->reservacion_id = $reservacion[0]['reservacion_id'];
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $reservacion[0]['ctacliente_id'] = $m->buscar_ctahuesped();

    $_SESSION['temp']['cliente_id'] = $reservacion[0]['cliente_id'];
    $_SESSION['temp']['garantiareservacion_id'] = $reservacion[0]['garantiareservacion_id'];
    $_SESSION['temp']['empresa_id'] = $reservacion[0]['empresa_id'];
    $_SESSION['temp']['reservacion_id'] = $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['reservacion_codigo'] = $reservacion[0]['reservacion_codigo'];
    $_SESSION['temp']['reservacion_codigoid'] = $reservacion[0]['reservacion_codigo'] . "-" . $reservacion[0]['reservacion_id'];
    $_SESSION['temp']['ctacliente_id'] = $reservacion[0]['ctacliente_id'];

    $ctahuesped = $m->get_ctahuesped();
    $reservacion = $m->reporte_reservacion06_reservacion_id();
    $m->codigo = $reservacion[0]['reservacion_codigo'];
    $tabla_tarifa['reporte'] = $m->TRF_generar_tabla_tarifas02();
    $tabla_tarifa['edicion'] = $m->TRF_generar_tabla_tarifas_paraeditar();
    $m->cliente_id = $reservacion[0]['cliente_id'];
    $cliente = $m->TRD_ver_datos_clientes();


    unset($_SESSION['tdc']);
    aprobacion01($ctahuesped);
    echo $twig->render('tarjetaregistro.html', array('dato' => $datos, 'cliente' => $cliente, 'reservaciones' => $reservacion));
}


if ($_GET['opcion'] == 'dashboard02') {

    $m = new reservacionModelo();
    $reservaciones = $m->ver_reservaciones();
    $reservaciones = $m->icono_garantiareservacion02($reservaciones);
    $clientes = $m->ver_clientes();
    $checkin = $m->dashboard_tabla_ocupaciones();
    $supervision = $m->ver_supervisiones();
    $supervision = $m->icono_supervisiones02($supervision);
    $dashboard['ocupaciones'] = $m->dashboard_ocupaciones();
    $dashboard['reservaciones'] = $m->dashboard_reservaciones();

    for ($i = 0; $i < count($checkin); $i++) {
        $m->codigoid = $checkin[$i]['ocupacion_reservacion_codigo'];
        $checkin[$i]['personas'] = ($m->contar_acompanantes() + 1);
    }


    $dashboard['catidad_huespedes'] = $m->DSH2_huespedes_checkin();
    $dashboard['huespedes_list'] = $m->DSH2_huespedes_checkin_list();
    $dashboard['checkin_list'] = $m->DSH2_reservaciones_checkin_hoy();
    $dashboard['reservaciones_list'] = $m->DSH2_reservaciones_pendientes_list();

    $dashboard['reservaciones_pendientes'] = $m->DSH2_reservaciones_pendientes();
    $dashboard['cantidad_checkin'] = $m->DSH2_cantidad_checkin_hoy();
    $dashboard['cantidad_checkout'] = $m->DSH2_cantidad_checkout_hoy();
    $dashboard['habitaciones_vacantes'] = $m->DSH2_habitaciones_vacantes();
    $dashboard['ocupaciones'] = $m->DSH2_cantidad_ocupaciones();
    $dashboard['habitaciones_fueraservicio'] = $m->DSH2_habitaciones_fueraservicio();


    $dashboard['resumen_amallaves'] = $m->DSH2_ver_resumen_rack_amallaves();
    $dashboard['tarifa'] = $m->DSH2_tarifa_de_habitaciones();






    echo $twig->render('dashboard02.html', array('dashboard' => $dashboard, 'supervisiones' => $supervision, 'dato' => $datos, 'clientes' => $clientes, 'checkin' => $checkin, 'reservaciones' => $reservaciones));
}
?>








