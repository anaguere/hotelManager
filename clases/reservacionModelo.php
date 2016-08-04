<?php

require_once 'padreModelo.php';

class reservacionModelo extends padreModelo {

    public $desde = "";
    public $hasta = "";
    public $categoria = "1,2,3";
    public $numero_documento = "1,2,3";
    public $reservacion_id = "0";
    public $empresa_id = "0";
    public $cliente_id = "0";
    public $garantiareservacion_id = "0";
    public $codigo = "0";
    public $nombre_cliente = "";
    public $habitacion_id = "";
    public $reservacion_hasta = "";
    public $reservacion_desde = "";
    public $estadoocupacion_id = "";
    public $observacion = "";
    public $ocupacion_id = "";
    public $codigoid = "";
    public $usuario = "7";
    public $acompanante_id = "";
    public $idtabla;
    public $tabla;
    public $supervision_id = "";
    public $ctahuesped_id = "";
    public $ctaasociada_id = "";
    public $dia = "";
    public $pago_id = null;
    public $estatusreserva = "";
    public $tipotarifa = "";
    public $precio = "";
    public $valoriva = "";
    public $detallereservacion_id;
    public $tipocuenta_asociada = 86;
    public $estatusreserva_reservadasingarantia = 97;
    public $estatusreserva_reservadacongarantia = 98;
    public $estatusreserva_ocupadasinpago = 95;
    public $estatusreserva_ocupadaconpago = 96;
    public $estatusreserva_sinocuparsinpago = 106;
    public $estatusreserva_sinocuparconpago = 105;
    public $estatusreserva_paraelpago = "95,106,97,98";
    public $tipotarifa_completa = 102;
    public $listadoctaasociada_id;
    public $cargos;
    public $abonos;
    public $iva;
    public $saldo;
    public $listacargos;
    public $listado_id_detallesreservacion;
    public $nuevo_reservacion_id;
    public $pedido_id;
    public $diassemana = array("DOM", "LUN", "MAR", "MIE", "JUE", "VIE", "SAB");
    public $cargos_pedidos;
    public $iva_pedidos;
    public $listado_id_pedidos;

    public function iva() {
        return 12;
    }

    public function fecha_alreves($fecha) {
        $dd = substr($fecha, 0, 2);
        $mm = substr($fecha, 3, 2);
        $yy = substr($fecha, 6, 4);
        $fecha = $yy . "/" . $mm . "/" . $dd;
        return $fecha;
    }

    public function totalizar($arr, $campo, $bs = true) {
        $total = 0;
        for ($i = 0; $i < count($arr); $i++) {

            $total+= $arr[$i][$campo];
        }

        if ($bs)
            return $this->bs($total);
        if (!$bs)
            return $total;
    }

    public function valoriva() {
        return (12 / 100);
    }

    public function aplicar_deduccion01($arr) {

        return $this->bs($total);
    }

    public function diasentrefechas() {

        $fecha_entrada = new DateTime($this->desde);
        $fecha_salida = new DateTime($this->hasta);

        $interval = $fecha_entrada->diff($fecha_salida);
        $resultado = $interval->format('%R%a');


        for ($i = 0; $i <= $resultado; $i++) {

            $fechas[$i] = $fecha_entrada->format('d-m-Y');
            $v = 'P1D';
            $fecha_entrada->add(new DateInterval($v));
        }

        return $fechas;
    }

    public function restar_dias($fecha, $dias) {
        $obj = new padreModelo();
        $sql = "select to_char(('" . $fecha . "'::Date-" . $dias . "),'DD/MM/YYYY')  as resultado from reservacion limit 1";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['resultado'];
    }

    public function restar_fechas($desde, $hasta) {

        $obj = new padreModelo();
        $sql = "select ( ('" . $hasta . "'::date ) - '" . $desde . "'::date ) as resultado from reservacion limit 1";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['resultado'];
    }

    public function validateDate($date, $format = 'd-m-Y') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function bs($val) {

        return $val = number_format($val, 0, ",", ".");
    }

    public function bs2($val) {

        return $val = number_format($val, 2, ",", ".");
    }

    public function hora_actual() {

        $localtime_assoc = localtime(time(), true);
        return $localtime_assoc["tm_hour"] . ":" . $localtime_assoc["tm_min"];
    }

    public function seleccion_tabla_tarifa() {

        $var = $this->codigo;

        if ($var[0] == 'P') {
            $tarifa01 = $this->ver_prereservaciones();
        } else {
            $tarifa01 = $this->ver_reservaciones_tarifa();
        }

        return $tarifa01;
    }

    public function set_codigoid($codigo) {
        $cod = explode("-", $codigo);
        $this->codigo = $cod[0];
        $this->reservacion_id = $cod[1];
        $this->codigoid = $codigo;
    }

    public function personas_queen($name, $id, $valor) {
        $sal.="<select name='$name' style='' class='form-control form-control2' onchange='combo(" . $id . ",this.value)'>";
        $sal.="<option value='" . $valor . "'>" . $valor . "</option>";
        $sal.="<option value='1'>1</option>";
        $sal.="<option value='2'>2</option>";
        $sal.="<option value='3'>3</option>";
        $sal.="<option value='4'>4</option>";
        return $sal.="</select>";
    }

