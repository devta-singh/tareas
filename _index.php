<?php //_index.php

ini_set("display_errors", 1);
error_reporting(15);

require_once("./inc/clases/clase_tarea.php");


$mi_tarea = new tarea();

$mi_tarea->listar();

?>