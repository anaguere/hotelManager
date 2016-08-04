function get_lsd(id){
return document.getElementById(id).value;
}

function limpiar_lsd(obj){
document.getElementById(obj).value="";	
}

function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function validarPass_lsd(v1,v2) {
 
var p1 = v1;
var p2 = v2;
var espacios = false;
var cont = 0;

 // Este bucle recorre la cadena para comprobar
 // que no todo son espacios
while (!espacios && (cont < p1.length)) {
  if (p1.charAt(cont) == " ")
     espacios = true;
     cont++;
  }
 
if (espacios) {
  alert ("La contrase�a no puede contener espacios en blanco");
  return false;
}
 
if (p1.length == 0 || p2.length == 0) {
  alert("Los campos del password no pueden quedar vacios");
  return false;
}
 
if (p1 != p2) {
  alert("Las passwords deben de coincidir");
  return false;
}else {
  return true;
}
}
 


function habilitar_lsd(obj){
document.getElementById(obj).disabled=false;
}

function inhabilitar_lsd(obj){
document.getElementById(obj).disabled=true;
}

function soloNumero_lsd(evt){
    var nav4 = window.Event ? true : false;
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || (key >= 48 && key <= 57) || key==46  );
}

function imprimir_lsd(div,msj){
document.getElementById(div).innerHTML=msj;	
}
function focus_lsd(obj){
document.getElementById(obj).focus();
}

function mostrar_lsd(div){
document.getElementById(div).style.display="block";
}

function ocultar_lsd(div){
document.getElementById(div).style.display="none";
}

function set_lsd(obj,valor){
document.getElementById(obj).value=valor;	
}

function espacios_lsd(valor){
return trim(valor);
}

function espacios_lsd(valor){
return trim(valor);
}

function mayus_lsd(valor){
return  toUpperCase(valor);
}

function minus_lsd(valor){
return  toLowerCase(valor);
}






var accion = {
	"url" 		: null,
	"salida"	: null,
	"motivo"	: null,
	"v1"	 	: null,
	"v2" 		: null,
	"v3" 		: null,
	"v4" 		: null,
	"v5" 		: null,
	"v6" 		: null,
	"v7" 		: null,
	"v8" 		: null,
	"v9" 		: null,
	"v10" 		: null,
	"v11" 		: null,
	"cantidad"      : 11,
	"url_neta"	: "/234",
	"error"		: function(msj){
	document.getElementById('error').innerHTML=msj;	
	},
	"vacio"		:function(x){
	
	
			if(x.length>0){
			return true;
			}else{
			return false;	
			}
	
	
	},
	
	"ejecutar"   : function(){

		url_x = this.url_neta+this.url;
	 	div = this.salida;
		var ajax=objetoAjax();
		
		ajax.open("POST","ajax.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");		
		
		if(this.cantidad==1){
		ajax.send("accion="+this.motivo+"&v1="+this.v1);	
		}else if(this.cantidad==2){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2);		
		}else if(this.cantidad==3){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3);	
		}else if(this.cantidad==4){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4);	
		}else if(this.cantidad==5){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5);	
		}else if(this.cantidad==6){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6);	
		}else if(this.cantidad==7){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7);	
		}else if(this.cantidad==8){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8);
		}else if(this.cantidad==9){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9);
		}else if(this.cantidad==10){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9+"&v10="+this.v10);
}else if(this.cantidad==11){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9+"&v10="+this.v10+"&v11="+this.v11);
}

		
		ajax.onreadystatechange=function(){

			if(ajax.readyState<4){
			document.getElementById(div).innerHTML="Cargando ...";			
			}

		    if (ajax.readyState==4){	
                    
                        if(div!==null){
                        document.getElementById(div).innerHTML=ajax.responseText;  
                        }
                    	
                    }
		}
	}


};






var accion_twig = {
	"url" 		: null,
	"salida"	: null,
	"motivo"	: null,
	"v1"	 	: null,
	"v2" 		: null,
	"v3" 		: null,
	"v4" 		: null,
	"v5" 		: null,
	"v6" 		: null,
	"v7" 		: null,
	"v8" 		: null,
	"v9" 		: null,
	"v10" 		: null,
	"v11" 		: null,
	"cantidad"      : 11,
	"url_neta"	: "controlador.php",
	"error"		: function(msj){
	document.getElementById('error').innerHTML=msj;	
	},
	"vacio"		:function(x){
	
	
			if(x.length>0){
			return true;
			}else{
			return false;	
			}
	
	
	},
	
	"ejecutar"   : function(){

		url_x = this.url_neta+this.url;
	 	div = this.salida;
		var ajax=objetoAjax();
		
		ajax.open("POST","../"+this.url+"/controlador.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");		
		
		if(this.cantidad==1){
		ajax.send("accion="+this.motivo+"&v1="+this.v1);	
		}else if(this.cantidad==2){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2);		
		}else if(this.cantidad==3){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3);	
		}else if(this.cantidad==4){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4);	
		}else if(this.cantidad==5){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5);	
		}else if(this.cantidad==6){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6);	
		}else if(this.cantidad==7){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7);	
		}else if(this.cantidad==8){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8);
		}else if(this.cantidad==9){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9);
		}else if(this.cantidad==10){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9+"&v10="+this.v10);
}else if(this.cantidad==11){
		ajax.send("accion="+this.motivo+"&v1="+this.v1+"&v2="+this.v2+"&v3="+this.v3+"&v4="+this.v4+"&v5="+this.v5+"&v6="+this.v6+"&v7="+this.v7+"&v8="+this.v8+"&v9="+this.v9+"&v10="+this.v10+"&v11="+this.v11);
}

		
		ajax.onreadystatechange=function(){

			if(ajax.readyState<4){
			document.getElementById(div).innerHTML="Cargando ...";			
			}

		    if (ajax.readyState==4){	
                    
                        if(div!==null){
                        document.getElementById(div).innerHTML=ajax.responseText;  
                        }
                    	
                    }
		}
	}
	


};






function accion_lsd(motivo,parametro,salida){
var x    =  accion;
x.motivo = motivo;
x.v1 	 = parametro[0];
x.v2 	 = parametro[1];
x.v3 	 = parametro[2];
x.v4 	 = parametro[3];
x.v5 	 = parametro[4];
x.v6 	 = parametro[5];
x.v7 	 = parametro[6];
x.v8 	 = parametro[7];
x.v9 	 = parametro[8];
x.salida = salida; 
x.ejecutar();
}

function ver_lsd(funcion,parametro){
var x    =  accion;
x.motivo = 'url';
x.v1 	 = funcion;
x.v2 	 = parametro;
x.salida = 'seccion'; 
x.ejecutar();
}



function twig_lsd(modulo,motivo,parametro,salida){
var x    =  accion_twig;
x.motivo = motivo;
x.url    = modulo;
x.v1 	 = parametro[0];
x.v2 	 = parametro[1];
x.v3 	 = parametro[2];
x.v4 	 = parametro[3];
x.v5 	 = parametro[4];
x.v6 	 = parametro[5];
x.v7 	 = parametro[6];
x.v8 	 = parametro[7];
x.v9 	 = parametro[8];
x.salida = salida; 
x.ejecutar();
}



function validarfecha(fecha){
      var fechaf = fecha.split("/");
      var day = fechaf[0];
      var month = fechaf[1];
      var year = fechaf[2];
      var date = new Date(year,month,'0');
      if((day-0)>(date.getDate()-0)){
            return false;
      }
      return true;
}
 