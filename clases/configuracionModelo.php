<?php

require_once 'padreModelo.php';

class configuracionModelo extends padreModelo {

    public $id = "";
    public $tabla = "maestro";
    public $usuario = "7";

    public function combopadre($valor1 = "") {

        $sal.="<select name='padre' style='font-size:14px' class='form-control form-control2' >";
        $obj = new padreModelo();
        $sql = "select * from " . $this->tabla . " where padre=1 or id=1";
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

    public function registro_maestro($dato) {
        $objeto2 = new padreModelo();



        if (strlen($this->id) > 0) {
            $objeto2->setConfig($this->tabla, $this->id);
        } else {
            $objeto2->setConfig($this->tabla);
        }
        $objeto2->add_data('padre', $dato['padre']);
        $objeto2->add_data('nombre1', $dato['nombre1']);
        $objeto2->add_data('nombre2', $dato['nombre2']);
        $objeto2->add_data('tipo1', $dato['tipo1']);
        $objeto2->add_data('tipo2', $dato['tipo2']);
        $objeto2->add_data('extra', $dato['extra']);
        $objeto2->add_data('usuario', $this->usuario);
        $objeto2->ejecutar();

        if (strlen($this->id) > 0) {
            
        } else {
            $this->id = $objeto2->verId($this->tabla);
        }

        return $this->id;
    }

    public function reporte_maestro() {

        $obj = new padreModelo();
        $sql = "select * from vw_configuracion order by configuracion_padre asc , configuracion_nombre1 desc   ";
        $data = $obj->ejecutar_query($sql);

        $total = 0;
        $tabla.="    
       
        <table id=\"example1\" class=\"table table-bordered table-striped\" style=\"font-size: 13px\">
         <thead>     
         <tr><th style = 'text-align:left;width:50px'>ID</th>  
            <th style = 'text-align:left;width:100px'>Nombre (1)</th>   
            <td  style='text-align:left;width:70px'>Padre</td>              
            <th style = 'text-align:left;width:50px'>Nombre (2)</th>        
            <th style = 'text-align:left;width:50px'>Tipo (1)</th>   
            <th style = 'text-align:left;width:50px'>Tipo (2)</th>    
            <th style = 'text-align:left;width:100px'>Extra</th> 
            <th style = 'text-align:left;width:50px'>Observación</th>             
         </tr>      
        </thead><tbody>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
               
                <tr> <td  style='text-align:left;'>" .$data[$i]['configuracion_id'] . "</td>      
                    <td  style='text-align:left;'><a href='?codeconfiguracion=" . $data[$i]['configuracion_id'] . "'>" . $data[$i]['configuracion_nombre1'] . "</a></td>   
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_padre'] . "</td>      
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_nombre2'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_tipo1'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_tipo2'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_extra'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['configuracion_observacion'] . "</td>      
                </tr>";
        }


        $tabla.="</tbody></table>";

        return $tabla;
    }

    public function reporte_maestro01() {

        $obj = new padreModelo();
        $sql = "select * from vw_configuracion where configuracion_id=" . $this->id . ";";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

    public function combos($padre, $defecto = "") {

        $obj1 = new padreModelo();
        $sql1 = "select extra from " . $this->tabla . " where id=" . $padre . " and estatu=true";
        $data1 = $obj1->ejecutar_query($sql1);
        
        $obj = new padreModelo();
        $sql = "select * from " . $this->tabla . " where padre=" . $padre . " and estatu=true order by nombre1 asc";
        $data = $obj->ejecutar_query($sql);
        
       
        
    $sal.="<select name='" . strtolower($data1[0]['extra']) . "' style='font-size:14px' class='form-control form-control2' >";
        foreach ($data as $dato) {

            if ($defecto == $dato['id']) {
                $sal.="<option value='" . $dato['id'] . "' selected >" . $dato['nombre1'] . "</option>";
            } else {
                $sal.="<option value='" . $dato['id'] . "'>" . $dato['nombre1'] . "</option>";
            }
        }

        return $sal.="</select>";
    }

}

?>
