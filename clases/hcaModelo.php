<?php

require_once 'padreModelo.php';

class hcaModelo extends padreModelo {



public function formato_fecha_alreves($fecha){
$val1	=	substr($fecha,4,1);

        if(($val1=='-')||($val1=='/')) { /* SI ES POR EL METODO CELULAR  */
            return true ;
	}else{ /* SI ES POR EL METODO PC  */
            return false;
        }    
    
}

public function validar_registro_ocupacion($fecha){
           
          
            $obj = new padreModelo();
            $sql ="select * from validacion_ocupacion where estatu='TRUE' and fecha='".$this->fecha_alreves($fecha)."'";
            $data = $obj->ejecutar_query($sql);

            if($data[0]['id']){
                $salida               = true;
            }else{
                $salida               = false;         
            }
                
            return $salida;
            
}

/*  antes era validar_registro_ocupacion2*/
public function validar_registro_ocupacion_confirmacion($fecha){
           
    
            $obj = new padreModelo();
            $sql ="select * from validacion_ocupacion where estatu='TRUE' and fecha='".$this->fecha_alreves($fecha)."'";
            $data = $obj->ejecutar_query($sql);

            if($data[0]['id']){
                $salida               = true;
            }else{
                $salida               = false;         
            }
                
            return $salida;
            
}



public  function fecha_ddmmyyyy($fecha){
	    $yy =substr($fecha,0,4);
	    $mm =substr($fecha,5,2);
	    $dd =substr($fecha,8,2);
	    $fecha = $dd."/".$mm."/".$yy;
	    return $fecha;
}


public function cantidad_ocupaciones_rack($fecha){
    
   $h = $this->buscar_habitaciones();
   $total = 0;
   for($i=0;$i<count($h);$i++){
   $ocupacion= $this->buscar_ocupacion($fecha,$h[$i]['id']);
   $total = $total + $ocupacion;
   }
   return $total;
}


public function consultar_fuera_servicio($fecha){
        
    
        $obj = new padreModelo();
        $sql1="select cantidad from fuera_servicio
	where estatu='TRUE' and
	fecha::date='".$this->fecha_alreves($fecha)."' and
	habitacion='41' order by id desc limit 1";
	$data1 = $obj->ejecutar_query($sql1);      
        
        $resultado['fuera_servicio']=$data1[0]['cantidad']; 
            
        
        
        $sql2="select cantidad from fuera_servicio
	where estatu='TRUE' and
	fecha::date='".$this->fecha_alreves($fecha)."' and
	habitacion='42' order by id desc limit 1";
	$data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['disponible']=$data2[0]['cantidad'];
        
        

        $sql2="select cantidad from fuera_servicio
    where estatu='TRUE' and
    fecha::date='".$this->fecha_alreves($fecha)."' and
    habitacion='43' order by id desc limit 1";
    $data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['huespedes']=$data2[0]['cantidad'];


        
        $sql2="select cantidad from fuera_servicio
    where estatu='TRUE' and
    fecha::date='".$this->fecha_alreves($fecha)."' and
    habitacion='44' order by id desc limit 1";
    $data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['vacante']=$data2[0]['cantidad'];
  

        
        $sql2="select cantidad from fuera_servicio
    where estatu='TRUE' and
    fecha::date='".$this->fecha_alreves($fecha)."' and
    habitacion='45' order by id desc limit 1";
    $data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['multivenca']=$data2[0]['cantidad'];
        
        
        
        $sql2="select cantidad from fuera_servicio
    where estatu='TRUE' and
    fecha::date='".$this->fecha_alreves($fecha)."' and
    habitacion='46' order by id desc limit 1";
    $data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['uso_casa']=$data2[0]['cantidad'];


        
        $sql2="select cantidad from fuera_servicio
    where estatu='TRUE' and
    fecha::date='".$this->fecha_alreves($fecha)."' and
    habitacion='47' order by id desc limit 1";
    $data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['complementaria']=$data2[0]['cantidad'];

        
        $resultado['total_disponible'] = $resultado['multivenca']+$resultado['uso_casa']+$resultado['complementaria']+$resultado['vacante'];
        $resultado['total_ocupadas']    = $resultado['multivenca']+$resultado['uso_casa']+$resultado['complementaria'];
        $resultado['total_no_ocupadas'] = $resultado['fuera_servicio']+$resultado['vacante'];
        $resultado['total']             = $resultado['multivenca']+$resultado['complementaria']+$resultado['uso_casa']+$resultado['fuera_servicio']+$resultado['vacante'];


       
        

        return $resultado;
    
}












public function consultar_fuera_servicio_reporte($fecha){
        
    
        $obj = new padreModelo();
        $sql1="select cantidad from fuera_servicio
	where estatu='TRUE' and
	fecha::date='".$this->fecha_alreves($fecha)."' and
	habitacion='41' order by id desc limit 1";
	$data1 = $obj->ejecutar_query($sql1);      
        
        $resultado['fuera_servicio']=$data1[0]['cantidad']; 
            
        $sql2="select cantidad from fuera_servicio
	where estatu='TRUE' and
	fecha::date='".$this->fecha_alreves($fecha)."' and
	habitacion='42' order by id desc limit 1";
	$data2 = $obj->ejecutar_query($sql2);    
        
        $resultado['disponible']=$data2[0]['cantidad'];
        
        return $resultado;
    
}




public function fecha_alreves($fecha){
	$dd =substr($fecha,0,2);
	$mm =substr($fecha,3,2);
	$yy =substr($fecha,6,4);
	$fecha = $yy."/".$mm."/".$dd;
	return $fecha;
}
  
   
   
   
public function registrar_validacion($fecha){
      
    	    $objeto2 = new padreModelo();
            $objeto2->setConfig('validacion_ocupacion');
	    $objeto2->add_data('usuario', '7');
            $objeto2->add_data('fecha',$this->fecha_alreves($fecha));                      
            $r = $objeto2->ejecutar();
            
            if($objeto2->getId()){
                $salida    = true;
            }else{
                $salida    = false;         
            }
    
            return $salida;
}   

   
function buscar_habitaciones(){
	$obj = new padreModelo();
	$sql="	select * from habitaciones ";
	$data = $obj->ejecutar_query($sql);
	return $data;
}


function buscar_habitacion($id){
	$obj = new padreModelo();
	$sql="	select * from habitaciones where id=".$id;
	$data = $obj->ejecutar_query($sql);
	return $data;
}


/* TRAMIENTOS DE FECHAS */

function buscar_ocupacion_1($fecha,$habitacion){

	$dd =substr($fecha,0,2);
	$mm =substr($fecha,3,2);
	$yy =substr($fecha,6,4);
	$fecha = $yy."/".$mm."/".$dd;

	$obj = new padreModelo();
        $sql="select cantidad from ocupacion
	where estatu='TRUE' and
	fecha::date='".$fecha."' and
	habitacion='".$habitacion."' order by id desc limit 1";

	$data = $obj->ejecutar_query($sql);
	
	if($data[0]['cantidad']>0)
	return $data[0]['cantidad'];
	else
	return '0';
	
}


/* esta funcion  se aplica para ocupacion_registro.php lsdajax del campo servicio - receptor.php servicio */
public function validar_registro_ocupacion_servicio($fecha){
           
            $obj = new padreModelo();
            $sql ="select * from validacion_ocupacion where estatu='TRUE' and fecha='".$this->fecha_alreves($fecha)."'";
            $data = $obj->ejecutar_query($sql);

            if($data[0]['id']){
                $salida               = true;
            }else{
                $salida               = false;         
            }             
            return $salida;         
}








public function validar_registro_ocupacion_disponible($fecha){
           

            $obj    = new padreModelo();
            $sql    ="select * from validacion_ocupacion where estatu='TRUE' and fecha='".$this->fecha_alreves($fecha)."'";
            $data   = $obj->ejecutar_query($sql);

            if($data[0]['id']){
                $salida               = true;
            }else{
                $salida               = false;         
            }
                
            return $salida;
            
}




/* FIN DE ESOS TRATAMIENTOS */







function buscar_ocupacion($fecha,$habitacion){


	$obj = new padreModelo();
        $sql="select cantidad from ocupacion
	where estatu='TRUE' and
	fecha::date='".$this->fecha_alreves($fecha)."' and
	habitacion='".$habitacion."' order by id desc limit 1";

	$data = $obj->ejecutar_query($sql);
	
	if($data[0]['cantidad']>0)
	return $data[0]['cantidad'];
	else
	return '0';
	
}   



#FUNCIONES ESPECIALES #
   
public function ocupacion_edicion_01($fecha,$id){


    $validacion = $this->validar_registro_ocupacion($fecha);

    if(!$validacion){
    $habitacion 			= $this->buscar_habitacion($id);
    $ocupacion['cantidad']   		= $this->buscar_ocupacion($fecha,$habitacion[0]['id']);	  
    $ocupacion['id'] 			= $habitacion[0]['id'];
    $ocupacion['habitacion']		= $habitacion[0]['habitacion'];
    $ocupacion['fecha']			= $fecha;
    $ocupacion['valor']			=  true;  
    }else{
    
    $habitacion 			= "Bloqueado";
    $ocupacion['cantidad']   		= "Bloqueado";
    $ocupacion['id'] 			= "Bloqueado";
    $ocupacion['habitacion']		= "Bloqueado";
    $ocupacion['fecha']			=  $fecha;
    $ocupacion['valor']			=  false;     
    }
     return $ocupacion;      
}



   
   
}

?>
