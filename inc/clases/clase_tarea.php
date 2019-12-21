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
}
class tarea extends base{
	
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
		$sql = "SELECT * FROM _tareas ";
		$resultados = $this->consulta($sql);

		while($datos = $resultados->fetch_assoc()){
			$id_tarea = $datos["id_tarea"];
			$id_madre = $datos["id_madre"];
			$nombre = $datos["nombre"];
			$descripcion = $datos["descripcion"];

			print <<<fin
				<br><a href="tarea_ver.php?id='$id_tarea'" title="$descripcion">$nombre</a>
fin;

		}

	}

	public function form(){
		//lee la plantilla del formulario


	}
}



?>