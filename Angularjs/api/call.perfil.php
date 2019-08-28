<?php
session_start();
$user = $_SESSION['user'];

$inst = $_SESSION['inst'];


if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("location: ../../");
    exit;
  }
// importamos

  require_once("../config/class.Database.php");
  $pag = 1;

  $respuesta = Database::get_todo_paginadoPefil( 'tbl_institucion', $pag, $inst );

echo json_encode( $respuesta );
?>