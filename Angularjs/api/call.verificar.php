<?php
session_start();
$user = $_SESSION['user'];

$inst = $_SESSION['inst'];

if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
  header("location: ../../");
  exit;
};
// Incluir el archivo de base de datos
include_once("../config/class.Database.php");


// if( isset( $_GET["pag"] ) ){
// 	$pag = $_GET["pag"];
// }else{
// 	$pag = 1;
// }

$buscar = $_GET["filter"];
$por_pagina = $_GET["limit"];
$pag = $_GET["page"];


$respuesta = Database::get_todo_paginado_adm_alltitulados( 'tbl_detalle_it', $pag, $buscar, $por_pagina );



echo json_encode( $respuesta );


?>
