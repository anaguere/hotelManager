<?php

require_once 'Conexion.php';

class padreModelo extends conexion {

    private $id;
    private $id_padre;
    private $tabla;
    private $nuevo;
    private $sql_campo;
    private $sql_valor;
    private $correlativo;
    private $numero_documento;
    private $sigla;
    public $estatu = "true";

    public function vacio($var, $msj = "") {

        if (strlen($var) > 0) {

            return false;
        } else {

            if (strlen($msj) > 0) {
                echo $msj;
            }

            return true;
        }
    }

    public function novacio($var) {

        if (strlen($var) > 0)
            return true;
        else
            return false;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTabla($tabla) {
        $this->tabla = $tabla;
    }

    public function setConfig($tabla, $id = null) {
        $this->tabla = $tabla;
        $this->id = $id;

        if (!$id) {
            $this->nuevo = true;
//            $operacion = 'nuevo';
        } else {
//            $operacion = 'editar';
        }
//        $this->historico($this->id, $operacion);
    }

    public function getId() {
        return $this->id;
    }

    public function ejecutar_query($query) {
        $this->abrirConexionPg();
        $this->sql = $query;
        $data = $this->ejecutarSentenciaPg(2);
        return $data;
    }

    public function add_data($campo, $valor, $strtoupper = TRUE) {


        strlen($valor) <= 0 ? $valor = 'null , ' : $valor = "'" . trim(pg_escape_string($strtoupper ? strtoupper(strtr($valor, "àáâãäåæçèéêëìíîïðñòóôõöøùüú", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ")) : $valor)) . "', ";


        if (isset($this->nuevo)) {//Nuevo Registro
            $this->sql_campo.=$campo . ',';
            $this->sql_valor.=$valor;
        } else {//Actualización de Registro
            $this->sql_valor.= $campo . " = " . $valor;
        }
    }

    public function add_($campo, $valor, $strtoupper = TRUE) {

        strlen($valor) <= 0 ? $valor = false : $valor = "'" . trim(pg_escape_string($strtoupper ? strtoupper(strtr($valor, "àáâãäåæçèéêëìíîïðñòóôõöøùüú", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ")) : $valor)) . "', ";
        if ($valor != false) {
            if (isset($this->nuevo)) {//Nuevo Registro
                $this->sql_campo.=$campo . ',';
                $this->sql_valor.=$valor;
            } else {//Actualización de Registro
                $this->sql_valor.= $campo . " = " . $valor;
            }
        }
    }

    public function ejecutar($tipo_id = 'id') {



        $this->abrirConexionPg();
        if (isset($this->nuevo)) {//Nuevo Registro
            $this->sql = " INSERT INTO " . $this->tabla . " (" . $this->sql_campo . "registro,estatu) VALUES (" . $this->sql_valor . "'now()','" . $this->estatu . "'); ";
        } else {//Actualización de Registro
            $this->sql = "UPDATE  " . $this->tabla . "  SET  " . substr($this->sql_valor, 0, -2);
            $this->sql.= " WHERE " . $tipo_id . " in (" . $this->id . ")";
        }
        $this->ejecutarSentenciaPg(2);
        if (isset($this->nuevo))//Nuevo Registro
            $this->id = $this->verId($this->tabla);
        unset($this->nuevo, $this->sql_campo, $this->sql_valor);
    }

    /**
     * Consulta la ultima id trabajada en la tabla.
     */
    public function verId($tabla) {
        $this->abrirConexionPg();
        $this->sql = "SELECT CURRVAL('" . $tabla . "_id_seq') as id";
        $data = $this->ejecutarSentenciaPg(2);
        return $data[0]['id'];
    }

    /**
     * Consulta la siguiente id de una tabla, tomada de la secuencia.
     */
    public function proxId($tabla) {
        $this->abrirConexionPg();
        $this->sql = "SELECT MAX(id)+1 as id FROM " . $tabla;
        $data = $this->ejecutarSentenciaPg(2);
        return $data[0]['id'];
    }

    public function ver_todo($condicion = false) {

        $this->abrirConexionPg();

        if (!$condicion) {
            $this->sql = "select  *  from " . $this->tabla . " where estatu=true ;";
        } else {
            $this->sql = "select  *  from " . $this->tabla . " where estatu=true " . $condicion . " ;";
        }

        $data = $this->ejecutarSentenciaPg(2);
        return $data;
    }

    public function ver_vista($vista, $condicion = '1=1') {
        $this->abrirConexionPg();
        $this->sql = "select  *  from " . $vista . " where  " . $condicion . ";";
        $data = $this->ejecutarSentenciaPg(2);
        return $data;
    }

    public function ver_uno($id, $campo = '') {
        $this->abrirConexionPg();
        if ($campo)
            $this->sql = "select  *  from " . $this->tabla . " where " . $campo . "='" . $id . "' and estatu=true";
        else
            $this->sql = "select  *  from " . $this->tabla . " where id='" . $id . "' and estatu=true";
        $data = $this->ejecutarSentenciaPg(2);
        return $data;
    }

    public function actualizar($tabla, $id, $campo, $valor) {
        $this->abrirConexionPg();
        $this->sql = "UPDATE  $tabla SET $campo='$valor'  WHERE id=$id";
        $data = $this->ejecutarSentenciaPg();
    }

    public function eliminar($campo = 'id', $tabla = FALSE) {
        $this->abrirConexionPg();
        if ($tabla)
            $this->tabla = $tabla;
        $this->sql = "UPDATE " . $this->tabla . " SET  estatu='FALSE'  WHERE $campo ='" . $_SESSION['id_eliminacion'] . "';";
        $data = $this->ejecutarSentenciaPg();
        #$this->historico($_SESSION['id_eliminacion'], 'eliminar'); OJO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        return $data;
    }

    /* public function historico($id_tabla, $operacion) {
      $this->abrirConexionPg();
      $this->sql = " insert into historico (id_usuario, id_tabla, operacion, tabla, registro,estatu) values ('" . $_SESSION['id_usuario'] . "','" . $id_tabla . "','" . $operacion . "','" . $this->tabla . "','now()','TRUE'); ";
      $this->ejecutarSentenciaPg();
      } */

    //GENERA TABLA CON [SELECT-OPTION]
    public function check_select($padre, $nombre = 'nombre', $name_check = 'select', $id_select = false, $idMasValue = false) {//(El id del padre,Nombre del campo a consultar, Nombre del check_select, id del select cuando editamos,Si necesitamos tener ID como el verdadero ID y el Value como el valor real del campo)
        $this->abrirConexionPg();
        $this->sql = "select  *  from maestro  where padre=" . $padre . " AND estatu=true order by id asc";
        $data_maestro = $this->ejecutarSentenciaPg(2);
        $select = "<select name='" . $name_check . "' id='" . $name_check . "' REQUIRED><option value=''></option>";
        for ($s = 0; $s <= count($data_maestro) - 1; $s++) {
            ($id_select == $data_maestro[$s]['id']) ? $selected = 'selected' : $selected = ''; //Solo se usa cuando queremos editar. El busca el id que le enviamos por parametro.
            if ($idMasValue)
                $select.= "<option id='" . $data_maestro[$s]['id'] . "' value='" . $data_maestro[$s][$nombre] . "' style='text-align:right' " . $selected . ">" . $data_maestro[$s]['nombre1'] . " | " . $data_maestro[$s]['nombre2'] . "</option>";
            else
                $select.= "<option value='" . $data_maestro[$s]['id'] . "' " . $selected . ">" . $data_maestro[$s][$nombre] . "</option>";
        }
        $select.="</select>";
        return $select;
    }

    //GENERA TABLA CON [CHECKBOX O RADIO] "JERARQUIA PADRE"
    public function check_unico($tipo_check, $tabla_padre, $titulo, $id_check = null, $name_check = false, $nombre = 'nombre', $condicion = '1 = 1') {//
        $this->abrirConexionPg();
        $this->sql = "select  *  from  $tabla_padre  where " . $condicion . " AND estatu=true";
        $data_padre = $this->ejecutarSentenciaPg(2);
        $tipo_check == 'radio' ? (!$name_check ? $name_check = 'name = radio required' : $name_check = 'required name = ' . $name_check) : $name_check = 'check[]';
        $tabla = "<tr><td colspan='2' class='td_titulo'><label>$titulo</label></td></tr>";
        for ($t = 0; $t <= count($data_padre) - 1; $t++) {
            ($id_check == $data_padre[$t]['id']) ? $checked = 'checked' : $checked = ''; //Solo se usa cuando queremos editar. El busca el id que le enviamos por parametro.
            $tabla.= "<tr><td><label class='label_sexy' style = 'text-align:left;
        '><input type = '$tipo_check' $name_check  id='" . $data_padre[$t]['id'] . "' value='" . $data_padre[$t]['id'] . "' $checked/>" . $data_padre[$t][$nombre] . "</label></td>";
            $t+=1;
            if (isset($data_padre[$t][$nombre])) {
                ($id_check == $data_padre[$t]['id']) ? $checked = 'checked' : $checked = '';
                $tabla.= "<td><label class='label_sexy' style = 'text-align:left;
        '><input type = '$tipo_check' $name_check id='" . $data_padre[$t]['id'] . "' value='" . $data_padre[$t]['id'] . "' $checked/>" . $data_padre[$t][$nombre] . "</label></td>";
            }
            $tabla.="</tr>";
        }
        return $tabla;
    }

    //GENERA TABLA CON [CHECKBOX O RADIO] "JERARQUIA PADRE - HIJO"
    public function check_multiple($tipo_check, $tabla_padre, $tabla_hijo, $id_relacion, $nombre = 'nombre', $condicion_padre = '1 = 1', $condicion_hijo = '1 = 1') {//(nombre tabla padre, nombre tabla hijo, id que relaciona al padre,nombre del campo a consultar, condicion)
        $this->abrirConexionPg();
        $this->sql = "select  *  from  $tabla_padre  where " . $condicion_padre . " AND estatu=true";
        $data_padre = $this->ejecutarSentenciaPg(2);
        $this->sql = "select  *  from  $tabla_hijo  where " . $condicion_hijo . "  AND estatu=true";
        $data_hijo = $this->ejecutarSentenciaPg(2);
        if (!$id_relacion)
            $id_relacion = "id_" . $tabla_padre;
        $tipo_check == 'radio' ? $name_check = 'radio' : $name_check = 'check[]';
        for ($c = 0; $c <= count($data_padre) - 1; $c++) {
            $tabla.= "<tr><td><label class='label_sexy' style='text-align:center;
        border-bottom: 1px solid #dddddd; color:#366097;'>" . $data_padre[$c][$nombre] . "</label></td>";
            $temp1 = $data_padre[$c]['id'];
            $c+=1;
            if ($data_padre[$c][$nombre]) {
                $tabla.= "<td><label class='label_sexy' style='text-align:center;border-bottom: 1px solid #dddddd; color:#366097;'>" . $data_padre[$c][$nombre] . "</label></td></tr>";
                $temp2 = $data_padre[$c]['id'];
            }

            for ($t = 0, $x = 0, $y = 0; $t <= count($data_hijo) - 1; $t++) {
                if ($data_hijo[$t][$nombre]) {
                    if ($data_hijo[$t][$id_relacion] == $temp1) {
                        $tabla_x[$x] = "<tr><td><label class='label_sexy' style = 'text-align:left;'><input type = '$tipo_check' name='$name_check' id='" . $data_hijo[$t]['id'] . "' value='" . $data_hijo[$t]['id'] . "'/>" . $data_hijo[$t][$nombre] . "</label></td>";
                        $x++;
                    }

                    if ($data_hijo[$t][$id_relacion] == $temp2) {
                        $tabla_y[$y] = "<td><label class='label_sexy' style = 'text-align:left;'><input type = '$tipo_check' name='$name_check' id='" . $data_hijo[$t]['id'] . "' value='" . $data_hijo[$t]['id'] . "'/>" . $data_hijo[$t][$nombre] . "</label></td></tr>";
                        $y++;
                    }
                }
            }

            $tx = count($tabla_x);
            $ty = count($tabla_y);

            if ($tx > $ty)
                $xy_max = $tx;
            else
                $xy_max = $ty;

            for ($xy = 0; $xy <= $xy_max - 1; $xy++) {
                if (!$tabla_x[$xy])
                    $tabla_x[$xy] = "<tr><td>";
                $tabla_xy.= "$tabla_x[$xy]$tabla_y[$xy]";
            }

            $tabla.= $tabla_xy;
            unset($temp1, $temp2, $tabla_y, $tabla_x, $tabla_xy);
        }
        return $tabla;
    }

    /*  funciones para el correlativo o numero de documento */

    private function actual_correlativo_en_maestro() {
        $this->abrirConexionPg();
        $this->sql = "select  tipo1 as correlativo  from  maestro where id=" . $this->id_padre;
        $data = $this->ejecutarSentenciaPg(2);
        return $data[0]['correlativo'];
    }

    private function nuevo_correlativo() {
        $correlativo = $this->actual_correlativo_en_maestro();
        $this->correlativo = $correlativo + 1;
        $longitud = strlen($this->correlativo);
        $ceros = (5 - $longitud);
        $ceros = $this->cero_correlativo($ceros);

        $numero_documento = $this->numero_documento = $this->sigla . "-" . date('Y') . $ceros . $this->correlativo;
        $this->actualizar_correlativo_en_maestro();
        return $numero_documento;
    }

    private function cero_correlativo($total) {
        $res = '';
        for ($i = 1; $i <= $total; $i++) {
            $res.='0';
        }
        return $res;
    }

    private function actualizar_correlativo_en_maestro() {

        $this->abrirConexionPg();
        $this->sql = " UPDATE maestro SET tipo1='" . $this->correlativo . "' WHERE id=" . $this->id_padre;
        $data = $this->ejecutarSentenciaPg(2);
        return $data[0]['correlativo'];
    }

    public function procesar_numero_documento($sigla, $id_padre) {
        $this->sigla = $sigla;
        $this->id_padre = $id_padre;
        $numero_documento = $this->nuevo_correlativo();
        return $numero_documento;
    }

    public function cargos_automaticos_reservaciones() {
        $this->abrirConexionPg();
        $this->sql = "update detallereservacion set cargado = 'NOW()' where dia::date<='NOW()' and cargado is  null";
        $data = $this->ejecutarSentenciaPg();
    }

    public function alertas($msj) {
        echo "<script>alert('" . $msj . "') </script>";
    }

    function encrypt($string) {
        /*$key = "lsdrojas@cluuf.com";
        $result = '';
        $string = base64_encode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        return base64_encode($string);
        
        }*/
        return base64_encode($string);
    }

    function decrypt($string) {
      /* $key = "lsdrojas@cluuf.com";
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
    */
        
        return  base64_decode($string);
        
    }
      

}

$objeto = new padreModelo();
#$objeto->cargos_automaticos_reservaciones();
?>
