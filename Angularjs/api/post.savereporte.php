<?php

session_start();
$user = $_SESSION['user'];

if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
  header("location: ../../");
  exit;
};
require_once("../config/class.Database.php");

$postdata = file_get_contents("php://input");
$jsonarray = json_decode($postdata, true);


foreach ($jsonarray as $row) {
    $id = $row['id'];
    $mensaje =$row['mensaje'];

    $sql ="INSERT INTO tbl_reportes(Id_Detalle,Re_Descripcion, Re_Estado)
         VALUES ($id,
                 '$mensaje',
                'Pendiente')";
    
                $hecho = Database::ejecutar_idu( $sql );
    
        $sql = "UPDATE tbl_detalle_it
        SET
        DIT_Estado    = 'Esperando'
        WHERE id_Detalle_IT= $id";
    
        $hecho = Database::ejecutar_idu( $sql );
    
    if (is_numeric($hecho) OR $hecho === true) {
      $respuesta = array ( 'err'=>false, 'Mensaje'=>'Enviado!');
    
    }else {
      $respuesta = array ( 'err'=>true, 'Mensaje'=>$hecho);
    }
   
};
// foreach ($jsonarray as $row) {
//     $idDetalle = $row["id_Detalle_IT"];
  
   
// };

// if ( isset( $request[0] )) { // verifica si esta definida o no


// }else { //insertar
    
//     $sql ="INSERT INTO tbl_reportes(Id_Detalle,Re_Descripcion, Re_Estado)
//     VALUES ('".$request['ids']."',
//             '".$request['descripcion']."',
//             'Pendiente')";

//             $hecho = Database::ejecutar_idu( $sql );

//     $sql = "UPDATE tbl_detalle_it
//     SET
//     DIT_Estado    = 'Pendiente'
//     WHERE id_Detalle_IT=" . $request['ids'];

//     $hecho = Database::ejecutar_idu( $sql );

// if (is_numeric($hecho) OR $hecho === true) {
//   $respuesta = array ( 'err'=>false, 'Mensaje'=>'Enviado!');

// }else {
//   $respuesta = array ( 'err'=>true, 'Mensaje'=>$hecho);
// }
// }
// $jsonarray[0]['id_Detalle_IT'])
echo json_encode($respuesta );


?>