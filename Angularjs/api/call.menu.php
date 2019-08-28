<?php
session_start();
$user = $_SESSION['user'];
$iduser = $_SESSION['iduser'];



if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("location: ../../");
    exit;
  }
// importamos

  require_once("../config/class.Database.php");
  $pag = 1;

  $respuesta = Database::get_todo_paginadoMenu( 'tbl_detalle_mu', $pag, $iduser );

echo json_encode( $respuesta );
?>