    public function motivo($valor1) {

        $sal.="<select name='motivo' style='' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from maestro where padre=35";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($valor1 == $dato['nombre1']) {
                $sal.="<option value='" . $dato['nombre1'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['nombre1'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function genero($name, $valor1) {

        $sal.="<select name='$name' style='' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from maestro where padre=39";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($valor1 == $dato['nombre2']) {
                $sal.="<option value='" . $dato['nombre2'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['nombre2'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function combomaestro($padre, $name, $valor1) {

        $sal.="<select name='$name' style='' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from maestro where padre='$padre' and estatu='true' ";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($valor1 == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }
    
    
    

    public function combosupervision($valor1) {

        $sal.="<select name='supervision_id'  onchange='supervision01(this.value)' id='supervision_id' style='' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from maestro where padre='43' and estatu='true' ";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($valor1 == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function combosupervision01($valor1) {

        $sal.="<select name='supervision_id' id='supervision01_id' style='' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from maestro where padre='" . $valor1 . "' and estatu='true' ";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($valor1 == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function personas_king($name, $id, $valor) {
        $sal.="<select name='$name' style='' class='form-control form-control2' onchange='combo($id,this.value)'>";
        $sal.="<option value='" . $valor . "'>" . $valor . "</option>";
        $sal.="<option value='1'>1</option>";
        $sal.="<option value='2'>2</option>";
        return $sal.="</select>";
    }

    public function combos_personas($name, $categoria, $id, $valor) {

        $sal.="<select name='$name' style='' class='form-control form-control2' onchange='combo(" . $id . ",this.value)'>";
        $sal.="<option value='" . $valor . "'>" . $valor . "</option>";
        $sal.="<option value='1'>1</option>";
        $sal.="<option value='2'>2</option>";
        $sal.="<option value='3'>3</option>";
        $sal.="<option value='4'>4</option>";
        return $sal.="</select>";
    }

    public function color_estadoocupacion($id) {

        if ($id == "19")
            return "success";
        if ($id == "20")
            return "primary";
        if ($id == "21")
            return "warning";
        if ($id == "22")
            return "danger";
    }

    public function icono_verificarocupacion($id) {

        if ($id == "O")
            return " <i class='fa fa-user'></i> ";
        else
            return "";
    }

    public function validaciones_ocupacion($checkout) {
        $hoy = date('d/m/Y');
        $hoy = $this->fecha_alreves($hoy);

        $fechahoy = new DateTime($hoy);
        $fechacheckout = new DateTime($checkout);
        $interval01 = $fechahoy->diff($fechacheckout);
        $resultado01 = $interval01->format('%R%a');

        if ($resultado01 >= 1) {
            return "<img src='dist/img/alertaverde.gif' /> <strong>" . $resultado01 . "<strong>";
        }

        if ($resultado01 < 0) {
            return "<img src='dist/img/alerta.gif' /> <strong>" . $resultado01 . "<strong>";
        }
    }

    public function ver_habitaciones() {
        $obj = new padreModelo();
        $sql = "select id from habitacion where estatu=true 
    and categoria_id in (" . $this->categoria . ") ";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $res[$i] = $data[$i]['id'];
        }
        return $res;
    }

    public function ver_estadoocupacion_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = "    select * from vw_amallaves where habitacion_id=" . $habitacion_id . " order by amallaves_id desc limit 1 ;";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function verificarocupacion_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = "  select * from ocupacion where habitacion_id='" . $habitacion_id . "' and estado='O';";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function ver_habitaciones_rack01() {
        $obj = new padreModelo();
        $sql = "    select * from vw_habitacion01 order by habitacion_y ,   habitacion_x";
        $data = $obj->ejecutar_query($sql);



        for ($i = 0; $i < count($data); $i++) {

            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_id'] = $data[$i]['habitacion_id'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_nombre'] = $data[$i]['habitacion_nombre'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_categoria'] = $data[$i]['habitacion_categoria'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_color'] = $data[$i]['habitacion_color'];
            $data2 = $this->ver_estadoocupacion_rack01($data[$i]['habitacion_id']);
            $data3 = $this->verificarocupacion_rack01($data[$i]['habitacion_id']);
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['verificacionocupacion_icono'] = $this->icono_verificarocupacion($data3['estado']);
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['validacionocupacion_icono'] = $this->validaciones_ocupacion($data3['fechahasta']);

            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['estadoocupacion_color'] = $this->color_estadoocupacion($data2['estadoocupacion_id']);
        }


        return $res;
    }

    public function ver_habitacion_rack01() {
        $obj = new padreModelo();
        $sql = "    select * from vw_habitacion01 where habitacion_id=" . $this->habitacion_id . ";";
        $data = $obj->ejecutar_query($sql);

        return $data[0];
    }

    public function no_disponibles() {

        $obj = new padreModelo();

        /*
          $sql = "select habitacion_id from estadohabitacion where
          ( (desde::date>='". $this->desde."' and desde::date<='" . $this->hasta . "') or
          (hasta::date>='" . $this->desde . "' and hasta::date<='" . $this->hasta . "')  or
          (desde::date<='" . $this->desde . "' and hasta::date>='" . $this->hasta . "') )

          "; */

        $sql = "select habitacion_id from reservacion where
        ( (desde::date>='" . $this->desde . "' and desde::date<='" . $this->hasta . "') or
        (hasta::date>='" . $this->desde . "' and hasta::date<='" . $this->hasta . "' ) or
        (desde::date<='" . $this->desde . "' and hasta::date>='" . $this->hasta . "' ) )
        and estatu = true and estadoreservacion_id in (2,1) ";


        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $res[$i] = $data[$i]['habitacion_id'];
        }

        return $res;
    }

    public function ver_disponibilidad() {
        $array1 = $this->no_disponibles();
        $array2 = $this->ver_habitaciones();
        $resultado = array_diff($array2, $array1);
        return $resultado;
    }

    public function salida_habitaciones_disponibles_detalle() {
        $obj = new padreModelo();
        $res = $this->ver_disponibilidad();


        $i = 0;
        foreach ($res as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $sql = "select * from vw_habitacion where habitacion_id in (" . $lista . ") ";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function salida_habitaciones_disponibles_resumen() {
        $obj = new padreModelo();
        $res = $this->ver_disponibilidad();
        $i = 0;
        foreach ($res as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $sql = "select count(categoria) as cantidad, categoria as categoria  , categoria_id as  categoria_id , color from vw_habitacion where habitacion_id in (" . $lista . ") group by categoria,color ,categoria_id";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    private function habitaciones_para_reservar01($categoria_id, $cantidad) {
        $obj = new padreModelo();
        $res = $this->ver_disponibilidad();


        $i = 0;
        foreach ($res as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }


        $sql = "select * from vw_habitacion where habitacion_id in (" . $lista . ") and categoria_id=" . $categoria_id . "  limit  " . $cantidad;
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function habitaciones_para_reservar02($categoria, $cantidad) {
        $x = 0;
        for ($i = 0; $i < count($categoria); $i++) {
            if ($cantidad[$i] > 0) {
                $habitaciones[$x] = $this->habitaciones_para_reservar01($categoria[$i], $cantidad[$i]);
                $x++;
            }
        }

        $codigo = "PR" . rand(10000, 90000);

        $y = 0;
        for ($i = 0; $i < count($habitaciones); $i++) {
            for ($x = 0; $x < count($habitaciones[$i]); $x++) {
                $habitaciones_reservar[$y]['nombre'] = $habitaciones[$i][$x]['nombre'];
                $habitaciones_reservar[$y]['categoria'] = $habitaciones[$i][$x]['categoria'];
                $habitaciones_reservar[$y]['habitacion_id'] = $habitaciones[$i][$x]['habitacion_id'];
                $habitaciones_reservar[$y]['categoria_id'] = $habitaciones[$i][$x]['categoria_id'];
                $habitaciones_reservar[$y]['color'] = $habitaciones[$i][$x]['color'];



                $reservacion_id = $this->registro_pre_reservacion($habitaciones[$i][$x]['habitacion_id'], $codigo);
                $this->registro_estadohabitacion($habitaciones[$i][$x]['habitacion_id'], 1);
                $habitaciones_reservar[$y]['reservacion_id'] = $reservacion_id;
                $habitaciones_reservar[$y]['codigo'] = $codigo;
                $habitaciones_reservar[$y]['combo'] = $this->combos_personas($habitaciones[$i][$x]['nombre'], $habitaciones[$i][$x]['categoria_id'], $reservacion_id);
                $y++;
            }
        }




        return $habitaciones_reservar;
    }

    public function habitaciones_para_reservar03($post, $habitaciones_reservadas) {

        $this->registro_cliente($post);
        $this->registro_garantia_reservacion($post);
        $this->registro_facturacion($post);


        $post['ch_tipocredito'] = 83;
        $post['ch_permiso'] = 84;
        $post['ch_tipocta'] = 85;

        $this->registro_ctacliente($post);
        $this->actualizar_ctahuesped_garantiareservacion();
        $this->registro_profile();

        for ($i = 0; $i < count($habitaciones_reservadas); $i++) {
            $this->reservacion_id = $habitaciones_reservadas[$i]['reservacion_id'];
            $this->habitacion_id = $habitaciones_reservadas[$i]['habitacion_id'];
            $codigo = $this->registro_reservacion($post);
            $this->registro_ctaclientereservacion($post);
            $this->proceso_registro_detallereservacion();
            $this->RSV_registro_huesped_reservacion();
        }

        return $codigo;
    }

    public function habitaciones_para_reservar04($habitacion) {

        $habitaciones = $this->habitaciones_para_reservar05($habitacion);
        $codigo = "PR" . rand(10000, 90000);

        $y = 0;
        for ($i = 0; $i < count($habitaciones); $i++) {

            $habitaciones_reservar[$y]['nombre'] = $habitaciones[$i]['nombre'];
            $habitaciones_reservar[$y]['categoria'] = $habitaciones[$i]['categoria'];
            $habitaciones_reservar[$y]['habitacion_id'] = $habitaciones[$i]['habitacion_id'];
            $habitaciones_reservar[$y]['categoria_id'] = $habitaciones[$i]['categoria_id'];
            $habitaciones_reservar[$y]['color'] = $habitaciones[$i]['color'];

            $reservacion_id = $this->registro_pre_reservacion($habitaciones[$i]['habitacion_id'], $codigo);
            $this->registro_estadohabitacion($habitaciones[$i]['habitacion_id'], 1);
            $habitaciones_reservar[$y]['reservacion_id'] = $reservacion_id;
            $habitaciones_reservar[$y]['codigo'] = $codigo;
            $habitaciones_reservar[$y]['combo'] = $this->combos_personas($habitaciones[$i]['nombre'], $habitaciones[$i]['categoria_id'], $reservacion_id);
            $y++;
        }

        return $habitaciones_reservar;
    }

    private function habitaciones_para_reservar05($habitaciones_id) {
        $obj = new padreModelo();


        $i = 0;
        foreach ($habitaciones_id as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }


        $sql = "select * from vw_habitacion where habitacion_id in (" . $lista . ") ;";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function habitaciones_para_recalcular01($habitaciones_reservadas) {

        $this->desactivar_reservacion();

        for ($i = 0; $i < count($habitaciones_reservadas); $i++) {
            $this->reservacion_id = $habitaciones_reservadas[$i]['reservacion_id'];
            $codigo = $this->registro_reservacion();
        }

        return $codigo;
    }

    public function busqueda_cliente() {
        $obj = new padreModelo();
        $sql = "select * from cliente where estatu=true and documento='" . $this->numero_documento . "' ";
        $data = $obj->ejecutar_query($sql);

        if ($data[0]['id'] > 0) {
            $data[0]['mensaje'] = "<i class='fa fa-check'></i> <i class='fa fa-user'></i> " . $data[0]['nombre'] . " " . $data[0]['apellido'];
            $data[0]['verificacion'] = true;
            $data[0]['verificacion'] = true;
            $data[0]['color'] = 'success';
        } else {
            $data[0]['mensaje'] = "<i class='fa fa-close'></i> <i class='fa fa-user'></i> Nuevo Huesped";
            $data[0]['verificacion'] = false;
            $data[0]['documento'] = $this->numero_documento;
            $data[0]['color'] = 'danger';
        }

        return $data[0];
    }

    public function desactivar_reservacion() {
        $objeto2 = new padreModelo();
        $objeto2->ejecutar_query("update reservacion set estadoreservacion_id=3 where codigo='" . $this->codigo . "';");
    }

    public function registro_garantia_reservacion($dato) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('garantiareservacion');
        $objeto2->add_data('cliente_id', $dato['cliente_id']);
        $objeto2->add_data('nombre_titular', $dato['nombre_titular']);
        $objeto2->add_data('documento_titular', $dato['documento_titular']);
        $objeto2->add_data('banco', $dato['banco']);
        $objeto2->add_data('codigo', $dato['codigo']);
        $objeto2->add_data('vencimiento', $dato['vencimiento']);
        $objeto2->add_data('referencia', $dato['referencia']);
        $objeto2->add_data('tipogarantia', $dato['tipogarantia']);
        $objeto2->add_data('numero', $dato['numero']);
        $objeto2->add_data('activacion', 'false');
        $objeto2->add_data('validacion', '');
        $objeto2->add_data('usuario_validador', '');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        $this->garantiareservacion_id = $objeto2->verId('garantiareservacion');
        return $this->garantiareservacion_id;
    }

    public function registro_garantia_reservacion01($dato) {
        /* PARA EL CHECKIN */



        $objeto2 = new padreModelo();
        $objeto2->setConfig('garantiareservacion', $this->garantiareservacion_id);
        $objeto2->add_data('cliente_id', $dato['cliente_id']);
        $objeto2->add_data('nombre_titular', $dato['nombre_titular']);
        $objeto2->add_data('documento_titular', $dato['documento_titular']);
        $objeto2->add_data('banco', $dato['banco']);
        $objeto2->add_data('codigo', $dato['codigo']);
        $objeto2->add_data('vencimiento', $dato['vencimiento']);
        $objeto2->add_data('numero', $dato['numero']);
        $objeto2->add_data('referencia', $dato['referencia']);
        $objeto2->add_data('tipogarantia', $dato['tipogarantia']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('observacion', $dato['garantiaobservacion']);
        $objeto2->add_data('montobloqueo', $dato['montobloqueo']);
        $objeto2->add_data('activacion', 'false');
        $objeto2->add_data('validacion', '');
        $objeto2->add_data('usuario_validador', '');
        $objeto2->ejecutar();
# $this->garantiareservacion_id = $dato['garantiareservacion_id'];
        return $this->garantiareservacion_id;
    }

    public function registro_validacion_garantia_reservacion01($dato) {
        /* PARA EL CHECKIN */

        if ($dato['aprobar'])
            $aprobacion = 'true';
        if ($dato['desaprobar'])
            $aprobacion = 'false';

        $objeto2 = new padreModelo();
        $objeto2->setConfig('garantiareservacion', $this->garantiareservacion_id);
        $objeto2->add_data('cliente_id', $dato['cliente_id']);
        $objeto2->add_data('nombre_titular', $dato['nombre_titular']);
        $objeto2->add_data('documento_titular', $dato['documento_titular']);
        $objeto2->add_data('banco', $dato['banco']);
        $objeto2->add_data('codigo', $dato['codigo']);
        $objeto2->add_data('vencimiento', $dato['vencimiento']);
        $objeto2->add_data('numero', $dato['numero']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('observacion', $dato['garantiaobservacion']);
        $objeto2->add_data('activacion', $aprobacion);
        $objeto2->add_data('validacion', 'NOW()');
        $objeto2->add_data('usuario_validador', $this->usuario);

        $objeto2->ejecutar();
# $this->garantiareservacion_id = $dato['garantiareservacion_id'];
        return $this->garantiareservacion_id;
    }

    public function registro_facturacion03($dato) {

        $obj = new padreModelo();
        $sql = "select empresa_id from ocupacion where id='" . $this->ocupacion_id . "' ";
        $data = $obj->ejecutar_query($sql);



        if (strlen($data[0]['empresa_id']) > 0) {
            $this->empresa_id = $data[0]['empresa_id'];
            $this->registro_facturacion01($dato);
        } else {
            if ((strlen($dato['nombre_empresa']) > 2) && (strlen($dato['rif_empresa']) > 7)) {
                $this->registro_facturacion($dato);
                $this->actualizar_facturacion_ocupacion();
            }
        }
    }

    public function registro_facturacion($dato) {

        if (strlen($dato['nombre_empresa']) < 1)
            return 0;

        $objeto2 = new padreModelo();
        $objeto2->setConfig('empresa');
        $objeto2->add_data('nombre', $dato['nombre_empresa']);
        $objeto2->add_data('rif', $dato['rif_empresa']);
        $objeto2->add_data('direccion', $dato['direccion_empresa']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        $this->empresa_id = $objeto2->verId('empresa');

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('empresa_id', $this->empresa_id);
        $objeto2->ejecutar();

        return $this->empresa_id;
    }

    public function registro_facturacion01($dato) {
        /* PARA EL CHECKIN */
        $objeto2 = new padreModelo();
        $objeto2->setConfig('empresa', $this->empresa_id);
        $objeto2->add_data('nombre', $dato['nombre_empresa']);
        $objeto2->add_data('rif', $dato['rif_empresa']);
        $objeto2->add_data('direccion', $dato['direccion_empresa']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->empresa_id = $dato['empresa_id'];
        return $this->empresa_id;
    }

    public function registro_facturacion02($dato) {

        if ($m->empresa_id > 0) {

            $objeto2->setConfig('empresa', $m->empresa_id);
            $objeto2->add_data('nombre', $dato['nombre_empresa']);
            $objeto2->add_data('rif', $dato['rif_empresa']);
            $objeto2->add_data('direccion', $dato['direccion_empresa']);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
            $this->empresa_id = $data[0]['empresa'];
            return $this->empresa_id;
        }





        /* PARA EL CHECKIN */
    }

    public function actualizar_facturacion_ocupacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('ocupacion', $this->ocupacion_id);
        $objeto2->add_data('empresa_id', $this->empresa_id);
        $objeto2->ejecutar();
    }

    public function actualizacion_cantidad_personas($id, $cantidad) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $id);
        $objeto2->add_data('personas', $cantidad);
        $objeto2->ejecutar();
    }

    public function actualizacion_tipotarifa($idreservacion, $idtipotarifa) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $idreservacion);
        $objeto2->add_data('tarifa_id', $idtipotarifa);
        $objeto2->ejecutar();
    }

    public function actualizacion_detallereservacion_precio($precio) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);
        $objeto2->add_data('precio', $precio);
        $objeto2->ejecutar('reservacion_id');
    }

    public function registro_cliente($dato) {

        $empresa = strlen($dato['nombre_empresa']);


        $objeto2 = new padreModelo();
        $sql = "  select  count(*) as cantidad from cliente where documento='" . $dato['documento'] . "'  ;";
        $data = $objeto2->ejecutar_query($sql);


        if ($dato[0]['cantidad'] > 0) {
            $objeto2->setConfig('cliente', $data[0]['id']);
        } else {
            $objeto2->setConfig('cliente');
        }

        $objeto2->add_data('empresa_id', $empresa);
        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('apellido', $dato['apellido']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('nacionalidad', $dato['nacionalidad']);
        $objeto2->add_data('idioma', $dato['idioma']);
        $objeto2->add_data('pais', $dato['pais']);
        $objeto2->add_data('ciudad', $dato['ciudad']);
        $objeto2->add_data('vip', $dato['vip']);
        $objeto2->add_data('preferencia', $dato['preferencia']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->ejecutar();

        if ($dato[0]['cantidad'] > 0) {
            $this->cliente_id = $data[0]['id'];
        } else {
            $this->cliente_id = $objeto2->verId('cliente');
        }

        return $this->cliente_id;
    }

    public function registro_cliente01($dato) {
        /*  PARA EL CHECKIN */

        $objeto2 = new padreModelo();
        $objeto2->setConfig('cliente', $this->cliente_id);
        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('apellido', $dato['apellido']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('movil', $dato['movil']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('nacionalidad', $dato['nacionalidad']);
        $objeto2->add_data('nacimiento', $dato['nacimiento']);
        $objeto2->add_data('genero', $dato['genero']);
        $objeto2->add_data('civil', $dato['civil']);
        $objeto2->add_data('tipo_documento', $dato['tipo_documento']);
        $objeto2->add_data('tipocliente_id', $dato['clientetipo_id']);
        $objeto2->add_data('observacion', $dato['clienteobservacion']);
        $objeto2->add_data('profesion', $dato['profesion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        return $this->cliente_id;
    }

    public function registro_cliente02($dato) {
        /*  PARA EL CHECKIN */

        $objeto2 = new padreModelo();
        $objeto2->setConfig('cliente', $this->cliente_id);
        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('apellido', $dato['apellido']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('nacionalidad', $dato['nacionalidad']);
        $objeto2->add_data('idioma', $dato['idioma']);
        $objeto2->add_data('pais', $dato['pais']);
        $objeto2->add_data('ciudad', $dato['ciudad']);
        $objeto2->add_data('vip', $dato['vip']);
        $objeto2->add_data('preferencia', $dato['preferencia']);

        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        return $this->cliente_id;
    }

    public function registro_pre_reservacion($habitacion_id, $codigo) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion');
        $objeto2->add_data('cliente_id', 1);
        $objeto2->add_data('habitacion_id', $habitacion_id);
        $objeto2->add_data('estadoreservacion_id', '0');
        $objeto2->add_data('desde', $this->desde);
        $objeto2->add_data('hasta', $this->hasta);
        $objeto2->add_data('codigo', $codigo);
        $objeto2->add_data('personas', 1);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('tarifa_id', 1);
        $objeto2->ejecutar();
        $reservacion_id = $objeto2->verId('reservacion');
        return $reservacion_id;
    }

    public function registro_estadohabitacion($habitacion_id, $modohabitacion_id) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('estadohabitacion');
        $objeto2->add_data('habitacion_id', $habitacion_id);
        $objeto2->add_data('modohabitacion_id', $modohabitacion_id);
        $objeto2->add_data('desde', $this->desde);
        $objeto2->add_data('hasta', $this->hasta);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function registro_reservacion($post = "") {
        $codigo = $this->generar_codigo();
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('estadoreservacion_id', 2);
        $objeto2->add_data('garantiareservacion_id', $this->garantiareservacion_id);
        $objeto2->add_data('empresa_id', $this->empresa_id);
        $objeto2->add_data('codigo', $codigo);
        $objeto2->add_data('contacto', $post['contacto']);
        $objeto2->add_data('contactoinfo', $post['contactoinfo']);
        $objeto2->add_data('medioreservacion', $post['medioreservacion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->codigo = $codigo;
        return $codigo;
    }

    public function actualizar_tarifa_manual($post) {

        $objeto = new padreModelo();
        $objeto->setConfig('cambiotarifa');
        $objeto->add_data('reservacion_id', $this->reservacion_id);
        $objeto->add_data('preciounitario', $post['preciounitario']);
        $objeto->add_data('motivo', $post['motivo']);
        $objeto->add_data('usuario', $this->usuario);
        $objeto->ejecutar();

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('precio_unitario', $post['preciounitario']);
        $objeto2->ejecutar();

        $objeto3 = new padreModelo();
        $objeto3->setConfig('detallereservacion', $this->reservacion_id);
        $objeto3->add_data('precio', $post['preciounitario']);
        $objeto3->add_data('valoriva', $this->iva());
        $objeto3->ejecutar('reservacion_id');

        return true;
    }

    public function cantidad_dias() {
        $obj = new padreModelo();
        $sql = "select  to_char(hasta - desde,'DD')  as dias  from reservacion where id=" . $this->reservacion_id;
        $data = $obj->ejecutar_query($sql);
        return $data[0]['dias'];
    }

    public function registro_reservacion01($precio, $iva, $total, $tarifa) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('precio_unitario', $precio);
        $objeto2->add_data('iva', $total);
        $objeto2->add_data('total', ( $total * $this->cantidad_dias()));
        $objeto2->add_data('tarifa', $tarifa);
        $objeto2->add_data('valoriva', $iva);
        $objeto2->ejecutar();
    }

    public function registro_reservacion02($dato) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('estadoreservacion_id', 3);
        $objeto2->add_data('observacion_cancelacion', $dato['observacionreservacion']);
        $objeto2->add_data('usuario_cancelacion', $this->usuario);
        $objeto2->add_data('fecha_cancelacion', 'NOW()');
        $objeto2->ejecutar();
    }

    public function registro_reservacion03() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('estadoreservacion_id', 1);
        $objeto2->ejecutar();
    }

    public function registro_reservacion04($post) {

        $obj = new padreModelo();
        $sql = "select * from reservacion where id='" . $this->reservacion_id . "' and estatu='true' limit 1;";
        $data = $obj->ejecutar_query($sql);

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', "'" . $data[0]['codigo'] . "'");
        $objeto2->add_data('contacto', $post['contacto']);
        $objeto2->add_data('contactoinfo', $post['contactoinfo']);
        $objeto2->add_data('medioreservacion', $post['medioreservacion']);
        $objeto2->add_data('origen', $post['origen']);
        $objeto2->add_data('destino', $post['destino']);
        $objeto2->add_data('formapago_id', $post['formapago_id']);
        $objeto2->ejecutar('codigo');
    }

    public function actualizacion_checkin_reservacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('checkin', 'NOW');
        $objeto2->ejecutar();
    }

    public function registro_ocupacion($post) {

        $obj = new padreModelo();
        $sql = "select id from ocupacion where reservacion_id in ('" . $this->reservacion_id . "') and estatu=true  ";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['id'] > 0)
            return false;

        $objeto2 = new padreModelo();
        $objeto2->setConfig('ocupacion');
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->add_data('reservacion_codigo', $this->codigo);
        $objeto2->add_data('fechadesde', $this->reservacion_desde);
        $objeto2->add_data('fechahasta', $this->reservacion_hasta);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('formapago_id', $post['formapago_id']);
        $objeto2->add_data('modoocupacion_id', 1);
        $objeto2->add_data('checkin', date('d-m-Y'));
        $objeto2->add_data('checkout', "");
        $objeto2->add_data('motivo', $post['motivo']);
        $objeto2->add_data('horaentrada', $this->hora_actual());
        $objeto2->add_data('horasalida', "");
        $objeto2->add_data('observacion', $post['ocupacionreservacion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('estado', 'O');
        $objeto2->add_data('garantiareservacion_id', $this->garantiareservacion_id);
        $objeto2->add_data('empresa_id', $this->empresa_id);
        $objeto2->ejecutar();
        $ocupacion_id = $objeto2->verId('ocupacion');
        return $ocupacion_id;
    }

    public function registro_ocupacion01($dato) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('ocupacion', $this->ocupacion_id);
        $objeto2->add_data('motivo', $dato['motivo']);
        $objeto2->add_data('formapago_id', $dato['formapago_id']);
        $objeto2->ejecutar();
        return $this->ocupacion_id;
    }

    public function registro_checkout() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('ocupacion', $this->ocupacion_id);
        $objeto2->add_data('checkout', 'NOW()');
        $objeto2->add_data('observacion', $this->observacion);
        $objeto2->add_data('estado', 'L');
        $objeto2->add_data('usuario_checkout', $this->usuario);
        $objeto2->ejecutar();
        return $this->ocupacion_id;
    }

    public function ocupacion_rack01() {
        $obj = new padreModelo();
        $sql = "select * from vw_ocupacion where habitacion_id=" . $this->habitacion_id . " and ocupacion_estado='O'; ";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function registro_amallaves02() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('amallaves');
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('estadoocupacion_id', 21);
        $objeto2->add_data('observacion', $this->observacion);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $amallaves_id = $objeto2->verId('amallaves');
        return $amallaves_id;
    }

    public function ver_reservaciones() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where estadoreservacion_id in (2) order by reservacion_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function icono_garantiareservacion01() {

        if (!$this->garantiareservacion_id)
            return "";
        $obj = new padreModelo();
        $sql = "select * from garantiareservacion where id=" . $this->garantiareservacion_id;
        $data = $obj->ejecutar_query($sql);

        return $data[0];
    }

    public function icono_cantidadpersonas01() {
        $obj = new padreModelo();
        $sql = "select personas from reservacion where id=" . $this->reservacion_id;
        $data = $obj->ejecutar_query($sql);
        return $data[0]['personas'];
    }

    public function icono_garantiareservacion02($data) {

        for ($i = 0; $i < count($data); $i++) {
            $this->garantiareservacion_id = $data[$i]['garantiareservacion_id'];

            $garantia = $this->icono_garantiareservacion01();

            #      $data[$i]['personas'] = $this->icono_cantidadpersonas01();

            if (strlen($garantia['referencia']) > 0)
                $data[$i]['garantia'] = "<i class='fa fa-credit-card text-green'></i>";
            else
                $data[$i]['garantia'] = "<i class='fa fa-credit-card text-red'></i>";
        }

        return $data;
    }

    public function diasemana_reservacion($data) {

        for ($i = 0; $i < count($data); $i++) {

            $data[$i]['semana_desde'] = $this->diassemana[date('N', strtotime($this->fecha_alreves($data[$i]['reservacion_desde'])))];
            $data[$i]['semana_hasta'] = $this->diassemana[date('N', strtotime($this->fecha_alreves($data[$i]['reservacion_hasta'])))];
        }


        return $data;
    }

    public function encriptar_codigo_get($data) {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['get'] = $this->encrypt($data[$i]['codigo_id']);
        }
        return $data;
    }

    public function encriptar_codigo_get_checkin($data) {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['get'] = $this->encrypt($data[$i]['ocupacion_id']);
        }
        return $data;
    }

    public function icono_supervisiones02($data) {

        for ($i = 0; $i < count($data); $i++) {

            if ($data[$i]['supervision_solucionado'] == 'SI')
                $data[$i]['supervision_solucionado'] = "<i class='fa  fa-check-circle text-green'></i>";
            if ($data[$i]['supervision_solucionado'] == 'NO')
                $data[$i]['supervision_solucionado'] = "<i class='fa fa-exclamation-circle text-red'></i>";
        }

        return $data;
    }

    public function ver_prereservaciones() {
        $obj = new padreModelo();
        $sql = "select * from vw_prereservacion_tarifas where reservacion_codigo='" . $this->codigo . "';";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combo'] = $this->combos_personas($data[$i]['habitacion_nombre'], $data[$i]['categoria_id'], $data[$i]['reservacion_id'], $data[$i]['reservacion_personas']);
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combotarifa'] = $this->combostarifa01($data[$i]['reservacion_id']);
        }

        return $data;
    }

    public function ver_reservaciones_tarifa() {
        $obj = new padreModelo();
        $sql = "select * from vw_prereservacion_tarifas_todas where reservacion_codigo='" . $this->codigo . "' and reservacion_estadoreservacion_id in (2,1);";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combo'] = $this->combos_personas($data[$i]['habitacion_nombre'], $data[$i]['categoria_id'], $data[$i]['reservacion_id'], $data[$i]['reservacion_personas']);
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combotarifa'] = $this->combostarifa01($data[$i]['reservacion_id']);
        }

        return $data;
    }

    public function ver_ocupaciones() {
        $obj = new padreModelo();
        $sql = "select * from vw_ocupacion where ocupacion_estado='O' order by ocupacion_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    private function generar_codigo() {
        $code = date('dym') . date('z') . $this->cliente_id;
        return $code;
    }

    public function reporte_reservacion01() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where reservacion_codigo='" . $this->codigo . "' and estadoreservacion_id in (2,1) ";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function reporte_reservacion02() {


        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where codigo_id='" . $this->codigo . "' ";
        $data = $obj->ejecutar_query($sql);

        if (($data[0]['estadoreservacion_id'] == 2) || ($data[0]['estadoreservacion_id'] == 1) || ($data[0]['estadoreservacion_id'] == 4)) {

            return $data;
        } else {

            $obj = new padreModelo();
            $sql = "select * from vw_reservacion where reservacion_codigo='" . $data[0]['reservacion_codigo'] . "'  and  estadoreservacion_id in (2,1,4) ";
            $data = $obj->ejecutar_query($sql);
            return $data;
        }
    }

    public function buscar_ctahuesped() {
        $obj = new padreModelo();
        $sql = "select ctacliente_id from detallereservacion where estatu=true and reservacion_id in ('" . $this->reservacion_id . "') order by id desc limit 1";
        $data = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data[0]['ctacliente_id'];
        return $data[0]['ctacliente_id'];
    }

    public function reporte_reservacion03() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where reservacion_id='" . $this->reservacion_id . "' and  estadoreservacion_id in (2) ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function reporte_reservacion04() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 where reservacion_id='" . $this->reservacion_id . "' ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function reporte_reservacion05() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 where reservacion_codigo='" . $this->codigo . "' ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function reporte_ocupacion01() {
        $obj = new padreModelo();
        $sql = "select * from vw_ocupacion where ocupacion_id='" . $this->ocupacion_id . "' ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function reporte_ocupacion02() {
        $obj = new padreModelo();
        $sql = "select * from vw_ocupacion where ocupacion_estado='L' order by ocupacion_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    /* TARIFAS  */

    public function ajustedetarifas01($precio, $iva, $total, $tarifa) {

        $obj = new padreModelo();
        $sql = "select  tarifa_id  from reservacion where id=" . $this->reservacion_id;
        $data = $obj->ejecutar_query($sql);

        $sql = "select  *  from administrativo.tarifa where id=" . $data[0]['tarifa_id'];
        $tarifa1 = $obj->ejecutar_query($sql);


        if (strlen($tarifa1[0]['descuento']) < 1) {
            $salida[0]['servicio_precio'] = $tarifa1[0]['costofijo'];
            $salida[0]['servicio_iva'] = $iva;
            $salida[0]['servicio_total'] = ( $tarifa1[0]['costofijo'] * ($iva / 100) ) + $tarifa1[0]['costofijo'];
            $salida[0]['servicio_denominacion'] = $tarifa;
            return $salida;
        }


        if ($tarifa1[0]['descuento'] == 0) {
            $salida[0]['servicio_precio'] = $precio;
            $salida[0]['servicio_iva'] = $iva;
            $salida[0]['servicio_total'] = $total;
            $salida[0]['servicio_denominacion'] = $tarifa;
            return $salida;
        }

        if ($tarifa1[0]['descuento'] == 100) {
            $salida[0]['servicio_precio'] = $precio;
            $salida[0]['servicio_iva'] = $iva;
            $salida[0]['servicio_total'] = 0;
            $salida[0]['servicio_denominacion'] = " 100% DESCUENTO ";
            return $salida;
        }

        if ($tarifa1[0]['descuento'] > 0) {
            $nuevoprecio = $total * ($tarifa1[0]['descuento'] / 100);
        }

        $salida[0]['servicio_precio'] = $precio;
        $salida[0]['servicio_iva'] = $iva;
        $salida[0]['servicio_total'] = $nuevoprecio;
        $salida[0]['servicio_denominacion'] = $tarifa / $tarifa1[0]['denominacion'];

        return $salida;
    }

    public function ajustedetarifa($precio, $iva, $total, $tarifa) {

        $obj = new padreModelo();
        $sql = "select  tarifa_id  from reservacion where id=" . $this->reservacion_id;
        $data = $obj->ejecutar_query($sql);

        $sql = "select  *  from administrativo.tarifa where id=" . $data[0]['tarifa_id'];
        $tarifa1 = $obj->ejecutar_query($sql);


        if (strlen($tarifa1[0]['descuento']) < 1) {
            $salida[0]['servicio_precio'] = $tarifa1[0]['costofijo'];
            $salida[0]['servicio_iva'] = $iva;
            $salida[0]['servicio_total'] = ( $tarifa1[0]['costofijo'] * ($iva / 100) ) + $tarifa1[0]['costofijo'];
            $salida[0]['servicio_denominacion'] = $tarifa;
            return $salida;
        }


        if ($tarifa1[0]['descuento'] == 0) {
            $salida[0]['servicio_precio'] = $precio;
            $salida[0]['servicio_iva'] = $iva;
            $salida[0]['servicio_total'] = $total;
            $salida[0]['servicio_denominacion'] = $tarifa;
            return $salida;
        }

        if ($tarifa1[0]['descuento'] == 100) {
            $salida[0]['servicio_precio'] = 0;
            $salida[0]['servicio_iva'] = 12;
            $salida[0]['servicio_total'] = 0;
            $salida[0]['servicio_denominacion'] = " 100% DESCUENTO ";
            return $salida;
        }

        if ($tarifa1[0]['descuento'] > 0) {
            $nuevoprecio = $precio * ($tarifa1[0]['descuento'] / 100);
        }

        $nuevoprecioiva = $nuevoprecio * ($iva / 100);
        $nuevoiva = $nuevoprecioiva + $nuevoprecio;

        $salida[0]['servicio_precio'] = $nuevoprecio;
        $salida[0]['servicio_iva'] = $iva;
        $salida[0]['servicio_total'] = $nuevoiva;
        $salida[0]['servicio_denominacion'] = $tarifa / $tarifa1[0]['denominacion'];

        return $salida;
    }

    public function combostarifa01($reservacion_id) {

        if ($reservacion_id) {
            $obj1 = new padreModelo();
            $sql = "select tarifa_id from reservacion  where  id in (" . $reservacion_id . ");";
            $data = $obj1->ejecutar_query($sql);
            $tarifa_id = $data[0]['tarifa_id'];
        }

        $sal.="<select name='combotarifa_id' id='combotarifa_id' style='width:100%'  onchange='combotarifa(" . $reservacion_id . ",this.value)' class='form-control form-control2 ' >";
        $obj = new padreModelo();
        $sql = "select * from administrativo.tarifa where  estatu='true' order by id asc ";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($tarifa_id == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['denominacion'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['denominacion'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function calculo_tarifa($personas, $categoria_id) {

        $obj = new padreModelo();
        $sql = "select * from vw_tarifa where tarifa_personas='" . $personas . "' and  tarifa_categoria_id='" . $categoria_id . "' limit 1 ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function asignacion_tarifa_prereservacion() {

        $prereservaciones = $this->ver_prereservaciones();


        for ($i = 0; $i < count($prereservaciones); $i++) {
            $this->reservacion_id = $prereservaciones[$i]['reservacion_id'];
            $this->tarifa01();
        }
    }

    public function generar_tabla_tarifas02() {
        $tarifa01 = $this->ver_reservaciones_tarifa();
        $tabla_tarifa = $this->reporte_tabla_tarifa02($tarifa01);
        return $tabla_tarifa;
    }

    public function generar_tabla_tarifas() {
#  $tarifa01 = $this->reporte_reservacion05();
        $tarifa01 = $this->ver_prereservaciones();
        $tabla_tarifa = $this->reporte_tabla_tarifa($tarifa01);
        return $tabla_tarifa;
    }

    public function generar_tabla_tarifas_paraeditar() {
#  $tarifa01 = $this->reporte_reservacion05();
        $tarifa01 = $this->ver_reservaciones_tarifa();
        $tabla_tarifa = $this->reporte_tabla_tarifa($tarifa01);
        return $tabla_tarifa;
    }

    public function generar_tabla_tarifas01() {
        $tarifa01 = $this->reporte_reservacion05();
        $tabla_tarifa = $this->reporte_tabla_tarifa01($tarifa01);
        return $tabla_tarifa;
    }

    public function tarifa01() {
        $this->actualizacion_cantidad_personas($this->reservacion_id, 1);
        $reservacion = $this->reporte_reservacion04();
        $tarifa = $this->calculo_tarifa(1, $reservacion[0]['habitacion_categoria_id']);
        $dias = $this->cantidad_dias();
        $total = $tarifa[0]['servicio_total'];
        $this->registro_reservacion01($tarifa[0]['servicio_precio'], $tarifa[0]['iva'], $total, $tarifa[0]['servicio_denominacion']);
    }

    public function reporte_tabla_tarifa($tarifa01) {



        $total = 0;
        $tabla.="    
  <div id='cambiotarifa' style='padding:20px;'> </div>            

        <table class = 'table table-hover' style = 'font-size: 13px'>
        <tr>
            <th style = 'text-align:left;width:15%'>Categoria</th>
            <th style = 'text-align:left;width:15%'>Tarifa</th>            
            <th style = 'text-align:center;width:10%'>N° Pers.</th>
            <th style = 'text-align:center;width:15%'>Desde</th>
            <th style = 'text-align:center;width:15%'>Hasta</th>            
            <th style = 'text-align:center;width:10%'>Noches</th>
            <th style = 'text-align:right;width:10%'>Costo</th>         
            <th style = 'text-align:right;width:10%'>Total</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {
            $actual = 0;

            if ($_SESSION['temp']['reservacion_id']) {
                if ($tarifa01[$i]['reservacion_id'] == $_SESSION['temp']['reservacion_id']) {
                    $actual = 1;
                }
            }

            if ($actual == 1)
                $tabla.="<tr style='background:#F2F5A9'>";
            else
                $tabla.="<tr>";

            $tabla.=" 
               
                    <td  style='text-align:left;'><span style='color:red'><strong>" . $tarifa01[$i]['habitacion_nombre'] . "</strong> </span>" . $tarifa01[$i]['habitacion_categoria'] . "</td>
                    <td  style='text-align:left;'>" . $tarifa01[$i]['combotarifa'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['combo'] . "</td>         
                     <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_desde'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_hasta'] . "</td>                           
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' ><a  onclick='editar(" . $tarifa01[$i]['reservacion_id'] . ")'><i class='fa fa-pencil'></i></a>  " . $this->bs2($tarifa01[$i]['servicio_precio']) . "</td>                  
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias']) . "</td>                                  
                </tr>";

            $total = ( $tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias'] ) + $total;
            $iva = ($total * $this->valoriva());
        }

        $tabla.="
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>         
            <td>            </td>              
            <td>            </td>         
            <td>            </td>             
            <td  style='text-align:right;font-size:14px; font-weight: bold' >  Impuestos </td>
            <td  style='text-align:right; font-size:14px; font-weight: bold;' >" . $this->bs2($iva) . "</td>                                  
        </tr>            
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>        
            <td>            </td>         
            <td>            </td>             
            <td>            </td>              
            <td  style='text-align:right;font-size:15px; font-weight: bold' >  TOTAL </td>
            <td  style='text-align:right; font-size:15px; font-weight: bold;' >" . $this->bs2($total + $iva) . "</td>                                  
        </tr>
        </table>";

        return $tabla;
    }

    public function reporte_tabla_tarifa__($tarifa01) {

        $total = 0;
        $tabla.="    
        <table class = 'table table-hover' style = 'font-size: 14px'>
        <tr>
            <th style = 'text-align:left;width:100px'>Categoria</th>
            <th style = 'text-align:left;width:100px'>Tarifa</th>            
            <th style = 'text-align:center;width:100px'>N° Pers.</th>
            <th style = 'text-align:center;width:100px'>Noches</th>
            <th style = 'text-align:right;width:100px'>Costo</th>         
            <th style = 'text-align:right;width:100px'>Total</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {

            $tabla.=" 
                <tr>
                    <td  style='text-align:left;'><small>" . $tarifa01[$i]['habitacion_nombre'] . "</small>" . $tarifa01[$i]['habitacion_categoria'] . "</td>
                    <td  style='text-align:left;'>" . $tarifa01[$i]['combotarifa'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['combo'] . "</td>            
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio']) . "</td>                  
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias']) . "</td>                                  
                </tr>";

            $total = $tarifa01[$i]['servicio_total'] + $total;
            $iva = ( $total * $this->valoriva() );
        }


        $tabla.="
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>         
            <td>            </td>              
            <td  style='text-align:right;font-size:14px; font-weight: bold' >  Impuestos </td>
            <td  style='text-align:right; font-size:14px; font-weight: bold;' >" . $this->bs2($iva) . "</td>                                  
        </tr>            
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>         
            <td>            </td>              
            <td  style='text-align:right;font-size:15px; font-weight: bold' >  TOTAL </td>
            <td  style='text-align:right; font-size:15px; font-weight: bold;' >" . $this->bs2($total) . "</td>                                  
        </tr>
        </table>";

        return $tabla;
    }

    /* RESERVACION02.HTML */

    public function reporte_tabla_tarifa02($tarifa01) {





        $total = 0;
        $tabla.="    
            
        <div id='cambiotarifa' style='padding:20px;'>   
      
        </div>  
        

        <table class = 'table table-hover' style = 'font-size: 13px'>
        <tr>
            <th style = 'text-align:left;width:15%'>Estatus Actual</th>
            <th style = 'text-align:left;width:15%'>Categoria</th>
            <th style = 'text-align:left;width:15%'>Tarifa</th>            
            <th style = 'text-align:center;width:5%'>N° Pers.</th>
            <th style = 'text-align:center;width:10%'>Desde</th>
            <th style = 'text-align:center;width:10%'>Hasta</th>            
            <th style = 'text-align:center;width:5%'>Noches</th>
            <th style = 'text-align:right;width:15%'>Costo</th>         
            <th style = 'text-align:right;width:15%'>Total</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {


            $codigo = $tarifa01[$i]['reservacion_codigo'] . "-" . $tarifa01[$i]['reservacion_id'];
            $codigo = $this->encrypt($codigo);

            $actual = 0;

            if ($_SESSION['temp']['reservacion_id']) {
                if ($tarifa01[$i]['reservacion_id'] == $_SESSION['temp']['reservacion_id']) {
                    $actual = 1;
                }
            }

            if ($actual == 1)
                $tabla.="<tr style='background:#F2F5A9'>";
            else
                $tabla.="<tr>";


            $tabla.=" <td  style='text-align:left;'>" . $tarifa01[$i]['reservacion_estadoreservacion'] . "</td>  "
                    . " <td  style='text-align:left;'>"
                    . "<a class='btn btn-block btn-default btn-xs' href='?opcion=busquedareservacion&codigo=" . $codigo . "'  style='color:red'><i class='fa fa-eye'></i> " . $tarifa01[$i]['habitacion_nombre'] . "</strong> </span>" . $tarifa01[$i]['habitacion_categoria'] . "</a></td>
                    <td  style='text-align:left;'>" . $tarifa01[$i]['servicio_nombre'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_personas'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_desde'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_hasta'] . "</td>                          
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio']) . "</td>                  
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias']) . "</td>                                  
                </tr>";

            $total = ( $tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias'] ) + $total;
            $iva = ($total * $this->valoriva());
        }


        $tabla.="
<tr>
            <td>            </td>
            <td>            </td>        
            <td>            </td>         
            <td>            </td>              
            <td>            </td>         
            <td>            </td>              
            <td  style='text-align:right;font-size:14px; font-weight: bold' >  Impuestos </td>
            <td  style='text-align:right; font-size:14px; font-weight: bold;' >" . $this->bs2($iva) . "</td>                                  
        </tr>            
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>      
            <td>            </td>         
            <td>            </td>              
            <td>            </td>              
            <td  style='text-align:right;font-size:15px; font-weight: bold' >  TOTAL </td>
            <td  style='text-align:right; font-size:15px; font-weight: bold;' >" . $this->bs2($total + $iva) . "</td>                                  
        </tr>
        </table>";

        return $tabla;
    }

    public function reporte_tabla_tarifa01($tarifa01) {
        $total = 0;
        $tabla.="    
        <table class = 'table table-hover' style = 'font-size: 14px'>
        <tr>
            <th style = 'text-align:left;width:100px'>Nro. Confirmación</th>        
            <th style = 'text-align:left;width:100px'>Habitación/Categoria</th>
            <th style = 'text-align:center;width:100px'>Entrada</th>            
            <th style = 'text-align:center;width:100px'>Salida</th>                
            <th style = 'text-align:center;width:100px'>Cantidad de personas</th>
            <th style = 'text-align:center;width:100px'>Cantidad de Días</th>
            <th style = 'text-align:right;width:100px'>Costo Diario<br> con IVA</th>
            <th style = 'text-align:right;width:100px'>Costo Total <br>  con IVA</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {
            $style = "";
            if ($tarifa01[$i]['reservacion_estadoreservacion_id'] == 3) {
                $tarifa01[$i]['servicio_total'] = $tarifa01[$i]['servicio_iva'] = $tarifa01[$i]['dias'] = $tarifa01[$i]['reservacion_personas'] = 0;
                $tarifa01[$i]['habitacion_categoria'].="[ CANCELADA ]";
                $style = "style='color:red' ";
            }

            $tabla.=" 
                <tr " . $style . " >
                    <td  style='text-align:left;'>" . $tarifa01[$i]['reservacion_codigo'] . "-" . $tarifa01[$i]['reservacion_id'] . "</td>                
                    <td  style='text-align:left;'>" . $tarifa01[$i]['habitacion_nombre'] . "/" . $tarifa01[$i]['habitacion_categoria'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_desde'] . "</td>   
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_hasta'] . "</td>                           
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_personas'] . "</td>            
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' > " . $this->bs($tarifa01[$i]['servicio_iva']) . "</td>
                    <td  style='text-align:right;' > " . $this->bs($tarifa01[$i]['servicio_total']) . "</td>                                  
                </tr>";

            $total = $tarifa01[$i]['servicio_total'] + $total;
        }

        $tabla.="
            <tr>
                <td></td>
                <td></td>
                <td></td>    
                <td></td>
                <td></td>
                <td></td>               
                <td  style='text-align:right; font-weight: bold; font-size:24px' >  TOTAL </td>
                <td  style='text-align:right; font-size:24px; font-weight: bold;' >" . $this->bs($total) . "</td>                                  
            </tr>
        </table>";

        return $tabla;
    }

    public function ver_clientes() {
        $obj = new padreModelo();
        $sql = "select * from vw_cliente";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_cliente() {
        $obj = new padreModelo();
        $sql = "select * from vw_cliente where cliente_id=" . $this->cliente_id;
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function contar_acompanantes() {
        $obj = new padreModelo();
        $sql = "select count(*) as total from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "';";
        $data = $obj->ejecutar_query($sql);
        return $data['0']['total'];
    }

    public function contar_acompanantes_m() {
        $obj = new padreModelo();
        $sql = "select count(*) as total from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "' and acompanante_genero ilike '%M%' ;";
        $data = $obj->ejecutar_query($sql);
        return $data['0']['total'];
    }

    public function contar_acompanantes_f() {
        $obj = new padreModelo();
        $sql = "select count(*) as total from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "' and acompanante_genero ilike '%F%' ;";
        $data = $obj->ejecutar_query($sql);
        return $data['0']['total'];
    }

    public function genero_cliente_m() {
        $obj = new padreModelo();
        $sql = "select count(*) as total from vw_ocupacion where cliente_genero ilike '%M%' and ocupacion_estado='O' ;";
        $data = $obj->ejecutar_query($sql);
        return $data['0']['total'];
    }

    public function genero_cliente_f() {
        $obj = new padreModelo();
        $sql = "select count(*) as total from vw_ocupacion where cliente_genero ilike '%F%' and ocupacion_estado='O' ;";
        $data = $obj->ejecutar_query($sql);
        return $data['0']['total'];
    }

    public function ver_acompanantes() {
        $obj = new padreModelo();
        $sql = "select * from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "';";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_acompanante($documento) {
        $obj = new padreModelo();
        $sql = "select * from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "'  and  acompanante_documento = '" . $documento . "';";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function registro_acompanante($dato) {

        if ($this->vacio($dato['acompanante_nombre'], 'Verifique  Nombre Completo'))
            return false;
        if ($this->vacio($dato['acompanante_nacimiento'], 'Verifique Fecha de nacimiento'))
            return false;

        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante');
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('codigoid', $this->codigoid);
        $objeto2->add_data('documento', $dato['acompanante_documento']);
        $objeto2->add_data('nombre', $dato['acompanante_nombre']);
        $objeto2->add_data('genero', $dato['acompanante_genero']);
        $objeto2->add_data('nacimiento', $dato['acompanante_nacimiento']);
        $objeto2->add_data('contacto', $dato['acompanante_contacto']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->acompanante_id = $objeto2->verId('acompanante');
        return $this->acompanante_id;
    }

    public function edicion_acompanante($dato) {
        if ($this->vacio($dato['acompanante_nombre'], 'Verifique  Nombre Completo'))
            return false;
        if ($this->vacio($dato['acompanante_nacimiento'], 'Verifique Fecha de nacimiento'))
            return false;


        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante', $dato['acompanante_id']);
        $objeto2->add_data('nombre', $dato['acompanante_nombre']);
        $objeto2->add_data('genero', $dato['acompanante_genero']);
        $objeto2->add_data('nacimiento', $dato['acompanante_nacimiento']);
        $objeto2->add_data('contacto', $dato['acompanante_contacto']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->acompanante_id = $id;
        return $this->acompanante_id;
    }

    public function eliminar_acompanante($dato) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante', $dato['acompanante_id']);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar();
        $this->acompanante_id = $id;
        return $this->acompanante_id;
    }

    public function dashboard_ocupaciones() {
        $obj = new padreModelo();
        $sql = "select count(*) as cantidad from vw_ocupacion where ocupacion_estado='O'";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function dashboard_tabla_ocupaciones() {
        $obj = new padreModelo();
        $sql = "select * from vw_ocupacion where ocupacion_estado='O' ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_supervisiones() {
        $obj = new padreModelo();
        $sql = "select * from vw_supervision order by supervision_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_supervision() {
        $obj = new padreModelo();
        $sql = "select * from vw_supervision where supervision_id=" . $this->supervision_id . ";";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function dashboard_reservaciones() {
        $obj = new padreModelo();
        $sql = "select count(*) as cantidad from vw_reservacion where estadoreservacion_id='2' ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function reporte_tabla_acompanante($data) {
        $total = 0;
        $tabla.="             
        <table class = 'table table-hover' style = 'font-size: 13px'>
        <tr>
            <th style = 'text-align:left;width:100px'>Nombre</th>
            <th style = 'text-align:center;width:100px'>Documento</th>
            <th style = 'text-align:center;width:50px'>Genero</th>
            <th style = 'text-align:center;width:50px'>Edad</th>
            <th style = 'text-align:center;width:70px'>Nacimiento</th>            
        </tr>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
                <tr>
                    <td  style='text-align:left;'><a href='javascript:editar(" . $data[$i]['acompanante_documento'] . ")'>" . $data[$i]['acompanante_nombre'] . "</a></td>
                    <td  style='text-align:center;'>" . $data[$i]['acompanante_documento'] . "</td>            
                    <td  style='text-align:center;' >" . $data[$i]['acompanante_genero'] . "</td>       
                    <td  style='text-align:center;' > " . $data[$i]['acompanante_edad'] . "</td>
                    <td  style='text-align:center;' > " . $data[$i]['acompanante_nacimiento'] . "</td>
                           
                </tr>";
        }

        $tabla.="</table>"
                . "  ";

        return $tabla;
    }

    public function reporte_tabla_acompanante01($data) {

        $res = "
            

                                <div class='row'>
                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >N° Documento</label>
                                            <input  value='" . $data['acompanante_documento'] . "'   id='acompanante_documento'  name='acompanante_documento' type='text' class='form-control form-control2' placeholder='N Documento Identidad'>   
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Nombre Completo</label>
                                            <input value='" . $data['acompanante_nombre'] . " ' id='acompanante_nombre'  name='acompanante_nombre' type='text' class='form-control form-control2' placeholder='Nombre Completo'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Nacimiento</label>
                                            <input value='" . $data['acompanante_nacimiento'] . "'  id='acompanante_nacimiento'  name='acompanante_nacimiento' type='text' class='form-control form-control2' placeholder='dd/mm/yyyy'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Genero</label>
                                            <input value='" . $data['acompanante_genero'] . "'  id='acompanante_genero'  name='acompanante_genero' type='text' class='form-control form-control2' placeholder='M / F'>              
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Origen</label>
                                            <input value='" . $data['acompanante_origen'] . "'  id='acompanante_origen'  name='acompanante_origen' type='text' class='form-control form-control2' placeholder=''>              
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Destino</label>
                                            <input  value='" . $data['acompanante_destino'] . "'  id='acompanante_destino'  name='acompanante_destino' type='text' class='form-control form-control2' placeholder=''>              
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Nacionalidad</label>
                                            <input   value='" . $data['acompanante_nacionalidad'] . "'  id='acompanante_nacionalidad'  name='acompanante_nacionalidad' type='text' class='form-control form-control2' placeholder=''>              
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Ocupación</label>
                                            <input   value='" . $data['acompanante_ocupacion'] . "'  id='acompanante_ocupacion'  name='acompanante_ocupacion' type='text' class='form-control form-control2' placeholder=''>              
                                        </div>
                                    </div> 
                                    <div class='col-md-4'>
                                        <div class='form-group' >
                                            <label class='control-label control-label2'  >Teléfono</label>
                                            <input   value='" . $data['acompanante_telefono'] . "' id='acompanante_telefono'  name='acompanante_telefono' type='text' class='form-control form-control2' placeholder=''>              
                                        </div>
                                    </div>                                        

                                    <div class='col-md-6'>
                                        <div class='form-group' >
                                            <br>
                                            <a href='javascript:registro()'  class='btn btn-block btn-success btn-xs ' >
                                                 Registrar  
                                             </a>
                                        </div>
                                    </div>   
                                    
                                    <div class='col-md-6'>
                                        <div class='form-group' >
                                            <br>
                                            <a  href='javascript:eliminar(" . $data['acompanante_documento'] . " )'  class='btn btn-block btn-danger btn-xs' >
                                                Eliminar 
                                            </a>  
                                        </div>
                                    </div>   

                                </div>

";

        return $res;
    }

    public function reporte_tabla_supervision() {

        $obj = new padreModelo();
        $sql = "select * from vw_supervision where habitacion_id=" . $this->habitacion_id . " and supervision_solucionado='NO' order by supervision_id desc ;";
        $data = $obj->ejecutar_query($sql);

        $res.= " <table class='table table-condensed'>
                           ";
        for ($i = 0; $i < count($data); $i++) {

            $res.="
                            <tr>
                                <td  ><i class='fa  fa-dot-circle-o'></i> " . $data[$i]['falla_nombre'] . " <br> <small>" . $data[$i]['supervision_observacion'] . "</small>
                                 <a  class='btn btn-block btn-danger btn-xs ' href='javascript:delsupervision(" . $data[$i]['supervision_id'] . ")'>Eliminar</a>    

                            </td>
                            </tr>";
        }

        $res.="</table>";

        return $res;
    }

    public function registro_supervision($post) {



        $this->tabla = "supervision";

        $objeto2 = new padreModelo();

        if ($this->idtabla) {
            $objeto2->setConfig($this->tabla, $this->idtabla);
        } else {
            $objeto2->setConfig($this->tabla);
        }

        if ($this->eliminaciontabla) {
            $objeto2->add_data('estatu', 'false');
        } else {
            $objeto2->add_data('falla_id', $post['falla_id']);
            $objeto2->add_data('habitacion_id', $post['habitacion_id']);
            $objeto2->add_data('observacion', $post['observacion']);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->add_data('solucionado', 'NO');
        }

        $objeto2->ejecutar();


        if ($this->idtabla) {
            return $this->idtabla;
        } else {
            $this->idtabla = $objeto2->verId($this->tabla);
            return $this->idtabla;
        }
    }

    public function registro_supervision01($post) {

        $this->tabla = "supervision";
        $objeto2 = new padreModelo();
        $objeto2->setConfig($this->tabla, $this->idtabla);
        $objeto2->add_data('solucion', $post['observacion_solucion']);
        $objeto2->add_data('usuario_solucion', $this->usuario);
        $objeto2->add_data('fecha_solucion', 'NOW()');
        $objeto2->add_data('solucionado', 'SI');
        $objeto2->ejecutar();
        return $this->idtabla;
    }

    public function base($post) {

        $this->tabla = "";

        $objeto2 = new padreModelo();

        if ($this->idtabla) {
            $objeto2->setConfig($this->tabla, $this->idtabla);
        } else {
            $objeto2->setConfig($this->tabla);
        }

        if ($this->eliminaciontabla) {
            $objeto2->add_data('estatu', 'false');
        } else {

            $objeto2->add_data('formapago_id', $post['formapago_id']);
            $objeto2->add_data('modoocupacion_id', 1);
            $objeto2->add_data('checkin', date('d-m-Y'));
            $objeto2->add_data('checkout', "");
            $objeto2->add_data('motivo', $post['motivo']);
            $objeto2->add_data('horaentrada', $this->hora_actual());
            $objeto2->add_data('horasalida', "");
            $objeto2->add_data('observacion', $post['ocupacionreservacion']);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->add_data('estado', 'O');
            $objeto2->add_data('garantiareservacion_id', $this->garantiareservacion_id);
            $objeto2->add_data('empresa_id', $this->empresa_id);
        }

        $objeto2->ejecutar();


        if ($this->idtabla) {
            return $this->idtabla;
        } else {
            $this->idtabla = $objeto2->verId($this->tabla);
            return $this->idtabla;
        }
    }

    private function generar_codigoctacliente() {
        $code = "CH" . date('dm') . $this->cliente_id . rand(100, 999);
        return $code;
    }

    public function registro_ctacliente($post) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.ctacliente');
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('codigo', $this->generar_codigoctacliente());
        $objeto2->add_data('tipocredito', $post['ch_tipocredito']);
        $objeto2->add_data('permiso', $post['ch_permiso']);
        $objeto2->add_data('garantiareservacion_id', $this->garantiareservacion_id);
        $objeto2->add_data('tipocta', $post['ch_tipocta']);
        $objeto2->add_data('padre', 0);
        $objeto2->add_data('hijo', 0);
        $objeto2->add_data('observacion', $post['ctacliente_observacion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->ctahuesped_id = $objeto2->verId('facturacion.ctacliente');
        return $this->ctahuesped_id;
    }

    public function asociacion_ctacliente($ctas) {

        for ($i = 0; $i < count($ctas); $i++) {
            $this->ctaasociada_id = $ctas[$i];
            $this->registro_ctacliente_grupo();
            $this->actualizar_ctacliente_asociacion();
        }
    }

    public function registro_ctacliente_grupo() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.ctacliente_grupo');
        $objeto2->add_data('ctacliente01_id', $this->ctahuesped_id);
        $objeto2->add_data('ctacliente02_id', $this->ctaasociada_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function actualizar_ctacliente_asociacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.ctacliente', $this->ctaasociada_id);
        $objeto2->add_data('tipocta', 86);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        return $this->ctaasociada_id;
    }

    public function registro_ctaclientereservacion($post) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.ctacliente_reservacion');
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $ctacliente_reservacionid = $objeto2->verId('facturacion.ctacliente_reservacion');
        return $ctacliente_reservacion_id;
    }

    public function ver_ctahuesped() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped where ctahuesped_estatu=true order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_ctahuespedgrupo() {
        $obj = new padreModelo();
        $sql = "
    select h.* from facturacion.ctacliente_grupo g join vw_ctahuesped h on h.ctahuesped_id = g.ctacliente02_id where ctacliente01_id='" . $this->ctahuesped_id . "' 
    UNION select h.* from  vw_ctahuesped h where h.ctahuesped_id='" . $this->ctahuesped_id . "'";

        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_ctahuesped_asociacion() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped where ctahuesped_estatu=true and ctahuesped_id<>" . $this->ctahuesped_id . " order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_ctahuespedes() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_ctahuesped01() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped where ctahuesped_estatu=true and ctahuesped_id=" . $this->ctahuesped_id . " order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function ver_detalleabonos() {

        $this->buscar_ctaasociadas();
        $i = 0;
        foreach ($this->ctaasociada_id as $key) {

            if ($i == 0)
                $lista.=$key['ctahuesped'];
            else
                $lista.="," . $key['ctahuesped'];
            $i++;
        }

        $this->listadoctaasociada_id = $lista;

        $obj = new padreModelo();
        $this->buscar_ctaasociadas();
        $sql = "select * from vw_pago p where ctacliente_id in (" . $this->listadoctaasociada_id . ");";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_detallecargos() {

        $this->buscar_ctaasociadas();
        $i = 0;
        foreach ($this->ctaasociada_id as $key) {

            if ($i == 0)
                $lista.=$key['ctahuesped'];
            else
                $lista.="," . $key['ctahuesped'];
            $i++;
        }

        $this->listadoctaasociada_id = $lista;

        $obj = new padreModelo();
        $this->buscar_ctaasociadas();
        $sql = "select * from vw_detallereservacion r where ctacliente_id in (" . $this->listadoctaasociada_id . ") and detallereservacion_cargado is not null;";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_detallecargos_roomservice() {

        $this->buscar_ctaasociadas();
        $i = 0;
        foreach ($this->ctaasociada_id as $key) {

            if ($i == 0)
                $lista.=$key['ctahuesped'];
            else
                $lista.="," . $key['ctahuesped'];
            $i++;
        }

        $this->listadoctaasociada_id = $lista;

        $obj = new padreModelo();
        $this->buscar_ctaasociadas();
        $sql = "select sum(producto_costo) as total,'Roomservice' as  servicio  from vw_roomservice2 r where ctacliente_id in (" . $this->listadoctaasociada_id . ");";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function ver_detalle_cargos_abonos() {
        $obj = new padreModelo();
        $sql = "   select * from vw_cargos_y_abonos where ctacliente_id in ('" . $this->listadoctaasociada_id . "');";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_detalle_cargos_abonos_por_profiles() {

        $p = $this->ver_profiles();

        $obj = new padreModelo();
        $x = 0;
        for ($i = 0; $i < count($p); $i++) {
            $sql = "select * from vw_cargos_y_abonos where ctacliente_id in ('" . $this->listadoctaasociada_id . "') and cliente_id in ('" . $p[$i]['cliente_id'] . "') union"
                    . " select * from vw_cargos_pedidos where ctacliente_id in ('" . $this->listadoctaasociada_id . "') and cliente_id in ('" . $p[$i]['cliente_id'] . "')";
            $res = $obj->ejecutar_query($sql);

            if ($res[0]['cliente_id'] > 0) {
                $data[$i] = $res;
                $x++;
            }
        }


        return $data;
    }

    public function ver_detallecargos_cargados() {

        $this->buscar_ctaasociadas();
        $i = 0;
        foreach ($this->ctaasociada_id as $key) {

            if ($i == 0)
                $lista.=$key['ctahuesped'];
            else
                $lista.="," . $key['ctahuesped'];
            $i++;
        }

        $this->listadoctaasociada_id = $lista;

        $obj = new padreModelo();
        $this->buscar_ctaasociadas();
        $sql = "select * from vw_detallereservacion r where ctacliente_id in (" . $this->listadoctaasociada_id . ") and detallereservacion_cargado is not null;";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function detalle_ctahuesped() {
        $data1 = $this->ver_ctahuesped01();
        $data2 = $this->get_totales();
        return array_merge($data1, $data2);
    }

    public function verificar_garantiactahuesped() {
        $obj = new padreModelo();
        $sql = "select * from vw_garantiareservacion where ctacliente_id in (" . $this->ctahuesped_id . ");";
        $data = $obj->ejecutar_query($sql);

        if ($data[0]['garantiareservacion_activacion'] == 't') {
            $data[0]['garantiareservacion_estatus'] = "<span style='color:green; font-weight:700; font-size:16px'> CTA GARANTIZADA </span>";
        } else {
            $data[0]['garantiareservacion_estatus'] = " <span style='color:red; font-weight:700; font-size:16px'> CTA SIN GARANTÍA </span>";
        }

        return $data;
    }

    public function detalle_ctahuespedes() {

        $data1 = $this->ver_ctahuespedes();

        for ($i = 0; $i < count($data1); $i++) {

            /* si es  82 es decir si es asociada */
            if ($data1[$i]['tipocta_id'] == $this->tipocuenta_asociada) {
                $this->ctahuesped_id = $data1[$i]['ctahuesped_id'];
                $this->ctahuesped_id = $this->buscar_padre_ctaasociada();
                $data1[$i]['ctahuesped_id'] = $this->ctahuesped_id;
                $codigo = $this->buscar_codigoid_ctaasociada();
                $data1[$i]['cta_huesped_tipocta'] = "Asociada a " . $codigo;
                $data1[$i]['totales'] = $this->get_totales();
            } else {

                $this->ctahuesped_id = $data1[$i]['ctahuesped_id'];
                $data1[$i]['totales'] = $this->get_totales();
            }
        }

        return $data1;
    }

    public function get_ctahuesped() {


        $data['ctabasicos'] = $this->detalle_ctahuesped();
        $data['ctacargors'] = $this->ver_detallecargos_roomservice();
        $data['ctacargos'] = $this->ver_detallecargos();
        $data['ctaabonos'] = $this->ver_detalleabonos();
        $data['ctagarantia'] = $this->verificar_garantiactahuesped();
        $data['ctacargoscheck'] = $this->ver_detallecargos_cargados();
        $data['cargosabonos'] = $this->ver_detalle_cargos_abonos();
        $data['profiles'] = $this->comboprofiles();
        $data['cargosabonos_profile'] = $this->ver_detalle_cargos_abonos_por_profiles();

        return $data;
    }

    public function ver_ctahuesped_detalle01() {
        $obj = new padreModelo();
        $sql = "select h.* from facturacion.ctacliente_grupo g join vw_ctahuesped_reservacion h on h.ctahuesped_id = g.ctacliente02_id where ctacliente01_id='" . $this->ctahuesped_id . "' 
    UNION select h.* from  vw_ctahuesped_reservacion h where h.ctahuesped_id='" . $this->ctahuesped_id . "'";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_ctahuesped_detalle02($res) {

        $i = 0;
        foreach ($res as $key) {

            $key = substr($key, 1);    // devuelve "f"

            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }

        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 r join vw_detallereservacion dr on dr.detallereservacion_reservacion_id = r.reservacion_id where  detallereservacion_id in (" . $lista . ") ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_cargosparatransferir($res) {

        /* Esta muestra las ctashuespedes para luego seleccionar  a cual transferirlos los cargos de ctahuesped02.html */

        $i = 0;
        foreach ($res as $key) {

            $inicial = substr($key, 0, 1);


            if ($inicial !== 'S') {
                $key = substr($key, 1);    // devuelve "f"

                if ($i == 0)
                    $lista.=$key;
                else
                    $lista.="," . $key;
                $i++;
            }
        }


        $this->listado_id_detallesreservacion = $lista;


        if (!$this->listado_id_detallesreservacion)
            return false;

        $obj = new padreModelo();
        $sql = "select *  from vw_detallereservacion dr  where  detallereservacion_id in (" . $lista . ") ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_pedidosparatransferir($res) {

        /* Esta muestra las ctashuespedes para luego seleccionar  a cual transferirlos los cargos de ctahuesped02.html */

        $i = 0;
        foreach ($res as $key) {

            $inicial = substr($key, 0, 1);

            if ($inicial == 'S') {
                $key = substr($key, 1);    // devuelve "f"

                if ($i == 0)
                    $lista.=$key;
                else
                    $lista.="," . $key;
                $i++;
            }
        }


        $this->listado_id_pedidos = $lista;
    }

    public function actualizar_cargosparatransferir01($res) {

        /* AQUI SE ACTUALIZA  */



        $i = 0;
        foreach ($res as $key) {

            $key = substr($key, 1);    // devuelve "f"

            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }

        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $lista);
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->ejecutar();
    }

    public function ver_ctahuesped_detallereservacion($reservacion_ids) {
        $res = $reservacion_ids;
        $i = 0;
        foreach ($res as $key) {


            if ($i == 0)
                $lista.=$key['reservacion_id'];
            else
                $lista.="," . $key['reservacion_id'];
            $i++;
        }

        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 r join vw_detallereservacion dr on dr.detallereservacion_reservacion_id = r.reservacion_id where  reservacion_id in (" . $lista . ")  ";
        $data = $obj->ejecutar_query($sql);

        $data = $this->icono_estatusreserva($data);
        $data = $this->calculos_detallesreservacion($data);

        return $data;
    }

    public function ver_ctahuesped_detalle_pago($res) {

        $i = 0;
        foreach ($res as $key) {

            $key = substr($key, 1);    // devuelve "f"

            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }

        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 r join vw_detallereservacion dr on dr.detallereservacion_reservacion_id = r.reservacion_id where  detallereservacion_id in (" . $lista . ")  ";
#        $sql = "select * from vw_reservacion01 r join vw_detallereservacion dr on dr.detallereservacion_reservacion_id = r.reservacion_id where  reservacion_id in (" . $lista . ")  ";

        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function busqueda_ctacliente() {
        $obj = new padreModelo();
        $sql = "select * from vw_cliente where cliente_documento='" . $this->numero_documento . "' ";
        $data = $obj->ejecutar_query($sql);

        if ($data[0]['id'] > 0) {
            $data[0]['mensaje'] = "Huesped Registrado";
            $data[0]['verificacion'] = true;
            $data[0]['verificacion'] = true;
            $data[0]['color'] = 'success';
        } else {
            $data[0]['mensaje'] = "Nuevo Huesped";
            $data[0]['verificacion'] = false;
            $data[0]['documento'] = $this->numero_documento;
            $data[0]['color'] = 'danger';
        }

        return $data[0];
    }

    public function registro_cliente_ctahuesped($dato) {
        /*  PARA EL CHECKIN */
        $objeto2 = new padreModelo();

        $this->numero_documento = $dato['documento'];
        $datacliente = $this->busqueda_ctacliente();

        if ($datacliente['cliente_id'] > 0) {
            $this->cliente_id = $datacliente['cliente_id'];
            $objeto2->setConfig('cliente', $this->cliente_id);
        } else {
            $objeto2->setConfig('cliente');
        }

        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('apellido', $dato['apellido']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('movil', $dato['movil']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('nacionalidad', $dato['nacionalidad']);
        $objeto2->add_data('nacimiento', $dato['nacimiento']);
        $objeto2->add_data('genero', $dato['genero']);
        $objeto2->add_data('civil', $dato['civil']);
        $objeto2->add_data('tipo_documento', $dato['tipo_documento']);
        $objeto2->add_data('tipocliente_id', $dato['clientetipo_id']);
        $objeto2->add_data('observacion', $dato['clienteobservacion']);
        $objeto2->add_data('profesion', $dato['profesion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        if ($this->cliente_id > 0) {
            
        } else {
            $this->cliente_id = $objeto2->verId('cliente');
        }

        return $this->cliente_id;
    }

    public function icono_tipoctahuesped($data) {

        for ($i = 0; $i < count($data); $i++) {

            if ($data[$i]['tipocta_id'] == '85')
                $data[$i]['icono_tipocta'] = "<i class='fa  fa-clock-o text-red'></i>";
            if ($data[$i]['tipocta_id'] == '86')
                $data[$i]['icono_tipocta'] = "<i class='fa fa-random text-yellow'></i>";
            if ($data[$i]['tipocta_id'] == '82')
                $data[$i]['icono_tipocta'] = "<i class='fa fa-check-circle text-green'></i>";
            if ($data[$i]['tipocta_id'] == '81')
                $data[$i]['icono_tipocta'] = "<i class='fa fa-check-o text-green'></i>";
        }

        return $data;
    }

    public function icono_estatusreserva($data) {

        for ($i = 0; $i < count($data); $i++) {

            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_ocupadasinpago)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-close text-red'></i>";
            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_ocupadaconpago)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-check-circle text-green'></i>";
            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_reservadacongarantia)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-credit-card text-yellow'></i>";
            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_reservadasingarantia)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-credit-card text-red'></i>";
            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_sinocuparconpago)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-check-circle text-yellow'></i>";
            if ($data[$i]['estatusreserva_id'] == $this->estatusreserva_sinocuparsinpago)
                $data[$i]['icono_estatusreserva'] = "<i class='fa fa-warning text-red'></i>";
        }

        return $data;
    }

    public function calculos_detallesreservacion($data) {

        $DEUDA = 0;
        $TOTAL = 0;
        $PAGO = 0;
        $VALORIVA = 0;
        $ENESPERA = 0;

        for ($i = 0; $i < count($data); $i++) {

            $VALORIVA = $data[$i]['detallereservacion_iva'];
            $TOTAL+=$data[$i]['detallereservacion_precio'];

            if (($data[$i]['estatusreserva_id'] == $this->estatusreserva_ocupadasinpago) || ($data[$i]['estatusreserva_id'] == $this->estatusreserva_sinocuparsinpago)) {

                $DEUDA +=$data[$i]['detallereservacion_precio'];
            } else if (($data[$i]['estatusreserva_id'] == $this->estatusreserva_ocupadaconpago) || ($data[$i]['estatusreserva_id'] == $this->estatusreserva_sinocuparconpago)) {

                $PAGO +=$data[$i]['detallereservacion_precio'];
            } else if (($data[$i]['estatusreserva_id'] == $this->estatusreserva_reservadacongarantia) || ($data[$i]['estatusreserva_id'] == $this->estatusreserva_reservadasingarantia)) {

                $ENESPERA +=$data[$i]['detallereservacion_precio'];
            }
        }

        $data[0]['enesperadecobro'] = $ENESPERA;
        $data[0]['totalsiniva'] = $DEUDA;
        $data[0]['pago'] = $PAGO;
        $data[0]['iva'] = $data[0]['totalsiniva'] * (12 / 100);
        $data[0]['totalconiva'] = $this->bs($data[0]['totalsiniva'] + $data[0]['iva']);
        $data[0]['totalsiniva'] = $this->bs($data[0]['totalsiniva']);
        $data[0]['iva'] = $this->bs($data[0]['iva']);

        return $data;
    }

    public function actualizar_tipoctahuesped() {
        $obj = new padreModelo();
        $sql = "select * from facturacion.ctacliente where garantiareservacion_id='" . $this->garantiareservacion_id . "' and estatu='true' ";
        $data = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data[0]['id'];
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.ctacliente', $this->ctahuesped_id);
        $objeto2->add_data('tipocta', 82);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function actualizar_ctahuesped_garantiareservacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('garantiareservacion', $this->garantiareservacion_id);
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function registro_ctaclienterepago($post) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.pago');
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->add_data('tipopago_id', $post['formapago_id']);
        $objeto2->add_data('tipotarjeta_id', $post['tipotarjeta']);
        $objeto2->add_data('numerotarjeta', $post['numerotarjeta']);
        $objeto2->add_data('referencia', $post['referencia']);
        $objeto2->add_data('razon', $post['razon']);
        $objeto2->add_data('rif', $post['rif']);
        $objeto2->add_data('direccion', $post['direccion']);
        $objeto2->add_data('observacion', $post['observacion']);
        $objeto2->add_data('cliente_id', $post['profile_id']);
        $objeto2->add_data('monto', $post['monto']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->pago_id = $objeto2->verId('facturacion.pago');
        return $this->pago_id;
    }

    public function registro_detallereservacion() {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion');
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->add_data('dia', $this->dia);
        $objeto2->add_data('pago_id', $this->pago_id);
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->add_data('estatusreserva_id', $this->estatusreserva);
        $objeto2->add_data('tipotarifa_id', $this->tipotarifa);
        $objeto2->add_data('precio', $this->precio);
        $objeto2->add_data('valoriva', $this->valoriva);
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function actualizar_pago_detallereservacion() {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->detallereservacion_id);
        $objeto2->add_data('pago_id', $this->pago_id);
        $objeto2->add_data('estatusreserva_id', 96);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function proceso_registro_detallereservacion() {

        $obj = new padreModelo();
        $sql = "select desde,hasta,precio_unitario,valoriva from  reservacion  where id='" . $this->reservacion_id . "'; ";
        $data = $obj->ejecutar_query($sql);

        $this->desde = $data[0]['desde'];
        $this->hasta = $data[0]['hasta'];
        $this->precio = $data[0]['precio_unitario'];
        $this->valoriva = $data[0]['valoriva'];

        $fechas = $this->diasentrefechas();
        for ($y = 0; $y < (count($fechas) - 1); $y++) {
            $this->dia = $fechas[$y];

            $this->pago_id = null;
            $this->estatusreserva = $this->estatusreserva_reservadasingarantia;
            $this->tipotarifa = $this->tipotarifa_completa;
            $this->registro_detallereservacion();
        }
    }

    public function proceso_registro_pago($res) {
        $i = 0;
        foreach ($res as $key) {

            $key = substr($key, 1);    // devuelve "f"

            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $this->detallereservacion_id = $lista;
        $this->actualizar_pago_detallereservacion();
    }

    public function proceso_verificacion_pago_checkin() {
        $obj = new padreModelo();
        $sql = "select * from  vw_verificacion_checkin_pago  ";
        $data = $obj->ejecutar_query($sql);

        for ($y = 0; $y < count($data); $y++) {


            if (($data[$y]['checkin'] == null) && ($data[$y]['pago_id'] == null)) {
//  echo " SIN PAGO " . $data[$y]['detallereservacion_id'];
            }


            if (($data[$y]['checkin'] <> null) && ($data[$y]['pago_id'] == null)) {
//echo " SIN PAGO CON CHECKIN " . $data[$y]['detallereservacion_id'];
            }


            if (($data[$y]['checkin'] <> null) && ($data[$y]['pago_id'] <> null)) {
// echo " OCUPADA PAGADA " . $data[$y]['detallereservacion_id'];
            }


            if (($data[$y]['checkin'] == null) && ($data[$y]['pago_id'] <> null)) {
//    echo "SIN  OCUPAR PAGADA " . $data[$y]['detallereservacion_id'];
            }
        }
    }

    public function actualizacion_estatusreserva_congarantia() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);
        $objeto2->add_data('pago_id', $this->pago_id);
        $objeto2->add_data('estatusreserva_id', $this->estatusreserva_reservadacongarantia);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar('reservacion_id');
    }

    public function actualizacion_estatusreserva_checkin() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);
        $objeto2->add_data('estatusreserva_id', $this->estatusreserva_ocupadasinpago);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar('reservacion_id');
    }

    private function buscar_abonos() {
        $obj = new padreModelo();
        $sql = "select ctacliente_id  as ctahuesped_id,sum(monto) as total from facturacion.pago where ctacliente_id=" . $this->ctahuesped_id . " and estatu=true group by ctacliente_id";
        $data = $obj->ejecutar_query($sql);
        $this->abonos = $data[0]['total'];
        return $data[0]['total'];
    }

    private function buscar_saldo() {

        $this->saldo = ( $this->cargos - $this->abonos ) + $this->iva;
    }

    private function buscar_cargos() {

        $this->buscar_ctaasociadas();
        $i = 0;
        foreach ($this->ctaasociada_id as $key) {

            if ($i == 0)
                $lista.=$key['ctahuesped'];
            else
                $lista.="," . $key['ctahuesped'];
            $i++;
        }

        $this->listadoctaasociada_id = $lista;

        $this->cargos = $this->buscar_cargos01();
        $this->buscar_cargos_pedidos();

        $this->cargos = $this->cargos + $this->cargos_pedidos;

        return $this->cargos;
    }

    private function buscar_ctaasociadas() {


        $obj = new padreModelo();
        $sql = "select g.ctacliente02_id as ctahuesped from facturacion.ctacliente c join facturacion.ctacliente_grupo g on c.id = g.ctacliente01_id and g.ctacliente01_id=" . $this->ctahuesped_id . "
union select c.id as ctahuesped from facturacion.ctacliente c where c.id=" . $this->ctahuesped_id . "  ";
        $data = $obj->ejecutar_query($sql);
        $this->ctaasociada_id = $data;
        return $this->ctaasociada_id;
    }

    private function buscar_cargos01() {
        $obj = new padreModelo();
        $sql = "select sum(precio) as total from detallereservacion where ctacliente_id in (" . $this->listadoctaasociada_id . ") and cargado is not null and estatu='true' UNION select sum(producto_costo) as total from vw_roomservice2 r where ctacliente_id in (" . $this->listadoctaasociada_id . ") ; ";
        $data = $obj->ejecutar_query($sql);
        $this->abonos = $data[0]['total'];
        return $data[0]['total'];
    }

    private function buscar_cargos_pedidos() {
        $obj = new padreModelo();
        $sql = "select sum(pedido_monto) as monto, sum(pedido_iva) as iva, ( sum(pedido_iva)+sum(pedido_monto) )as total from vw_pedido where ctacliente_id in(" . $this->listadoctaasociada_id . ") group by ctacliente_id";
        $data = $obj->ejecutar_query($sql);
        $this->cargos_pedidos = $data[0]['monto'];
        $this->iva_pedidos = $data[0]['iva'];
    }

    private function buscar_impuestos() {

        $this->iva = $this->cargos * $this->valoriva();
    }

    public function get_totales() {
        $this->buscar_totales();
        $data['ctahuesped_abonos'] = $this->bs($this->abonos);
        $data['ctahuesped_iva'] = $this->bs($this->iva);
        $data['ctahuesped_cargos'] = $this->bs($this->cargos);
        $data['ctahuesped_saldo'] = $this->bs($this->saldo);
        $data['ctahuesped_valoriva'] = $this->bs($this->valoriva());

        return $data;
    }

    private function buscar_totales() {
        $this->buscar_cargos();
        $this->buscar_abonos();
        $this->buscar_impuestos();
        $this->buscar_saldo();
    }

    private function buscar_padre_ctaasociada() {
        $obj = new padreModelo();
        $sql = "select ctacliente01_id  from facturacion.ctacliente_grupo where ctacliente02_id=" . $this->ctahuesped_id . ";";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['ctacliente01_id'];
    }

    private function buscar_codigoid_ctaasociada() {
        $obj = new padreModelo();
        $sql = "select codigo  from facturacion.ctacliente where id=" . $this->ctahuesped_id . "  and estatu=true;";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['codigo'];
    }

    public function ver_ctahuespedesactivas() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function buscar_cargosparafacturar() {

        $i = 0;
        foreach ($this->listacargos as $key) {

            $key = substr($key, 1);    // devuelve "f"

            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $this->detallereservacion_id = $lista;

        $obj = new padreModelo();
        $sql = "select sum(precio) as total from detallereservacion where id in (" . $this->detallereservacion_id . ")";
        $data = $obj->ejecutar_query($sql);

        $total['cargos'] = $data[0]['total'];
        $total['iva'] = $data[0]['total'] * (0.12);
        $total['abonos'] = $this->buscar_abonos();
        $total['total'] = ( $data[0]['total'] + $data[0]['total'] * (0.12) );
        return $total;
    }

    private function buscar_totalesfacturacion() {
        $this->buscar_cargosparafacturar();
        $this->buscar_abonos();
        $this->buscar_impuestos();
        $this->buscar_saldo();
    }

    public function get_totalesfacturacion() {
        $this->buscar_totalesfacturacion();
        $data['ctahuesped_abonos'] = $this->bs($this->abonos);
        $data['ctahuesped_iva'] = $this->bs($this->iva);
        $data['ctahuesped_cargos'] = $this->bs($this->cargos);
        $data['ctahuesped_saldo'] = $this->bs($this->saldo);
        $data['ctahuesped_valoriva'] = $this->bs($this->valoriva());

        return $data;
    }

    public function detalle_ctahuespedfacturacion() {
        $data1 = $this->ver_ctahuesped01();
        $data2 = $this->get_totalesfacturacion();
        return array_merge($data1, $data2);
    }

    public function get_ctahuespedfacturacion() {
        $data['ctabasicos'] = $this->detalle_ctahuespedfacturacion();
        $data['ctacargos'] = $this->ver_detallecargos();
        $data['ctaabonos'] = $this->ver_detalleabonos();
        $data['ctagarantia'] = $this->verificar_garantiactahuesped();

        return $data;
    }

    public function anulacion_pago($post) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('facturacion.pago', $this->pago_id);
        $objeto2->add_data('estatu', 'false');
        $objeto2->add_data('observacion', $post['observacion']);
        $objeto2->ejecutar();
    }

    public function ver_pago() {
        $obj = new padreModelo();
        $sql = "select * from  vw_pago where pago_id='" . $this->pago_id . "';";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function recalcularhabitaciones_para_reservar03($post, $habitaciones_reservadas) {

        for ($i = 0; $i < count($habitaciones_reservadas); $i++) {
            $this->reservacion_id = $habitaciones_reservadas[$i]['reservacion_id'];
            $codigo = $this->registro_reservacion();
            $this->registro_ctaclientereservacion($post);
            $this->proceso_registro_detallereservacion();
        }

        return $codigo;
    }

    public function ocupacion_id_reservacion_codigo() {
        $obj = new padreModelo();
        $sql = "select ocupacion_reservacion_id from vw_ocupacion where ocupacion_id=" . $this->ocupacion_id;
        $data = $obj->ejecutar_query($sql);
        return $data['0']['ocupacion_reservacion_id'];
    }

    public function registro_profile($responsable = true) {

        $obj = new padreModelo();
        $sql = "select id from  profile where ctacliente_id='" . $this->ctahuesped_id . "' and cliente_id='" . $this->cliente_id . "';";
        $data = $obj->ejecutar_query($sql);

        if (!$data[0]['id'] > 0) {
            $objeto2 = new padreModelo();
            $objeto2->setConfig('profile');
            $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
            $objeto2->add_data('cliente_id', $this->cliente_id);
            $objeto2->add_data('responsable', $responsable);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
        }
    }

    public function nuevoresponsable_profile() {

        $obj = new padreModelo();
        $sql = "select id from  profile where ctacliente_id='" . $this->ctahuesped_id . "' and cliente_id='" . $this->cliente_id . "';";
        $data = $obj->ejecutar_query($sql);

        if (!$data[0]['id'] > 0) {
            $objeto2 = new padreModelo();
            $objeto2->setConfig('profile');
            $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
            $objeto2->add_data('cliente_id', $this->cliente_id);
            $objeto2->add_data('responsable', TRUE);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
        } else {


            $objeto2 = new padreModelo();
            $objeto2->setConfig('profile', $data[0]['id']);
            $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
            $objeto2->add_data('cliente_id', $this->cliente_id);
            $objeto2->add_data('responsable', TRUE);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
        }


        $obj = new padreModelo();
        $sql = "update  profile set responsable='false' where ctacliente_id='" . $this->ctahuesped_id . "' and cliente_id <> '" . $this->cliente_id . "';";
        $data = $obj->ejecutar_query($sql);

        $obj = new padreModelo();
        $sql = "update  facturacion.ctacliente set cliente_id='" . $this->cliente_id . "' where id='" . $this->ctahuesped_id . "' ;";
        $data = $obj->ejecutar_query($sql);
    }

    public function ver_profiles() {
        $obj = new padreModelo();
        $sql = "select  * from vw_profile_ctacliente where ctacliente_id='" . $this->ctahuesped_id . "' ; ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function comboprofiles() {

        $sal.="<select name='profile_id' id='profile_id' style='' class='form-control form-control2 ' >";
        $data = $this->ver_profiles();

#   $sal.="<option value='#' selected >Seleccione Profile</option>";

        foreach ($data as $dato) {
            $sal.="<option value='" . $dato['cliente_id'] . "'  >" . $dato['cliente_apellido'] . " " . $dato['cliente_nombre'] . "</option>";
        }

        return $sal.="</select>";
    }

    public function transferencia_profile_cargos() {

        if (!$this->listado_id_detallesreservacion)
            return false;
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->listado_id_detallesreservacion);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function transferencia_profile_pedidos() {

        if (!$this->listado_id_pedidos)
            return false;

        $objeto2 = new padreModelo();
        $objeto2->setConfig('pedido', $this->listado_id_pedidos);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function actualizacion_detallereservacion_estatus($id, $estatus) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $id);
        $objeto2->add_data('estatusreserva_id', $estatus);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar();
    }

    public function actualizacion_habitacion_detallereservacion($id) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $id);
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('precio', $this->precio);
        $objeto2->add_data('registro', 'NOW()');
        $objeto2->ejecutar();
    }

    public function actualizacion_estatus_detallereservacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar('reservacion_id');
    }

    public function actualizacion_habitacion_detallereservacion01() {


        $obj = new padreModelo();
        $sql = "select id from detallereservacion where reservacion_id = '" . $this->reservacion_id . "' and cargado is null and estatu = true";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {

            $sql = "insert into detallereservacion
            (select ( select nextval('detallereservacion_id_seq') ), '" . $this->nuevo_reservacion_id . "', estatusreserva_id,
                tipotarifa_id, dia, registro, usuario, estatu, pago_id,
            '" . $this->precio . "', valoriva, ctacliente_id, facturado, fechafacturado, cargado,
            cliente_id,'" . $this->habitacion_id . "' from detallereservacion where id = " . $data[$i]['id'] . " )";

            $obj->ejecutar_query($sql);

            $id_nuevo = $obj->verId('detallereservacion');
            $this->actualizacion_detallereservacion_estatus($data[$i]['id'], 115);
            $this->actualizacion_habitacion_detallereservacion($id_nuevo);
        }
    }

    public function actualizacion_habitacion_reservacion01() {



        $obj = new padreModelo();

        $sql = "  insert into reservacion
            (select ( select nextval('reservacion_id_seq') ),
            cliente_id , '" . $this->habitacion_id . "' ,  estadoreservacion_id ,  desde ,  hasta ,  hora_entrada ,
  hora_salida ,  observacion ,  registro ,  usuario ,
  estatu ,  garantiareservacion_id ,  empresa_id ,  codigo ,
  personas ,  precio_unitario ,  iva  ,  total  ,  tarifa ,  usuario_cancelacion ,
  observacion_cancelacion ,  fecha_cancelacion ,  tarifa_id,
  descuento ,  descuento2,  valoriva ,  checkin,
  contacto,  contactoinfo,  medioreservacion   
            from reservacion where id =  '" . $this->reservacion_id . "' )";

        $obj->ejecutar_query($sql);
        $id_nuevo = $obj->verId('reservacion');
        $this->nuevo_reservacion_id = $id_nuevo;



        $sql = "select personas from reservacion where id =  '" . $this->reservacion_id . "' ";
        $res1 = $obj->ejecutar_query($sql);
        $personas = $res1[0]['personas'];
        $precio = 0;



        $sql = "select servicio_precio,servicio_denominacion from vw_tarifa where tarifa_categoria_id  = ( select categoria_id from habitacion where id='" . $this->habitacion_id . "' ) 
and tarifa_personas='" . $personas . "' ";
        $res2 = $obj->ejecutar_query($sql);
        $precio = $res2[0]['servicio_precio'];
        $nombre_tarifa = $res2[0]['servicio_denominacion'];


        if ($precio < 1) {

            $sql = "select servicio_precio,servicio_denominacion from vw_tarifa where tarifa_categoria_id  = ( select categoria_id from habitacion where id='" . $this->habitacion_id . "' ) 
and tarifa_personas='1' ";
            $res2 = $obj->ejecutar_query($sql);
            $precio = $res2[0]['servicio_precio'];
            $nombre_tarifa = $res2[0]['servicio_denominacion'];
        }


        $this->precio = $precio;


        if ($this->verificar_detallereservacion_sin_cargos()) {

            $obj->setConfig('reservacion', $this->reservacion_id);
            $obj->add_data('estadoreservacion_id', '3');
            $obj->add_data('observacion_sistema', "CAMBIO DE HABITACION POR ID-RSV  " . $this->nuevo_reservacion_id . "  ");
            $obj->ejecutar();
        } else {

            $obj->setConfig('reservacion', $this->reservacion_id);
            $obj->add_data('hasta', $this->desde);
            $obj->ejecutar();
        }



        $this->actualizacion_reservacion_cambiohabitacion($id_nuevo, $personas, $precio, $nombre_tarifa);

        $resultado['nuevo_reservacion_id'] = $this->nuevo_reservacion_id;
        $resultado['precio'] = $this->precio;


        return $resultado;
    }

    public function actualizacion_reservacion_cambiohabitacion($id, $personas, $precio, $nombre_tarifa) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $id);
        $objeto2->add_data('desde', $this->desde);
        $objeto2->add_data('hasta', $this->hasta);
        $objeto2->add_data('registro', 'NOW()');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('personas', $personas);
        $objeto2->add_data('precio_unitario', $precio);
        $objeto2->add_data('tarifa', $nombre_tarifa);
        $objeto2->ejecutar();
    }

    public function reparar_extremos() {

        $obj = new padreModelo();

        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>='" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);
    }

    public function buscar_fechas_extremos() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE SALIDA */
        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is null order by id desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where  estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is null order by id asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];

        return $data;
    }

    public function actualizacion_cargado_detallereservacion() {

        $obj = new padreModelo();
        $sql = "select id from detallereservacion where  estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is null order by id asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $data2[0]['id']);
        $objeto2->add_data('cargado', 'NOW()');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function buscar_ctahuesped_con_reservacion_id() {
        $obj = new padreModelo();
        $sql = "select  ctacliente_id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and estatu=true limit 1";
        $data1 = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data1[0]['ctacliente_id'];
        return $this->ctahuesped_id;
    }

    public function actualizacion_detallereservacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);

        $objeto2->ejecutar('reservacion_id');
    }

    public function actualizacion_extension_reservacion_sin_cargo() {

        $obj = new padreModelo();
        $obj->setConfig('reservacion', $this->reservacion_id);
        $obj->add_data('desde', $this->desde);
        $obj->add_data('hasta', $this->hasta);
        $obj->ejecutar();
    }

    public function actualizacion_extension_reservacion_con_cargo() {

        $obj = new padreModelo();
        $obj->setConfig('reservacion', $this->reservacion_id);
        $obj->add_data('hasta', $this->hasta);
        $obj->ejecutar();
    }

    public function actualizacion_extension_detallereservacion() {
        $obj = new padreModelo();
        $fechas = $this->diasentrefechas();



        for ($i = 1; $i < count($fechas) - 1; $i++) {

            $sql = "insert into detallereservacion
            (select ( select nextval('detallereservacion_id_seq') ), '" . $this->reservacion_id . "', estatusreserva_id,
                tipotarifa_id,'" . $fechas[$i] . "',registro, usuario, 'TRUE', pago_id,
           precio, valoriva, ctacliente_id, facturado, fechafacturado, cargado,
            cliente_id,habitacion_id,true from detallereservacion where reservacion_id = " . $this->reservacion_id . " and cargado is null   order by id desc limit 1 )";

            $obj->ejecutar_query($sql);
        }

        $this->reparar_extremos();
    }

    public function actualizar_cambios_fechas_detallereservacion() {

        $obj = new padreModelo();
        $sql = "update  detallereservacion set estatu=false where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is  null and clon=FALSE ";
        $obj->ejecutar_query($sql);

#$sql = "update  detallereservacion set clon=false where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is  null  and clon='true'";
#$obj->ejecutar_query($sql);
    }

    public function verificar_detallereservacion_sin_cargos() {
        $obj = new padreModelo();
        $sql = "select  id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is not null";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function reporte_reservacion06_reservacion_id() {


        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where reservacion_id='" . $this->reservacion_id . "'  ";
        $data = $obj->ejecutar_query($sql);

        if (($data[0]['estadoreservacion_id'] <= 2) || ($data[0]['estadoreservacion_id'] == 4)) {
            $this->codigo = $data[0]['reservacion_codigo'];
        } else {

            $obj = new padreModelo();
            $sql = "select * from vw_reservacion where reservacion_codigo='" . $data[0]['reservacion_codigo'] . "'  and  estadoreservacion_id in (2,1,4) ";
            $data = $obj->ejecutar_query($sql);
            $this->reservacion_id = $data[0]['reservacion_id'];
            $this->codigo = $data[0]['reservacion_codigo'];
        }


        $data[0]['checkout'] = $this->verificar_checkout_reservacion();

        $data2 = $this->verificar_checkin_reservacion();
        $data[0]['checkin'] = $data2[0]['checkin'];
        $data[0]['validar_nocheckin'] = $data2[0]['validar_nocheckin'];

        $sql = "select r.*,m.nombre1 as formapago from reservacion r join maestro m on m.id = r.formapago_id where r.id in ('" . $this->reservacion_id . "') ";
        $data5 = $obj->ejecutar_query($sql);

        $data[0]['reservacion_motivo'] = $data5[0]['motivo'];
        $data[0]['reservacion_destino'] = $data5[0]['destino'];
        $data[0]['reservacion_origen'] = $data5[0]['origen'];
        $data[0]['formapago_id'] = $data5[0]['formapago_id'];
        $data[0]['reservacion_formapago'] = $data5[0]['formapago'];
        $data[0]['personas'] = $data5[0]['personas'];
        $data[0]['reservacion_noches'] = $this->restar_fechas($data[0]['reservacion_desde'], $data[0]['reservacion_hasta']);


        $data[0]['acompanantes'] = $this->RSV_ver_huespedes_reservacion();


        return $data;
    }

    public function verificar_garantia_reservacion_automatica() {

        $obj = new padreModelo();
        $sql = "select * from vw_reservacion where reservacion_id='" . $this->reservacion_id . "' ";
        $data = $obj->ejecutar_query($sql);
        $id_garantia = $data[0]['garantiareservacion_id'];

        $sql = "select * from garantiareservacion where id='" . $id_garantia . "'";
        $data = $obj->ejecutar_query($sql);

        if (strlen($data['0']['referencia']) > 0) {


            $objeto2 = new padreModelo();
            $objeto2->setConfig('garantiareservacion', $id_garantia);
            $objeto2->add_data('activacion', 'TRUE');
            $objeto2->add_data('validacion', 'NOW()');
            $objeto2->add_data('usuario_validador', $this->usuario);
            $objeto2->ejecutar();
        } else {
            
        }
    }

    public function validacion_fecha_checkin() {

        $fecha = $this->buscar_fechas_extremos();
        $obj = new padreModelo();
        $sql = "select 'NOW()'::date -'" . $fecha['desde'] . "'::date as resultado from reservacion limit 1";
        $data = $obj->ejecutar_query($sql);


        if ($data[0]['resultado'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function verificar_checkin_reservacion() {

        $obj = new padreModelo();
        $sql = "select checkin from reservacion where id in ('" . $this->reservacion_id . "') and estatu=true";
        $data = $obj->ejecutar_query($sql);

        if (strlen($data[0]['checkin']) > 1) {
            $data[0]['validar_nocheckin'] = 0;
            return $data;
        } else {

            if ($this->validacion_fecha_checkin()) {
                $data[0]['validar_nocheckin'] = 1;
            } else {
                $data[0]['validar_nocheckin'] = 0;
            }


            return $data;
        }
    }

    public function verificar_checkout_reservacion() {

        $obj = new padreModelo();
        $sql = "select checkout from reservacion where id in ('" . $this->reservacion_id . "') and estatu=true ";
        $data = $obj->ejecutar_query($sql);

        if (strlen($data[0]['checkout']) > 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public function ver_productos() {
        $obj = new padreModelo();
        $sql = "select  * from facturacion.producto where  estatu=true";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function ver_productos_por_habitacion() {
        $obj = new padreModelo();
        $sql = "select  * from vw_detallepedido where habitacion_id='" . $this->habitacion_id . "'";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function registrar_pedidodetalle() {


        $objeto1 = new padreModelo();
        $sql = "select * from vw_producto where producto_id in ( " . $this->producto_id . " );";
        $data = $objeto1->ejecutar_query($sql);


        $objeto2 = new padreModelo();
        $objeto2->setConfig('pedidodetalle');
        $objeto2->add_data('pedido_id', $this->pedido_id);
        $objeto2->add_data('producto_id', $this->producto_id);
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('cantidad', $this->cantidad);
        $objeto2->add_data('monto', $data[0]['producto_costo']);
        $objeto2->add_data('exento', $data[0]['producto_exento']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function registrar_calculospedido() {

        $calculo = $this->calculo_pedido();

        $objeto2 = new padreModelo();
        $objeto2->setConfig('pedido', $this->pedido_id);
        $objeto2->add_data('monto', $calculo['monto']);
        $objeto2->add_data('porcentajeiva', $this->iva());
        $objeto2->add_data('iva', $calculo['iva']);
        $objeto2->add_data('exento', $calculo['exento']);
        $objeto2->add_data('total', $calculo['total']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
    }

    public function registrar_pedido01() {

        $this->responsable_ctahuesped();

        $objeto2 = new padreModelo();
        $objeto2->setConfig('pedido');
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('ctacliente_id', $this->ctahuesped_id);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('monto', '0');
        $objeto2->add_data('porcentajeiva', $this->iva());
        $objeto2->add_data('iva', '0');
        $objeto2->add_data('exento', '0');
        $objeto2->add_data('total', '0');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        $this->pedido_id = $objeto2->verId('pedido');
        return $this->pedido_id;
    }

    public function registrar_pedido($p, $c) {

        if (!$this->ctahuesped_id) {
            $this->ctahuesped_id = 0;
        }


        $this->registrar_pedido01();

        for ($i = 0; $i < count($p); $i++) {
            $this->producto_id = $p[$i];
            $this->cantidad = $c[$i];
            if ($this->cantidad > 0)
                $this->registrar_pedidodetalle();
        }

        $this->registrar_calculospedido();
    }

    public function buscar_ctahuesped_roomservice() {
        $obj = new padreModelo();
        $sql = "select ctacliente_id  from detallereservacion where dia::date='NOW()'  and habitacion_id='" . $this->habitacion_id . "' and estatu=true limit 1";
        $data = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data[0]['ctacliente_id'];
        return $data[0]['ctacliente_id'];
    }

    public function responsable_ctahuesped() {
        $obj = new padreModelo();
        $sql = " select cliente_id from vw_profile where ctacliente_id in ('" . $this->ctahuesped_id . "') and responsable=true limit 1";
        $data = $obj->ejecutar_query($sql);
        $this->cliente_id = $data[0]['cliente_id'];
        return $data[0]['cliente_id'];
    }

    public function calculo_pedido() {
        $obj = new padreModelo();
        $sql = " select * from pedidodetalle where estatu=true and pedido_id  in ('" . $this->pedido_id . "')";
        $data = $obj->ejecutar_query($sql);

        $total['iva'] = 0;
        $total['total'] = 0;
        $total['monto'] = 0;
        $total['exento'] = 0;

        for ($i = 0; $i < count($data); $i++) {

            if ($data[$i]['exento'] == "t") {

                $iva = 0;
                $exento = $data[$i]['cantidad'] * $data[$i]['monto'];
                $total = $exento;
                $monto = $exento;
            } else {
                $exento = 0;
                $monto = $data[$i]['cantidad'] * $data[$i]['monto'];
                $iva = $monto * $this->valoriva();
                $total = $monto + $iva;
            }


            $total1['iva'] = $total1['iva'] + $iva;
            $total1['total'] = $total1['total'] + $total;
            $total1['monto'] = $total1['monto'] + $monto;
            $total1['exento'] = $total1['exento'] + $exento;
        }




        return $total1;
    }

    /* CAMBIO HABITACION */

    public function CH_CtaHuesped() {
        $obj = new padreModelo();
        $sql = "select  ctacliente_id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and estatu=true limit 1";
        $data1 = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data1[0]['ctacliente_id'];
        return $this->ctahuesped_id;
    }

    public function CH_fechas_extremos() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        echo $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE SALIDA */
        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is not null order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where    reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true order by dia asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];



        return $data;
    }

    public function CH_fechas_extremos_con_checkin() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is null order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where    reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true order by dia asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];



        return $data;
    }

    public function CH_fechas_extremos_sin_checkin() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is  null order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where    reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true order by dia asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];



        return $data;
    }

    public function CH_reservaciones_bloqueadas() {
        $obj = new padreModelo();
        $sql = "select  id from reservacion where id = '" . $this->reservacion_id . "'  and estadoreservacion_id in (4,3) and estatu='true' ";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function CH_ver_habitaciones() {
        $obj = new padreModelo();
        $sql = "select id from habitacion where estatu=true 
    and categoria_id in (" . $this->categoria . ") ";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $res[$i] = $data[$i]['id'];
        }
        return $res;
    }

    public function CH_no_disponibles() {

        $obj = new padreModelo();

        $sql = "select habitacion_id from estadohabitacion where
     ( (desde::date>='" . $this->desde . "' and desde::date<='" . $this->hasta . "') or 
     (hasta::date>='" . $this->desde . "' and hasta::date<='" . $this->hasta . "')  or 
     (desde::date<='" . $this->desde . "' and hasta::date>='" . $this->hasta . "') ) 
     
    ";

        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $res[$i] = $data[$i]['habitacion_id'];
        }

        return $res;
    }

    public function CH_ver_disponibilidad() {
        $array1 = $this->CH_no_disponibles();
        $array2 = $this->CH_ver_habitaciones();
        $resultado = array_diff($array2, $array1);
        return $resultado;
    }

    public function CH_salida_habitaciones_disponibles_detalle() {
        $obj = new padreModelo();
        $res = $this->CH_ver_disponibilidad();


        $i = 0;
        foreach ($res as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $sql = "select * from vw_habitacion where habitacion_id in (" . $lista . ") ";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function CH_salida_habitaciones_disponibles_resumen() {
        $obj = new padreModelo();
        $res = $this->CH_ver_disponibilidad();
        $i = 0;
        foreach ($res as $key) {
            if ($i == 0)
                $lista.=$key;
            else
                $lista.="," . $key;
            $i++;
        }
        $sql = "select count(categoria) as cantidad, categoria as categoria  , categoria_id as  categoria_id , color from vw_habitacion where habitacion_id in (" . $lista . ") group by categoria,color ,categoria_id";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function CH_actualizacion_habitacion_reservacion01() {



        /* SINO TIENE CHECKIN ENTONCES ACTUALIZA SIN CHECKIN */
        if ($this->CH_verificar_detallereservacion_sin_cargos()) {
            $checkin = " checkin";
        } else {
            $checkin = " 'NOW()' ";
        }


        $obj = new padreModelo();

        $sql = "  insert into reservacion
            (select ( select nextval('reservacion_id_seq') ),
            cliente_id , '" . $this->habitacion_id . "' ,  estadoreservacion_id ,  desde ,  hasta ,  hora_entrada ,
  hora_salida ,  observacion ,  registro ,  usuario ,
  estatu ,  garantiareservacion_id ,  empresa_id ,  codigo ,
  personas ,  precio_unitario ,  iva  ,  total  ,  tarifa ,  usuario_cancelacion ,
  observacion_cancelacion ,  fecha_cancelacion ,  tarifa_id,
  descuento ,  descuento2,  valoriva ," . $checkin . ",
  contacto,  contactoinfo,  medioreservacion   
            from reservacion where id =  '" . $this->reservacion_id . "' )";

        $obj->ejecutar_query($sql);
        $id_nuevo = $obj->verId('reservacion');
        $this->nuevo_reservacion_id = $id_nuevo;



        $sql = "select personas from reservacion where id =  '" . $this->reservacion_id . "' ";
        $res1 = $obj->ejecutar_query($sql);
        $personas = $res1[0]['personas'];
        $precio = 0;



        $sql = "select servicio_precio,servicio_denominacion from vw_tarifa where tarifa_categoria_id  = ( select categoria_id from habitacion where id='" . $this->habitacion_id . "' ) 
and tarifa_personas='" . $personas . "' ";
        $res2 = $obj->ejecutar_query($sql);
        $precio = $res2[0]['servicio_precio'];
        $nombre_tarifa = $res2[0]['servicio_denominacion'];


        if ($precio < 1) {

            $sql = "select servicio_precio,servicio_denominacion from vw_tarifa where tarifa_categoria_id  = ( select categoria_id from habitacion where id='" . $this->habitacion_id . "' ) 
and tarifa_personas='1' ";
            $res2 = $obj->ejecutar_query($sql);
            $precio = $res2[0]['servicio_precio'];
            $nombre_tarifa = $res2[0]['servicio_denominacion'];
        }


        $this->precio = $precio;


        if ($this->CH_verificar_detallereservacion_sin_cargos()) {

            $obj->setConfig('reservacion', $this->reservacion_id);
            $obj->add_data('estadoreservacion_id', '3');
            $obj->add_data('observacion_sistema', "CAMBIO DE HABITACION POR ID-RSV  " . $this->nuevo_reservacion_id . "  ");
            $obj->ejecutar();
        } else {

            //   $historico = $this->CH_verificar_detallereservacion_con_cargos_viejos();

            $obj->setConfig('reservacion', $this->reservacion_id);
            $obj->add_data('hasta', $this->desde);
            $obj->add_data('estadoreservacion_id', '4');
            $obj->ejecutar();
        }



        $this->CH_actualizacion_reservacion_cambiohabitacion($id_nuevo, $personas, $precio, $nombre_tarifa);

        $resultado['nuevo_reservacion_id'] = $this->nuevo_reservacion_id;
        $resultado['precio'] = $this->precio;


        return $resultado;
    }

    public function CH_verificar_detallereservacion_sin_cargos() {
        $obj = new padreModelo();
        $sql = "select  id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is not null and estatu='true' ";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function CH_verificar_detallereservacion_con_cargos_viejos() {
        $obj = new padreModelo();
        $sql = "select  id,dia from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "' and estatu='true'  and cargado is not null and dia::date <  'NOW()' order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return $data1[0]['dia'];
        } else {
            return false;
        }
    }

    public function CH_actualizacion_reservacion_cambiohabitacion($id, $personas, $precio, $nombre_tarifa) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $id);
        $objeto2->add_data('desde', $this->desde);
        $objeto2->add_data('hasta', $this->hasta);
        $objeto2->add_data('registro', 'NOW()');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('personas', $personas);
        $objeto2->add_data('precio_unitario', $precio);
        $objeto2->add_data('tarifa', $nombre_tarifa);
        $objeto2->ejecutar();
    }

    public function CH_actualizacion_habitacion_detallereservacion01() {


        if ($this->CH_verificar_detallereservacion_sin_cargos() == 1) {

            $obj = new padreModelo();
            $sql = "select id from detallereservacion where reservacion_id = '" . $this->reservacion_id . "' and cargado is null and estatu = true";
            $data = $obj->ejecutar_query($sql);
        } else {

            $obj = new padreModelo();
            $sql = "select id from detallereservacion where reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true ";
            $data = $obj->ejecutar_query($sql);
        }



        for ($i = 0; $i < count($data); $i++) {

            $sql = "insert into detallereservacion
            (select ( select nextval('detallereservacion_id_seq') ), '" . $this->nuevo_reservacion_id . "', estatusreserva_id,
                tipotarifa_id, dia, registro, usuario, estatu, pago_id,
            '" . $this->precio . "', valoriva, ctacliente_id, facturado, fechafacturado, cargado,
            cliente_id,'" . $this->habitacion_id . "' from detallereservacion where id = " . $data[$i]['id'] . " )";

            $obj->ejecutar_query($sql);

            $id_nuevo = $obj->verId('detallereservacion');
            $this->CH_actualizacion_detallereservacion_estatus($data[$i]['id'], 115);
            $this->CH_actualizacion_habitacion_detallereservacion($id_nuevo);
        }
    }

    public function CH_actualizacion_detallereservacion_estatus($id, $estatus) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $id);
        $objeto2->add_data('estatusreserva_id', $estatus);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar();
    }

    public function CH_actualizacion_habitacion_detallereservacion($id) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $id);
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('precio', $this->precio);
        $objeto2->add_data('registro', 'NOW()');
        $objeto2->ejecutar();
    }

    /* TABLA DE LAS TARIFAS */

    public function TRF_generar_tabla_tarifas02() {
        $tarifa01 = $this->TRF_ver_reservaciones_tarifa();
        $tabla_tarifa = $this->TRF_reporte_tabla_tarifa02($tarifa01);
        return $tabla_tarifa;
    }

    public function TRF_ver_reservaciones_tarifa() {
        $obj = new padreModelo();
        $sql = "select * from vw_prereservacion_tarifas_todas where reservacion_codigo='" . $this->codigo . "' and reservacion_estadoreservacion_id in (2,1,4);";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combo'] = $this->TRF_combos_personas($data[$i]['habitacion_nombre'], $data[$i]['categoria_id'], $data[$i]['reservacion_id'], $data[$i]['reservacion_personas']);
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['combotarifa'] = $this->combostarifa01($data[$i]['reservacion_id']);
        }

        return $data;
    }

    public function TRF_combos_personas($name, $categoria, $id, $valor) {

        $sal.="<select name='$name' style='' class='form-control form-control2' onchange='combo(" . $id . ",this.value)'>";
        $sal.="<option value='" . $valor . "'>" . $valor . "</option>";
        $sal.="<option value='1'>1</option>";
        $sal.="<option value='2'>2</option>";
        $sal.="<option value='3'>3</option>";
        $sal.="<option value='4'>4</option>";
        return $sal.="</select>";
    }

    public function TRF_reporte_tabla_tarifa02($tarifa01) {


        $total = 0;
        $tabla.="    
            
        <div id='cambiotarifa' style='padding:20px;'>   
      
        </div>  
        

        <table class = 'table table-hover' style = 'font-size: 13px'>
        <tr>
            <th style = 'text-align:left;width:15%'>Estatus Actual</th>
            <th style = 'text-align:left;width:15%'>Categoria</th>
            <th style = 'text-align:left;width:15%'>Tarifa</th>            
            <th style = 'text-align:center;width:5%'>N° Pers.</th>
            <th style = 'text-align:center;width:10%'>Desde</th>
            <th style = 'text-align:center;width:10%'>Hasta</th>            
            <th style = 'text-align:center;width:5%'>Noches</th>
            <th style = 'text-align:right;width:15%'>Costo</th>         
            <th style = 'text-align:right;width:15%'>Total</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {


            $codigo = $tarifa01[$i]['reservacion_codigo'] . "-" . $tarifa01[$i]['reservacion_id'];
            $codigo = $this->encrypt($codigo);

            $actual = 0;

            if ($_SESSION['temp']['reservacion_id']) {
                if ($tarifa01[$i]['reservacion_id'] == $_SESSION['temp']['reservacion_id']) {
                    $actual = 1;
                }
            }

            if ($actual == 1)
                $tabla.="<tr style='background:#F2F5A9'>";
            else
                $tabla.="<tr>";


            $tabla.=" <td  style='text-align:left;'>" . $tarifa01[$i]['reservacion_estadoreservacion'] . "</td>  "
                    . " <td  style='text-align:left;'>"
                    . "<a class='btn btn-block btn-default btn-xs' href='?opcion=busquedareservacion&codigo=" . $codigo . "'  style='color:red'><i class='fa fa-eye'></i> " . $tarifa01[$i]['habitacion_nombre'] . "</strong> </span>" . $tarifa01[$i]['habitacion_categoria'] . "</a></td>
                    <td  style='text-align:left;'>" . $tarifa01[$i]['servicio_nombre'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_personas'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_desde'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_hasta'] . "</td>                          
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio']) . "</td>                  
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias']) . "</td>                                  
                </tr>";

            $total = ( $tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias'] ) + $total;
            $iva = ($total * $this->valoriva());
        }


        $tabla.="
<tr>
            <td>            </td>
            <td>            </td>        
            <td>            </td>         
            <td>            </td>              
            <td>           </td>     
            <td>            </td>            
            <td>            </td>              
            <td  style='text-align:right;font-size:14px; font-weight: bold' >  Impuestos </td>
            <td  style='text-align:right; font-size:14px; font-weight: bold;' >" . $this->bs2($iva) . "</td>                                  
        </tr>            
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>      
            <td>            </td>
            <td>            </td>         
            <td>            </td>              
            <td>            </td>              
            <td  style='text-align:right;font-size:15px; font-weight: bold' >  TOTAL </td>
            <td  style='text-align:right; font-size:15px; font-weight: bold;' >" . $this->bs2($total + $iva) . "</td>                                  
        </tr>
        </table>";

        return $tabla;
    }

    public function TRF_combostarifa01($reservacion_id) {

        if ($reservacion_id) {
            $obj1 = new padreModelo();
            $sql = "select tarifa_id from reservacion  where  id in (" . $reservacion_id . ");";
            $data = $obj1->ejecutar_query($sql);
            $tarifa_id = $data[0]['tarifa_id'];
        }

        $sal.="<select name='combotarifa_id' id='combotarifa_id' style='width:100%'  onchange='combotarifa(" . $reservacion_id . ",this.value)' class='form-control form-control2 ' >";
        $obj = new padreModelo();
        $sql = "select * from administrativo.tarifa where  estatu='true' order by id asc ";
        $data = $obj->ejecutar_query($sql);

        foreach ($data as $dato) {

            if ($tarifa_id == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['denominacion'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['denominacion'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

    public function TRF_generar_tabla_tarifas_paraeditar() {
#  $tarifa01 = $this->reporte_reservacion05();
        $tarifa01 = $this->TRF_ver_reservaciones_tarifa();
        $tabla_tarifa = $this->TRF_reporte_tabla_tarifa($tarifa01);
        return $tabla_tarifa;
    }

    public function TRF_reporte_tabla_tarifa($tarifa01) {

        $total = 0;
        $tabla.="    
  <div id='cambiotarifa' style='padding:20px;'> </div>            

        <table class = 'table table-hover' style = 'font-size: 13px'>
        <tr>
            <th style = 'text-align:left;width:15%'>Estatus Actual</th>
            <th style = 'text-align:left;width:15%'>Categoria</th>
            <th style = 'text-align:left;width:15%'>Tarifa</th>            
            <th style = 'text-align:center;width:5%'>N° Pers.</th>
            <th style = 'text-align:center;width:10%'>Desde</th>
            <th style = 'text-align:center;width:10%'>Hasta</th>            
            <th style = 'text-align:center;width:5%'>Noches</th>
            <th style = 'text-align:right;width:15%'>Costo</th>         
            <th style = 'text-align:right;width:15%'>Total</th>
        </tr>";

        for ($i = 0; $i < count($tarifa01); $i++) {
            $actual = 0;

            if ($_SESSION['temp']['reservacion_id']) {
                if ($tarifa01[$i]['reservacion_id'] == $_SESSION['temp']['reservacion_id']) {
                    $actual = 1;
                }
            }

            if ($actual == 1)
                $tabla.="<tr style='background:#F2F5A9'>";
            else
                $tabla.="<tr>";

            $tabla.=" 
                     <td  style='text-align:left;'>" . $tarifa01[$i]['reservacion_estadoreservacion'] . "</td> 
                    <td  style='text-align:left;'><span style='color:red'><strong>" . $tarifa01[$i]['habitacion_nombre'] . "</strong> </span>" . $tarifa01[$i]['habitacion_categoria'] . "</td>
                    <td  style='text-align:left;'>" . $tarifa01[$i]['combotarifa'] . "</td>
                    <td  style='text-align:center;'>" . $tarifa01[$i]['combo'] . "</td>         
                     <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_desde'] . "</td>  
                    <td  style='text-align:center;'>" . $tarifa01[$i]['reservacion_hasta'] . "</td>                           
                    <td  style='text-align:center;' >" . $tarifa01[$i]['dias'] . "</td>       
                    <td  style='text-align:right;' ><a  onclick='editar(" . $tarifa01[$i]['reservacion_id'] . ")'><i class='fa fa-pencil'></i></a>  " . $this->bs2($tarifa01[$i]['servicio_precio']) . "</td>                  
                    <td  style='text-align:right;' > " . $this->bs2($tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias']) . "</td>                                  
                </tr>";

            $total = ( $tarifa01[$i]['servicio_precio'] * $tarifa01[$i]['dias'] ) + $total;
            $iva = ($total * $this->valoriva());
        }

        $tabla.="
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>         
            <td>            </td>              
            <td>            </td>         
            <td>            </td>        
            <td>            </td>              
            <td  style='text-align:right;font-size:14px; font-weight: bold' >  Impuestos </td>
            <td  style='text-align:right; font-size:14px; font-weight: bold;' >" . $this->bs2($iva) . "</td>                                  
        </tr>            
<tr>
            <td>            </td>
            <td>            </td>            
            <td>            </td>        
            <td>            </td>         
            <td>            </td>             
           <td>            </td>           
            <td>            </td>              
            <td  style='text-align:right;font-size:15px; font-weight: bold' >  TOTAL </td>
            <td  style='text-align:right; font-size:15px; font-weight: bold;' >" . $this->bs2($total + $iva) . "</td>                                  
        </tr>
        </table>";

        return $tabla;
    }

    /* CHECKIN */

    public function CHK_ver_reservaciones() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion02 where estadoreservacion_id in (1) and checkout is null order by reservacion_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function CHK_actualizacion_reservacion($post) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('origen', $post['origen']);
        $objeto2->add_data('destino', $post['destino']);
        $objeto2->add_data('formapago_id', $post['formapago_id']);
        $objeto2->add_data('checkin', 'NOW');
        $objeto2->add_data('estadoreservacion_id', 1);
        $objeto2->add_data('motivo', $dato['motivo']);
        $objeto2->ejecutar();
        return $this->reservacion_id;
    }

    /* CAMBIO DE FECHAS */

    public function CF_diasentrefechas() {

        $fecha_entrada = new DateTime($this->desde);
        $fecha_salida = new DateTime($this->hasta);

        $interval = $fecha_entrada->diff($fecha_salida);
        $resultado = $interval->format('%R%a');


        for ($i = 0; $i <= $resultado; $i++) {

            $fechas[$i] = $fecha_entrada->format('d-m-Y');
            $v = 'P1D';
            $fecha_entrada->add(new DateInterval($v));
        }

        return $fechas;
    }

    public function CF_CtaHuesped() {
        $obj = new padreModelo();
        $sql = "select  ctacliente_id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and estatu=true limit 1";
        $data1 = $obj->ejecutar_query($sql);
        $this->ctahuesped_id = $data1[0]['ctacliente_id'];
        return $this->ctahuesped_id;
    }

    public function CF_fechas_extremos_con_checkin() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is not null order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where    reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true order by dia asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];



        return $data;
    }

    public function CF_fechas_extremos_sin_checkin() {

        $obj = new padreModelo();

        /* SE BUSCAN LAS FECHAS DE LA OCUPACION */
        $sql = "select desde,hasta,id from reservacion where estatu=true and  id = '" . $this->reservacion_id . "'";
        $data0 = $obj->ejecutar_query($sql);

        /* SE COLOCA FALSO LAS FECHAS DE DETALLERESERVACION QUE NO ESTEN EN EL RANGO DEL REGISTRO DE OCUPACION */
        $sql = "update detallereservacion set estatu=false where (dia<'" . $data0[0]['desde'] . "' or dia>'" . $data0[0]['hasta'] . "') and reservacion_id  in ('" . $this->reservacion_id . "')";
        $obj->ejecutar_query($sql);

        $sql = "select to_char((dia::date+1), 'DD/MM/YYYY') as fecha from detallereservacion where estatu=true and  reservacion_id = '" . $this->reservacion_id . "' and cargado is  null order by dia desc limit 1";
        $data1 = $obj->ejecutar_query($sql);

        /* OBTENEMOS LA FECHA DE ENTRADA */
        $sql = "select to_char(dia, 'DD/MM/YYYY') as fecha from detallereservacion where    reservacion_id = '" . $this->reservacion_id . "' and dia::date>='NOW()'  and estatu = true order by dia asc limit 1";
        $data2 = $obj->ejecutar_query($sql);

        $data['desde'] = $data2[0]['fecha'];
        $data['hasta'] = $data1[0]['fecha'];



        return $data;
    }

    public function CF_verificar_detallereservacion_sin_cargos() {
        $obj = new padreModelo();
        $sql = "select  id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is not null";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function CF_verificar_detallereservacion_dia_cargado($fecha) {
        $obj = new padreModelo();
        $sql = "select  id from detallereservacion where  reservacion_id = '" . $this->reservacion_id . "'  and cargado is not null and dia::date='" . $fecha . "'";
        $data1 = $obj->ejecutar_query($sql);
        if ($data1[0]['id'] > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function CF_cambiar_estatus_detallereservacion($fecha) {
        $obj = new padreModelo();
        $sql = "update  detallereservacion  set estatu='false'  where  reservacion_id = '" . $this->reservacion_id . "'  and dia::date='" . $fecha . "' and cargado is  null";

        $data1 = $obj->ejecutar_query($sql);
    }

    public function CF_actualizacion_extension_reservacion_sin_cargo() {

        $obj = new padreModelo();
        $obj->setConfig('reservacion', $this->reservacion_id);
        $obj->add_data('desde', $this->desde);
        $obj->add_data('hasta', $this->hasta);
        $obj->ejecutar();
    }

    public function CF_actualizacion_extension_detallereservacion() {
        $obj = new padreModelo();
        $fechas = $this->CF_diasentrefechas();



        for ($i = 0; $i < count($fechas) - 1; $i++) {


            $this->CF_cambiar_estatus_detallereservacion($fechas[$i]);

            $sql = "insert into detallereservacion
            (select ( select nextval('detallereservacion_id_seq') ), '" . $this->reservacion_id . "', estatusreserva_id,
                tipotarifa_id,'" . $fechas[$i] . "',registro, usuario, 'TRUE', pago_id,
           precio, valoriva, ctacliente_id, facturado, fechafacturado, cargado,
            cliente_id,habitacion_id,true from detallereservacion where reservacion_id = " . $this->reservacion_id . " and cargado is null   order by id desc limit 1 )";

            $val = $this->CF_verificar_detallereservacion_dia_cargado($fechas[$i]);

            if ($val == 0) {
                $obj->ejecutar_query($sql);
            }
        }

        $this->reparar_extremos();
    }

    public function CF_restar_dias($fecha, $dias) {
        $obj = new padreModelo();
        $sql = "select to_char(('" . $fecha . "'::Date-" . $dias . "),'DD/MM/YYYY')  as resultado from reservacion limit 1";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['resultado'];
    }

    public function CF_actualizacion_estatus_detallereservacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('detallereservacion', $this->reservacion_id);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar('reservacion_id');
    }

    public function CF_actualizacion_extension_reservacion_con_cargo() {

        $obj = new padreModelo();
        $obj->setConfig('reservacion', $this->reservacion_id);
        $obj->add_data('hasta', $this->hasta);
        $obj->ejecutar();
    }

    /* CUENTAS HUESPEDES */

    public function CTH_detalle_ctahuespedes() {
        $data1 = $this->ver_ctahuespedes();

        for ($i = 0; $i < count($data1); $i++) {

            /* si es  82 es decir si es asociada */
            if ($data1[$i]['tipocta_id'] == $this->tipocuenta_asociada) {
                $this->ctahuesped_id = $data1[$i]['ctahuesped_id'];
                $this->ctahuesped_id = $this->CTH_buscar_padre_ctaasociada();
                $data1[$i]['ctahuesped_id'] = $this->ctahuesped_id;
                $codigo = $this->CTH_buscar_codigoid_ctaasociada();
                $data1[$i]['cta_huesped_tipocta'] = "Asociada a " . $codigo;
                $data1[$i]['totales'] = $this->get_totales();
            } else {

                $this->ctahuesped_id = $data1[$i]['ctahuesped_id'];
                $data1[$i]['totales'] = $this->get_totales();
            }

            $data1[$i]['get'] = $this->encrypt($data1[$i]['ctahuesped_id']);
        }

        return $data1;
    }

    public function CTH_ver_ctahuespedes() {
        $obj = new padreModelo();
        $sql = "select * from vw_ctahuesped order by ctahuesped_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    private function CTH_buscar_codigoid_ctaasociada() {
        $obj = new padreModelo();
        $sql = "select codigo  from facturacion.ctacliente where id=" . $this->ctahuesped_id . "  and estatu=true;";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['codigo'];
    }

    private function CTH_buscar_padre_ctaasociada() {
        $obj = new padreModelo();
        $sql = "select ctacliente01_id  from facturacion.ctacliente_grupo where ctacliente02_id=" . $this->ctahuesped_id . ";";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['ctacliente01_id'];
    }

    public function CTH_icono_tipoctahuesped($data) {



        return $data;
    }

    public function CTH_icono_garantiareservacion01() {

        if (!$this->garantiareservacion_id)
            return "";
        $obj = new padreModelo();
        $sql = "select referencia,montobloqueo from garantiareservacion where estatu=true and id=" . $this->garantiareservacion_id;
        $data = $obj->ejecutar_query($sql);

        return $data[0];
    }

    public function CTH_icono_garantiareservacion02($data) {

        for ($i = 0; $i < count($data); $i++) {
            $this->garantiareservacion_id = $data[$i]['garantiareservacion_id'];

            $garantia = $this->CTH_icono_garantiareservacion01();

            $data[$i]['montobloqueo'] = $garantia['montobloqueo'];

            if (strlen($garantia['referencia']) > 0)
                $data[$i]['garantia'] = "<i class='fa fa-credit-card text-green'></i>";
            else
                $data[$i]['garantia'] = "<i class='fa fa-credit-card text-red'></i>";
        }





        return $data;
    }

    /* RACK */

    public function RCK_ver_habitaciones_rack01() {
        $obj = new padreModelo();
        $sql = "    select * from vw_habitacion01 order by habitacion_y ,   habitacion_x";
        $data = $obj->ejecutar_query($sql);



        for ($i = 0; $i < count($data); $i++) {

            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_id'] = $data[$i]['habitacion_id'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_nombre'] = $data[$i]['habitacion_nombre'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_categoria'] = $data[$i]['habitacion_categoria'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['habitacion_color'] = $data[$i]['habitacion_color'];
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['validacionocupacion_icono'] = $this->RCK_ver_checkin_rack01($data[$i]['habitacion_id']);




            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['supervision_icono'] = $this->RCK_ver_supervision_rack01($data[$i]['habitacion_id']);
            $data2 = $this->RCK_ver_estadoocupacion_rack01($data[$i]['habitacion_id']);
            $res[$data[$i]['habitacion_y']][$data[$i]['habitacion_x']]['estadoocupacion_color'] = $this->RCK_color_estadoocupacion($data2['estadoocupacion_id']);
        }


        return $res;
    }

    public function RCK_ver_habitacion_rack01() {
        $obj = new padreModelo();
        $sql = "    select * from vw_habitacion01 where habitacion_id=" . $this->habitacion_id . ";";
        $data = $obj->ejecutar_query($sql);

        return $data[0];
    }

    public function RCK_ver_estadoocupacion_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = "    select * from vw_amallaves where habitacion_id=" . $habitacion_id . " order by amallaves_id desc limit 1 ;";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function RCK_ver_supervision_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_supervision where supervision_solucionado = 'NO' and habitacion_id in (" . $habitacion_id . " ) ;";
        $data = $obj->ejecutar_query($sql);

        if ($data[0]['cantidad'] > 0)
            return "<i class='fa fa-exclamation-triangle text-yellow'></i>";
        else
            return "";
    }

    public function RCK_ver_checkin_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_checkin where habitacion_id in (" . $habitacion_id . " ) ;";
        $data = $obj->ejecutar_query($sql);

        if ($data[0]['cantidad'] > 0)
            return "<i class='fa fa-user text-white'></i>";
        else
            return "";
    }

    public function RCK_verificarocupacion_rack01($habitacion_id) {

        $obj = new padreModelo();
        $sql = "  select * from ocupacion where habitacion_id='" . $habitacion_id . "' and estado='O';";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function RCK_icono_verificarocupacion($id) {

        if ($id == "O")
            return " <i class='fa fa-user'></i> ";
        else
            return "";
    }

    public function RCK_validaciones_ocupacion($checkout) {
        $hoy = date('d/m/Y');
        $hoy = $this->fecha_alreves($hoy);

        $fechahoy = new DateTime($hoy);
        $fechacheckout = new DateTime($checkout);
        $interval01 = $fechahoy->diff($fechacheckout);
        $resultado01 = $interval01->format('%R%a');

        if ($resultado01 >= 1) {
            return "<img src='dist/img/alertaverde.gif' /> <strong>" . $resultado01 . "<strong>";
        }

        if ($resultado01 < 0) {
            return "<img src='dist/img/alerta.gif' /> <strong>" . $resultado01 . "<strong>";
        }
    }

    public function RCK_color_estadoocupacion($id) {

        if ($id == "19")
            return "success";
        if ($id == "20")
            return "primary";
        if ($id == "21")
            return "warning";
        if ($id == "22")
            return "danger";
    }

    public function RCK_ver_supervision_habitacion_rack($habitacion_id) {



        $obj = new padreModelo();
        $sql = " select * from vw_supervision where supervision_solucionado = 'NO' and habitacion_id in (" . $habitacion_id . " ) ;";
        $data = $obj->ejecutar_query($sql);

        return $data;
    }

    public function RCK_datos_reservacion() {
        $obj = new padreModelo();
        $sql = "select * from reservacion where estatu=true and checkin is not null and habitacion_id='" . $this->habitacion_id . "' order by  checkin::date desc limit 1";
        $data = $obj->ejecutar_query($sql);
        $this->reservacion_id = $data[0]['id'];
        return $data;
    }

    public function RCK_datos_reservacion_tarifa() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion01 where reservacion_id in ( " . $this->reservacion_id . " ) ";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function RCK_datos_reservacion_para_hoy() {
        $obj = new padreModelo();
        $sql = "   select count(*) as cantidad from reservacion where desde::date = 'now()' and
    cliente_id is not null and habitacion_id is not null and estatu = true ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function RCK_datos_reservacion_checkin() {
        $obj = new padreModelo();
        $sql = "   select count(*) as cantidad from reservacion where desde::date = 'now()' and
    cliente_id is not null and habitacion_id is not null and checkin is not null and estatu = true ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function RCK_datos_reservacion_checkout() {
        $obj = new padreModelo();
        $sql = "   select count(*) as cantidad from reservacion where desde::date = 'now()' and
    cliente_id is not null and habitacion_id is not null and checkout is not null and estatu = true ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    /* AMA DE LLAVES */

    public function AML_registro_amallaves01() {

        $this->AML_actualizar_estados();

        $objeto2 = new padreModelo();
        $objeto2->setConfig('amallaves');
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('estadoocupacion_id', $this->estadoocupacion_id);
        $objeto2->add_data('observacion', $this->observacion);
        $objeto2->add_data('actualizado', '1');
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $amallaves_id = $objeto2->verId('amallaves');
        return $amallaves_id;
    }

    public function AML_actualizar_estados() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('amallaves', $this->habitacion_id);
        $objeto2->add_data('actualizado', '0');
        $objeto2->ejecutar('habitacion_id');
    }

    public function AML_ver_resumen_rack() {
        $obj = new padreModelo();
        $sql = "  select  total from vw_amallaves_resumen where estado = 'VL'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['VL'] = $data[0]['total'];
        $data[0]['total'] = 0;


        $sql = "  select  total from vw_amallaves_resumen where estado = 'VS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['VS'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'OL'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['OL'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'OS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;

        $total['OS'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'FS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;

        $total['FS'] = $data[0]['total'];

        return $total;
    }

    /* CAMBIO DE HUESPED PRINCIPAL */

    public function RSV_actualizacion_huesped_principal() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function RSV_registro_cliente($dato) {

        $objeto2 = new padreModelo();
        $sql = "  select  count(*) as cantidad from cliente where documento='" . $dato['documento'] . "'  ;";
        $data = $objeto2->ejecutar_query($sql);

        if ($data[0]['cantidad'] > 0)
            return false;

        $empresa = strlen($dato['nombre_empresa']);


        if ($dato['cliente_id'] > 0) {
            $objeto2->setConfig('cliente', $dato['cliente_id']);
        } else {
            $objeto2->setConfig('cliente');
        }

        $objeto2->add_data('empresa_id', $empresa);
        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('apellido', $dato['apellido']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('nacionalidad', $dato['nacionalidad']);
        $objeto2->add_data('idioma', $dato['idioma']);
        $objeto2->add_data('pais', $dato['pais']);
        $objeto2->add_data('ciudad', $dato['ciudad']);
        $objeto2->add_data('vip', $dato['vip']);
        $objeto2->add_data('preferencia', $dato['preferencia']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->ejecutar();

        if ($dato['cliente_id'] > 0) {
            $this->cliente_id = $dato['cliente_id'];
        } else {
            $this->cliente_id = $objeto2->verId('cliente');
        }

        return $this->cliente_id;
    }

    public function RSV_delete_huesped() {

        $objeto2 = new padreModelo();
        $sql = "  select  count(*) as cantidad from acompanante_reservacion where reservacion_id in (" . $this->reservacion_id . ") and cliente_id in (" . $this->cliente_id . ") and estatu=true  ";
        $data = $objeto2->ejecutar_query($sql);

        if ($data[0]['cantidad'] < 2)
            return false;

        $sql = "  UPDATE  acompanante_reservacion set estatu=false where reservacion_id in (" . $this->reservacion_id . ") and cliente_id in (" . $this->cliente_id . ") and estatu=true  ";
        $data = $objeto2->ejecutar_query($sql);
    }

    public function RSV_registro_huesped_reservacion() {

        $objeto2 = new padreModelo();
        $sql = "  select  count(*) as cantidad from acompanante_reservacion where reservacion_id in (" . $this->reservacion_id . ") and cliente_id in (" . $this->cliente_id . ") and estatu=true  ";
        $data = $objeto2->ejecutar_query($sql);

        if ($data[0]['cantidad'] > 0)
            return false;

        $objeto2->setConfig('acompanante_reservacion');
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        return $objeto2->verId('acompanante_reservacion');
    }

    public function RSV_actualizacion_huesped_principal01() {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function RSV_checkout_reservacion($post) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('checkout', 'NOW()');
        $objeto2->add_data('usuario_checkout', $this->usuario);
        $objeto2->add_data('observacion_checkout', $post['observacionhospedaje']);
        $objeto2->ejecutar();
    }

    public function RSV_checkout_detallereservacion() {

        $obj = new padreModelo();
        $sql = "update  detallereservacion set estatusreserva_id = '99' , estatu='false' where reservacion_id in (" . $this->reservacion_id . ") and cargado is null ";
        $data = $obj->ejecutar_query($sql);
    }

    public function RSV_registro_checkout_estadohabitacion() {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('amallaves');
        $objeto2->add_data('habitacion_id', $this->habitacion_id);
        $objeto2->add_data('estadoocupacion_id', 20);
        $objeto2->add_data('observacion', " CHECKOUT ");
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $amallaves_id = $objeto2->verId('amallaves');
        return $amallaves_id;
    }

    public function RSV_ver_reservaciones_checkout() {
        $obj = new padreModelo();
        $sql = "select * from vw_reservacion02 where estadoreservacion_id in (1) and checkout is not null order by reservacion_id desc";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function RSV_registro_reservacion04($post) {

        $obj = new padreModelo();
        $sql = "select * from reservacion where id='" . $this->reservacion_id . "' and estatu='true' limit 1;";
        $data = $obj->ejecutar_query($sql);

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', "'" . $data[0]['codigo'] . "'");
        $objeto2->add_data('contacto', $post['contacto']);
        $objeto2->add_data('contactoinfo', $post['contactoinfo']);
        $objeto2->add_data('medioreservacion', $post['medioreservacion']);
        $objeto2->add_data('origen', $post['origen']);
        $objeto2->add_data('destino', $post['destino']);
        $objeto2->add_data('formapago_id', $post['formapago_id']);
        $objeto2->add_data('motivo', $post['motivo']);
        $objeto2->ejecutar('codigo');
    }

    public function RSV_nuevoacompañante() {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('reservacion', $this->reservacion_id);
        $objeto2->add_data('cliente_id', $this->cliente_id);
        $objeto2->ejecutar();
    }

    public function RSV_ver_acompanantes() {
        $obj = new padreModelo();
        $sql = "select * from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "';";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function RSV_ver_acompanante($documento) {
        $obj = new padreModelo();
        $sql = "select * from vw_acompanante where acompanante_codigoid='" . $this->codigoid . "'  and  acompanante_documento = '" . $documento . "';";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function RSV_registro_acompanante($dato) {

        if ($this->vacio($dato['acompanante_nombre'], 'Verifique  Nombre Completo'))
            return false;
        if ($this->vacio($dato['acompanante_nacimiento'], 'Verifique Fecha de nacimiento'))
            return false;

        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante');
        $objeto2->add_data('reservacion_id', $this->reservacion_id);
        $objeto2->add_data('documento', $dato['documento']);
        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('genero', $dato['acompanante_genero']);
        $objeto2->add_data('nacimiento', $dato['acompanante_nacimiento']);
        $objeto2->add_data('contacto', $dato['acompanante_contacto']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->acompanante_id = $objeto2->verId('acompanante');
        return $this->acompanante_id;
    }

    public function RSV_edicion_acompanante($dato) {
        if ($this->vacio($dato['acompanante_nombre'], 'Verifique  Nombre Completo'))
            return false;
        if ($this->vacio($dato['acompanante_nacimiento'], 'Verifique Fecha de nacimiento'))
            return false;


        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante', $dato['acompanante_id']);
        $objeto2->add_data('nombre', $dato['acompanante_nombre']);
        $objeto2->add_data('genero', $dato['acompanante_genero']);
        $objeto2->add_data('nacimiento', $dato['acompanante_nacimiento']);
        $objeto2->add_data('contacto', $dato['acompanante_contacto']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->acompanante_id = $id;
        return $this->acompanante_id;
    }

    public function RSV_eliminar_acompanante($dato) {

        $objeto2 = new padreModelo();
        $objeto2->setConfig('acompanante', $dato['acompanante_id']);
        $objeto2->add_data('estatu', 'false');
        $objeto2->ejecutar();
        $this->acompanante_id = $id;
        return $this->acompanante_id;
    }

    public function RSV_ver_huespedes_reservacion() {
        $obj = new padreModelo();
        $sql = "select * from vw_cliente where cliente_id in "
                . "(select  cliente_id  from acompanante_reservacion where estatu=true and reservacion_id in (" . $this->reservacion_id . ") ) ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    /* TARJETA DE REGISTRO */

    public function TRD_ver_datos_clientes() {
        $obj = new padreModelo();
        $sql = "    select * from vw_cliente where cliente_id in (" . $this->cliente_id . ")";
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function TRD_ver_datos_clientes_hoy() {
        $obj = new padreModelo();
        $sql = "select * from vw_cliente where cliente_id in (
                select cliente_id from reservacion where
               ( (desde::date>='NOW()'  and desde::date<='NOW()') or 
               (hasta::date>='NOW()' and hasta::date<='NOW()' )  or 
               (desde::date<='NOW()' and hasta::date>='NOW()' ) )
               and estatu=true  and estadoreservacion_id in (1)
        )";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    /* DASHBOARD2 */

    public function DSH2_huespedes_checkin() {
        $obj = new padreModelo();
        $sql = " select sum(personas) as cantidad from vw_checkin  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_huespedes_checkin_list() {
        $obj = new padreModelo();
        $sql = " select * from vw_cliente where cliente_id in (select cliente_id  from vw_checkin  )  ";
        $data = $obj->ejecutar_query($sql);



        return $data;
    }

    public function DSH2_reservaciones_pendientes() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_reservaciones_enespera  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_reservaciones_pendientes_list() {
        $obj = new padreModelo();
        $sql = " select * from vw_reservaciones_enespera  ";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['get'] = $this->encrypt($data[$i]['reservacion_codigoid']);
        }

        return $data;
    }

    public function DSH2_reservaciones_checkin_hoy() {
        $obj = new padreModelo();
        $sql = " select *  from vw_checkin ";
        $data = $obj->ejecutar_query($sql);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['get'] = $this->encrypt($data[$i]['codigo_id']);
        }

        return $data;
    }

    public function DSH2_cantidad_checkin_hoy() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from  vw_checkin where reservacion_checkin::date = 'NOW()'  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_cantidad_ocupaciones() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from  vw_checkin   ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_cantidad_checkout_hoy() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_checkout   where reservacion_checkin::date = 'NOW()'  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_cantidad_checkout_fecha($date = "NOW()") {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_checkout where reservacion_checkout = '" . $date . "'  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_cantidad_checkin_fecha($date = "NOW()") {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_checkin where reservacion_checkin = '" . $date . "'  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_ver_resumen_rack_amallaves() {
        $obj = new padreModelo();
        $sql = "  select  total from vw_amallaves_resumen where estado = 'VL'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['VL'] = $data[0]['total'];
        $data[0]['total'] = 0;


        $sql = "  select  total from vw_amallaves_resumen where estado = 'VS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['VS'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'OL'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;
        $total['OL'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'OS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;

        $total['OS'] = $data[0]['total'];
        $data[0]['total'] = 0;

        $sql = "  select  total from vw_amallaves_resumen where estado = 'FS'";
        $data = $obj->ejecutar_query($sql);
        if ($data[0]['total'] < 1)
            $data[0]['total'] = 0;

        $total['FS'] = $data[0]['total'];

        return $total;
    }

    public function DSH2_habitaciones_activas() {
        $obj = new padreModelo();
        $sql = "     select count(*) as cantidad from vw_amallaves where actualizado=1 and estadoocupacion_id not in (56) order by habitacion_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_fueraservicio() {
        $obj = new padreModelo();
        $sql = "     select count(*) as cantidad from vw_amallaves where actualizado=1 and estadoocupacion_id  in (56)  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_ocupadas() {
        $obj = new padreModelo();
        $sql = "     select count(*) as cantidad from vw_amallaves where actualizado=1 and estadoocupacion_id  in (21,22)  ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_vacantes() {
        $obj = new padreModelo();
        $sql = "select 
count(*) as cantidad 
from vw_amallaves where 
actualizado=1 and estadoocupacion_id in (19,20) ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_sucias() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_amallaves where actualizado=1 and estadoocupacion_id  in (20,22) order by habitacion_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_limpias() {
        $obj = new padreModelo();
        $sql = " select count(*) as cantidad from vw_amallaves where actualizado=1 and estadoocupacion_id  in (20,22) order by habitacion_id desc ";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['cantidad'];
    }

    public function DSH2_habitaciones_ocupadas_categoria_hoy() {

        /* habitacion ocupadas hoy por categoria -- reservadas y checkin  */
        $obj = new padreModelo();
        $sql = "select count(h.categoria) as cantidad,h.categoria  as categoria from reservacion r join vw_habitacion h on h.habitacion_id=r.habitacion_id where
     ( (r.desde::date>='NOW()'  and r.desde::date<='NOW()') or 
     (r.hasta::date>='NOW()' and r.hasta::date<='NOW()')  or 
     (r.desde::date<='NOW()' and r.hasta::date>='NOW()') ) 
     and r.estatu=true  and r.estadoreservacion_id in (2,1) group by h.categoria";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function DSH2_tarifa_de_habitaciones() {

        /* habitacion ocupadas hoy por categoria -- reservadas y checkin  */
        $obj = new padreModelo();
        $sql = "select * from vw_tarifa ";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

}
?>

