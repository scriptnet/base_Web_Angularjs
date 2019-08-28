<?php

session_start();
$user = $_SESSION['user'];

if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
  header("location: ../../");
  exit;
};


$method = $_SERVER['REQUEST_METHOD'];
if ('PUT' === $method) {

    require_once("../config/class.Database.php");
    
  
    

    $postdata = file_get_contents("php://input");
    $jsonarray = json_decode($postdata, true);

    foreach ($jsonarray as $row) {
        $id = $row['id'];
        $sql = "UPDATE tbl_detalle_it SET
                DIT_Estado = 'Rechazado'
                WHERE id_Detalle_IT=" .$id;
        $hecho = Database::ejecutar_idu( $sql );

    }
    if (is_numeric($hecho) OR $hecho === true) {
        $respuesta = array ( 'err'=>false, 'Mensaje'=>'Actualizado!');
      
      }else {
        $respuesta = array ( 'err'=>true, 'Mensaje'=>$hecho);
      }


   

}else{
    echo "============================\n maldito perro que haces aqui? -.-";
    $respuesta = Array ( 'err'=>true, 'Mensaje'=>'Error de data!');
}

echo json_encode($respuesta);
?>