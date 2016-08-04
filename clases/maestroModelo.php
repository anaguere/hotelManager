<?php

require_once 'padreModelo.php';

class maestroModelo extends padreModelo {

    public $id = "";
    public $tabla = "facturacion.maestro";
    public $usuario = "7";

    public function combopadre($valor1 = "") {

        $sal.="<select name='padre' style='font-size:14px' class='form-control' >";
        $obj = new padreModelo();
        $sql = "select * from " . $this->tabla . " where padre=0";
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
        $objeto2->add_data('observacion', $dato['observacion']);
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
        $sql = "select * from facturacion.vw_maestroinventario order by maestro_id desc  ";
        $data = $obj->ejecutar_query($sql);

        $total = 0;
        $tabla.="    
       
        <table id=\"example1\" class=\"table table-bordered table-striped\" style=\"font-size: 14px\">
         <thead>     
         <tr>
            <th style = 'text-align:left;width:100px'>Nombre (1)</th>   
            <td  style='text-align:left;width:70px'>Padre</td>              
            <th style = 'text-align:left;width:100px'>Nombre (2)</th>        
            <th style = 'text-align:left;width:50px'>Tipo (1)</th>   
            <th style = 'text-align:left;width:50px'>Tipo (2)</th>    
            <th style = 'text-align:left;width:100px'>Extra</th> 
            <th style = 'text-align:left;width:100px'>Observación</th>             
         </tr>      
        </thead><tbody>";

        for ($i = 0; $i < count($data); $i++) {

            $tabla.=" 
               
                <tr>
                    <td  style='text-align:left;'><a href='?codeinventario=" . $data[$i]['maestro_id'] . "'>" . $data[$i]['maestro_nombre1'] . "</a></td>   
                    <td  style='text-align:left;'>" . $data[$i]['maestro_padre'] . "</td>      
                    <td  style='text-align:left;'>" . $data[$i]['maestro_nombre2'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['maestro_tipo1'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['maestro_tipo2'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['maestro_extra'] . "</td>   
                    <td  style='text-align:left;'>" . $data[$i]['maestro_observacion'] . "</td>      
                </tr>";
        }


        $tabla.="</tbody></table>";

        return $tabla;
    }

    public function reporte_maestro01() {

        $obj = new padreModelo();
        $sql = "select * from facturacion.vw_maestroinventario where maestro_id=".$this->id.";";
        $data = $obj->ejecutar_query($sql);
        return $data;
    }

}



?>
