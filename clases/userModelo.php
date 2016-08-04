<?php

require_once 'padreModelo.php';

class userModelo extends padreModelo {

    public $usuario_id = "";
    public $usuario = "7";
    public $nombre_usuario = "";
    public $acciongrupo_id = "";

    private function pass() {

        return strtoupper(md5($this->pass));
    }

    public function registro_usuario($dato) {
        $objeto2 = new padreModelo();

        $this->usuario_cedula = $dato['cedula'];
        $id = $this->comprobar_usuario();

        if (strlen($id) > 0) {
            $this->usuario_id = $id;
            $objeto2->setConfig('seguridad.usuario', $id);
        } else {
            $objeto2->setConfig('seguridad.usuario');
        }

        $objeto2->add_data('nombre', $dato['nombre']);
        $objeto2->add_data('cedula', $dato['cedula']);
        $objeto2->add_data('login', $dato['login']);
        $objeto2->add_data('contrasena', $dato['contrasena']);
        $objeto2->add_data('correo', $dato['correo']);
        $objeto2->add_data('genero', $dato['genero']);
        $objeto2->add_data('telefono', $dato['telefono']);
        $objeto2->add_data('grupo_id', $dato['grupo_id']);
        $objeto2->add_data('cargo', $dato['cargo']);
        $objeto2->add_data('direccion', $dato['direccion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        if (strlen($id) > 0) {
            
        } else {
            $this->usuario_id = $objeto2->verId('seguridad.usuario');
        }

        return $this->usuario_id;
    }

    public function grupo($valor1) {

        $sal.="<select name='grupo_id' style='font-size:12px' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from seguridad.maestro where padre=2";
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

    public function ver_usuario() {
        $obj = new padreModelo();
        $sql = "select * from seguridad.vw_usuario where usuario_id=" . $this->usuario_id;
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function ver_acciongrupo() {
        $obj = new padreModelo();
        $sql = "select * from seguridad.vw_acciongrupo where grupoaccion_id=" . $this->acciongrupo_id;
        $data = $obj->ejecutar_query($sql);
        return $data[0];
    }

    public function comprobar_usuario() {
        $obj = new padreModelo();
        $sql = "select usuario_id from seguridad.vw_usuario where usuario_cedula='" . $this->usuario_cedula . "';";
        $data = $obj->ejecutar_query($sql);
        return $data[0]['usuario_id'];
    }

    public function reporte_tabla_usuario() {

        $obj = new padreModelo();
        $sql = "select * from seguridad.vw_usuario order by usuario_nombre asc ";
        $data = $obj->ejecutar_query($sql);

        $total = 0;
        $tabla.="    
        <table id='example1' class = 'table table-hover' style = 'font-size: 12px'>
        <tr>
            <th style = 'text-align:left;width:120px'>Nombre</th>
            <th style = 'text-align:center;width:100px'>Cargo</th>
            <th style = 'text-align:center;width:100px'>Telefono</th>
            <th style = 'text-align:right;width:100px'>Login</th>
            <th style = 'text-align:right;width:100px'>Grupo</th>
        </tr>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
                <tr>
                    <td  style='text-align:left;'><a href='?opcion=consultausuario&code=" . $data[$i]['usuario_id'] . "'>" . $data[$i]['usuario_nombre'] . "</a></td>
                    <td  style='text-align:center;'>" . $data[$i]['usuario_cargo'] . "</td>            
                    <td  style='text-align:center;' >" . $data[$i]['usuario_telefono'] . "</td>      
                    <td  style='text-align:right;' > " . $data[$i]['usuario_login'] . "</td>
                    <td  style='text-align:right;' >" . $data[$i]['usuario_grupo'] . "</td>                                  
                </tr>";
        }


        $tabla.="</table>";

        return $tabla;
    }

    public function reporte_tabla_acciones() {

        $obj = new padreModelo();
        $sql = "select * from seguridad.maestro where padre=9 and estatu='true' ";
        $data = $obj->ejecutar_query($sql);

        $total = 0;
        $tabla.="    
        <table  id='example2' class = 'table table-hover' style = 'font-size: 12px'>
        <tr>
            <th style = 'text-align:left;width:70px'>ID</th>        
            <th style = 'text-align:left;width:120px'>Nombre</th>
            <th style = 'text-align:left;width:100px'>Descripción</th>

        </tr>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
                <tr>
                    <td  style='text-align:left;'><a href='?opcion=consultausuario&code=" . $data[$i]['id'] . "'>" . $data[$i]['id'] . "</a></td>
                    <td  style='text-align:left;'>" . $data[$i]['nombre1'] . "</td>            
                    <td  style='text-align:left;' >" . $data[$i]['extra'] . "</td>      
 </tr>";
        }


        $tabla.="</table>";

        return $tabla;
    }

    public function reporte_tabla_acciongrupo() {

        $obj = new padreModelo();
        $sql = "select * from seguridad.vw_acciongrupo order  by grupo_nombre asc ";
        $data = $obj->ejecutar_query($sql);

        $tabla.="    
        <table id='example3' class = 'table table-hover' style = 'font-size: 12px'>
        <tr>
            <th style = 'text-align:left;width:50px'>ID</th>        
            <th style = 'text-align:left;width:100px'>Grupo</th>
            <th style = 'text-align:left;width:150px'>Acción</th>

        </tr>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
                <tr>
                    <td  style='text-align:left;'><a href='?opcion=consultaacciongrupo&code=" . $data[$i]['grupoaccion_id'] . "'>" . $data[$i]['grupoaccion_id'] . "</a></td>
                    <td  style='text-align:left;'><a href='?opcion=consultaacciongrupo&code=" . $data[$i]['grupoaccion_id'] . "'>" . $data[$i]['grupo_nombre'] . "</a></td>            
                    <td  style='text-align:left;' >" . $data[$i]['accion_nombre'] . "</td>      
 </tr>";
        }


        $tabla.="</table>";

        return $tabla;
    }

