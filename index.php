<?php //_index.php

ini_set("display_errors", 1);
error_reporting(15);

require_once("./inc/clases/clase_tarea.php");

$op=null;
if(isset($_REQUEST["op"])){
	$op = $_REQUEST["op"];
}
if(isset($_REQUEST["t"])){
	$t = $_REQUEST["t"];
}


$mi_tarea = new tarea();

switch($op){
	//crea un registro en la tabla tareas
	case 'crear':
	{
		//$datos = $mi_tarea->cargar_datos("post");
		//$mi_tarea->grabar($datos);

		$datos = $mi_tarea->recuperar_datos(array("nombre","descripcion","id_madre"));
		$mi_tarea->crear($datos);
		$t = time();
		$sustituciones=array(
			"titulo"=> "Formulario Tareas",
			"destino"=>"tarea.php?t=$t&op=grabar",
			"nombre"=> "nombre de tarea",
			"descripcion"=> "aqui la descripción"
		);
		print "<br>".$mi_tarea->form($sustituciones);		
	}	

	case 'grabar':
	{
		//$datos = $mi_tarea->cargar_datos("post");
		//$mi_tarea->grabar($datos);

		$datos = $mi_tarea->recuperar_datos(array("nombre","descripcion","id_madre"));
		$mi_tarea->grabar($datos);
		$t = time();
		$sustituciones=array(
			"titulo"=> "Formulario Tareas",
			"destino"=>"?t=$t&op=modificar",
			"nombre"=> "nombre de tarea",
			"descripcion"=> "aqui la descripción"

		);
		print "<br>".$mi_tarea->form($sustituciones);		
	}	

	default:
	{
		$mi_tarea->listar();

		$t = time();
		$sustituciones=array(
			"titulo"=> "Formulario Tareas",
			"destino"=>"?t=$t&op=grabar",
			"nombre"=> "nombre de tarea",
			"descripcion"=> "aqui la descripción",
			"opciones_id_madre"=>$mi_tarea->opciones_id_madre
		);
		print "<br>".$mi_tarea->form($sustituciones);
	}
}
?>