<?php

require_once 'Conexion.php';

class formModelo extends conexion {

    public $name;
    public $value;
    public $style;
    public $id;
    public $padre;
    public $defecto;
    public $sql;
    public $data;
    public $name_select = "nombre";
    public $id_select = "id";
    public $seleccione=0;
    public $orderby = "id ";

     public function data_lsd($select, $from, $where = "1=1") {
        $this->abrirConexionPg();
       $this->sql = "select  $select  from $from  where " . $where . " order by  " .$this->orderby ." ;";
        $data_maestro = $this->ejecutarSentenciaPg(2);
        $this->data = $data_maestro;
        return $data_maestro;
    }
    
    
    

    public function select_lsd() {//(El id del padre,Nombre del campo a consultar, Nombre del check_select)
        
        
        $data = $this->data;
        $select= "<select name='" . $this->name . "' style='" . $this->style . "'  id='" . $this->id . "'>";


       if($this->seleccione==1){
        $select.= "<option value='seleccione' SELECTED >SELECCIONE</option>";
    }

        for ($s = 0; $s <= count($data) - 1; $s++) {
            
            if ($data[$s]['id']==trim($this->defecto)) {  
             $select.= "<option value='" . $data[$s][$this->id_select] . "' SELECTED >" . $data[$s][$this->name_select] . "</option>";
      } else {
            $select.="<option value='" . $data[$s][$this->id_select] . "'>" . $data[$s][$this->name_select] . "</option>";
       }
            
        }
        $select.="</select>";

        return $select;
    }


    public function select_lsd_tipo_ticket() {
      
        
  
        
        $data = $this->data;
        $select= "<select onchange=\"combo_tipo_ticket(this.value);\" name='" . $this->name . "' style='" . $this->style . "'  id='" . $this->id . "'>";

       if($this->seleccione==1){
        $select.= "<option value='seleccione' SELECTED >SELECCIONE</option>";
    }

        for ($s = 0; $s <= count($data) - 1; $s++) {



            if ($data[$s]['id']==trim($this->defecto)) {  
             $select.= "<option value='" . $data[$s][$this->id_select] . "' SELECTED >" . $data[$s][$this->name_select] . "</option>";
            } else {
            $select.="<option  value='" . $data[$s][$this->id_select] . "'>" . $data[$s][$this->name_select] . "</option>";
            }
            
        }
        $select.="</select>";

        return $select;
    }


    public function select_lsd2() {//(El id del padre,Nombre del campo a consultar, Nombre del check_select)
        $data = $this->data;

        $select.= "<select name='" . $this->name . "' style='" . $this->style . "'  id='" . $this->id . "'>";

        for ($s = 0; $s <= count($data) - 1; $s++) {

            if ($data[$s]['nombre1'] == $this->defecto) {
                $select.= "<option value='" . $data[$s][$this->id_select] . "' SELECTED >" . $data[$s][$this->name_select] . "</option>";
            } else {
                $select.="<option value='" . $data[$s][$this->id_select] . "'>" . $data[$s][$this->name_select] . "</option>";
            }
        }
        $select.="</select>";

        return $select;
    }



    public function personas_queen($defecto = "") {
        $this->name = "personas_queen";
        $this->style = "width: 500px";
        $this->seleccione=1;
        $this->id="personas_queen";   
        $this->data_lsd(' id , nombre1 ', 'maestro', 'padre = 26');
        $this->name_select = "nombre1";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }


    
    public function select_filtro_lsd($nombre) {//(El id del padre,Nombre del campo a consultar, Nombre del check_select)
        $data = $this->data;

        $select.= "<select name='" . $this->name . "' style='" . $this->style . "'  id='" . $this->id . "'>";

        for ($s = 0; $s <= count($data) - 1; $s++) {

            if ($data[$s]['id'] == $this->defecto) {
                $select.= "<option value='" . $data[$s][$this->id_select] . "' SELECTED >" . $data[$s][$this->name_select] . "</option>";
            } else {
                $select.="<option value='" . $data[$s][$this->id_select] . "'>" . $data[$s][$this->name_select] . "</option>";
            }
          
        }
        $select.="<option value='T'>TODOS LOS(AS) ".strtoupper($nombre)."</option>";
        $select.="</select>";

        return $select;
    }



    public function personal($defecto = "") {

        $this->name = "id_personal";
        $this->style = "width: 300px";
           $this->seleccione=1;    
            $this->id="id_personal";         
        $this->data_lsd(' id , nombre ', 'seguridad.vista_usuario', "id_departamento ilike  '%ND60202000%'");
        $this->name_select = "nombre";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }
   

    public function todo_personal($defecto = "") {
        $this->name = "id_todo_personal";
        $this->style = "width: 500px";
        $this->orderby = " nombre_asignacion ";
        $this->data_lsd(' id , nombre_asignacion ', ' seguridad.vista_usuario_ticket ' , ' 12=12  ');
        $this->name_select = "nombre_asignacion";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }

    public function maestro($defecto = "") {
        $this->name = "id_maestro";
        $this->style = "width: 300px";
        $this->data_lsd(' id , nombre1 ', 'maestro', ' (id  in (24)  or  padre in (24) ) ');
        $this->name_select = "nombre1";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }

    public function avance($defecto = "") {
        $this->name = "avance";
        $this->style = "width: 500px";
       $this->seleccione=1;
        $this->id="avance";   
        $this->data_lsd(' id , nombre1 ', 'vista_maestro', 'id_padre = 27');
        $this->name_select = "nombre1";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }
    

    public function tipo_ticket($defecto = "") {
        $this->name = "id_tipo_ticket";
        $this->style = "width: 500px";
        $this->seleccione=1;   
        $this->id="tipo_ticket";   
        $this->data_lsd(' id , nombre1 ', 'vista_maestro', 'id_padre = 25');
        $this->name_select = "nombre1";
        $this->defecto = $defecto;
        return $this->select_lsd_tipo_ticket();
    }  
    

    public function tipo_ticket2($padre) {

        $this->name = "id_tipo_ticket2";
        $this->style = "width: 500px";
        $this->id="tipo_ticket2";         
        $this->data_lsd(' id , nombre1 ', 'vista_maestro', 'id_padre ='.$padre);
        $this->name_select = "nombre1";
        return $this->select_lsd();
    }  
 
     public function tipo_ticket3($defecto,$padre) {

        $this->name = "id_tipo_ticket2";
        $this->style = "width: 500px";
        $this->defecto = $defecto;
        $this->id="tipo_ticket2";         
        $this->data_lsd(' id , nombre1 ', 'vista_maestro', 'id_padre ='.$padre);
        $this->name_select = "nombre1";
        return $this->select_lsd();
    } 
    

    public function unidad_apoyo($defecto = "") {
        $this->name = "id_unidad_apoyo";
        $this->style = "width: 300px";
        $this->data_lsd(' id , nombre , departamento ', 'seguridad.vista_usuario2', ' 1 = 1');
        $this->name_select = "departamento";
        $this->defecto = $defecto;
        return $this->select_lsd();
    }  
 
    public function fecha_entrada(){
        return "<input name='fecha_entrada' id='fecha_entrada' type='text' readonly >";  
    }
    
    public function fecha_salida(){
        return "<input name='fecha_salida' id='fecha_salida' type='text' readonly >";  
}



     public function total_tickets() {
        $this->abrirConexionPg();
        $this->sql = " select count(*),estatus from intranet.vista_ticket_asignacion group by estatus; ";
        $data_maestro = $this->ejecutarSentenciaPg(1);
        $this->data = $data_maestro;
        return $data_maestro;
    }
    



    

}

?>