    public function registro_acciones($dato) {
        $objeto2 = new padreModelo();
        $objeto2->setConfig('seguridad.maestro');
        $objeto2->add_data('padre', '9');
        $objeto2->add_data('nombre1', $dato['accion_nombre']);
        $objeto2->add_data('extra', $dato['accion_descripcion']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();
        $this->accion_id = $objeto2->verId('seguridad.maestro');
        return $this->accion_id;
    }

    public function registro_acciongrupo($dato) {

          $objeto2 =new padreModelo();
          $sql = "select * from seguridad.vw_acciongrupo where accion_id='".$dato['accion_id']."' and  grupo_id='".$dato['grupo_id']."';  ";
          $data = $objeto2->ejecutar_query($sql);
          
          if($data[0]['grupoaccion_id']>0){
              return false;
          }
          
        if (strlen($this->acciongrupo_id)>0) {
            $objeto2->setConfig('seguridad.maestro', $this->acciongrupo_id);
            $objeto2->add_data('padre', '22');
            $objeto2->add_data('tipo1', $dato['accion_id']);
            $objeto2->add_data('tipo2', $dato['grupo_id']);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
            return $this->acciongrupo_id;
        } else {

            $objeto2->setConfig('seguridad.maestro');
            $objeto2->add_data('padre', '22');
            $objeto2->add_data('tipo1', $dato['accion_id']);
            $objeto2->add_data('tipo2', $dato['grupo_id']);
            $objeto2->add_data('usuario', $this->usuario);
            $objeto2->ejecutar();
            $this->acciongrupo_id = $objeto2->verId('seguridad.maestro');

            return $this->acciongrupo_id;
        }
    }

    public function acciones($valor1) {

        $sal.="<select name='accion_id' style='font-size:12px' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from seguridad.maestro where padre=9";
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

    public function secuencia($valoracepta,$valor){
        
        if ($valoracepta == $valor ){
            return true;  
        }else{
           return false;
        }
            
        
    }
    
    
    /*
      CREATE TABLE seguridad.usuario
      (
      id serial NOT NULL,
      nombre character varying(100),
      apellido character varying(100),
      cedula integer,
      ficha character varying(50),
      cargo character varying(100),
      departamento character varying(200),
      correo character varying(100),
      login character varying(50),
      contrasena character varying(32),
      registro timestamp without time zone,
      estatu boolean NOT NULL DEFAULT true,
      usuario integer,
      CONSTRAINT pk_usuario_id PRIMARY KEY (id )
      )
     *  */
}

?>
