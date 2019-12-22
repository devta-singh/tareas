<?php //tareas.php

/*
Programa para mantener lista de tareas.

	Las características sin las siguientes:
	-Se puede crear una tarea en cualquier momento.
	-Se puede comentar una tarea en cualquier momento.
	-Una tarea puede ser tarea principal o depender de otra (tener una tarea madre)

La página muestra la lista de tareas y con cada tarea un botón para crear una hija de esa tarea.

Un formulario para crear una tarea.

Los campos a registrar son: nombre, descripcion, id_madre, cuando se crea, cuando_se_modifica.

El SQL:
CREATE TABLE _tareas (
	id_tarea integer primary key auto_increment,
	id_madre integer default '0',
	nombre varchar(50) not null default '',
	descripcion text null,
	cuando_se_crea datetime null,
	cuando_se_modifica timestamp null default CURRENT_TIMESTAMP
);

La clase tarea se debe definir con las siguientes funciones:
tarea_crear
tarea_modificar
tarea_borrar
tarea_cargar
tarea_crear_hija
tarea_obtener_madre
tarea_editar
tarea_listar
tarea_form

y la secuencia de funcionamiento debe ser esta:

tarea_listar (muestra el menu de tareas con la lista de tareas y un enlace a crear tarea nueva, eventualmente muestra un mensaje)

tarea_form > tarea_crear > tarea_listar (con mensaje)
tarea_form > tarea_modificar > tarea_listar (con mensaje)
tarea_listar > tarea_form > tarea_modificar > tarea_listar
tarea_listar > tarea_borrar
tarea_listar > tarea_crear (con madre) > tarea_listar (con mensaje)


EL código PHP


*/

//definimos las constantes para la conexion por defecto de la BBDD
define ("db_server", "localhost");
define ("db_user", "root");
define ("db_pass", "root");
define ("db_ddbb", "tareas");

class base{
	
	//establecemos la variable interna privada mysqli
	//solo desde los métodos de la clase se puede usar
	private $mysqli=null;
	private $num_filas=0;
	private $filas_afectadas=0;
	private $last_insert_id=0;
	private $error="";
	private $errno=0;
	private $resultados=null;
	public $plantillas = array();

	function __construct ($server=db_server, $user=db_user, $pass=db_pass, $ddbb=db_ddbb){
		if($this->mysqli = new Mysqli($server, $user, $pass, $ddbb)){
			return($this->mysqli);
		}else{
			return(false);
		}
	}

	public function consulta($sql){
		if($this->resultados = $this->mysqli->query($sql)){
			$this->num_filas = $this->resultados->num_rows;
			return($this->resultados);
		}else{
			$this->error = $this->mysqli->error;
			$this->errno = $this->mysqli->errno;
			$this->num_filas = 0;
			return(false);
		}
		
		$this->filas_afectadas = $this->mysqli->affected_rows;
		$this->last_insert_id = $this->mysqli->insert_id;
	}

	public function recuperar_datos($lista=array(),$origen="REQUEST"){
		//recorremos las opciones de origen
		switch($origen){

			default:
			{
				//leemos de REQUEST, a saco
				$datos = array();
				foreach($_REQUEST as $clave => $valor){
					if(in_array($clave, $lista)){
						$datos["$clave"] = $valor;
					}
				}
				return($datos);
				break;
			}
		}//fin switch

		return(FALSE);
	}//fin function

}//fin clase base

class plantilla{
	var $contenido;
	var $mensajes;
	var $error;
	function __construct($contenido="", $es_fichero=false){
		if($es_fichero){
			//Para hacer falta comprobar que el fichero exista
			// y alimentar los mensajes y el codigo o mensaje de error
			$this->contenido=file_get_contents($contenido);
		}else{
			$this->contenido=$contenido;
		}
	}

	public function reemplaza($que_busco, $que_pongo){
		$this->contenido = str_replace($que_busco, $que_pongo, $this->contenido);
	}

	public function volcar(){
		return($this->contenido);
	}
}

class tarea extends base{

	var $plantilla;
	var $lista_variables_a_cargar = array("id_tarea","nombre","descripcion");
	var $varios=array();
	var $A_id_madre=array();
	var $opciones_id_madre="";

	private function cargar_datos($origen = "post"){
		$valores = array();

		//$mi_tarea->grabar($datos);

		if($origen=="post"){
			//ver las variables que llegan por post
			foreach($_POST as $clave => $valor){
				print "<br>recuperando de PSOT la variable $clave con valor: $valor";
				//comprobamos si está en la lista de variables a cargar
				if(in_array($clave, $lista_variables_a_cargar)){
					$this->$clave = $valor;
					$valores["clave"]=$valor;
				}

			}
			return($valores);
		}
		return(array());
	}

	public function grabar($datos){
		foreach($datos as $clave => $valor){

		}
	}
	
	public function crear($nombre, $descripcion, $id_madre){

		$sql = "INSERT INTO "._tabla_tareas." SET nombre='$nombre', descripcion='$descripcion', id_madre='$id_madre'";
		$this->consulta($sql);
		if($this->resultados){
			$id = $this->last_insert_id;
			return($id);
		}else{
			return(false);
		}
	}

	public function modificar($id_tarea, $id_madre, $nombre, $descripcion){
		$sql = "UPDATE "._tabla_tareas." SET nombre='$nombre', descripcion='$descripcion', id_madre='$id_madre' WHERE id_tarea = '$id_tarea'" ;
		$this->consulta($sql);
		if($this->resultados && $this->filas_afectadas){
			return(true);
		}else{
			return(false);
		}

	}

	public function borrar(){}

	public function cargar(){}

	public function crear_hija(){}

	public function obtener_madre(){}

	public function editar(){}

	public function listar($id_madre=0){
		$sql = "SELECT * FROM _tareas ORDER BY id_madre ASC, nombre ASC ";
		$resultados = $this->consulta($sql);

		$salida="";
		while($datos = $resultados->fetch_assoc()){
			$id_tarea = $datos["id_tarea"];
			


			$id_madre = $datos["id_madre"];
			$nombre = $datos["nombre"];
			$descripcion = $datos["descripcion"];

			$this->A_id_madre[$id_madre]=$nombre;
			$this->opciones_id_madre.=<<<fin
			<option value="$id_madre" title="$descripcion">$nombre</option>
fin;

			$salida.= <<<fin
				<br><a href="tarea_ver.php?id=$id_tarea" title="$descripcion">$nombre</a>
fin;


		}//fin while
		print $salida;

	}

	public function form($sustituciones=null){
		//lee la plantilla del formulario
		$this->plantillas["form"]= new plantilla("./plantillas/form_tarea.html", TRUE);

		if(is_array($sustituciones)){
			foreach($sustituciones as $clave => $valor){
				$this->plantillas["form"]->reemplaza("#$clave#",$valor);
			}
		}

		//toma su contenido
		$html_form = $this->plantillas["form"]->volcar();
		return($html_form);
		
	}
}



?>