/**
 * @author angel
 * @web www.webadictos.com.mx
 * @mail daniel@webadictos.com.mx
 *
 * Función que incluye los archivos CSS o Javascript
 * en la página.
 * Uso: 
 * include("path/to/archive");
 * Tambien acepta un segundo parametro que es un objeto que puede contener los siguientes valores
 * - cache: true | false Si se especifica false se le añade al final del archivo un numero aleatorio para que no se guarde cache en el navegador
 * - dom: true | false si se especifica true el archivo se cargará creando un nuevo elemento script o link en el DOM, si es false el archivo se cargará con un document.write
 * - type: js | css  este parámetro solo es necesario si el archivo a cargar tiene una extensión diferente a js o css por ej un archivo .php
 * - onload: funcion | en el caso de que se cargue un archivo js via DOM es posible especificar una acción al finalizar la carga del archivo.
 */

function include(file,opt){
	
	if(file=="") return;

	//Genera una id para el archivo con el fin de evitar que se cargue 2 veces.

	idfile = file.replace(location.hostname,"");
	idfile = idfile.replace(location.protocol,"");
	idfile = idfile.replace("//","");

	if(document.getElementById(idfile)){ return };
		
	if(typeof opt=="undefined") opt = {};
	if(typeof opt.cache=="undefined") opt.cache = true;
	if(typeof opt.dom=="undefined")  opt.dom = false;
	if(typeof opt.type=="undefined")  opt.type = "";
	
	
	ext = (opt.type!="") ? opt.type : file.substring(file.lastIndexOf('.')+1);

	if(!opt.cache){
	    var random = new Date().getTime().toString();    	 
		if(file.indexOf("?")!=-1) file = file+"&"+random;
		else file = file+"?"+random; 
	}
	
	if(opt.dom){
		var head = document.getElementsByTagName('head').item(0)	
	}
	
	
	switch(ext){
		case "css":
		  if(!opt.dom)	
			document.write('<link rel="stylesheet" href="'+file+'" id="'+idfile+'" type="text/css"><\/link>');
		  else{
		    css = document.createElement('link');
		    css.rel  = 'stylesheet';
		    css.href = file;
			css.type = 'text/css';
			css.id = idfile;
			head.appendChild(css); 		  	
		  }			
		break;
		
		case "js":
		 if(!opt.dom){
			document.write('<script type="text/javascript" id="'+idfile+'" src="'+file+'"><\/script>');
		 }
		 else{
		    script = document.createElement('script');
		    script.src = file;
			script.type = 'text/javascript';
			script.id = idfile;
			head.appendChild(script);
			
			if(typeof opt.oncomplete!="undefined"){
				//Para IE
			    script.onreadystatechange = function () {if (script.readyState == 'complete') {if(typeof opt.oncomplete == "function") {eval(opt.oncomplete());}}}
				//Para Firefox
			    script.onload = function () {if(typeof opt.oncomplete == "function") {opt.oncomplete();}}
			}	 	
		 }

		break;
	}
}